<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'                                      => 'required|regex:/^[\pL\s\-]+$/u|min:4|max:200', 
            'password'                                  => 'required|min:8|max:20',
            'password_confirmation'                          => 'required',
            'email'                                     => 'required|email|unique:users,email',  
            'phone'                                     => 'required|numeric|unique:users,phone',  
            'jk'                                        => 'required|in:pria,wanita',
        ];
    }

    public function messages()
    {
        return [
            'name.required'                         => 'Nama Lengkap Tidak Boleh Kosong',
            'name.regex'                            => 'Isi Nama Lengkap Tidak Valid',
            'name.max'                              => 'Nama Tidak boleh lebih dari 200 karakter',

            'password.required'                     => 'Password Tidak boleh kosong',
            'password.min'                          => 'Minimal Password 8 Karakter',
            'password.max'                          => 'Maximal Password 200 Karakter',

            'password_confirmation.required'             => 'Konfirmasi Password harus diisi',

            'email.required'                        => 'Alamat Email Harus Diisi',
            'email.email'                           => 'Alamat Email Tidak valid',
            'email.unique'                          => 'Alamat Email ini sudah terdaftar atau sudah digunakan',

            
            'phone.required'                        => 'Nomor Ponsel Harus Diisi',
            'phone.min'                             => 'Nomor Ponsel Minimal 10 Karakter',
            'phone.max'                             => 'Nomor Ponsel Maximal 20 Karakter',
            'jk.required'                           => 'Jenis Kelamin Harus Diisi',
            'jk.in'                                 => 'Jenis Kelamin tidak valid',
            
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
