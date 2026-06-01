<?php

namespace App\Repositories;

use App\Models\Account;

class AccountRepository implements IAccountRepository
{
    public function findAccountOptions(): array
    {
        return Account::query()
            ->where('is_active', true)
            ->select(['id', 'code'])
            ->get()
            ->pluck('code', 'id')
            ->toArray();
    }

    public function findAccount(int $id): ?Account {
        return Account::find($id);
    }
}
