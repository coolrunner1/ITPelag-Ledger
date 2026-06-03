<?php

namespace App\Services;

use App\DTOs\CreateAccountDTO;
use App\DTOs\UpdateAccountDTO;
use App\Models\Account;
use App\Repositories\AccountRepository;
use App\Repositories\IAccountRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use const App\Constants\ACTIVE_ACCOUNT_TYPES;

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

    function getAccount(int $id, ?bool $showBalance): ?Account
    {
        $account = $this->accountRepository->findAccount($id);

        if (!$account) {
            throw new ModelNotFoundException("Account was not found");
        }

        if ($showBalance) {
            $account->setAttribute(
                'balance',
                $this->calculateBalance($account)
            );
        }

        return $account;
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

    private function calculateBalance(Account $account): float
    {
        $totals = $this->accountRepository->getPostedTotals($account);

        $hasNormalDebitBalance = in_array($account->type, ACTIVE_ACCOUNT_TYPES);

        $balance = $hasNormalDebitBalance
            ? $totals['debit'] - $totals['credit']
            : $totals['credit'] - $totals['debit'];

        return round($balance, 2);
    }
}
