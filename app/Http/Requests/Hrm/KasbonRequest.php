<?php

namespace App\Http\Requests\Hrm;

use Illuminate\Foundation\Http\FormRequest;

class KasbonRequest extends FormRequest
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
            'method.id'         => 'required',
            'employee.id'       => 'required',
            'type'              => 'required|in:int,out',
            'amount'            => 'required|numeric|min:1'
        ];
    }

    public function messages()
    {
        return [
            'method.id.required'                             => 'Metode pembayaran tidak boleh kosong',
            'employee.id.required'                           => 'Pegawai tidak boleh kosong',
            'amount.min'                                => 'Nominal harus lebih dari 0',
            'amount.required'                           => 'Nominal harus di isi'
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
