<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'claim_id' => 'required|exists:claims,id',
            'stage_name' => 'required|string|max:255',
            'stage_description' => 'nullable|string',
            'notes' => 'nullable|string',
            'stage_date' => 'nullable|date',
            'is_current' => 'nullable|boolean',
            'stage_order' => 'nullable|integer|min:1',
            'stage_status' => 'required|in:Pending,In Progress,Completed,On Hold,Cancelled',
            'status' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'claim_id.required' => 'Claim is required.',
            'stage_name.required' => 'Stage name is required.',
            'stage_status.required' => 'Stage status is required.',
            'stage_status.in' => 'Stage status must be valid.',
        ];
    }
}
