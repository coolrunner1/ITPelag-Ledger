<?php

namespace App\DTOs;

final readonly class CreateTransactionDTO
{
    /*public function __construct(
        public string $email,
        public string $password,
        public string $firstName,
        public string $lastName,
        public ?string $phoneNumber = null,
        public bool $agreeToTerms = false,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            phoneNumber: $data['phone_number'] ?? null,
            agreeToTerms: $data['agree_to_terms'] ?? false,
        );
    }

    public static function fromRequest(Request $request): self
    {
        return self::fromArray($request->validated());
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'phone_number' => $this->phoneNumber,
            'agree_to_terms' => $this->agreeToTerms,
        ];
    }*/
}
