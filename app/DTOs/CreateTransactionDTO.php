<?php

namespace App\DTOs;

use Illuminate\Http\Request;

final readonly class CreateTransactionDTO implements IDTO
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
        $validated = $request->validated();

        if (!isset($validated['is_posted'])) {
            $validated['is_posted'] = false;
        }

        return self::fromArray($validated);
    }

    public function toArray(): array
    {
        $arr = [
            'date' => $this->date,
            'description' => $this->description,
        ];

        if ($this->isPosted !== null) {
            $arr['is_posted'] = $this->isPosted;
        }

        return $arr;
    }
}
