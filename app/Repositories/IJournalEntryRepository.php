<?php

namespace App\Repositories;

use App\DTOs\JournalEntryDTO;
use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Traits\EnumeratesValues;

interface IJournalEntryRepository
{
    function getJournalEntries(): iterable;
    function findJournalEntry(int $id): ?JournalEntry;
    function findJournalEntriesByTransactionId(int $transactionId): Collection|EnumeratesValues;
    function createJournalEntry(JournalEntryDTO $entry): JournalEntry;
    function updateJournalEntry(int $id, JournalEntryDTO $entry): ?JournalEntry;
    function deleteJournalEntry(int $id): bool;
}
