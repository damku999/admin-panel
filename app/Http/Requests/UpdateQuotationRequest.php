<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuotationRequest extends FormRequest
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
            // Customer and Basic Info
            'customer_id' => 'required|exists:customers,id',
            'vehicle_number' => 'nullable|string|max:20',
            'make_model_variant' => 'required|string|max:255',
            'rto_location' => 'required|string|max:255',
            'manufacturing_year' => 'required|integer|min:1980|max:' . (date('Y') + 1),
            'date_of_registration' => 'required|date|before_or_equal:today',
            'cubic_capacity_kw' => 'required|integer|min:1',
            'seating_capacity' => 'required|integer|min:1|max:50',
            'fuel_type' => 'required|in:Petrol,Diesel,CNG,Electric,Hybrid',
            'policy_type' => 'required|in:Comprehensive,Own Damage,Third Party',
            'policy_tenure_years' => 'required|integer|in:1,2,3',
            'whatsapp_number' => 'nullable|string|regex:/^[6-9]\d{9}$/',
            
            // IDV Components
            'idv_vehicle' => 'required|numeric|min:10000|max:10000000',
            'idv_trailer' => 'nullable|numeric|min:0',
            'idv_cng_lpg_kit' => 'nullable|numeric|min:0',
            'idv_electrical_accessories' => 'nullable|numeric|min:0',
            'idv_non_electrical_accessories' => 'nullable|numeric|min:0',
            'total_idv' => 'required|numeric|min:10000',
            
            // Add-on covers
            'addon_covers' => 'nullable|array',
            'addon_covers.*' => 'string|in:Zero Depreciation,Engine Protection,Road Side Assistance,NCB Protection,Invoice Protection,Key Replacement,Personal Accident,Tyre Protection,Consumables',
            
            // Additional fields
            'notes' => 'nullable|string|max:2000',
            
            // Company quotes validation
            'companies' => 'nullable|array',
            'companies.*.id' => 'nullable|exists:quotation_companies,id',
            'companies.*.insurance_company_id' => 'required_with:companies.*|exists:insurance_companies,id',
            'companies.*.plan_name' => 'nullable|string|max:255',
            'companies.*.quote_number' => 'nullable|string|max:255',
            'companies.*.basic_od_premium' => 'required_with:companies.*|numeric|min:0',
            'companies.*.total_addon_premium' => 'nullable|numeric|min:0',
            'companies.*.cng_lpg_premium' => 'nullable|numeric|min:0',
            'companies.*.net_premium' => 'nullable|numeric|min:0',
            'companies.*.sgst_amount' => 'nullable|numeric|min:0',
            'companies.*.cgst_amount' => 'nullable|numeric|min:0',
            'companies.*.gst_amount' => 'nullable|numeric|min:0',
            'companies.*.final_premium' => 'nullable|numeric|min:0',
            'companies.*.is_recommended' => 'nullable|boolean',
            
            // Addon breakdown fields
            'companies.*.addon_zero_dep' => 'nullable|numeric|min:0',
            'companies.*.addon_zero_dep_note' => 'nullable|string|max:100',
            'companies.*.addon_engine_protection' => 'nullable|numeric|min:0',
            'companies.*.addon_engine_protection_note' => 'nullable|string|max:100',
            'companies.*.addon_rsa' => 'nullable|numeric|min:0',
            'companies.*.addon_rsa_note' => 'nullable|string|max:100',
            'companies.*.addon_ncb_protection' => 'nullable|numeric|min:0',
            'companies.*.addon_ncb_protection_note' => 'nullable|string|max:100',
            'companies.*.addon_invoice_protection' => 'nullable|numeric|min:0',
            'companies.*.addon_invoice_protection_note' => 'nullable|string|max:100',
            'companies.*.addon_key_replacement' => 'nullable|numeric|min:0',
            'companies.*.addon_key_replacement_note' => 'nullable|string|max:100',
            'companies.*.addon_personal_accident' => 'nullable|numeric|min:0',
            'companies.*.addon_personal_accident_note' => 'nullable|string|max:100',
            'companies.*.addon_tyre_protection' => 'nullable|numeric|min:0',
            'companies.*.addon_tyre_protection_note' => 'nullable|string|max:100',
            'companies.*.addon_consumables' => 'nullable|numeric|min:0',
            'companies.*.addon_consumables_note' => 'nullable|string|max:100',
            'companies.*.addon_others' => 'nullable|numeric|min:0',
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
            'idv_vehicle.required' => 'Vehicle IDV is required.',
            'idv_vehicle.min' => 'Vehicle IDV must be at least Rs.10,000.',
            'idv_vehicle.max' => 'Vehicle IDV cannot exceed Rs.1,00,00,000.',
            'total_idv.required' => 'Total IDV is required.',
            'total_idv.min' => 'Total IDV must be at least Rs.10,000.',
            'policy_type.required' => 'Policy type is required.',
            'policy_type.in' => 'Please select a valid policy type.',
            'policy_tenure_years.required' => 'Policy tenure is required.',
            'policy_tenure_years.in' => 'Policy tenure must be 1, 2, or 3 years.',
            'whatsapp_number.regex' => 'Please enter a valid 10-digit mobile number.',
            'addon_covers.*.in' => 'Please select valid add-on covers.',
            'notes.max' => 'Notes cannot exceed 2000 characters.',
            
            // Company validation messages
            'companies.*.insurance_company_id.required_with' => 'Please select an insurance company.',
            'companies.*.insurance_company_id.exists' => 'Selected insurance company does not exist.',
            'companies.*.basic_od_premium.required_with' => 'Basic OD premium is required for company quotes.',
            'companies.*.basic_od_premium.numeric' => 'Basic OD premium must be a valid amount.',
            'companies.*.basic_od_premium.min' => 'Basic OD premium must be zero or greater.',
            'companies.*.quote_number.max' => 'Quote number cannot exceed 255 characters.',
            'companies.*.plan_name.max' => 'Plan name cannot exceed 255 characters.',
            
            // Addon note validation
            'companies.*.addon_zero_dep_note.max' => 'Zero depreciation note cannot exceed 100 characters.',
            'companies.*.addon_engine_protection_note.max' => 'Engine protection note cannot exceed 100 characters.',
            'companies.*.addon_rsa_note.max' => 'RSA note cannot exceed 100 characters.',
            'companies.*.addon_ncb_protection_note.max' => 'NCB protection note cannot exceed 100 characters.',
            'companies.*.addon_invoice_protection_note.max' => 'Invoice protection note cannot exceed 100 characters.',
            'companies.*.addon_key_replacement_note.max' => 'Key replacement note cannot exceed 100 characters.',
            'companies.*.addon_personal_accident_note.max' => 'Personal accident note cannot exceed 100 characters.',
            'companies.*.addon_tyre_protection_note.max' => 'Tyre protection note cannot exceed 100 characters.',
            'companies.*.addon_consumables_note.max' => 'Consumables note cannot exceed 100 characters.',
        ];
    }
}
