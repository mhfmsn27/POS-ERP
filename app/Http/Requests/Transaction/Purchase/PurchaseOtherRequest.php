<?php

namespace App\Http\Requests\Transaction\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOtherRequest extends FormRequest
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
            'payment_information.discount_type' => 'required|in:percent,fixed',
            'payment_information.tax'           => 'required|numeric',
            'payment_information.discount'      => 'required|numeric',
            'payment_information.finalTotal'    => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'payment_information.discount_type.required'        => 'Diskon Tipe Harus Diisi',
            'payment_information.tax.required'                  => 'Pajak Harus Diisi, Miminal 0',
            'payment_information.discount.required'             => 'Diskon Harus Diisi Minimal 0',
            'payment_information.finalTotal.required'           => 'Final Total Harus Diisi Minimal 0',
            'general_information.supplier.id.required'          => 'Informasi Supplier Harus Diisi',
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
