<?php

namespace Piyush\PassportAuth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $first = config('passport-auth.registration.first_name_field', 'first_name');
        $last = config('passport-auth.registration.last_name_field', 'last_name');

        return [
            'name' => ['sometimes', 'nullable', 'string', 'min:2', 'max:100'],
            $first => ['sometimes', 'nullable', 'string', 'min:2', 'max:100'],
            $last => ['sometimes', 'nullable', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . config('passport-auth.users_table', 'users') . ',email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $name = $this->input('name');
            $first = $this->input(config('passport-auth.registration.first_name_field', 'first_name'));
            if (!$name && !$first) {
                $validator->errors()->add('name', 'Name is required.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.min' => 'Name must be at least 2 characters.',
            'name.max' => 'Name may not be greater than 100 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
