<?php

namespace App\Repositories;

use App\DTOs\JournalEntryDTO;
use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Traits\EnumeratesValues;

interface IJournalEntryRepository
{
    /**
     * Retrieve all journal entries from the database.
     *
     * @return iterable<JournalEntry> A list or stream of journal entry models.
     */
    public function getJournalEntries(): iterable;

    /**
     * Find a single journal entry by its primary key.
     *
     * @param int $id The journal entry ID.
     * @return JournalEntry|null The journal entry instance, or null if not found.
     */
    public function findJournalEntry(int $id): ?JournalEntry;

    /**
     * Retrieve all journal entries associated with a specific transaction.
     *
     * @param int $transactionId The parent transaction ID.
     * @return Collection<int, JournalEntry>|EnumeratesValues List of matching journal entries.
     */
    public function findJournalEntriesByTransactionId(int $transactionId): Collection|EnumeratesValues;

    /**
     * Create and persist a new journal entry record.
     *
     * @param JournalEntryDTO $entry The data transfer object containing entry details.
     * @return JournalEntry The newly created journal entry model instance.
     */
    public function createJournalEntry(JournalEntryDTO $entry): JournalEntry;

    /**
     * Update an existing journal entry record by its ID.
     *
     * @param int $id The journal entry ID to update.
     * @param JournalEntryDTO $entry The data transfer object containing updated values.
     * @return JournalEntry|null The updated journal entry instance, or null if not found.
     */
    public function updateJournalEntry(int $id, JournalEntryDTO $entry): ?JournalEntry;

    /**
     * Delete a journal entry record matching the given ID.
     *
     * @param int $id The journal entry ID to delete.
     * @return bool True on successful deletion, false otherwise.
     */
    public function deleteJournalEntry(int $id): bool;
}
