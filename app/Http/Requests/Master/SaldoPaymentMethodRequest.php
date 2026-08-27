<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class SaldoPaymentMethodRequest extends FormRequest
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
            'amount'        => 'required|numeric|min:1',
            'date'          => 'required',
            'note'          => 'nullable',
            'name'          => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'date.required'                             => 'Tanggal Wajib di isi',
            'amount.required'                           => 'Nominal Harus di isi',
            'amount.min'                                => 'Minimal Nominal lebih dari 0' 
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
