<?php

namespace App\Repositories;

use App\Models\JournalEntry;

interface IJournalEntryRepository
{
    function getJournalEntries(): iterable;
    function findJournalEntry(int $id): ?JournalEntry;
    function findJournalEntryByTransactionId(int $transactionId);
}
