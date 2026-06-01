<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;

class TransactionRepository extends Repository
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

    function getTransaction(int $id): ?Transaction
    {
        return Transaction::find($id);
    }

    function createTransaction(DataWrapperContract $data): Transaction
    {

    }

    function updateTransaction(Transaction $transaction, DataWrapperContract $data): Transaction
    {

    }

    function deleteTransaction(int $id): bool
    {
        return Transaction::destroy($id);
    }
}
