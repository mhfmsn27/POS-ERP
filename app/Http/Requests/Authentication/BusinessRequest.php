<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class BusinessRequest extends FormRequest
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
            'name'                      => 'required',
            'phone'                     => 'required|numeric',
            'accountant_use'            => 'required|in:yes,no',
            'email'                     => 'required|email',
            'tax_option'                => 'required|in:yes,no',
            'address'                   => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required'             => 'Nama Lengkap Harus Di isi',
            'phone.required'            => 'Nomor Ponsel Harus Di isi',
            'email.required'            => 'Email Harus di isi',
            'address.required'          => 'Alamat Harus Di isi',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = new JsonResponse([
            'message' => $validator->errors()->first(),
            'status' => false,
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
