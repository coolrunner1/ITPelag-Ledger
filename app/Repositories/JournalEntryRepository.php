<?php

namespace App\Repositories;

use App\Models\JournalEntry;
use App\DTOs\JournalEntryDTO;
use \Illuminate\Database\Eloquent\Collection;
use \Illuminate\Support\Traits\EnumeratesValues;

class JournalEntryRepository implements IJournalEntryRepository
{
    function getJournalEntries(): iterable
    {
        return JournalEntry::all();
    }

    function findJournalEntry(int $id): ?JournalEntry
    {
        return JournalEntry::find($id);
    }

    function findJournalEntriesByTransactionId(int $transactionId): Collection|EnumeratesValues
    {
        return JournalEntry::all()->where('transaction_id', $transactionId);
    }

    function createJournalEntry(JournalEntryDTO $entry): JournalEntry
    {
        return JournalEntry::create($entry->toArray());
    }

    function updateJournalEntry(int $id, JournalEntryDTO $entry): ?JournalEntry
    {
        $journalEntry = $this->findJournalEntry($id);

        if (!$journalEntry) {
            return null;
        }

        $journalEntry->update($entry->toArray());

        return $journalEntry;
    }

    public function deleteJournalEntry(int $id): bool
    {
        return JournalEntry::destroy($id);
    }
}
