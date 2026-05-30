<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            ['name' => 'Cash', 'code' => '1010', 'type' => 'asset'],
            ['name' => 'Accounts Receivable', 'code' => '1200', 'type' => 'asset'],
            ['name' => 'Accounts Payable', 'code' => '2010', 'type' => 'liability'],
            ['name' => 'Owner Capital', 'code' => '3010', 'type' => 'equity'],
            ['name' => 'Service Revenue', 'code' => '4010', 'type' => 'revenue'],
            ['name' => 'Rent Expense', 'code' => '5010', 'type' => 'expense'],
        ];

        foreach ($accounts as $account) {
            Account::updateOrCreate(['code' => $account['code']], $account);
        }
    }
}
