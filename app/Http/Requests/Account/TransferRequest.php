<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'amount'        => 'required|numeric|min:1',
            'date'          => 'required',
            'account.id'    => 'required',
            'note'          => 'nullable',
            'name'          => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'account.id.required'   => 'Pilih Tujuan Akun Transfer',
            'amount.min'            => 'Nominal Transfer harus di isi',

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
