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
     * Fetches accounts and converts them into an array for a select input
     *
     * @return array
     */
    public function findAccountOptions(): array;
    public function findAccounts(?string $search, ?string $type, ?string $isActive): iterable;
    public function findAccountsWithTransactionsAndJournalEntries(): Collection;
    public function findAccount(int $id): ?Account;
    public function checkPostedTransaction(Account $account): bool;
    public function getPostedTotals(Account $account): array;
    public function createAccount(CreateAccountDTO $data): Account;
    public function updateAccount(int $id, UpdateAccountDTO $data): ?Account;
    function deleteAccountById(int $id): bool;
    function deleteAccount(Account $account): bool;
}
