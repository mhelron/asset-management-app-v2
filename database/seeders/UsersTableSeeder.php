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
            ['name' => "Ma'am Kimberly", 'department' => 'Accounting'],
            ['name' => "Ma'am Dean Jacinto", 'department' => 'Accounting'],
            ['name' => "Sir Clark", 'department' => 'Accounting'],
            ['name' => "Ma'am Bridget F. Supang", 'department' => 'Accounting'],
            ['name' => "Sir Mark Peralta", 'department' => 'Accounting'],
            ['name' => "Ma'am Ma Rosario Dela Cruz", 'department' => 'Accounting'],
            ['name' => "Amanda Nerja", 'department' => 'Accounting'],
            ['name' => "Ma'am Rachel", 'department' => 'Accounting'],
            ['name' => "Ma'am Joy", 'department' => 'Executive Office'],
            ['name' => "Ma'am Jonna Abilar", 'department' => 'Human Resource'],
            ['name' => "Ma'am Fe Adrianne Conte", 'department' => 'Human Resource'],
            ['name' => "Ma'am Faith Labiang", 'department' => 'Human Resource'],
            ['name' => "Ma'am Arlita Samoy", 'department' => 'Human Resource'],
            ['name' => "Sir Ray", 'department' => 'Maintenance'],
            ['name' => "Sir CJ Bathan", 'department' => 'Maintenance'],
            ['name' => "Ma'am Andrea", 'department' => 'Production Warehouse'],
            ['name' => "Ma'am Jessi", 'department' => 'Production Warehouse'],
            ['name' => "Ma'am Danica Reyes", 'department' => 'Production Warehouse'],
            ['name' => "Ma'am Jessica Collado", 'department' => 'Purchasing'],
            ['name' => "Ma'am Gracelle Razon", 'department' => 'Purchasing'],
            ['name' => "Ma'am Kerstine Taduran", 'department' => 'Purchasing'],
            ['name' => "Sir Dave Iringco", 'department' => 'Purchasing'],
            ['name' => "Ma'am Sharmaine", 'department' => 'Purchasing'],
            ['name' => "Ma'am Janers Zuniega", 'department' => 'Quality Assurance'],
            ['name' => "Ma'am Vernette Rosacay", 'department' => 'Quality Assurance'],
            ['name' => "Ma'am Mara", 'department' => 'Research and Development'],
            ['name' => "Ma'am Zar", 'department' => 'Research and Development'],
            ['name' => "Ma'am Diana Fabros", 'department' => 'Research and Development'],
            ['name' => "Sir Bernard", 'department' => 'Sales And Marketing'],
            ['name' => "Ma'am Roz", 'department' => 'Sales And Marketing'],
        ];

        $counter = 1;

        foreach ($users as $userData) {
            $department = Department::firstOrCreate(['name' => $userData['department']]);

            User::create([
                'first_name' => $userData['name'],
                'last_name' => 'Ewan',
                'email' => "example{$counter}@email.com",
                'user_role' => 'staff',
                'password' => Hash::make('password123'),
                'department_id' => $department->id,
            ]);

            $counter++;
        }
    }
}
