<?php

namespace App\Http\Requests\Transaction\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPurchaseRequest extends FormRequest
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
            'general_information.supplier.id'   => 'required',

            'items'                   => 'required|array|min:1',
            'items.*.variation_id'    => 'required',
            'items.*.qty'             => 'required|numeric|min:1',
            'items.*.product_id'      => 'required|min:1',
            'items.*.unit_price'      => 'required|numeric',
            'items.*.subtotal'        => 'required|numeric', 

            'payment_information.discount_type' => 'required|in:percent,fixed',
            'payment_information.tax'           => 'required|numeric',
            'payment_information.discount'      => 'required|numeric',
            'payment_information.finalTotal'    => 'required|numeric',

            // With Payment
            'with_pay'                          => 'required|boolean',
            'payment_information.date'          => 'required_if:with_pay,true', 
            'payment_information.method.id'     => 'required_if:with_pay,true',
            'payment_information.pay_total'     => 'required_if:with_pay,true',
        ];
    }

    public function messages()
    {
        return [
            'general_information.supplier.id.required'      => 'Informasi Supplier Harus Diisi',
            'general_infromation.status.required'           => 'Status Pembelian wajib diisi',
            'general_information.status.in'                 => 'Status Pembelian tidak valid',

            'items.required'                  => 'Produk Detail harus anda isi',
            'items.min'                       => 'Minimal menambahkan produk detail satu',
            'items.*.variation_id.required'   => 'Salah Satu Produk yang akan di masukkan tidak valid',
            'items.*.qty.min'                 => 'Qty Harus Lebih Dari Angka 0',
            'items.*.unit_price.numeric'      => 'Harga Modal Harus Berupa Angka',
            'items.*.qty.required'            => 'Qty  Wajib Diisi',
            'items.*.unit_price.required'     => 'Harga Modal Wajib Diisi',

            'payment_information.discount_type.required'            => 'Diskon Tipe Harus Diisi',
            'payment_information.tax.required'                      => 'Pajak Harus Diisi, Miminal 0',
            'payment_information.discount.required'                 => 'Diskon Harus Diisi Minimal 0',
            'payment_information.finalTotal.required'               => 'Final Total Harus Diisi Minimal 0',
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
