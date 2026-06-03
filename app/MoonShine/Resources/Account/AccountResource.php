<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Account;

use App\DTOs\CreateAccountDTO;
use App\DTOs\UpdateAccountDTO;
use App\Models\Account;
use App\MoonShine\Resources\Account\Pages\AccountDetailPage;
use App\MoonShine\Resources\Account\Pages\AccountFormPage;
use App\MoonShine\Resources\Account\Pages\AccountIndexPage;
use App\Services\IAccountService;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\TypeCasts\ModelDataWrapper;

/**
 * @extends ModelResource<Account, AccountIndexPage, AccountFormPage, AccountDetailPage>
 */
class AccountResource extends CrudResource
{
    public function __construct(
        private readonly IAccountService $accountService,
    )
    {
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
        $validator = Validator::make(request()->input('filter', []), [
            'type' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return new Collection([]);
        }

        $validated = $validator->validated();

        $search = request()->input('search');

        return $this->accountService->getAccounts(
            $search,
            type: $validated['type'] ?? null,
            isActive: $validated['is_active'] ?? null
        );
    }

    public function findItem(bool $orFail = false): ?DataWrapperContract
    {
        $id = $this->getItemID();

        $account = $this->accountService->getAccount(intval($id), true);

        if (!$account) {
            return null;
        }

        return new ModelDataWrapper($account);
    }

    public function save(DataWrapperContract $item, ?FieldsContract $fields = null): DataWrapperContract
    {
        $accountId = $this->getItemID() ? (int)$this->getItemID() : null;
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
            $this->accountService->deleteAccount((int)$id);
        }
    }

}
