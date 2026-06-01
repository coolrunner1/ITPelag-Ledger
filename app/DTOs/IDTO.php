<?php

namespace App\DTOs;

use Illuminate\Http\Request;

interface IDTO
{
    public static function fromArray(array $data): self;

    public static function fromRequest(Request $request): self;

    public function toArray(): array;
}
