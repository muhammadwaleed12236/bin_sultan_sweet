<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccountHead;
use App\Models\Account;

class AccountHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $heads = [
            [
                'name' => 'Current Assets',
                'type' => 'Asset',
                'status' => 1,
                'accounts' => [
                    ['account_code' => '1001', 'title' => 'Cash in Hand (Counter)', 'type' => 'Debit', 'opening_balance' => 0, 'status' => 1],
                    ['account_code' => '1002', 'title' => 'Bank Account (Main)', 'type' => 'Debit', 'opening_balance' => 0, 'status' => 1],
                    ['account_code' => '1003', 'title' => 'Petty Cash', 'type' => 'Debit', 'opening_balance' => 0, 'status' => 1],
                ]
            ],
            [
                'name' => 'Operating Expenses',
                'type' => 'Expense',
                'status' => 1,
                'accounts' => [
                    ['account_code' => '2001', 'title' => 'Raw Material Purchase Expense', 'type' => 'Debit', 'opening_balance' => 0, 'status' => 1],
                    ['account_code' => '2002', 'title' => 'Shop Rent Expense', 'type' => 'Debit', 'opening_balance' => 0, 'status' => 1],
                    ['account_code' => '2003', 'title' => 'Electricity & Utilities', 'type' => 'Debit', 'opening_balance' => 0, 'status' => 1],
                    ['account_code' => '2004', 'title' => 'Staff Salaries & Wages', 'type' => 'Debit', 'opening_balance' => 0, 'status' => 1],
                    ['account_code' => '2005', 'title' => 'Packaging & Bags Expense', 'type' => 'Debit', 'opening_balance' => 0, 'status' => 1],
                    ['account_code' => '2006', 'title' => 'General / Tea Expense', 'type' => 'Debit', 'opening_balance' => 0, 'status' => 1],
                ]
            ],
            [
                'name' => 'Sales Revenue',
                'type' => 'Income',
                'status' => 1,
                'accounts' => [
                    ['account_code' => '3001', 'title' => 'Retail Sweets & Bakery Sales', 'type' => 'Credit', 'opening_balance' => 0, 'status' => 1],
                ]
            ],
        ];

        foreach ($heads as $headData) {
            $accounts = $headData['accounts'];
            unset($headData['accounts']);

            $head = AccountHead::firstOrCreate(
                ['name' => $headData['name']],
                $headData
            );

            foreach ($accounts as $acc) {
                Account::firstOrCreate(
                    ['account_code' => $acc['account_code']],
                    array_merge($acc, ['head_id' => $head->id])
                );
            }
        }

        $this->command->info('✅ Chart of Accounts & Heads seeded successfully.');
    }
}
