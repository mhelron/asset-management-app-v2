<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Seeder for roles
            $this->call([
            RolesTableSeeder::class,
        ]);

        // Create a default admin user
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@test.com', // Using 'admin' as the email
            'user_role' => 'admin', // Assuming you have a user_role field
            'password' => Hash::make('pass'), // Password is 'pass'
            'department_id' => null, // Set this if you have a department
        ]);

        // Call the UsersTableSeeder to add more users
        $this->call(UsersTableSeeder::class);
    }
}
