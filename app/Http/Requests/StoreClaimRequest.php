<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimRequest extends FormRequest
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
        $rules = [
            'insurance_type' => 'required|in:Health,Truck',
            'policy_no' => 'nullable|string|max:255',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_insurance_id' => 'nullable|exists:customer_insurances,id',
            'vehicle_number' => 'nullable|string|max:255',
            'incident_date' => 'nullable|date',
            'intimation_date' => 'nullable|date',
            'claim_amount' => 'nullable|numeric|min:0',
            'claim_status' => 'nullable|string',
            'insurance_claim_number' => 'nullable|string|max:255',
            'liability_type' => 'nullable|in:Cashless,Reimbursement',
            'remarks' => 'nullable|string',
            
            // Health Insurance optional fields
            'patient_name' => 'nullable|string|max:255',
            'hospital_name' => 'nullable|string|max:255',
            'admission_date' => 'nullable|date',
            
            // Truck Insurance optional fields  
            'driver_name' => 'nullable|string|max:255',
            'driver_contact_number' => 'nullable|string|max:20',
            'accident_location' => 'nullable|string',
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'insurance_type.required' => 'Insurance type is required.',
            'customer_id.exists' => 'Selected customer does not exist.',
            'customer_insurance_id.exists' => 'Selected insurance policy does not exist.',
            'insurance_type.in' => 'Insurance type must be either Health or Truck.',
            'liability_type.in' => 'Liability type must be either Cashless or Reimbursement.',
            'incident_date.date' => 'Please enter a valid incident date.',
            'intimation_date.date' => 'Please enter a valid intimation date.',
            'claim_amount.numeric' => 'Claim amount must be a valid number.',
            'claim_amount.min' => 'Claim amount must be greater than or equal to 0.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Merge default values
        $this->merge([
            'fir_required' => $this->fir_required ?? false,
            'third_party_injury' => $this->third_party_injury ?? false,
        ]);
    }
}
