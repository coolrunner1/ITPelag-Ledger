<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Transaction;

use App\DTOs\CreateTransactionDTO;
use App\DTOs\UpdateTransactionDTO;
use App\Models\Transaction;
use App\MoonShine\Handlers\CustomExportHandler;
use Illuminate\Database\Eloquent\Builder;
use MoonShine\Crud\Handlers\Handler;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\ExportHandler;
use App\MoonShine\Resources\Transaction\Pages\TransactionIndexPage;
use App\MoonShine\Resources\Transaction\Pages\TransactionFormPage;
use App\MoonShine\Resources\Transaction\Pages\TransactionDetailPage;
use App\Services\ILedgerService;
use App\Services\LedgerService;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\TypeCasts\ModelDataWrapper;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends CrudResource<array, TransactionIndexPage, TransactionFormPage, TransactionDetailPage>
 */
class TransactionResource extends CrudResource implements HasImportExportContract {
    use ImportExportConcern;

    private ILedgerService $ledgerService;

    public function __construct(
        LedgerService $ledgerService,
    ) {
        $this->ledgerService = $ledgerService;
    }
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
        $validator = Validator::make(request()->input('filter', []), [
            'date'       => ['nullable', 'date_format:Y-m-d'],
            'account_id' => ['nullable', 'integer', 'exists:accounts,id'],
        ]);

        if ($validator->fails()) {
            return new Collection([]);
        }

        $validated = $validator->validated();


        $search = request()->input('search');

        return $this->ledgerService->getTransactions(
            $search,
            date: $validated['date'] ?? null,
            accountId: $validated['account_id'] ?? null
        );
    }

    public function findItem(bool $orFail = false): ?DataWrapperContract
    {
        $id = $this->getItemID();

        $transaction = $this->ledgerService->getTransaction(intval($id));

        if (!$transaction) {
            return null;
        }

        return new ModelDataWrapper($transaction);
    }

    public function save(DataWrapperContract $item, ?FieldsContract $fields = null): DataWrapperContract
    {
        $transactionId   = $this->getItemID() ? (int) $this->getItemID() : null;
        $transactionData = request()->only(['date', 'description', 'is_posted']);
        $journalEntries  = request()->input('journalEntries', []);

        try {
            if (!$transactionId) {
                $transaction = $this->ledgerService->createTransaction(
                    CreateTransactionDTO::fromArray($transactionData),
                    $journalEntries
                );
            } else {
                $transaction = $this->ledgerService->updateTransaction(
                    $transactionId,
                    UpdateTransactionDTO::fromArray($transactionData),
                    $journalEntries
                );
            }

            $this->isRecentlyCreated = is_null($transactionId);

            return new ModelDataWrapper($transaction);
        } catch (Exception $e) {
            throw ValidationException::withMessages([
                'error' => $e->getMessage(),
            ]);
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
        } catch (Exception $e) {
            throw ValidationException::withMessages([
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function massDelete(array $ids): void
    {
        foreach ($ids as $id) {
            $this->ledgerService->deleteTransaction((int) $id);
        }
    }

    public function getQuery(): Builder
    {
        return Transaction::query();
    }

    protected function import(): ?Handler
    {
        return null;
    }

    protected function exportFields(): iterable
    {
        return [
            ID::make(),
            Date::make('Date', 'date'),
            Text::make('Description', 'description'),
            Text::make('Is Posted', 'is_posted')
                ->changePreview(fn($value) => $value ? 'Yes' : 'No'),
            Date::make('Created at', 'created_at')->readOnly(),
        ];
    }

    protected function export(): ?Handler
    {
        return null;
    }

    protected function handlers(): ListOf
    {
        return new ListOf(ExportHandler::class, [
            CustomExportHandler::make('Export to Excel')
                ->icon('cloud-arrow-down')
                ->notifyUsers(fn() => [auth()->id()])
                ->disk('public')
                ->filename(sprintf('export_%s', date('Ymd-His')))
                ->dir('/exports'),

            ExportHandler::make('Export to CSV')
                ->icon('document-text')
                ->notifyUsers(fn() => [auth()->id()])
                ->disk('public')
                ->filename(sprintf('export_%s', date('Ymd-His')))
                ->dir('/exports')
                ->csv()
                ->delimiter(',')
        ]);
    }
}
