<?php

namespace App\DTOs;

use Illuminate\Http\Request;

final readonly class UpdateTransactionDTO implements IDTO
{
    public function __construct(
        public ?string $date,
        public ?string $description,
        public ?bool $isPosted,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            date: $data['date'] ?? null,
            description: $data['description'] ?? null,
            isPosted: $data['is_posted'] ?? null,
        );
    }

    public static function fromRequest(Request $request): self
    {
        return self::fromArray($request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'date' => $this->date,
            'description' => $this->description,
            'is_posted' => $this->isPosted,
        ], fn ($value) => $value !== null);
    }
}
