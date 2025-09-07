<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimLiabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'claim_id' => 'required|exists:claims,id',
            'liability_type' => 'required|in:Cashless,Reimbursement',
            'claim_amount' => 'required|numeric|min:0',
            'salvage_amount' => 'nullable|numeric|min:0',
            'claim_charge' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'payment_reference_number' => 'nullable|string|max:255',
            'payment_date' => 'nullable|date',
            'payment_notes' => 'nullable|string',
            'payment_status' => 'required|in:Pending,Processed,Completed,Failed',
            'remarks' => 'nullable|string',
            'is_final' => 'nullable|boolean',
            'status' => 'required|boolean',
        ];
    }
}
