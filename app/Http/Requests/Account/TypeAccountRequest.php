<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class TypeAccountRequest extends FormRequest
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
            'name'                                      => 'required|string|unique:account_types,name,NULL,id,deleted_at,NULL',
            'coa_code'                                  => 'required|unique:account_types,coa_code,NULL,id,deleted_at,NULL',
            'price'                                     => 'required|boolean',
            'modal'                                     => 'required|boolean',
            'type'                                      => 'required|in:non_bank_cash,bank_cash'
        ];
    }

    public function messages()
    {
        return [
            'name.unique'                               => 'Nama ini sudah digunakan sebelumnya',
            'coa_code.unique'                           => 'Kode ini sudah pernah digunakan sebelumnya',

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
