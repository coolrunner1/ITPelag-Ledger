<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $descriptions = ['Client Consultation Payment', 'Monthly Office Rent', 'SaaS Platform Income', 'Server Hosting Fee'];

        for ($i = 1; $i <= 30; $i++) {
            Transaction::create([
                'date' => date('Y-m-d', strtotime('-' . rand(1, 90) . ' days')),
                'description' => $descriptions[array_rand($descriptions)],
            ]);
        }
    }
}
