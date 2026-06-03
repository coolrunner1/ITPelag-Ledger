<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransactionRequest extends FormRequest
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
        return [
            'date'        => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'min:5', 'max:255'],
            'is_posted'   => ['nullable', 'boolean'],

            'journalEntries' => ['nullable', 'array'],

            'journalEntries.*.id'             => ['nullable', 'integer', 'exists:journal_entries,id'],
            'journalEntries.*.transaction_id' => ['required', 'integer', 'exists:transactions,id'],
            'journalEntries.*.account_id'     => ['required', 'integer', 'exists:accounts,id'],
            'journalEntries.*.amount'         => ['required', 'numeric', 'min:0'],
            'journalEntries.*.type'           => ['required', 'string', Rule::in(['debit', 'credit'])],

        ];
    }
}
