<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
        $customerId = $this->route('customer')?->id;
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,' . $customerId,
            'mobile_number' => 'required|numeric|digits:10',
            'status' => 'required|numeric|in:0,1',
            'type' => 'required|in:Retail,Corporate',
            'pan_card_number' => 'required_if:type,Retail|nullable|string|max:10',
            'pan_card_path' => 'nullable|file|max:1024|mimetypes:application/pdf,image/jpeg,image/png',
            'aadhar_card_number' => 'required_if:type,Retail|nullable|string|max:12',
            'aadhar_card_path' => 'nullable|file|max:1024|mimetypes:application/pdf,image/jpeg,image/png',
            'gst_number' => 'required_if:type,Corporate|nullable|string|max:15',
            'gst_path' => 'nullable|file|max:1024|mimetypes:application/pdf,image/jpeg,image/png',
        ];

        if (!empty($this->date_of_birth)) {
            $rules['date_of_birth'] = 'date_format:Y-m-d';
        }

        if (!empty($this->wedding_anniversary_date)) {
            $rules['wedding_anniversary_date'] = 'date_format:Y-m-d';
        }

        if (!empty($this->engagement_anniversary_date)) {
            $rules['engagement_anniversary_date'] = 'date_format:Y-m-d';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Customer name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already taken.',
            'mobile_number.required' => 'Mobile number is required.',
            'mobile_number.digits' => 'Mobile number must be exactly 10 digits.',
            'type.required' => 'Customer type is required.',
            'type.in' => 'Customer type must be either Retail or Corporate.',
            'pan_card_number.required_if' => 'PAN card number is required for Retail customers.',
            'aadhar_card_number.required_if' => 'Aadhar card number is required for Retail customers.',
            'gst_number.required_if' => 'GST number is required for Corporate customers.',
            'date_of_birth.date_format' => 'Date of birth must be in Y-m-d format.',
            'wedding_anniversary_date.date_format' => 'Wedding anniversary date must be in Y-m-d format.',
            'engagement_anniversary_date.date_format' => 'Engagement anniversary date must be in Y-m-d format.',
        ];
    }
}
