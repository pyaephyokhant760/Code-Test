<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [];

        for ($i = 0; $i < 10; $i++) {
            $employees[] = [
                'company_id' => DB::table('companies')->inRandomOrder()->value('id'), // Get a valid company ID
                'name' => Str::random(10),
                'email' => Str::random(10) . '@example.com',
                'profile' => Str::random(10) . 'profile.png',
                'position' => Str::random(10),
                'salary' => rand(1000, 10000), // Generate random salary
                'phone' => '09' . rand(100000000, 999999999), // Generate valid phone number
            ];
        }

        DB::table('employees')->insert($employees);
    }
}
