<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
            return [
                'name'=> 'required|string|min:3',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|confirmed|min:8',
            ];
    
        }
        public function messages(): array
        {
            return [
                'name.required' => 'The name is required.',
                'email.required' => 'The email is required.',
                'password.required' => 'The password is required.',
                'email.email' => 'The email must be a email.',
                'name.string' => 'The name must be a string.',
            ];
        }
    }
    