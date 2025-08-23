<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuotationRequest extends FormRequest
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
            'customer_id' => 'required|exists:customers,id',
            'vehicle_number' => 'nullable|string|max:20',
            'make_model_variant' => 'required|string|max:255',
            'rto_location' => 'required|string|max:255',
            'manufacturing_year' => 'required|integer|min:1980|max:' . (date('Y') + 1),
            'date_of_registration' => 'required|date|before_or_equal:today',
            'cubic_capacity_kw' => 'required|integer|min:1',
            'seating_capacity' => 'required|integer|min:1|max:50',
            'fuel_type' => 'required|in:Petrol,Diesel,CNG,Electric,Hybrid',
            'ncb_percentage' => 'nullable|numeric|min:0|max:50',
            'idv_vehicle' => 'required|numeric|min:10000|max:10000000',
            'idv_trailer' => 'nullable|numeric|min:0',
            'idv_cng_lpg_kit' => 'nullable|numeric|min:0',
            'idv_electrical_accessories' => 'nullable|numeric|min:0',
            'idv_non_electrical_accessories' => 'nullable|numeric|min:0',
            'policy_type' => 'required|in:Comprehensive,Own Damage,Third Party',
            'policy_tenure_years' => 'required|integer|in:1,2,3',
            'whatsapp_number' => 'nullable|string|regex:/^[6-9]\d{9}$/',
            'addon_covers' => 'nullable|array',
            'addon_covers.*' => 'string|max:255',
            'notes' => 'nullable|string|max:1000',
            'companies' => 'nullable|array',
            'companies.*.insurance_company_id' => 'required_with:companies|exists:insurance_companies,id',
            'companies.*.quote_number' => 'nullable|string|max:255',
            'companies.*.plan_name' => 'nullable|string|max:255',
            'companies.*.basic_od_premium' => 'required_with:companies|numeric|min:0',
            'companies.*.tp_premium' => 'required_with:companies|numeric|min:0',
            'companies.*.total_addon_premium' => 'nullable|numeric|min:0',
            'companies.*.cng_lpg_premium' => 'nullable|numeric|min:0',
            // Individual addon fields
            'companies.*.addon_zero_dep' => 'nullable|numeric|min:0',
            'companies.*.addon_engine_protection' => 'nullable|numeric|min:0',
            'companies.*.addon_rsa' => 'nullable|numeric|min:0',
            'companies.*.addon_ncb_protection' => 'nullable|numeric|min:0',
            'companies.*.addon_invoice_protection' => 'nullable|numeric|min:0',
            'companies.*.addon_key_replacement' => 'nullable|numeric|min:0',
            'companies.*.addon_personal_accident' => 'nullable|numeric|min:0',
            'companies.*.addon_tyre_protection' => 'nullable|numeric|min:0',
            'companies.*.addon_consumables' => 'nullable|numeric|min:0',
            'companies.*.addon_others' => 'nullable|numeric|min:0',
            // Addon note fields
            'companies.*.addon_zero_dep_note' => 'nullable|string|max:100',
            'companies.*.addon_engine_protection_note' => 'nullable|string|max:100',
            'companies.*.addon_rsa_note' => 'nullable|string|max:100',
            'companies.*.addon_ncb_protection_note' => 'nullable|string|max:100',
            'companies.*.addon_invoice_protection_note' => 'nullable|string|max:100',
            'companies.*.addon_key_replacement_note' => 'nullable|string|max:100',
            'companies.*.addon_personal_accident_note' => 'nullable|string|max:100',
            'companies.*.addon_tyre_protection_note' => 'nullable|string|max:100',
            'companies.*.addon_consumables_note' => 'nullable|string|max:100',
            'companies.*.addon_others_note' => 'nullable|string|max:100',
            'companies.*.net_premium' => 'nullable|numeric|min:0',
            'companies.*.sgst_amount' => 'nullable|numeric|min:0',
            'companies.*.cgst_amount' => 'nullable|numeric|min:0',
            'companies.*.total_premium' => 'nullable|numeric|min:0',
            'companies.*.final_premium' => 'nullable|numeric|min:0',
            'companies.*.total_od_premium' => 'nullable|numeric|min:0',
            'companies.*.is_recommended' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Please select a customer.',
            'customer_id.exists' => 'Selected customer does not exist.',
            'make_model_variant.required' => 'Vehicle make, model and variant is required.',
            'rto_location.required' => 'RTO location is required.',
            'manufacturing_year.required' => 'Manufacturing year is required.',
            'manufacturing_year.min' => 'Manufacturing year must be 1980 or later.',
            'manufacturing_year.max' => 'Manufacturing year cannot be in the future.',
            'date_of_registration.required' => 'Date of registration is required.',
            'date_of_registration.before_or_equal' => 'Registration date cannot be in the future.',
            'cubic_capacity_kw.required' => 'Engine capacity is required.',
            'seating_capacity.required' => 'Seating capacity is required.',
            'fuel_type.required' => 'Fuel type is required.',
            'fuel_type.in' => 'Please select a valid fuel type.',
            'ncb_percentage.numeric' => 'NCB percentage must be a valid number.',
            'ncb_percentage.min' => 'NCB percentage cannot be negative.',
            'ncb_percentage.max' => 'NCB percentage cannot exceed 50%.',
            'idv_vehicle.required' => 'Vehicle IDV is required.',
            'idv_vehicle.min' => 'Vehicle IDV must be at least ₹10,000.',
            'idv_vehicle.max' => 'Vehicle IDV cannot exceed ₹1,00,00,000.',
            'policy_type.required' => 'Policy type is required.',
            'policy_type.in' => 'Please select a valid policy type.',
            'policy_tenure_years.required' => 'Policy tenure is required.',
            'policy_tenure_years.in' => 'Policy tenure must be 1, 2, or 3 years.',
            'whatsapp_number.regex' => 'Please enter a valid 10-digit mobile number.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }
}
