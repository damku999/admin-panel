<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
            'description' => 'nullable|string',
            'liability_type' => 'nullable|in:Cashless,Reimbursement',
            'current_stage' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'closure_reason' => 'nullable|string',
        ];

        // Health Insurance specific validation
        if ($this->insurance_type === 'Health') {
            $rules = array_merge($rules, [
                'patient_name' => 'nullable|string|max:255',
                'contact_number' => 'nullable|string|max:20',
                'admission_date' => 'nullable|date',
                'treating_doctor_name' => 'nullable|string|max:255',
                'hospital_name' => 'nullable|string|max:255',
                'hospital_address' => 'nullable|string',
                'illness' => 'nullable|string',
                'approx_hospitalization_days' => 'nullable|integer|min:1',
                'approx_cost' => 'nullable|numeric|min:0',
            ]);
        }

        // Truck Insurance specific validation
        if ($this->insurance_type === 'Truck') {
            $rules = array_merge($rules, [
                'driver_contact_number' => 'nullable|string|max:20',
                'spot_location_address' => 'nullable|string',
                'fir_required' => 'nullable|boolean',
                'third_party_injury' => 'nullable|boolean',
                'accident_description' => 'nullable|string',
            ]);
        }

        return $rules;
    }

}
