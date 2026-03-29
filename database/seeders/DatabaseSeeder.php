<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create a default company
        $company = Company::create([
            'name' => 'Default Company',
            'logo' => null,
        ]);

        // Create superadmin (role_id = 1)
        User::create([
            'name'        => 'Super Admin',
            'email'       => 'superadmin@example.com',
            'password'    => Hash::make('password'),
            'role_id'     => 1,
            'company_id'  => $company->id,
            'hourly_rate' => 0,
        ]);
    }
}
