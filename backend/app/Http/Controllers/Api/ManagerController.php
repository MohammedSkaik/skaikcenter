<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ManagerController extends Controller
{
    /**
     * Display a listing of managers (users with admin/center_admin types).
     */
    public function index()
    {
        // Filter users who are admins
        $managers = User::whereIn('type', ['center_admin', 'sub_admin'])
            ->with(['roles', 'permissions']) // Eager load roles
            ->get();

        return response()->json(['data' => $managers]);
    }

    /**
     * Store a newly created manager.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->whereNull('deleted_at')->where('is_deleted', 0)
            ],
            'phone' => 'nullable|string|max:20',
            'identification_number' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users')->whereNull('deleted_at')->where('is_deleted', 0)
            ],
            'password' => 'required|string|min:8',
            'type' => ['required', Rule::in(['center_admin', 'sub_admin'])],
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'array', // Direct permissions override
            'permissions.*' => 'exists:permissions,name'
        ]);

        $manager = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'identification_number' => $validated['identification_number'] ?? null,
            'password' => Hash::make($validated['password']),
            'type' => $validated['type'],
            'status' => 'active'
        ]);

        // Assign Roles
        if (!empty($validated['roles'])) {
            $manager->assignRole($validated['roles']);
        }

        // Assign Direct Permissions (Hybrid system)
        if (!empty($validated['permissions'])) {
            $manager->givePermissionTo($validated['permissions']);
        }

        return response()->json([
            'message' => 'Manager created successfully',
            'data' => $manager->load(['roles', 'permissions'])
        ], 201);
    }

    /**
     * Display the specified manager.
     */
    public function show(string $id)
    {
        $manager = User::with(['roles', 'permissions'])
            ->whereIn('type', ['center_admin', 'sub_admin'])
            ->findOrFail($id);

        return response()->json(['data' => $manager]);
    }

    /**
     * Update the specified manager.
     */
    public function update(Request $request, string $id)
    {
        $manager = User::whereIn('type', ['center_admin', 'sub_admin'])->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users')->ignore($manager->id)->whereNull('deleted_at')->where('is_deleted', 0)
            ],
            'phone' => 'nullable|string',
            'type' => ['sometimes', Rule::in(['center_admin', 'sub_admin'])],
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'array',
        ]);

        $manager->update($request->only(['name', 'email', 'phone', 'type', 'status']));

        // Sync Roles
        if ($request->has('roles')) {
            $manager->syncRoles($validated['roles']);
        }

        // Sync Direct Permissions
        if ($request->has('permissions')) {
            $manager->syncPermissions($validated['permissions']);
        }

        return response()->json([
            'message' => 'Manager updated successfully',
            'data' => $manager->load(['roles', 'permissions'])
        ]);
    }

    /**
     * Remove the specified manager.
     */
    public function destroy(string $id)
    {
        $manager = User::whereIn('type', ['center_admin', 'sub_admin'])->findOrFail($id);
        $manager->delete(); // Soft delete based on Model

        return response()->json(['message' => 'Manager deleted successfully']);
    }
}
