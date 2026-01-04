<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Group permissions by common prefixes/modules if we stick to a naming convention like 'manage_users'
        // For now, just listing all.
        $permissions = Permission::all();

        // Optional: Grouping logic could be added here

        return response()->json([
            'data' => $permissions
        ]);
    }
}
