<?php

namespace App\Services;

use App\Models\Transaction;
use App\Repositories\AccountRepository;
use App\Repositories\JournalEntryRepository;
use App\Repositories\TransactionRepository;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;

class LedgerService extends Service
{
    public function __construct(
        private readonly TransactionRepository  $transactionRepository,
        private readonly JournalEntryRepository $journalEntryRepository,
        private readonly AccountRepository      $accountRepository,
    )
    {
    }

    function getJournalEntries()
    {
        return $this->journalEntryRepository->getJournalEntries();
    }

    function getAccountOptions(): array
    {
        return $this->accountRepository->findAccountOptions();
    }

    function getTransactions(?string $search, ?string $date, ?string $accountId): iterable
    {
        return $this->transactionRepository->findTransactions($search, $date, $accountId);
    }

    function deleteTransaction(int $id): bool
    {
        return $this->transactionRepository->deleteTransaction($id);
    }

    function getTransactionWithJournalEntry(int $id): ?Transaction
    {
        $transaction = $this->transactionRepository->getTransaction($id);

        if (is_null($transaction)) {
            return null;
        }

        return $transaction;
    }
}
