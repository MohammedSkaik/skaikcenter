<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions (Basic placeholder permissions for now)
        $permissions = [
            'manage_users',
            'manage_academics',
            'manage_finance',
            'view_reports',
            'take_attendance',
            'view_own_data',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // create roles and assign created permissions

        // 1. Super Admin
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // 2. Center Admin
        $centerAdminRole = Role::firstOrCreate(['name' => 'center_admin']);
        $centerAdminRole->givePermissionTo(['manage_users', 'manage_academics', 'manage_finance', 'view_reports']);

        // 3. Sub Admin (Customizable, basic default)
        $subAdminRole = Role::firstOrCreate(['name' => 'sub_admin']);
        $subAdminRole->givePermissionTo(['manage_users']);

        // 4. Teacher
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $teacherRole->givePermissionTo(['take_attendance']);

        // 5. Student
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $studentRole->givePermissionTo(['view_own_data']);

        // 6. Guardian
        $guardianRole = Role::firstOrCreate(['name' => 'guardian']);
        $guardianRole->givePermissionTo(['view_own_data']); // view wards data logic will be in code

        // 7. Accountant
        $accountantRole = Role::firstOrCreate(['name' => 'accountant']);
        $accountantRole->givePermissionTo(['manage_finance', 'view_reports']);


        // Create Super Admin User
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@skaikcenter.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'type' => 'super_admin',
                'status' => 'active',
                'phone' => '0000000000',
                'identification_number' => '000000000',
                'email_verified_at' => now(),
            ]
        );

        $superAdmin->assignRole($superAdminRole);
    }
}
