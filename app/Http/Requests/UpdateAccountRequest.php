<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $accountId = $this->route('account');

        return [
            'name' => ['nullable', 'string', "min:5", 'max:255'],

            'code' => [
                'nullable',
                'string',
                Rule::unique('accounts', 'code')
                    ->ignore($accountId),
            ],

            'type' => [
                'nullable',
                Rule::in([
                    'asset',
                    'liability',
                    'equity',
                    'revenue',
                    'expense',
                ]),
            ],

            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
