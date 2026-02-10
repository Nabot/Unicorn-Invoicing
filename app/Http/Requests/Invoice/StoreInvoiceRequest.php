<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create-invoices');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => [
                'required',
                Rule::exists('clients', 'id')->where('company_id', $this->user()->company_id),
            ],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'terms' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.discount' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.vat_applicable' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'client_id.required' => 'Please select a client for this invoice.',
            'client_id.exists' => 'The selected client does not exist or does not belong to your company.',
            'due_date.required' => 'Please select a due date for this invoice.',
            'due_date.date' => 'The due date must be a valid date.',
            'due_date.after_or_equal' => 'The due date must be today or a future date.',
            'items.required' => 'Please add at least one item to the invoice.',
            'items.min' => 'Please add at least one item to the invoice.',
            'items.*.description.required' => 'Each item must have a description.',
            'items.*.description.max' => 'Item description cannot exceed 255 characters.',
            'items.*.quantity.required' => 'Each item must have a quantity.',
            'items.*.quantity.numeric' => 'Quantity must be a valid number.',
            'items.*.quantity.min' => 'Quantity must be at least 0.01.',
            'items.*.quantity.max' => 'Quantity cannot exceed 999,999.99.',
            'items.*.unit_price.required' => 'Each item must have a unit price.',
            'items.*.unit_price.numeric' => 'Unit price must be a valid number.',
            'items.*.unit_price.min' => 'Unit price cannot be negative.',
            'items.*.unit_price.max' => 'Unit price cannot exceed 999,999.99.',
            'items.*.discount.numeric' => 'Discount must be a valid number.',
            'items.*.discount.min' => 'Discount cannot be negative.',
            'items.*.discount.max' => 'Discount cannot exceed 999,999.99.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'terms.max' => 'Terms cannot exceed 1000 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize string inputs
        if ($this->has('notes')) {
            $this->merge([
                'notes' => strip_tags($this->notes),
            ]);
        }

        if ($this->has('terms')) {
            $this->merge([
                'terms' => strip_tags($this->terms),
            ]);
        }

        // Sanitize item descriptions
        if ($this->has('items')) {
            $items = $this->items;
            foreach ($items as $key => $item) {
                if (isset($item['description'])) {
                    $items[$key]['description'] = strip_tags($item['description']);
                }
            }
            $this->merge(['items' => $items]);
        }
    }
}
