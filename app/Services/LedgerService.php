<?php

namespace App\Services;

use App\Models\Transaction;
use App\Repositories\IAccountRepository;
use App\Repositories\IJournalEntryRepository;
use App\Repositories\ITransactionRepository;
use App\Repositories\AccountRepository;
use App\Repositories\JournalEntryRepository;
use App\Repositories\TransactionRepository;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use Nette\NotImplementedException;

class LedgerService implements ILedgerService
{

    private ITransactionRepository  $transactionRepository;
    private IJournalEntryRepository $journalEntryRepository;
    private IAccountRepository      $accountRepository;

    public function __construct(
        TransactionRepository  $transactionRepository,
        JournalEntryRepository $journalEntryRepository,
        AccountRepository      $accountRepository,
    )
    {
        $this->transactionRepository = $transactionRepository;
        $this->journalEntryRepository = $journalEntryRepository;
        $this->accountRepository = $accountRepository;
    }

    function getAccountOptions(): array
    {
        return $this->accountRepository->findAccountOptions();
    }

    function getTransactions(?string $search, ?string $date, ?string $accountId): iterable
    {
        return $this->transactionRepository->findTransactions($search, $date, $accountId);
    }

    public function createTransaction(array $data, array $entries): Transaction
    {
        throw NotImplementedException::notImplemented();
    }

    public function updateTransaction(int $id, array $data, array $entries): ?Transaction
    {
        throw NotImplementedException::notImplemented();
    }

    function deleteTransaction(int $id): bool
    {
        return $this->transactionRepository->deleteTransaction($id);
    }

    function getTransactionWithJournalEntries(int $id): ?Transaction
    {
        return $this->transactionRepository->getTransaction($id);
    }
}
