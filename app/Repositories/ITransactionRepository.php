<?php

namespace App\Repositories;

use App\DTOs\TransactionDTO;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface ITransactionRepository
{
    /**
     * Fetches transactions and applies filters
     *
     * @param string|null $search Search by description
     * @param string|null $date Filter by date
     * @param int|null $accountId Filter by the account id
     * @return Collection
     */
    public function findTransactions(?string $search, ?string $date, ?int $accountId): Collection;

    public function findTransaction(int $id): ?Transaction;

    public function createTransaction(TransactionDTO $transaction): Transaction;

    public function updateTransaction(int $id, TransactionDTO $transactionDTO): ?Transaction;

    public function deleteTransaction(int $id): bool;
    public function getQuery(): Builder;
}
