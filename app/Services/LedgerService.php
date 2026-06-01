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
        private readonly TransactionRepository $transactionRepository,
        private readonly JournalEntryRepository $journalEntryRepository
    ) {}

    function getJournalEntries()
    {
        return $this->journalEntryRepository->getJournalEntries();
    }

    function getTransactions(): iterable {
        return $this->transactionRepository->getTransactions();
    }

    function getTransactionsByDate(string $date): iterable
    {

    }

    function getTransactionsByAccountId(string $accountId): iterable
    {

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
