<?php

namespace App\Services;

use App\DTOs\CreateTransactionDTO;
use App\DTOs\UpdateTransactionDTO;
use App\Models\Transaction;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

interface ILedgerService
{
    /**
     * Gets accounts that are converted into an array for a select input
     *
     * @return array
     */
    function getAccountOptions(): array;

    /**
     * Fetches transactions
     *
     * @param string|null $search Search by description
     * @param string|null $date Filter by date
     * @param string|null $accountId Filter by the account id
     * @return Collection
     */
    function getTransactions(?string $search, ?string $date, ?string $accountId): Collection;

    /**
     * Creates a transaction with journal entries and checks their validity (debit must be equal to credit)
     *
     * @param CreateTransactionDTO $data Transaction details
     * @param array $entries Array of journal entries
     * @return Transaction
     * @throws Exception|Throwable
     */
    public function createTransaction(CreateTransactionDTO $data, array $entries): Transaction;

    /**
     * Fetches a transaction
     *
     * @param  int              $id The Transaction ID
     * @return Transaction|null
     */
    function getTransaction(int $id): ?Transaction;

    /**
     * Updates an existing transaction by id with journal entries and checks their validity (debit must be equal to credit)
     *
     * @param int $id The Transaction ID
     * @param UpdateTransactionDTO $data Transaction details
     * @param array $entries Array of journal entries
     * @return Transaction|null
     */
    public function updateTransaction(int $id, UpdateTransactionDTO $data, array $entries): ?Transaction;

    /**
     * Deletes an existing transaction by id
     *
     * @param int $id The Transaction ID
     * @throws Exception|ModelNotFoundException|Throwable
     */
    function deleteTransaction(int $id): bool;

    /**
     * Fetches a transaction with its journal entries
     *
     * @param  int              $id The Transaction ID
     * @return Transaction|null
     * @throws Exception|ModelNotFoundException
     */
    function getTransactionWithJournalEntries(int $id): ?Transaction;

    public function getTrialBalance(string $startDate, string $endDate): Collection;
}
