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
    /**
     * Find a specific transaction by its primary key.
     *
     * @param int $id The transaction ID.
     * @return Transaction|null The transaction instance, or null if not found.
     */
    public function findTransaction(int $id): ?Transaction;

    /**
     * Find a transaction by its ID and eager-load its associated journal entries.
     *
     * @param int $id The transaction ID.
     * @return Transaction|null The transaction instance with relations, or null if not found.
     */
    public function findTransactionWithJournalEntries(int $id): ?Transaction;

    /**
     * Create and persist a new transaction record.
     *
     * @param CreateTransactionDTO $transaction The data transfer object containing transaction details.
     * @return Transaction The newly created transaction model instance.
     */
    public function createTransaction(CreateTransactionDTO $transaction): Transaction;

    /**
     * Update an existing transaction record instance.
     *
     * @param Transaction $transaction The transaction instance to update.
     * @param UpdateTransactionDTO $transactionDTO The data transfer object containing updated values.
     * @return Transaction|null The updated transaction instance, or null if the update failed.
     */
    public function updateTransaction(Transaction $transaction, UpdateTransactionDTO $transactionDTO): ?Transaction;

    /**
     * Update an existing transaction record by its database ID.
     *
     * @param int $id The transaction ID to update.
     * @param UpdateTransactionDTO $transactionDTO The data transfer object containing updated values.
     * @return Transaction|null The updated transaction instance, or null if not found.
     */
    public function updateTransactionById(int $id, UpdateTransactionDTO $transactionDTO): ?Transaction;

    /**
     * Delete the given transaction record from the database.
     *
     * @param Transaction $transaction The transaction instance to delete.
     * @return bool True on successful deletion, false otherwise.
     */
    public function deleteTransaction(Transaction $transaction): bool;

    /**
     * Delete a transaction record matching the given database ID.
     *
     * @param int $id The transaction ID to delete.
     * @return bool True on successful deletion, false otherwise.
     */
    public function deleteTransactionById(int $id): bool;
}
