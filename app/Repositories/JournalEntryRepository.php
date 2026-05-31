<?php

namespace App\Repositories;

use App\Models\JournalEntry;

class JournalEntryRepository extends Repository
{
    function findJournalEntry(int $id): ?JournalEntry {
        return JournalEntry::find($id);
    }

    function findJournalEntryByTransactionId(int $transactionId): ?JournalEntry {
        return JournalEntry::all()->where('transaction_id', $transactionId)->first();
    }
}
