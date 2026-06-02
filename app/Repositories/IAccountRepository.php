<?php

namespace App\Repositories;

use App\DTOs\AccountDTO;
use App\Models\Account;
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
    public function createAccount(AccountDTO $data): Account;
    public function updateAccount(int $id, AccountDTO $data): ?Account;
    function deleteAccount(int $id): bool;
}
