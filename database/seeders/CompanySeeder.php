<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::updateOrCreate(
            ['code' => 'MAIN001'],
            [
                'name' => 'الشركة الرئيسية',
                'status' => 'active',
                'notes' => 'شركة افتراضية أولية للنظام',
                'parent_company_id' => null,
            ]
        );
    }
}