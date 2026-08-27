<?php

namespace App\Http\Requests\Transaction\Sales;

use Illuminate\Foundation\Http\FormRequest;

class SalesPaymentRequest extends FormRequest
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
            'customer.id'           => 'required',
            'total_payment'         => 'required|numeric',
            'date'                  => 'required',
            'payment_method'        => 'required|in:cash,saldo',
            'method.id'             => 'required_if:payment_method,cash',


            'fakturs'                   => 'required|array|min:1',
            'fakturs.*.id'              => 'required',

        ];
    }

    public function messages()
    {
        return [
            'customer.id.required'      => 'Informasi customer Harus Diisi',
            'total_payment.required'    => 'Nominal Pembayaran Harus Di isi',
            'total_payment.min'         => 'Minimal Pembayaran lebih dari 0',
            'payment_method.required'   => 'Metode Pembayaran Harus diisi',
            'method.id.required_if'     => 'Bank / Cash Harus di pilih',

            'fakturs.required'                  => 'Faktur Detail harus anda isi',
            'fakturs.min'                       => 'Minimal menambahkan Faktur detail satu',
            'fakturs.*.id.required'             => 'Salah Satu Faktur yang akan di masukkan tidak valid',
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
