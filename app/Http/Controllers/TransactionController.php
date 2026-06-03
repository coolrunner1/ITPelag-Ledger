<?php

namespace App\Http\Controllers;

use App\DTOs\CreateTransactionDTO;
use App\DTOs\UpdateTransactionDTO;
use App\Exceptions\CustomNotFoundException;
use App\Exceptions\CustomValidationException;
use App\Http\Requests\CreateTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\ILedgerService;
use Exception;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function __construct(
        private readonly ILedgerService $ledgerService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $validator = Validator::make(request()->query(), [
            'search'     => ['nullable', 'string'],
            'date'       => ['nullable', 'date_format:Y-m-d'],
            'accountId'  => ['nullable', 'integer', 'exists:accounts,id'],
        ]);

        $validated = $validator->validated();

        return $this->ledgerService->getTransactions(
            search: $validated['search'] ?? null,
            date: $validated['date'] ?? null,
            accountId: $validated['accountId'] ?? null
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
    public function store(CreateTransactionRequest $request)
    {
        try {
            $account = $this->ledgerService->createTransaction(
                CreateTransactionDTO::fromRequest($request),
                $request->journalEntries
            );
            return response()->json($account);
        } catch (CustomValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            return $this->ledgerService->getTransactionWithJournalEntries($id);
        } catch (CustomNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);

        } catch (CustomValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, int $id)
    {
        try {
            $account = $this->ledgerService->updateTransaction(
                $id,
                UpdateTransactionDTO::fromRequest($request),
                $request->journalEntries
            );
            return response()->json($account);
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
            $this->ledgerService->deleteTransaction($id);

            return response()->noContent();
        } catch (CustomNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        } catch (CustomValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
