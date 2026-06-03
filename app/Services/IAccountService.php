<?php

namespace App\Services;

use App\DTOs\CreateAccountDTO;
use App\DTOs\UpdateAccountDTO;
use App\Models\Account;
use App\Models\Transaction;

interface IAccountService
{
    /**
     * Fetches accounts
     *
     * @param string|null $search    Search by description
     * @param string|null $type      Filter by type
     * @param string|null $isActive  Filter by is_active field
     * @return iterable
     */
    function getAccounts(?string $search, ?string $type, ?string $isActive): iterable;

    /**
     * Creates a new account
     *
     * @param CreateAccountDTO $data Account details
     * @return Account
     */
    public function createAccount(CreateAccountDTO $data): Account;

    /**
     * Fetches an account
     *
     * @param  int              $id The Account ID
     * @return Account|null
     */
    function getAccount(int $id): ?Account;

    /**
     * Updates an existing account by id
     *
     * @param int $id The account ID
     * @param UpdateAccountDTO $data Account details
     * @return Account|null Updated account
     */
    public function updateAccount(int $id, UpdateAccountDTO $data): ?Account;

    /**
     * Deletes an existing account by id
     *
     * @param int $id The Transaction ID
     */
    function deleteAccount(int $id): bool;
}
