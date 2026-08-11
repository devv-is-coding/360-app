<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

trait ProfileValidationRules
{
    /**
     * Get the validation rules used to validate user profiles.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function profileRules(?int $userId = null): array
    {
        return [
            'username' => $this->usernameRules($userId),
            'email' => $this->emailRules($userId),
            'first_name' => $this->givenNameRules(),
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => $this->givenNameRules(),
            'contact_number' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * Get the validation rules used to validate usernames.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function usernameRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'max:255',
            'alpha_dash',
            $this->uniqueUserRule('username', $userId),
        ];
    }

    /**
     * Get the validation rules used to validate a first or last name.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function givenNameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * Get the validation rules used to validate user emails.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function emailRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            $this->uniqueUserRule('email', $userId),
        ];
    }

    /**
     * Build a unique rule for a user column, ignoring the given user when updating.
     */
    private function uniqueUserRule(string $column, ?int $userId): Unique
    {
        $rule = Rule::unique(User::class, $column);

        return $userId === null ? $rule : $rule->ignore($userId);
    }
}
