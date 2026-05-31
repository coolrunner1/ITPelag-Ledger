<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntry extends Model
{
    public $timestamps = false;

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
