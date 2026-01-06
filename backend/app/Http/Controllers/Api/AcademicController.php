<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;

class AcademicController extends Controller
{
    // Academic Years
    public function index()
    {
        return response()->json([
            'data' => AcademicYear::with('semesters')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                \Illuminate\Validation\Rule::unique('academic_years')->whereNull('deleted_at')
            ],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $year = AcademicYear::create($validated);

        // Auto-create simplified default semesters if requested (Optional logic, skipping for now to keep explicit)

        return response()->json(['message' => 'Year created', 'data' => $year], 201);
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => [
                'string',
                \Illuminate\Validation\Rule::unique('academic_years')->ignore($academicYear->id)->whereNull('deleted_at')
            ],
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'status' => 'in:active,inactive,archived'
        ]);

        $academicYear->update($validated);

        return response()->json(['message' => 'Year updated', 'data' => $academicYear]);
    }

    public function destroy(AcademicYear $academicYear)
    {
        // Force delete related data to ensure clean removal
        // Note: Ideally these should be foreign key cascades, but explicit delete is safer for logic awareness
        \DB::transaction(function () use ($academicYear) {
            // Delete Classes ? (Study Classes usually depend on Semester OR Year)
            // $academicYear->study_classes()->delete(); 

            // Delete Grades (Education Stages)
            $academicYear->grades()->delete();

            // Delete Semesters
            $academicYear->semesters()->delete();

            // Finally delete the year
            $academicYear->delete();
        });

        return response()->json(['message' => 'Year and all related data deleted successfully']);
    }

    // Semesters
    public function storeSemester(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'in:active,inactive,archived'
        ]);

        $semester = $academicYear->semesters()->create($validated);

        return response()->json(['message' => 'Semester created', 'data' => $semester], 201);
    }

    public function updateSemester(Request $request, Semester $semester)
    {
        $validated = $request->validate([
            'name' => 'string',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'status' => 'in:active,inactive,archived'
        ]);

        $semester->update($validated);
        return response()->json(['message' => 'Semester updated', 'data' => $semester]);
    }

    public function destroySemester(Semester $semester)
    {
        $semester->delete();
        return response()->json(['message' => 'Semester deleted']);
    }
}
