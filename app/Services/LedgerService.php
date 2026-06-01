<?php

namespace App\Services;

use App\Models\Transaction;
use App\Repositories\AccountRepository;
use App\Repositories\JournalEntryRepository;
use App\Repositories\TransactionRepository;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use Nette\NotImplementedException;

class LedgerService extends Service
{
    public function __construct(
        private readonly TransactionRepository  $transactionRepository,
        private readonly JournalEntryRepository $journalEntryRepository,
        private readonly AccountRepository      $accountRepository,
    )
    {
    }

    function getAccountOptions(): array
    {
        return $this->accountRepository->findAccountOptions();
    }

    /**
     * Fetches transactions
     *
     * @param string|null $search    Search by description
     * @param string|null $date      Filter by date
     * @param string|null $accountId Filter by the account id
     * @return iterable
     */
    function getTransactions(?string $search, ?string $date, ?string $accountId): iterable
    {
        return $this->transactionRepository->findTransactions($search, $date, $accountId);
    }

    /**
     * Creates a transaction with journal entries and checks their validity (debit must be equal to credit)
     *
     * @param array $data Transaction details
     * @param array $entries Array of journal entries
     * @return Transaction
     */
    public function createTransaction(array $data, array $entries): Transaction
    {
        throw NotImplementedException::notImplemented();
    }

    /**
     * Updates an existing transaction by id with journal entries and checks their validity (debit must be equal to credit)
     *
     * @param int      $id        The Transaction ID
     * @param array    $data      Transaction details
     * @param array    $entries   Array of journal entries
     * @return Transaction
     */
    public function updateTransaction(int $id, array $data, array $entries): Transaction
    {
        throw NotImplementedException::notImplemented();
    }

    /**
     * Deletes an existing transaction by id
     *
     * @param int $id The Transaction ID
     */
    function deleteTransaction(int $id): bool
    {
        return $this->transactionRepository->deleteTransaction($id);
    }

    /**
     * Fetches a transaction with its journal entries
     *
     * @param  int              $id The Transaction ID
     * @return Transaction|null
     */
    function getTransactionWithJournalEntries(int $id): ?Transaction
    {
        return $this->transactionRepository->getTransaction($id);
    }
}
