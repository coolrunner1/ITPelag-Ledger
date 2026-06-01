<?php

namespace App\DTOs;

use Illuminate\Http\Request;

final readonly class TransactionDTO implements IDTO
{
    public function __construct(
        public string $date,
        public string $description,
        public bool $isPosted,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            date: $data['date'],
            description: $data['description'],
            isPosted: $data['is_posted'],
        );
    }

    public static function fromRequest(Request $request): self
    {
        return self::fromArray($request->validated());
    }

    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'description' => $this->description,
            'is_posted' => $this->isPosted,
        ];
    }
}
