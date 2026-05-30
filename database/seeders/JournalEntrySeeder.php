<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JournalEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cashId = Account::where('code', '1010')->value('id');
        $revenueId = Account::where('code', '4010')->value('id');
        $rentId = Account::where('code', '5010')->value('id');

        Transaction::all()->each(function ($transaction) use ($cashId, $revenueId, $rentId) {
            $randomAmount = rand(100, 1500) + (rand(0, 99) / 100);
            $desc = $transaction->description;

            if (str_contains($desc, 'Payment') || str_contains($desc, 'Income')) {
                JournalEntry::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $cashId,
                    'amount' => $randomAmount,
                    'type' => 'debit',
                ]);
                JournalEntry::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $revenueId,
                    'amount' => $randomAmount,
                    'type' => 'credit',
                ]);
            } else {
                JournalEntry::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $rentId,
                    'amount' => $randomAmount,
                    'type' => 'debit',
                ]);
                JournalEntry::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $cashId,
                    'amount' => $randomAmount,
                    'type' => 'credit',
                ]);
            }
        });
    }
}

