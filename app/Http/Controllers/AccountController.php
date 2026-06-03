<?php

namespace App\Http\Controllers;

use App\DTOs\CreateAccountDTO;
use App\DTOs\UpdateAccountDTO;
use App\Http\Requests\CreateAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use App\Services\AccountService;
use App\Services\IAccountService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    private IAccountService $accountService;

    public function __construct(
        AccountService $accountService,
    ) {
        $this->accountService = $accountService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $validator = Validator::make(request()->query(), [
            'search'     => ['nullable', 'string'],
            'type'       => ['nullable', 'string'],
            'isActive'   => ['nullable', 'boolean'],
        ]);

        $validated = $validator->validate();

        return $this->accountService->getAccounts(
            search: $validated['search'] ?? null,
            type: $validated['type'] ?? null,
            isActive: $validated['is_active'] ?? null
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAccountRequest $request)
    {
        $account = $this->accountService->createAccount(
            CreateAccountDTO::fromRequest($request)
        );

        return response()->json($account, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $validator = Validator::make(request()->query(), [
            'showBalance'   => ['nullable', 'boolean'],
        ]);

        $validated = $validator->validate();

        try {
            return $this->accountService->getAccount($id, $validated["showBalance"] ?? null);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountRequest $request, int $id)
    {
        try {
            $account = $this->accountService->updateAccount(
                $id,
                UpdateAccountDTO::fromRequest($request)
            );
            return response()->json($account);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $this->accountService->deleteAccount($id);

            return response()->noContent();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
