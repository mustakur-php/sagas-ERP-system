<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            FuelTypeSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $company = Company::updateOrCreate(
            ['code' => 'MAIN001'],
            [
                'name' => 'الشركة الرئيسية',
                'status' => 'active',
                'notes' => 'شركة افتراضية أولية للنظام',
                'parent_company_id' => null,
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'admin@sagas.com'],
            [
                'name' => 'System Admin',
                'phone' => '0500000000',
                'company_id' => $company->id,
                'station_id' => null,
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );

        $adminRole = Role::where('name', 'super_admin')->first();

        if ($adminRole) {
            $user->roles()->syncWithoutDetaching([$adminRole->id]);
        }
    }
}