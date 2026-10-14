<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBoqItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('boq.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'cost_code_id' => ['nullable', 'exists:cost_codes,id'],
            'item_code' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string'],
            'unit' => ['required', 'string', 'max:30'],
            'planned_quantity' => ['required', 'numeric', 'min:0.0001'],
            'unit_rate' => ['required', 'numeric', 'min:0'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }
}
