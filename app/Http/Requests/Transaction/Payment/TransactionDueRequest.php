<?php

namespace App\Http\Requests\Transaction\Payment;

use Illuminate\Foundation\Http\FormRequest;

class TransactionDueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
            'date'                  => 'required',
            'amount'                => 'required|numeric|min:1',
            'method.id'             => 'required',
            'type'                  => 'required|in:supplier,customer'
        ];
    }

    public function messages()
    {
        return [
            'date.required'                             => 'Tanggal Wajib di isi',
            'amount.required'                           => 'Nominal Harus di isi',
            'amount.min'                                => 'Minimal Nominal lebih dari 0',
            'method.id'                                 => 'Metode Pembayaran Wajib Anda isi'
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
