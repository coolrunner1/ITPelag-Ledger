<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

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

            // 4. Look inside child rows to restrict rows to a specific accounting line
            ->when($accountId, function ($query, $accountId) {
                $query->whereHas('journalEntries', function ($subQuery) use ($accountId) {
                    $subQuery->where('account_id', $accountId);
                });
            })

            // 5. Always display the latest bookkeeping entries first
            ->latest('date')

            // 6. Return a paginator instance which MoonShine grids natively expect
            ->get();
    }

    function getTransaction(int $id): ?Transaction
    {
        return Transaction::find($id);
    }


    function findTransactionsByDate(string $date)
    {
        return Transaction::all()->where('date', $date);
    }

    function findTransactionsByAccountId(int $accountId)
    {
        return Transaction::all()->where('account_id', $accountId);
    }
}
