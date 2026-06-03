<?php

namespace App\Repositories;

use App\Models\Transaction;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\DTOs\CreateTransactionDTO;
use App\DTOs\UpdateTransactionDTO;

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
            ->get()
            ->makeHidden(['journalEntries']);
    }

    public function findTransaction(int $id): ?Transaction
    {
        return Transaction::find($id);
    }

    public function findTransactionWithJournalEntries(int $id): ?Transaction
    {
        return Transaction::query()
            ->with(['journalEntries'])
            ->when($id, function ($query, $id) {
                $query->where('id', $id);
            })
            ->first();
    }

    public function createTransaction(CreateTransactionDTO $transaction): Transaction
    {
        return Transaction::create($transaction->toArray());
    }

    public function updateTransaction(Transaction $transaction, UpdateTransactionDTO $transactionDTO): ?Transaction
    {
        $transaction->update($transactionDTO->toArray());

        return $transaction;
    }

    public function updateTransactionById(int $id, UpdateTransactionDTO $transactionDTO): ?Transaction
    {
        $transaction = $this->findTransaction($id);

        $transaction->update($transactionDTO->toArray());

        return $transaction;
    }

    public function deleteTransaction(Transaction $transaction): bool
    {
        return $transaction->delete();
    }

    public function deleteTransactionById(int $id): bool
    {
        return Transaction::destroy($id);
    }
}
