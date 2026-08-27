<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class ChangeProfileRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id                                             = auth()->user()->id;
        return [
            'name'                                      => 'required|max:200|regex:/^[\pL\s\-]+$/u', 
            'email'                                     => "required|email|unique:users,email,{$id},id,deleted_at,NULL",
            'phone'                                     => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required'                             => 'Nama Lengkap Wajib di isi',
            'email.required'                            => 'Email Wajib di isi',
            'email.unique'                              => 'Email sudah ada yang menggunakan',
            'phone.required'                            => 'Nomor Hp sudah ada yang menggunakan'
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
