<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;

class StructureController extends Controller
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

    // Subjects (Madda)
    public function storeSubject(Request $request, Grade $grade)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price_package' => 'numeric|min:0',
            'price_single' => 'numeric|min:0'
        ]);

        $subject = $grade->subjects()->create($validated);
        return response()->json(['message' => 'Subject created', 'data' => $subject], 201);
    }

    public function updateSubject(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'string',
            'price_package' => 'numeric|min:0',
            'price_single' => 'numeric|min:0'
        ]);

        $subject->update($validated);
        return response()->json(['message' => 'Subject updated', 'data' => $subject]);
    }

    public function destroySubject(Subject $subject)
    {
        $subject->delete();
        return response()->json(['message' => 'Subject deleted']);
    }
}
