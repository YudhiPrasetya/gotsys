<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $user = User::firstOrCreate([
            'username' => 'Admin',
            'email' => 'admin@embuh.com',
            'email_verified_at' => Carbon::now('Asia/Jakarta'),
            'name' => 'Admin User',
            'password' => bcrypt('Admin123!')
        ]);

        // Create admin role
        $role = Role::firstOrCreate(['name' => 'Admin']);

        // Get all permissions
        $permissions = Permission::pluck('id', 'id')->all();

        // Sync all permissions to admin role
        $role->syncPermissions($permissions);

        // Assign admin role to user
        $user->assignRole([$role->id]);
    }
}
