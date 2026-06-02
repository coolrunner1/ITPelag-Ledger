<?php

namespace App\Services;

use App\DTOs\TransactionDTO;
use App\Models\Transaction;

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
     * @param string|null $search    Search by description
     * @param string|null $date      Filter by date
     * @param string|null $accountId Filter by the account id
     * @return iterable
     */
    function getTransactions(?string $search, ?string $date, ?string $accountId): iterable;

    /**
     * Creates a transaction with journal entries and checks their validity (debit must be equal to credit)
     *
     * @param TransactionDTO $data Transaction details
     * @param array $entries Array of journal entries
     * @return Transaction
     */
    public function createTransaction(TransactionDTO $data, array $entries): Transaction;

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
     * @param TransactionDTO $data Transaction details
     * @param array $entries Array of journal entries
     * @return Transaction|null
     */
    public function updateTransaction(int $id, TransactionDTO $data, array $entries): ?Transaction;

    /**
     * Deletes an existing transaction by id
     *
     * @param int $id The Transaction ID
     */
    function deleteTransaction(int $id): bool;

    /**
     * Fetches a transaction with its journal entries
     *
     * @param  int              $id The Transaction ID
     * @return Transaction|null
     */
    function getTransactionWithJournalEntries(int $id): ?Transaction;
}
