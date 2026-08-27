<?php

namespace App\Http\Requests\Transaction\Sales;

use Illuminate\Foundation\Http\FormRequest;

class SalesItemRequest extends FormRequest
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
            'id'                                => 'required',
            'variation_id'                      => 'required',
            'qty'                               => 'required|numeric|min:1',
            'product_id'                        => 'required|min:1',
            'unit_price'                        => 'required|numeric',
            'subtotal'                          => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'variation_id.required'   => 'Salah Satu Produk yang akan di masukkan tidak valid',
            'qty.min'                 => 'Qty Harus Lebih Dari Angka 0',
            'unit_price.numeric'      => 'Harga Modal Harus Berupa Angka',
            'qty.required'            => 'Qty  Wajib Diisi',
            'unit_price.required'     => 'Harga Modal Wajib Diisi',
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
