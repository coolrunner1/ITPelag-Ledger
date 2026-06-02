<?php

namespace App\Services;

use App\DTOs\AccountDTO;
use App\Models\Account;
use App\Repositories\AccountRepository;
use App\Repositories\IAccountRepository;
use Exception;

class AccountService implements IAccountService
{
    private IAccountRepository $accountRepository;

    public function __construct(AccountRepository $accountRepository) {
        $this->accountRepository = $accountRepository;
    }

    function getAccounts(?string $search, ?string $type, ?string $isActive): iterable
    {
        return $this->accountRepository->findAccounts($search, $type, $isActive);
    }

    public function createAccount(AccountDTO $data): Account
    {
        return $this->accountRepository->createAccount($data);
    }

    function getAccount(int $id): ?Account
    {
        return $this->accountRepository->findAccount($id);
    }

    /**
     * @throws Exception
     */
    public function updateAccount(int $id, AccountDTO $data): ?Account
    {
        $account = $this->accountRepository->updateAccount($id, $data);

        if (!$account) {
            throw new Exception("Account was not found");
        }

        return $account;
    }

    function deleteAccount(int $id): bool
    {
        return $this->accountRepository->deleteAccount($id);
    }
}
