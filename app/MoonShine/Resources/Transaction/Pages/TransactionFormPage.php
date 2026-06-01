<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Transaction\Pages;

use App\MoonShine\Resources\JournalEntry\JournalEntryResource;
use App\Services\ILedgerService;
use App\Services\LedgerService;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\RelationRepeater;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use App\MoonShine\Resources\Transaction\TransactionResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;
use Throwable;


/**
 * @extends FormPage<TransactionResource>
 */
class TransactionFormPage extends FormPage
{
    protected bool $isAsync = false;
    protected bool $errorsAbove = true;
    private ILedgerService $ledgerService;

    public function __construct(
        LedgerService $ledgerService,
    ) {
        $this->ledgerService = $ledgerService;
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                Date::make('Date', 'date'),
                Text::make('Description', 'description'),
                RelationRepeater::make(
                    'Journal Entries',
                    'journalEntries',
                    JournalEntryResource::class
                )
                    ->fields([
                        Number::make('Amount', 'amount')->default(1),
                        Select::make('Type', 'type')->options([
                            'debit' => 'Debit',
                            'credit' => 'Credit',
                        ]),
                        Select::make('Account', 'account_id')->options(
                            $this->ledgerService->getAccountOptions()
                        )->nullable()
                    ])
            ]),
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    protected function formButtons(): ListOf
    {
        return parent::formButtons();
    }

    protected function rules(mixed $item): array
    {
        return [
            'date'        => ['required', 'date_format:Y-m-d'],
            'description' => ['required', 'string', 'min:5', 'max:255'],
            'journalEntries.*.amount' => ['required', 'numeric', 'gt:0'],
            'journalEntries.*.account_id' => ['required', 'integer', 'exists:accounts,id'],
        ];
    }

    public function validationMessages(): array
    {
        return [
            'journalEntries.*.amount.required' => 'The amount needs to be specified for the journal entry №:position.',
            'journalEntries.*.amount.gt' => 'The amount must be greater than 0 (journal entry №:position).',
            'journalEntries.*.account_id.required' => 'The account needs to be specified for the journal entry №:position.',
        ];
    }

    /**
     * @param  FormBuilder  $component
     *
     * @return FormBuilder
     */
    protected function modifyFormComponent(FormBuilderContract $component): FormBuilderContract
    {
        return $component;
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function topLayer(): array
    {
        return [
            ...parent::topLayer()
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer()
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer()
        ];
    }
}
