<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            [
                'name' => 'Asset',
                'description' => 'Resources owned by the business'
            ],
            [
                'name' => 'Liability',
                'description' => 'Debts and obligations of the business'
            ],
            [
                'name' => 'Equity',
                'description' => 'Owner\'s interest in the business'
            ],
            [
                'name' => 'Revenue',
                'description' => 'Income earned from business activities'
            ],
            [
                'name' => 'Expense',
                'description' => 'Costs incurred in running the business'
            ]
        ];

        foreach ($types as $type) {
            AccountType::create($type);
        }
    }
} 