<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'User-list', 'User-show', 'User-create','User-edit','User-delete',
            'Role-list', 'Role-show', 'Role-create', 'Role-edit','Role-delete',
            'Permission-list', 'Permission-show', 'Permission-create', 'Permission-edit', 'Permission-delete',
            'Dashboard-access',
            'ProcessSequence-list', 'ProcessSequence-show', 'ProcessSequence-create', 'ProcessSequence-insert', 'ProcessSequence-edit', 'ProcessSequence-delete',
            'Customer-list', 'Customer-show', 'Customer-create', 'Customer-edit', 'Customer-delete',
            'Order-list', 'Order-show', 'Order-create', 'Order-edit', 'Order-delete',
            'ProcessSequenceOrder-list', 'ProcessSequenceOrder-show', 'ProcessSequenceOrder-create', 'ProcessSequenceOrder-edit', 'ProcessSequenceOrder-delete',
            'ProgressOrder-list', 'ProgressOrder-create', 'ProgressOrder-edit'
        ];
        foreach($permissions as $p){
            Permission::firstOrCreate(['name' => $p]);
        }
    }
}
