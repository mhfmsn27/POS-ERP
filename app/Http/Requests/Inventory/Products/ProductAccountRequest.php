<?php

namespace App\Http\Requests\Inventory\Products;

use Illuminate\Foundation\Http\FormRequest;

class ProductAccountRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'supply.id'                     => 'required',
            'sale.id'                       => 'required',
            'return_sale.id'                => 'required',
            'discount.id'                   => 'required',
           // 'sent.id'                       => 'required',
            'cost.id'                       => 'required',
            'retur_purchase.id'             => 'required',
            // 'supplier_debt.id'              => 'required'

        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'message' => $validator->errors()->first(),
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
