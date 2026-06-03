<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Account::insert([
            ['name' => 'Cash',           'code' => '1000', 'type' => 'asset',    'is_active' => true],
            ['name' => 'Bank',           'code' => '1001', 'type' => 'asset',    'is_active' => true],
            ['name' => 'Revenue',        'code' => '4000', 'type' => 'revenue',  'is_active' => true],
            ['name' => 'Expense',        'code' => '5000', 'type' => 'expense',  'is_active' => true],
            ['name' => 'Accounts Payable','code' => '2000', 'type' => 'liability','is_active' => true],
            ['name' => 'Equity',         'code' => '3000', 'type' => 'equity',   'is_active' => true],
        ]);
    }
}
