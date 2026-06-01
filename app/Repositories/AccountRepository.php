<?php

namespace App\Repositories;

use App\Models\Account;

class AccountRepository extends Repository
{
    function findAccountOptions(): array
    {
        return Account::query()
            ->where('is_active', true)
            ->select(['id', 'code'])
            ->get()
            ->pluck('code', 'id')
            ->toArray();
    }

    function findAccount(int $id) {
        return Account::find($id);
    }
}
