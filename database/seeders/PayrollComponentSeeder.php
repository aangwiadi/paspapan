<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PayrollComponent;

class PayrollComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $components = [
            [
                'name' => 'Uang Makan',
                'type' => 'allowance',
                'calculation_type' => 'daily_presence',
                'amount' => 50000,
                'is_taxable' => false,
            ],
            [
                'name' => 'Uang Transport',
                'type' => 'allowance',
                'calculation_type' => 'daily_presence',
                'amount' => 25000,
                'is_taxable' => false,
            ],
            [
                'name' => 'Tunjangan Kesehatan',
                'type' => 'allowance',
                'calculation_type' => 'fixed',
                'amount' => 150000,
                'is_taxable' => true,
            ],
            [
                'name' => 'PPh 21 (Simulasi)',
                'type' => 'deduction',
                'calculation_type' => 'percentage_basic',
                'percentage' => 5.0, // 5%
                'is_taxable' => false,
            ],
        ];

        foreach ($components as $comp) {
            PayrollComponent::updateOrCreate(
                ['name' => $comp['name']],
                $comp
            );
        }
    }
}
