<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FuelType;

class FuelTypeSeeder extends Seeder
{
    public function run(): void
    {
        $fuelTypes = [
            ['name' => '91', 'code' => 'F91'],
            ['name' => '95', 'code' => 'F95'],
            ['name' => 'ديزل', 'code' => 'DSL'],
        ];

        foreach ($fuelTypes as $fuelType) {
            FuelType::updateOrCreate(
                ['name' => $fuelType['name']],
                ['code' => $fuelType['code']]
            );
        }
    }
}