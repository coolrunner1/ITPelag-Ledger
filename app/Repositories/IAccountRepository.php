<?php

namespace App\Repositories;

use App\Models\Account;

interface IAccountRepository
{
    /**
     * Fetches accounts and converts them into an array for a select input
     *
     * @return array
     */
    public function findAccountOptions(): array;
    public function findAccount(int $id): ?Account;
}
