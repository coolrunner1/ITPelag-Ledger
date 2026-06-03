<?php

namespace App\Providers;

use App\Repositories\AccountRepository;
use App\Repositories\IAccountRepository;
use App\Repositories\IJournalEntryRepository;
use App\Repositories\ITransactionRepository;
use App\Repositories\JournalEntryRepository;
use App\Repositories\TransactionRepository;
use App\Services\AccountService;
use App\Services\IAccountService;
use App\Services\ILedgerService;
use App\Services\LedgerService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ITransactionRepository::class, TransactionRepository::class);
        $this->app->bind(IJournalEntryRepository::class, JournalEntryRepository::class);
        $this->app->bind(IAccountRepository::class, AccountRepository::class);
        $this->app->bind(IAccountService::class, AccountService::class);
        $this->app->bind(ILedgerService::class, LedgerService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
