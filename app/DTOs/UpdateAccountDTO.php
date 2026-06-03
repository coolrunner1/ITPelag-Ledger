<?php

namespace App\DTOs;

use Illuminate\Http\Request;

final readonly class UpdateAccountDTO implements IDTO
{
    public function __construct(
        public ?string $name,
        public ?string $code,
        public ?string $type,
        public ?bool $isActive,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            code: $data['code'] ?? null,
            type: $data['type'] ?? null,
            isActive: $data['is_active'] ?? null,
        );
    }

    public static function fromRequest(Request $request): self
    {
        return self::fromArray($request->validated());
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'is_active' => $this->isActive,
        ], fn ($value) => $value !== null);
    }
}
