<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class LoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email'                         => 'required|email',
            'password'                      => 'required'
        ];
    }

    public function messages()
    {
        return [

            'email.required'                        => 'Alamet Email Harus Diisi', 
            'email.email'                           => 'Alamat Email Harus berisi Email Aktif',
            'password.required'                     => 'Password tidak boleh kosong',  

        ];
    }


    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = new JsonResponse([
            'message' => $validator->errors()->first(),
            'status' => false,
        ], 400);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
