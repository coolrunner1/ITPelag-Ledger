<?php

namespace App\DTOs;

use Illuminate\Http\Request;

final readonly class JournalEntryDTO implements IDTO
{
    public function __construct(
        public int $transactionId,
        public int $accountId,
        public float $amount,
        public string $type,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            transactionId: $data['transaction_id'],
            accountId: $data['account_id'],
            amount: $data['amount'],
            type: $data['type'],
        );
    }

    public static function fromRequest(Request $request): self
    {
        return self::fromArray($request->validated());
    }

    public function toArray(): array
    {
        return [
            'transaction_id' => $this->transactionId,
            'account_id' => $this->accountId,
            'amount' => $this->amount,
            'type' => $this->type,
        ];
    }
}
