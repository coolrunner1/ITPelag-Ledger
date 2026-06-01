<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'date',
        'description',
        'is_posted',
    ];

    protected $casts = [
        'date' => 'date',
        'is_posted' => 'boolean',
    ];

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }
}
