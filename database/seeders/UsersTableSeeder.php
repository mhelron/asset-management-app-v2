<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => "Ma'am Kimberly", 'department' => 'Accounting', 'role' => 'Staff'],
            ['name' => "Ma'am Dean Jacinto", 'department' => 'Accounting', 'role' => 'Staff'],
            ['name' => "Sir Clark", 'department' => 'Accounting', 'role' => 'Staff'],
            ['name' => "Ma'am Bridget F. Supang", 'department' => 'Accounting', 'role' => 'Staff'],
            ['name' => "Sir Mark Peralta", 'department' => 'Accounting', 'role' => 'Staff'],
            ['name' => "Ma'am Ma Rosario Dela Cruz", 'department' => 'Accounting', 'role' => 'Staff'],
            ['name' => "Amanda Nerja", 'department' => 'Accounting', 'role' => 'Staff'],
            ['name' => "Ma'am Rachel", 'department' => 'Accounting', 'role' => 'Staff'],
            ['name' => "Ma'am Joy", 'department' => 'Executive Office', 'role' => 'Manager'],
            ['name' => "Ma'am Jonna Abilar", 'department' => 'Human Resource', 'role' => 'Manager'],
            ['name' => "Ma'am Fe Adrianne Conte", 'department' => 'Human Resource', 'role' => 'Staff'],
            ['name' => "Ma'am Faith Labiang", 'department' => 'Human Resource', 'role' => 'Staff'],
            ['name' => "Ma'am Arlita Samoy", 'department' => 'Human Resource', 'role' => 'Staff'],
            ['name' => "Sir Ray", 'department' => 'Maintenance', 'role' => 'Manager'],
            ['name' => "Sir CJ Bathan", 'department' => 'Maintenance', 'role' => 'Staff'],
            ['name' => "Ma'am Andrea", 'department' => 'Production Warehouse', 'role' => 'Manager'],
            ['name' => "Ma'am Jessi", 'department' => 'Production Warehouse', 'role' => 'Staff'],
            ['name' => "Ma'am Danica Reyes", 'department' => 'Production Warehouse', 'role' => 'Staff'],
            ['name' => "Ma'am Jessica Collado", 'department' => 'Purchasing', 'role' => 'Manager'],
            ['name' => "Ma'am Gracelle Razon", 'department' => 'Purchasing', 'role' => 'Staff'],
            ['name' => "Ma'am Kerstine Taduran", 'department' => 'Purchasing', 'role' => 'Staff'],
            ['name' => "Sir Dave Iringco", 'department' => 'Purchasing', 'role' => 'Staff'],
            ['name' => "Ma'am Sharmaine", 'department' => 'Purchasing', 'role' => 'Staff'],
            ['name' => "Ma'am Janers Zuniega", 'department' => 'Quality Assurance', 'role' => 'Manager'],
            ['name' => "Ma'am Vernette Rosacay", 'department' => 'Quality Assurance', 'role' => 'Staff'],
            ['name' => "Ma'am Mara", 'department' => 'Research and Development', 'role' => 'Manager'],
            ['name' => "Ma'am Zar", 'department' => 'Research and Development', 'role' => 'Staff'],
            ['name' => "Ma'am Diana Fabros", 'department' => 'Research and Development', 'role' => 'Staff'],
            ['name' => "Sir Bernard", 'department' => 'Sales And Marketing', 'role' => 'Manager'],
            ['name' => "Ma'am Roz", 'department' => 'Sales And Marketing', 'role' => 'Staff'],
        ];

        $counter = 1;

        foreach ($users as $userData) {
            $department = Department::firstOrCreate(['name' => $userData['department']]);

            $user = User::create([
                'first_name' => $userData['name'],
                'last_name' => 'Ewan',
                'email' => "example{$counter}@email.com",
                'user_role' => strtolower($userData['role']),
                'password' => Hash::make('password123'),
                'department_id' => $department->id,
            ]);
            
            // Assign the appropriate role using Spatie Permissions
            $user->assignRole($userData['role']);

            $counter++;
        }
    }
}
