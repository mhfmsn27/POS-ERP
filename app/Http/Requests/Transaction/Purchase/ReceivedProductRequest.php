<?php

namespace App\Http\Requests\Transaction\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class ReceivedProductRequest extends FormRequest
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
            'supplier.id'   => 'required',

            'items'                   => 'required|array|min:1',
            'items.*.variation_id'    => 'required',
            'items.*.qty'             => 'required|numeric|min:1',
            'items.*.product_id'      => 'required|min:1', 
        ];
    }

    public function messages()
    {
        return [
            'supplier.id.required'      => 'Informasi Supplier Harus Diisi',

            'items.required'                  => 'Produk Detail harus anda isi',
            'items.min'                       => 'Minimal menambahkan produk detail satu',
            'items.*.variation.id.required'   => 'Salah Satu Produk yang akan di masukkan tidak valid',
            'items.*.qty.min'                 => 'Qty Harus Lebih Dari Angka 0', 
            'items.*.qty.required'            => 'Qty  Wajib Diisi', 
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
