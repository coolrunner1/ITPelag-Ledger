<?php

namespace App\DTOs;

use Illuminate\Http\Request;

final readonly class CreateAccountDTO implements IDTO
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
        $validated = $request->validated();

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        return self::fromArray($validated);
    }

    public function toArray(): array
    {
        $arr = [
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
        ];

        if ($this->isActive !== null) {
            $arr['is_active'] = $this->isActive;
        }

        return $arr;
    }
}
