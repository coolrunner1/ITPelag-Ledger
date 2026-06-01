<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\JournalEntry;

use Illuminate\Database\Eloquent\Model;
use App\Models\JournalEntry;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<JournalEntry>
 */
class JournalEntryResource extends ModelResource
{
    protected string $model = JournalEntry::class;

    protected string $title = 'JournalEntries';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [];
    }
}
