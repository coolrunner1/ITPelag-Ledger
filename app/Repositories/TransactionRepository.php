<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository implements ITransactionRepository
{
    public function findTransactions(?string $search, ?string $date, ?int $accountId): Collection
    {
        return Transaction::query()
            ->with(['journalEntries.account'])
            ->when($search, function ($query, $search) {
                $query->where('description', 'ILIKE', "%{$search}%");
            })
            ->when($date, function ($query, $date) {
                $query->whereDate('date', $date);
            })
            ->when($accountId, function ($query, $accountId) {
                $query->whereHas('journalEntries', function ($subQuery) use ($accountId) {
                    $subQuery->where('account_id', $accountId);
                });
            })
            ->latest('date')
            ->get();
    }

    public function getTransaction(int $id): ?Transaction
    {
        return Transaction::find($id);
    }

    public function createTransaction(Transaction $transaction): Transaction
    {
        return Transaction::create($transaction);
    }

    public function updateTransaction(int $id, Transaction $transaction): ?Transaction
    {
        return null;
    }

    public function deleteTransaction(int $id): bool
    {
        return Transaction::destroy($id);
    }
}
