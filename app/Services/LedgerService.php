<?php

namespace App\Services;

use App\DTOs\TransactionDTO;
use App\DTOs\JournalEntryDTO;
use App\DTOs\IDTO;
use App\Models\JournalEntry;
use App\Models\Transaction;
use App\Repositories\IAccountRepository;
use App\Repositories\IJournalEntryRepository;
use App\Repositories\ITransactionRepository;
use App\Repositories\AccountRepository;
use App\Repositories\JournalEntryRepository;
use App\Repositories\TransactionRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

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

    /**
     * @throws Exception|Throwable
     */
    public function createTransaction(array $data, array $entries): Transaction
    {
        $this->checkJournalEntriesValidity($entries);

        return DB::transaction(function () use ($data, $entries) {
            $transaction = $this->transactionRepository->createTransaction(TransactionDTO::fromArray($data));

            foreach ($entries as $entry) {
                $this->checkAccountValidity((int) $entry['account_id']);

                $entry['transaction_id'] = $transaction->id;
                $this->journalEntryRepository->createJournalEntry(JournalEntryDTO::fromArray($entry));
            }

            return $transaction;
        });
    }

    function getTransaction(int $id): ?Transaction
    {
        return $this->transactionRepository->findTransaction($id);
    }

    /**
     * @throws Throwable
     */
    public function updateTransaction(int $id, array $data, array $entries): ?Transaction
    {
        $this->checkJournalEntriesValidity($entries);

        return DB::transaction(function () use ($id, $data, $entries) {
            $transaction = $this->transactionRepository->updateTransaction($id, TransactionDTO::fromArray($data));

            if (!$transaction) {
                throw new Exception("Transaction with id {$id} does not exist.");
            }

            $oldEntries = $this->journalEntryRepository->findJournalEntriesByTransactionId($id);

            $submittedIds = collect($entries)
                ->map(fn($entry) => isset($entry['id']) && !empty($entry['id']) ? (int) $entry['id'] : null)
                ->filter()
                ->toArray();

            $entriesToDelete = collect($oldEntries)->filter(function ($oldEntry) use ($submittedIds) {
                $oldId = is_object($oldEntry) ? $oldEntry->id : $oldEntry['id'];

                return !in_array($oldId, $submittedIds);
            });

            foreach ($entriesToDelete as $oldEntry) {
                $oldId = is_object($oldEntry) ? $oldEntry->id : $oldEntry['id'];

                $this->journalEntryRepository->deleteJournalEntry($oldId);
            }

            foreach ($entries as $entry) {
                $this->checkAccountValidity((int) $entry['account_id']);

                $entryId = isset($entry['id']) ? (int) $entry['id'] : null;
                $journalEntry = null;

                $entry['transaction_id'] = $transaction->id;

                if ($entryId) {
                    $journalEntry = $this->journalEntryRepository->updateJournalEntry($entryId, JournalEntryDTO::fromArray($entry));
                }

                if (!$journalEntry) {
                    $this->journalEntryRepository->createJournalEntry(JournalEntryDTO::fromArray($entry));
                }
            }
            return $transaction;
        });
    }

    function deleteTransaction(int $id): bool
    {
        return $this->transactionRepository->deleteTransaction($id);
    }

    function getTransactionWithJournalEntries(int $id): ?Transaction
    {
        return $this->transactionRepository->findTransaction($id);
    }

    /**
     * @throws Exception
     */
    private function checkJournalEntriesValidity(array $entries): void
    {
        $debit = 0;
        $credit = 0;

        if (empty($entries)) {
            throw new Exception("There must be at least two entries: credit and debit.");
        }

        foreach ($entries as $entry) {
            if ($entry["type"] === "debit") {
                $debit = $debit + $entry["amount"];
            } else {
                $credit = $credit + $entry["amount"];
            }
        }

        if (abs($debit - $credit) > 0.001) {
            throw new Exception("Accounting Error: Total Debits ({$debit}) must be equal to Total Credits ({$credit}).");
        }
    }

    /**
     * @throws Exception
     */
    private function checkAccountValidity(int $id): void {
        $account = $this->accountRepository->findAccount($id);

        if (!$account) {
            throw new Exception("Accounting Error: Account ID {$id} does not exist.");
        }

        if (!$account->is_active) {
            throw new Exception("Account '{$account->name}' ({$account->code}) is currently inactive.");
        }
    }
}
