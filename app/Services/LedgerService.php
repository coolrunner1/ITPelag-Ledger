<?php

namespace App\Services;

use App\DTOs\TransactionDTO;
use App\DTOs\JournalEntryDTO;
use App\DTOs\IDTO;
use App\Models\Transaction;
use App\Repositories\IAccountRepository;
use App\Repositories\IJournalEntryRepository;
use App\Repositories\ITransactionRepository;
use App\Repositories\AccountRepository;
use App\Repositories\JournalEntryRepository;
use App\Repositories\TransactionRepository;
use Exception;
use Illuminate\Support\Facades\DB;

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
     * @throws Exception|\Throwable
     */
    public function createTransaction(array $data, array $entries): Transaction
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

        return DB::transaction(function () use ($data, $entries) {
            $transaction = $this->transactionRepository->createTransaction(TransactionDTO::fromArray($data));

            foreach ($entries as $entry) {
                $account = $this->accountRepository->findAccount((int) $entry['account_id']);

                if (!$account) {
                    throw new Exception("Accounting Error: Account ID {$entry['account_id']} does not exist.");
                }

                if (!$account->is_active) {
                    throw new Exception("Account '{$account->name}' ({$account->code}) is currently inactive.");
                }

                $entry['transaction_id'] = $transaction->id;
                $this->journalEntryRepository->createJournalEntry(JournalEntryDTO::fromArray($entry));
            }

            return $transaction;
        });
    }

    public function updateTransaction(int $id, array $data, array $entries): ?Transaction
    {
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
