<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudyClass;
use Illuminate\Http\Request;

class StudyClassController extends Controller
{
    public function index(Request $request)
    {
        $query = StudyClass::with(['grade', 'semester', 'subject']);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        return response()->json(['data' => $query->latest()->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'semester_id' => 'required|exists:semesters,id',
            'grade_id' => 'required|exists:grades,id',
            'name' => [
                'required',
                'string',
                \Illuminate\Validation\Rule::unique('study_classes')
                    ->where('semester_id', $request->semester_id)
                    ->where('grade_id', $request->grade_id)
                    ->whereNull('deleted_at')
                    ->where('is_deleted', 0)
            ],
            'type' => 'required|in:package,single',
            'subject_id' => 'required_if:type,single|nullable|exists:subjects,id',
            'max_students' => 'integer|min:1'
        ]);

        $studyClass = StudyClass::create($validated);
        return response()->json(['message' => 'Study Class created', 'data' => $studyClass], 201);
    }

    public function update(Request $request, StudyClass $studyClass)
    {
        $validated = $request->validate([
            'name' => [
                'string',
                \Illuminate\Validation\Rule::unique('study_classes')
                    ->where('semester_id', $studyClass->semester_id)
                    ->where('grade_id', $studyClass->grade_id)
                    ->ignore($studyClass->id)
                    ->whereNull('deleted_at')
                    ->where('is_deleted', 0)
            ],
            'max_students' => 'integer|min:1',
            'type' => 'in:package,single',
            'subject_id' => 'required_if:type,single|nullable|exists:subjects,id',
        ]);

        $studyClass->update($validated);
        return response()->json(['message' => 'Study Class updated', 'data' => $studyClass]);
    }

    public function destroy(StudyClass $studyClass)
    {
        $studyClass->delete();
        return response()->json(['message' => 'Study Class deleted']);
    }
}
