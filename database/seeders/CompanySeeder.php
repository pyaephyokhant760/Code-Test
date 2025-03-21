<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            'name' => Str::random(10),
            'email' => Str::random(10) . '@example.com',
            'website' => 'https://www.' . Str::random(10) . '.com',
            'logo' => Str::random(10) . 'Logo.png',
        ]);
    }
}
