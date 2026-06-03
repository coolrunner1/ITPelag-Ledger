<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use const App\Constants\ACTIVE_ACCOUNT_TYPES;

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
}
