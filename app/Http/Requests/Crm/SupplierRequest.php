<?php

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
            'name'                  => 'required',
            'is_account'            => 'required|boolean',
            'term.id'               => 'required',
            'debt.id'               => 'required_if:is_account,true', 
        ];
    }

    public function messages()
    {
        return [
            'term.id.required'                             => 'Syarat Pembayaran Wajib Di isi',
            'debt.id.required_if'                          => 'Akun Utang Harus di isi', 
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
