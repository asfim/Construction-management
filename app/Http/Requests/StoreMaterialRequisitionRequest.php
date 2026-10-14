<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequisitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('mr.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'requisition_no' => ['required', 'string', 'max:50'],
            'requisition_date' => ['required', 'date'],
            'required_by_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.cost_code_id' => ['nullable', 'exists:cost_codes,id'],
            'items.*.quantity_requested' => ['required', 'numeric', 'min:0.0001'],
            'items.*.remarks' => ['nullable', 'string', 'max:255'],
        ];
    }
}
