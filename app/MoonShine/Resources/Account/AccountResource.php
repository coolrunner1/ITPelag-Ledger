<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Account;

use App\DTOs\CreateAccountDTO;
use App\DTOs\TransactionDTO;
use App\DTOs\UpdateAccountDTO;
use App\MoonShine\Resources\Transaction\Pages\TransactionDetailPage;
use App\MoonShine\Resources\Transaction\Pages\TransactionFormPage;
use App\MoonShine\Resources\Transaction\Pages\TransactionIndexPage;
use App\Services\AccountService;
use App\Services\IAccountService;
use App\Services\ILedgerService;
use App\Services\LedgerService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;
use App\MoonShine\Resources\Account\Pages\AccountIndexPage;
use App\MoonShine\Resources\Account\Pages\AccountFormPage;
use App\MoonShine\Resources\Account\Pages\AccountDetailPage;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\TypeCasts\ModelDataWrapper;

/**
 * @extends ModelResource<Account, AccountIndexPage, AccountFormPage, AccountDetailPage>
 */
class AccountResource extends CrudResource
{
    private IAccountService $accountService;

    public function __construct(
        AccountService $accountService,
    ) {
        $this->accountService = $accountService;
    }
    protected ?string $casterKeyName = 'id';

    protected string $title = 'Accounts';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            AccountIndexPage::class,
            AccountFormPage::class,
            AccountDetailPage::class,
        ];
    }
    /**
     * @return iterable
     */
    public function getItems(): iterable
    {
        $filterData = request()->input('filter', []);

        $validator = Validator::make($filterData, [
            'type'       => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return new Collection([]);
        }

        $search = request()->input('search');

        return $this->accountService->getAccounts(
            $search,
            type: $filterData['type'] ?? null,
            isActive: $filterData['is_active'] ?? null
        );
    }

    public function findItem(bool $orFail = false): ?DataWrapperContract
    {
        $id = $this->getItemID();

        $account = $this->accountService->getAccount(intval($id));

        if (!$account) {
            return null;
        }

        return new ModelDataWrapper($account);
    }

    public function save(DataWrapperContract $item, ?FieldsContract $fields = null): DataWrapperContract
    {
        $accountId   = $this->getItemID() ? (int) $this->getItemID() : null;
        $accountData = request()->only(['name', 'code', 'type', 'is_active']);

        try {
            if (!$accountId) {
                $account = $this->accountService->createAccount(
                    CreateAccountDTO::fromArray($accountData),
                );
            } else {
                $account = $this->accountService->updateAccount(
                    $accountId,
                    UpdateAccountDTO::fromArray($accountData),
                );
            }

            $this->isRecentlyCreated = is_null($accountId);

            return new ModelDataWrapper($account);
        } catch (Exception $e) {
            throw ValidationException::withMessages([
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function delete(DataWrapperContract $item, ?FieldsContract $fields = null): bool
    {
        try {
            $account = $item->getOriginal();
            if (is_null($account)) {
                throw new Exception("Item is null");
            }
            return $this->accountService->deleteAccount($account->id);
        } catch (Exception $e) {
            throw ValidationException::withMessages([
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function massDelete(array $ids): void
    {
        foreach ($ids as $id) {
            $this->accountService->deleteAccount((int) $id);
        }
    }

}
