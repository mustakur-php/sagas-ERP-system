<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('code', 'MAIN001')->first();

        $user = User::updateOrCreate(
            ['email' => 'admin@sagas.com'],
            [
                'name' => 'System Admin',
                'phone' => '0500000000',
                'company_id' => $company?->id,
                'station_id' => null,
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );

        $role = Role::where('name', 'super_admin')->first();

        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }
    }
}