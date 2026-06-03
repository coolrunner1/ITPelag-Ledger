<?php

namespace Tests\Feature;

use App\DTOs\CreateTransactionDTO;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Transaction;
use App\Repositories\AccountRepository;
use App\Repositories\JournalEntryRepository;
use App\Repositories\TransactionRepository;
use App\Services\ILedgerService;
use App\Services\LedgerService;
use Database\Seeders\TestAccountsSeeder;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LedgerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ILedgerService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TestAccountsSeeder::class);

        $this->service = new LedgerService(
            new TransactionRepository(),
            new JournalEntryRepository(),
            new AccountRepository(),
        );
    }

    #[Test]
    public function creates_transaction_when_debits_equal_credits(): void
    {
        $cash = Account::where('code', '1000')->firstOrFail();
        $revenue = Account::where('code', '4000')->firstOrFail();

        $transaction = $this->service->createTransaction(
            new CreateTransactionDTO(
                date: now()->toDateString(),
                description: 'Balanced transaction',
                isPosted: false
            ),
            [
                [
                    'account_id' => $cash->id,
                    'amount' => 100,
                    'type' => 'debit',
                ],
                [
                    'account_id' => $revenue->id,
                    'amount' => 100,
                    'type' => 'credit',
                ],
            ]
        );

        $this->assertInstanceOf(Transaction::class, $transaction);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
        ]);

        $this->assertDatabaseCount('journal_entries', 2);
    }

    #[Test]
    public function throws_exception_when_debits_not_equal_credits(): void
    {
        $cash = Account::where('code', '1000')->firstOrFail();
        $revenue = Account::where('code', '4000')->firstOrFail();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Accounting Error');

        $this->service->createTransaction(
            new CreateTransactionDTO(
                date: now()->toDateString(),
                description: 'Invalid transaction',
                isPosted: false
            ),
            [
                [
                    'account_id' => $cash->id,
                    'amount' => 100,
                    'type' => 'debit',
                ],
                [
                    'account_id' => $revenue->id,
                    'amount' => 50,
                    'type' => 'credit',
                ],
            ]
        );
    }

    #[Test]
    public function requires_at_least_two_entries(): void
    {
        $cash = Account::where('code', '1000')->firstOrFail();

        $this->expectException(Exception::class);

        $this->service->createTransaction(
            new CreateTransactionDTO(
                date: now()->toDateString(),
                description: 'Single entry',
                isPosted: false
            ),
            [
                [
                    'account_id' => $cash->id,
                    'amount' => 100,
                    'type' => 'debit',
                ],
            ]
        );
    }

    #[Test]
    public function validates_account_exists(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('does not exist');

        $this->service->createTransaction(
            new CreateTransactionDTO(
                date: now()->toDateString(),
                description: 'Unknown account',
                isPosted: false
            ),
            [
                [
                    'account_id' => 999999,
                    'amount' => 100,
                    'type' => 'debit',
                ],
                [
                    'account_id' => 999998,
                    'amount' => 100,
                    'type' => 'credit',
                ],
            ]
        );
    }

    #[Test]
    public function accepts_multiple_lines_balanced_correctly(): void
    {
        $cash = Account::where('code', '1000')->firstOrFail();
        $bank = Account::where('code', '1001')->firstOrFail();
        $revenue = Account::where('code', '4000')->firstOrFail();

        $transaction = $this->service->createTransaction(
            new CreateTransactionDTO(
                date: now()->toDateString(),
                description: 'Multi-line transaction',
                isPosted: false
            ),
            [
                [
                    'account_id' => $cash->id,
                    'amount' => 50,
                    'type' => 'debit',
                ],
                [
                    'account_id' => $bank->id,
                    'amount' => 150,
                    'type' => 'debit',
                ],
                [
                    'account_id' => $revenue->id,
                    'amount' => 200,
                    'type' => 'credit',
                ],
            ]
        );

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
        ]);

        $this->assertEquals(
            3,
            JournalEntry::where('transaction_id', $transaction->id)->count()
        );
    }
}
