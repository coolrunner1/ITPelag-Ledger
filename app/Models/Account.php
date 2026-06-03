<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use const App\Constants\ACTIVE_ACCOUNT_TYPES;
use const App\Constants\ACTIVE_TYPES;
use const App\Constants\PASSIVE_TYPES;

class Account extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'is_active',
    ];

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function getBalanceAttribute(): float
    {
        $baseQuery = $this->journalEntries()
            ->whereHas(
                'transaction',
                fn($q) => $q->where('is_posted', true)
            );

        $debit = (clone $baseQuery)
            ->where('type', 'debit')
            ->sum('amount');

        $credit = (clone $baseQuery)
            ->where('type', 'credit')
            ->sum('amount');

        $hasNormalDebitBalance = in_array(
            $this->type,
            ACTIVE_ACCOUNT_TYPES,
        );

        $balance = $hasNormalDebitBalance
            ? $debit - $credit
            : $credit - $debit;

        return round($balance, 2);
    }
}
