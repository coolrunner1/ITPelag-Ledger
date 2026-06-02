<?php

namespace App\Services;

use App\DTOs\TransactionDTO;
use App\DTOs\JournalEntryDTO;
use App\DTOs\IDTO;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Transaction;
use App\Repositories\IAccountRepository;
use App\Repositories\IJournalEntryRepository;
use App\Repositories\ITransactionRepository;
use App\Repositories\AccountRepository;
use App\Repositories\JournalEntryRepository;
use App\Repositories\TransactionRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Throwable;

use Illuminate\Database\Eloquent\Collection;

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

    function getTransactions(?string $search, ?string $date, ?string $accountId): Collection
    {
        return $this->transactionRepository->findTransactions($search, $date, $accountId);
    }

    /**
     * @throws Exception|Throwable
     */
    public function createTransaction(TransactionDTO $data, array $entries): Transaction
    {
        $this->checkJournalEntriesValidity($entries);

        return DB::transaction(function () use ($data, $entries) {
            $transaction = $this->transactionRepository->createTransaction($data);

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
    public function updateTransaction(int $id, TransactionDTO $data, array $entries): ?Transaction
    {
        $this->checkJournalEntriesValidity($entries);

        return DB::transaction(function () use ($id, $data, $entries) {
            $transaction = $this->transactionRepository->updateTransaction($id, $data);

            if (!$transaction) {
                throw new Exception("Transaction with id {$id} does not exist or can't be updated.");
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

    /**
     * @throws Exception
     */
    function deleteTransaction(int $id): bool
    {
        $transaction = $this->transactionRepository->deleteTransaction($id);
        if (!$transaction) {
            throw new Exception("Transaction does not exist or has been already posted.");
        }
        return true;
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

    public function getTrialBalance(string $startDate, string $endDate): Collection
    {
        $from = Carbon::parse($startDate);
        $to = Carbon::parse($endDate);

        $totalOpeningDebit = 0;
        $totalOpeningCredit = 0;

        $totalDebitTurnover = 0;
        $totalCreditTurnover = 0;

        $totalClosingDebit = 0;
        $totalClosingCredit = 0;

        $trialBalance = new Collection();

        $accounts = $this->accountRepository->findAccountsWithTransactionsAndJournalEntries();

        foreach ($accounts as $account) {

            $entries = $account->journalEntries
                ->filter(
                    fn($e) =>
                        $e->transaction &&
                        $e->transaction->is_posted
                );

            $openingDebitTurnover = $entries
                ->filter(fn($e) =>
                    $e->transaction->date <= $from &&
                    $e->type === 'debit'
                )
                ->sum('amount');

            $openingCreditTurnover = $entries
                ->filter(fn($e) =>
                    $e->transaction->date <= $from &&
                    $e->type === 'credit'
                )
                ->sum('amount');

            $debitTurnover = $entries
                ->filter(fn($e) =>
                    $e->transaction->date >= $from &&
                    $e->transaction->date <= $to &&
                    $e->type === 'debit'
                )
                ->sum('amount');

            $totalDebitTurnover += $debitTurnover;

            $creditTurnover = $entries
                ->filter(fn($e) =>
                    $e->transaction->date > $from &&
                    $e->transaction->date < $to &&
                    $e->type === 'credit'
                )
                ->sum('amount');

            $totalCreditTurnover += $creditTurnover;

            $openingNet =
                $openingDebitTurnover -
                $openingCreditTurnover;

            $openingDebit = max($openingNet, 0);

            $totalOpeningDebit += $openingDebit;

            $openingCredit = abs(min($openingNet, 0));

            $totalOpeningCredit += $openingCredit;

            $closingNet =
                $openingNet +
                $debitTurnover -
                $creditTurnover;

            $closingDebit = max($closingNet, 0);

            $totalClosingDebit += $closingDebit;

            $closingCredit = abs(min($closingNet, 0));

            $totalClosingCredit += $closingCredit;

            $trialBalance->push([
                'code' => $account->code,
                'name' => $account->name,

                'opening_debit' => round($openingDebit,2),
                'opening_credit' => round($openingCredit,2),

                'debit_turnover' => round($debitTurnover,2),
                'credit_turnover' => round($creditTurnover,2),

                'closing_debit' => round($closingDebit,2),
                'closing_credit' => round($closingCredit,2),
            ]);
        }

        $trialBalance->push([
            'code' => "",
            'name' => "",

            'opening_debit' => round($totalOpeningDebit,2),
            'opening_credit' => round($totalOpeningCredit,2),

            'debit_turnover' => round($totalDebitTurnover,2),
            'credit_turnover' => round($totalCreditTurnover,2),

            'closing_debit' => round($totalClosingDebit,2),
            'closing_credit' => round($totalClosingCredit,2),
        ]);

        return $trialBalance;
    }

    public function getTransactionQuery(): Builder {
        return $this->transactionRepository->getQuery();
    }
}
