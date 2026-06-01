<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Transaction;

use App\Models\Transaction;
use App\MoonShine\Resources\Transaction\Pages\TransactionIndexPage;
use App\MoonShine\Resources\Transaction\Pages\TransactionFormPage;
use App\MoonShine\Resources\Transaction\Pages\TransactionDetailPage;

use App\Services\LedgerService;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\TypeCasts\ModelDataWrapper;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Support\Enums\ToastType;

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
        $filterData = request()->input('filter', []);

        $validator = Validator::make($filterData, [
            'date'       => ['nullable', 'date_format:Y-m-d'],
            'account_id' => ['nullable', 'integer', 'exists:accounts,id'],
        ]);

        if ($validator->fails()) {
            return new Collection([]);
        }

        $search = request()->input('search');

        return $this->ledgerService->getTransactions(
            $search,
            date: $filterData['date'] ?? null,
            accountId: $filterData['account_id'] ?? null
        );
    }

    public function findItem(bool $orFail = false): ?DataWrapperContract
    {
        $id = $this->getItemID();

        $transaction = $this->ledgerService->getTransactionWithJournalEntries(intval($id));

        if (!$transaction) {
            return null;
        }

        return new ModelDataWrapper($transaction);
    }

    public function save(DataWrapperContract $item, ?FieldsContract $fields = null): DataWrapperContract
    {
        $transactionId   = $this->getItemID() ? (int) $this->getItemID() : null;
        $transactionData = request()->only(['date', 'description']);
        $journalEntries  = request()->input('journalEntries', []);

        try {
            if (!$transactionId) {
                $transaction = $this->ledgerService->createTransaction(
                    $transactionData,
                    $journalEntries
                );
            }
            // 3. Mark the state flag as successfully instantiated for the MoonShine component layout
            $this->isRecentlyCreated = is_null($transactionId);

            return new ModelDataWrapper($transaction);
        } catch (Exception $e) {
            throw new HttpResponseException(
                response()->json(['message' => $e->getMessage()], 422)
            );
        }
    }

    public function delete(DataWrapperContract $item, ?FieldsContract $fields = null): bool
    {
        try {
            $transaction = $item->getOriginal();
            if (is_null($transaction)) {
                throw new Exception("Item is null");
            }
            return $this->ledgerService->deleteTransaction($transaction->id);
        } catch (\Exception $e) {
            throw new HttpResponseException(
                response()->json(['message' => $e->getMessage()], 422)
            );
        }
    }

    public function massDelete(array $ids): void
    {
        foreach ($ids as $id) {
            $this->ledgerService->deleteTransaction((int) $id);
        }
    }
}
