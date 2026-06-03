<?php

namespace Tests\Feature;

use App\Services\ILedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LedgerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ILedgerService $service;

    protected function setUp(): void
    {

    }

    #[Test]
    public function creates_transaction_when_debits_equal_credits(): void
    {

    }

    #[Test]
    public function throws_exception_when_debits_not_equal_credits(): void
    {

    }

    #[Test]
    public function requires_at_least_two_entries(): void
    {

    }

    #[Test]
    public function validates_account_exists(): void
    {

    }

    #[Test]
    public function accepts_multiple_lines_balanced_correctly(): void
    {

    }

    #[Test]
    public function rejects_negative_amount(): void
    {

    }
}
