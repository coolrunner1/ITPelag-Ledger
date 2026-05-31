<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Transaction;

use App\Models\Transaction;
use App\MoonShine\Resources\Transaction\Pages\TransactionIndexPage;
use App\MoonShine\Resources\Transaction\Pages\TransactionFormPage;
use App\MoonShine\Resources\Transaction\Pages\TransactionDetailPage;

use App\Services\LedgerService;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\TypeCasts\ModelDataWrapper;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends CrudResource<array, TransactionIndexPage, TransactionFormPage, TransactionDetailPage>
 */
class TransactionResource extends CrudResource {

    public function __construct(
        private readonly LedgerService $ledgerService,
    ) {}
    protected ?string $casterKeyName = 'id';

    protected string $title = 'Transactions';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            TransactionIndexPage::class,
            TransactionFormPage::class,
            TransactionDetailPage::class,
        ];
    }

    /**
     * @return iterable
     */
    public function getItems(): iterable
    {
        return $this->ledgerService->getTransactions();
    }

    public function findItem(bool $orFail = false): ?DataWrapperContract
    {
        $id = $this->getItemID();

        $transaction = $this->ledgerService->getTransactionWithJournalEntry(intval($id));

        if (!$transaction) {
            return null;
        }

        return new ModelDataWrapper($transaction);
    }

    public function save(DataWrapperContract $item, ?FieldsContract $fields = null): DataWrapperContract
    {
        $this->isRecentlyCreated = true;

        return $item;
    }

    public function delete(DataWrapperContract $item, ?FieldsContract $fields = null): bool
    {
        return true;
    }

    public function massDelete(array $ids): void
    {
        //
    }
}
