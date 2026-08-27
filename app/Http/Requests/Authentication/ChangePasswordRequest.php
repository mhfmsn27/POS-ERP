<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'new'                              => 'required|min:8|max:20',
            'old'                                  => 'required',
            'confirm'                     => 'required',
        ];
    }

    public function messages()
    {
        return [

            'new.required'                     => 'Password baru wajib di isi',
            'new.min'                          => 'Password minimal di isi 8 karakter',
            'new.max'                          => 'Maximal Password 20 karakter',

            'old.required'                         => 'Password lama harus di isi',
            'confirm.required'            => 'Konfirmasi password harus di isi',

        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'message' => $validator->errors()->first(),
        ], 419);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
