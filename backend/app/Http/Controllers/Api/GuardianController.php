<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuardianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Guardian::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('identification_number', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'data' => $query->latest()->paginate(15)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('guardians')->whereNull('deleted_at')->where('is_deleted', 0)
            ],
            'email' => 'nullable|email|max:255',
            'identification_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('guardians')->whereNull('deleted_at')->where('is_deleted', 0)
            ],
            'notes' => 'nullable|string'
        ]);

        $guardian = Guardian::create(array_merge($validated, ['is_deleted' => 0]));

        return response()->json([
            'message' => 'Guardian created successfully',
            'data' => $guardian
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $guardian = Guardian::findOrFail($id);
        return response()->json(['data' => $guardian]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guardian $guardian)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('guardians')->ignore($guardian->id)->whereNull('deleted_at')->where('is_deleted', 0)
            ],
            'email' => 'nullable|email|max:255',
            'identification_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('guardians')->ignore($guardian->id)->whereNull('deleted_at')->where('is_deleted', 0)
            ],
            'notes' => 'nullable|string'
        ]);

        $guardian->update($validated);

        return response()->json([
            'message' => 'Guardian updated successfully',
            'data' => $guardian
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guardian $guardian)
    {
        // Check for related students later
        $guardian->delete();

        return response()->json(['message' => 'Guardian deleted successfully']);
    }
}
