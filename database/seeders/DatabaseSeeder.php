<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Role::create(['name' => 'admin']);
        // Role::create(['name' => 'server']);

        // // Create permissions
        // Permission::create(['name' => 'add staff member']);
        // Permission::create(['name' => 'create order']);

        // // Assign permissions to roles
        // $admin = Role::findByName('admin');
        // $server = Role::findByName('server');

        // $admin->givePermissionTo(["add staff member", "create order"]);
        // $server->givePermissionTo("create order");
        // In UsersTableSeeder.php
        $adminUser = User::where('email', 'user1@example.com')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $adminUser->assignRole($adminRole);

        
        $serverUser = User::where('email', 'user2@example.com')->first();
        $serverRole = Role::where('name', 'server')->first();
        $serverUser->assignRole($serverRole);

        $managerUser = User::where('email', 'user3@example.com')->first();
        $managerRole = Role::where('name', 'manager')->first();
        $managerUser->assignRole($managerRole);

        $adminRole->givePermissionTo('manage roles');
        $managerRole->givePermissionTo('manage permissions');
        $serverRole->givePermissionTo('create order');

    }
}
