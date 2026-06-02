<?php

namespace App\Repositories;

use App\DTOs\AccountDTO;
use App\Models\Account;

class AccountRepository implements IAccountRepository
{
    public function findAccountOptions(): array
    {
        return Account::query()
            ->where('is_active', true)
            ->select(['id', 'code', 'type'])
            ->get()
            ->mapWithKeys(function (Account $account) {
                $label = "{$account->code} [{$account->type}]";

                return [$account->id => $label];
            })
            ->toArray();
    }

    public function findAccounts(?string $search, ?string $type, ?string $isActive): iterable {
        return Account::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('code', 'ILIKE', "%{$search}%");
                });
            })
            ->when($type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($isActive, function ($query, $isActive) {
                $query->where('is_active', $isActive);
            })
            ->latest('updated_at')
            ->get();
    }

    public function findAccount(int $id): ?Account
    {
        return Account::find($id);
    }

    public function createAccount(AccountDTO $data): Account {
        return Account::create($data->toArray());
    }

    public function updateAccount(int $id, AccountDTO $data): ?Account
    {
        $account = $this->findAccount($id);

        if (!$account) {
            return null;
        }

        $account->update($data->toArray());

        return $account;
    }
    function deleteAccount(int $id): bool {
        return Account::destroy($id);
    }
}
