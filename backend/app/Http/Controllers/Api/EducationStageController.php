<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;

class EducationStageController extends Controller
{
    // Get Grades (Filtered by Academic Year)
    public function index(Request $request)
    {
        $yearId = $request->query('year_id');

        $query = Grade::with(['creator', 'subjects']);

        if ($yearId) {
            $query->where('academic_year_id', $yearId);
        }

        return response()->json([
            'data' => $query->orderBy('level_order')->get()
        ]);
    }

    // Store Grade
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'level_order' => 'required|integer',
            'package_price' => 'numeric|min:0'
        ]);

        $grade = Grade::create([
            'academic_year_id' => $validated['academic_year_id'],
            'name' => $validated['name'],
            'level_order' => $validated['level_order'],
            'package_price' => $validated['package_price'] ?? 0,
            'is_deleted' => 0 // Force active status
        ]);

        return response()->json(['message' => 'Grade created successfully', 'data' => $grade], 201);
    }

    public function update(Request $request, Grade $grade)
    {
        $validated = $request->validate([
            'name' => 'string',
            'level_order' => 'integer'
        ]);

        $grade->update($validated);
        return response()->json(['message' => 'Grade updated', 'data' => $grade]);
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();
        return response()->json(['message' => 'Grade deleted']);
    }

    // Attach Subject to Grade
    public function storeSubject(Request $request, Grade $grade)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'price_single' => 'numeric|min:0',
            'price_one_session' => 'numeric|min:0'
        ]);

        // Check if already attached
        if ($grade->subjects()->where('subject_id', $validated['subject_id'])->exists()) {
            return response()->json(['message' => 'Subject already attached to this grade'], 422);
        }

        $grade->subjects()->attach($validated['subject_id'], [
            'price_single' => $validated['price_single'] ?? 0,
            'price_one_session' => $validated['price_one_session'] ?? 0
        ]);

        return response()->json(['message' => 'Subject attached successfully']);
    }

    // Update Subject Prices in Grade
    public function updateSubject(Request $request, Grade $grade, Subject $subject)
    {
        $validated = $request->validate([
            'price_single' => 'numeric|min:0',
            'price_one_session' => 'numeric|min:0'
        ]);

        $grade->subjects()->updateExistingPivot($subject->id, $validated);

        return response()->json(['message' => 'Subject prices updated']);
    }

    // Detach Subject from Grade
    public function destroySubject(Grade $grade, Subject $subject)
    {
        $grade->subjects()->detach($subject->id);
        return response()->json(['message' => 'Subject detached']);
    }

    // Import Grades from Previous Year
    public function importFromPrevious(Request $request)
    {
        $validated = $request->validate([
            'target_year_id' => 'required|exists:academic_years,id'
        ]);

        $targetYearId = $validated['target_year_id'];

        // Find a previous year (latest one that is not the target)
        $previousYear = \App\Models\AcademicYear::where('id', '!=', $targetYearId)
            ->where('start_date', '<', function ($query) use ($targetYearId) {
                $query->select('start_date')->from('academic_years')->where('id', $targetYearId);
            })
            ->orderBy('start_date', 'desc')
            ->first();

        // Fallback: If no date logic matches, just get the latest different one
        if (!$previousYear) {
            $previousYear = \App\Models\AcademicYear::where('id', '!=', $targetYearId)->latest()->first();
        }

        if (!$previousYear) {
            return response()->json(['message' => 'Unfortunately, no previous academic year found to import from.'], 404);
        }

        // DB Transaction for safety
        \DB::transaction(function () use ($previousYear, $targetYearId) {
            $prevGrades = Grade::where('academic_year_id', $previousYear->id)->with('subjects')->get();

            foreach ($prevGrades as $prevGrade) {
                // Check duplicate name in target year
                if (Grade::where('academic_year_id', $targetYearId)->where('name', $prevGrade->name)->exists()) {
                    continue; // Skip existing
                }

                // Create Grade
                $newGrade = Grade::create([
                    'academic_year_id' => $targetYearId,
                    'name' => $prevGrade->name,
                    'level_order' => $prevGrade->level_order,
                    'package_price' => $prevGrade->package_price,
                    'description' => $prevGrade->description
                ]);

                // Attach Subjects
                foreach ($prevGrade->subjects as $subject) {
                    $newGrade->subjects()->attach($subject->id, [
                        'price_single' => $subject->pivot->price_single,
                        'price_one_session' => $subject->pivot->price_one_session
                    ]);
                }
            }
        });

        return response()->json(['message' => 'Data imported successfully from ' . $previousYear->name]);
    }
}
