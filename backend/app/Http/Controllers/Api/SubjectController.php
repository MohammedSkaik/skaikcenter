<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Subject::orderBy('name', 'asc')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                \Illuminate\Validation\Rule::unique('subjects')->whereNull('deleted_at')->where('is_deleted', 0)
            ]
        ]);

        $subject = Subject::create($validated);
        return response()->json(['message' => 'Subject created', 'data' => $subject], 201);
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => [
                'string',
                \Illuminate\Validation\Rule::unique('subjects')->ignore($subject->id)->whereNull('deleted_at')->where('is_deleted', 0)
            ]
        ]);

        $subject->update($validated);
        return response()->json(['message' => 'Subject updated', 'data' => $subject]);
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return response()->json(['message' => 'Subject deleted']);
    }
}
