<?php

namespace App\Services;

use App\DTOs\CreateAccountDTO;
use App\DTOs\UpdateAccountDTO;
use App\Models\Account;
use App\Repositories\AccountRepository;
use App\Repositories\IAccountRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    public function createAccount(CreateAccountDTO $data): Account
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
    public function updateAccount(int $id, UpdateAccountDTO $data): ?Account
    {
        $account = $this->accountRepository->updateAccount($id, $data);

        if (!$account) {
            throw new ModelNotFoundException("Account was not found");
        }

        return $account;
    }

    function deleteAccount(int $id): bool
    {
        if (!$this->accountRepository->deleteAccount($id)) {
            throw new Exception("Account was not found or has posted transactions");
        }
        return true;
    }
}
