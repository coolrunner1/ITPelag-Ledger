<?php

namespace App\DTOs;

use Illuminate\Http\Request;

final readonly class AccountDTO implements IDTO
{
    public function __construct(
        public string $name,
        public string $code,
        public string $type,
        public bool $isActive,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            code: $data['code'],
            type: $data['type'],
            isActive: $data['is_active'],
        );
    }

    public static function fromRequest(Request $request): self
    {
        return self::fromArray($request->validated());
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'isActive' => $this->isActive,
        ];
    }
}
