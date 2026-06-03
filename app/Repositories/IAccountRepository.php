<?php

namespace App\Repositories;

use App\DTOs\CreateAccountDTO;
use App\DTOs\UpdateAccountDTO;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface IAccountRepository
{
    /**
     * Fetches accounts and converts them into an array for a select input.
     *
     * @return array<int|string, string> Formatted account options for dropdowns.
     */
    public function findAccountOptions(): array;

    /**
     * Search and filter accounts based on criteria.
     *
     * @param string|null $search The search query for account names or codes.
     * @param string|null $type The account type group filter.
     * @param string|null $isActive The active status filter state ('true'/'false').
     * @return iterable<Account> List of filtered accounts.
     */
    public function findAccounts(?string $search, ?string $type, ?string $isActive): iterable;

    /**
     * Retrieve all accounts eager-loaded with their transactions and journal entries.
     *
     * @return Collection<int, Account> Collection of accounts with relations loaded.
     */
    public function findAccountsWithTransactionsAndJournalEntries(): Collection;

    /**
     * Find a specific account by its database primary key.
     *
     * @param int $id The account ID.
     * @return Account|null The account instance or null if not found.
     */
    public function findAccount(int $id): ?Account;

    /**
     * Check if the account has any transactions that have already been posted.
     *
     * @param Account $account The account instance to check.
     * @return bool True if a posted transaction exists, false otherwise.
     */
    public function checkPostedTransaction(Account $account): bool;

    /**
     * Calculate and return the total posted debits and credits for the account.
     *
     * @param Account $account The account instance.
     * @return array{debit: float, credit: float} Array containing debit and credit sums.
     */
    public function getPostedTotals(Account $account): array;

    /**
     * Create and persist a new account record.
     *
     * @param CreateAccountDTO $data The data transfer object containing account inputs.
     * @return Account The newly created account model instance.
     */
    public function createAccount(CreateAccountDTO $data): Account;

    /**
     * Update an existing account record by its ID.
     *
     * @param int $id The account ID to update.
     * @param UpdateAccountDTO $data The data transfer object containing updated values.
     * @return Account|null The updated account instance, or null if it could not be found.
     */
    public function updateAccount(int $id, UpdateAccountDTO $data): ?Account;

    /**
     * Delete an account record matching the given database ID.
     *
     * @param int $id The account ID to delete.
     * @return bool True on successful deletion, false otherwise.
     */
    public function deleteAccountById(int $id): bool;

    /**
     * Delete the given account record from the database.
     *
     * @param Account $account The account instance to delete.
     * @return bool True on successful deletion, false otherwise.
     */
    public function deleteAccount(Account $account): bool;
}
