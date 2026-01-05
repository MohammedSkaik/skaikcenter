<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;

class EducationStageController extends Controller
{
    // Grades (Marhala)
    public function index()
    {
        return response()->json([
            'data' => Grade::with('subjects')->orderBy('level_order')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:grades,name',
            'level_order' => 'integer'
        ]);

        $grade = Grade::create($validated);
        return response()->json(['message' => 'Grade created', 'data' => $grade], 201);
    }

    public function update(Request $request, Grade $grade)
    {
        $validated = $request->validate([
            'name' => 'string|unique:grades,name,' . $grade->id,
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
            'price_package' => 'numeric|min:0',
            'price_single' => 'numeric|min:0'
        ]);

        // Check if already attached
        if ($grade->subjects()->where('subject_id', $validated['subject_id'])->exists()) {
            return response()->json(['message' => 'Subject already attached to this grade'], 422);
        }

        $grade->subjects()->attach($validated['subject_id'], [
            'price_package' => $validated['price_package'] ?? 0,
            'price_single' => $validated['price_single'] ?? 0
        ]);

        return response()->json(['message' => 'Subject attached successfully']);
    }

    // Update Subject Prices in Grade
    public function updateSubject(Request $request, Grade $grade, Subject $subject)
    {
        $validated = $request->validate([
            'price_package' => 'numeric|min:0',
            'price_single' => 'numeric|min:0'
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
}
