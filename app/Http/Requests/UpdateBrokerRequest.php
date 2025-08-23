<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrokerRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile_number' => 'nullable|numeric|digits:10',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Broker name is required.',
            'name.string' => 'Broker name must be a valid string.',
            'name.max' => 'Broker name cannot exceed 255 characters.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'mobile_number.numeric' => 'Mobile number must be numeric.',
            'mobile_number.digits' => 'Mobile number must be exactly 10 digits.',
        ];
    }
}
