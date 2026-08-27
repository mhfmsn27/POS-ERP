<?php

namespace App\Http\Requests\Transaction\StockOpname;

use Illuminate\Foundation\Http\FormRequest;

class StockOpnameRequest extends FormRequest
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
            'date'                              => 'nullable',
            'note'                              => 'nullable',
            'ref_no'                            => 'nullable',


            'items'                             => 'required|array|min:1',
            'items.*.variation_id'              => 'required',
            'items.*.qty'                       => 'required|numeric',
            'items.*.product_id'                => 'required',
            'items.*.type'                      => 'required|in:add,min',
            'items.*.quantity'                  => 'required|numeric',
            'items.*.hasil_qty'                 => 'required|numeric',
            'items.*.unit'                      => 'nullable',

        ];
    }

    public function messages()
    {
        return [
            'items.min'                             => 'Silahkan isi produk yang akan di Stok Opname Minimal 1 produk',
            'items.*.qty.required'                  => 'Quantity Stok Opname harus di isi, minimal 0'
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
