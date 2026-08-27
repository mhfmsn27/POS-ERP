<?php

namespace App\Http\Requests\Transaction\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseProductItemRequest extends FormRequest
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
            'product_information.items'                           => 'required|array|min:1',
            'product_information.items.*.variation_id'            => 'required',
            'product_information.items.*.qty'                     => 'required|numeric|min:1',
            'product_information.items.*.product_id'              => 'required',
            'product_information.items.*.unit_price'              => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'product_information.items.*.variation_id.required'   => 'Salah Satu Produk yang akan di masukkan tidak valid',
            'product_information.items.*.qty.min'                 => 'Qty Harus Lebih Dari Angka 0',
            'product_information.items.*.unit_price.numeric'      => 'Harga Modal Harus Berupa Angka',
            'product_information.items.*.qty.required'            => 'Qty  Wajib Diisi',
            'product_information.items.*.unit_price.required'     => 'Harga Modal Wajib Diisi',
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
