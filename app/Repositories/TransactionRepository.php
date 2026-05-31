<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository extends Repository
{
    function getTransactions(): Collection
    {
        return Transaction::all();
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
