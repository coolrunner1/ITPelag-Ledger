<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Services\ILedgerService;
use App\Services\LedgerService;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Text;


class TrialBalance extends Page
{
    private ILedgerService $ledgerService;

    public function __construct(LedgerService $ledgerService) {
        $this->ledgerService = $ledgerService;
    }
    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle()
        ];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Trial Balance';
    }

    /**
     * @return list<ComponentContract>
     */
    public function components(): array
    {
        $from = request()->get(
            'from',
            now()->startOfMonth()->toDateString()
        );

        $to = request()->get(
            'to',
            now()->toDateString()
        );

        $report = $this->ledgerService
            ->getTrialBalance($from, $to);

        return [

            FormBuilder::make()
                ->method(FormMethod::GET)
                ->fields([

                    Date::make('From', 'from')
                        ->default($from),

                    Date::make('To', 'to')
                        ->default($to),

                ])
                ->submit('Generate'),

            TableBuilder::make()
                ->items($report)
                ->fields([
                    Text::make('Code','code'),

                    Text::make('Account','name'),

                    Text::make(
                        'Opening Debit',
                        'opening_debit'
                    ),

                    Text::make(
                        'Opening Credit',
                        'opening_credit'
                    ),

                    Text::make(
                        'Debit Turnover',
                        'debit_turnover'
                    ),

                    Text::make(
                        'Credit Turnover',
                        'credit_turnover'
                    ),

                    Text::make(
                        'Closing Debit',
                        'closing_debit'
                    ),

                    Text::make(
                        'Closing Credit',
                        'closing_credit'
                    ),
                ])
        ];
    }
}
