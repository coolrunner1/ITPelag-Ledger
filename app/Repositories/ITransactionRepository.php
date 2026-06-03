<?php

namespace App\Repositories;

use App\DTOs\CreateTransactionDTO;
use App\DTOs\UpdateTransactionDTO;
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

    public function findTransactionWithJournalEntries(int $id): ?Transaction;

    public function createTransaction(CreateTransactionDTO $transaction): Transaction;

    public function updateTransaction(Transaction $transaction, UpdateTransactionDTO $transactionDTO): ?Transaction;

    public function updateTransactionById(int $id, UpdateTransactionDTO $transactionDTO): ?Transaction;

    public function deleteTransaction(Transaction $transaction): bool;

    public function deleteTransactionById(int $id): bool;
}
