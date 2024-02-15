<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'user1',
            'last_name' => 'user1',
            'email' => 'user1@example.com',
            'phone' => '0632434344',
            'password' => bcrypt('123'),
            // 'c_password' => bcrypt('123'),
        ]);
        User::create([
            'first_name' => 'user2',
            'last_name' => 'user2',
            'email' => 'user2@example.com',
            'phone' => '0632434345',
            'password' => bcrypt('123'),
            // 'c_password' => bcrypt('123'),
        ]);
        User::create([
            'first_name' => 'user3',
            'last_name' => 'user3',
            'email' => 'user3@example.com',
            'phone' => '0632434346',
            'password' => bcrypt('123'),
            // 'c_password' => bcrypt('123'),
        ]);
        User::create([
            'first_name' => 'user4',
            'last_name' => 'user4',
            'email' => 'user4@example.com',
            'phone' => '0632434347',
            'password' => bcrypt('123'),
            // 'c_password' => bcrypt('123'),
        ]);
    }
}
