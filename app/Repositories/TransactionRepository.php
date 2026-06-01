<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use App\DTOs\TransactionDTO;

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

    public function findTransaction(int $id): ?Transaction
    {
        return Transaction::find($id);
    }

    public function createTransaction(TransactionDTO $transaction): Transaction
    {
        return Transaction::create($transaction->toArray());
    }

    public function updateTransaction(int $id, TransactionDTO $transactionDTO): ?Transaction
    {
        $transaction = $this->findTransaction($id);

        if (!$transaction) {
            return null;
        }

        $transaction->update($transactionDTO->toArray());

        return $transaction;
    }

    public function deleteTransaction(int $id): bool
    {
        return Transaction::destroy($id);
    }
}
