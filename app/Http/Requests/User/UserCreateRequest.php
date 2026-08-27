<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
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
            'stores'                                    => 'required|array|min:1',
            'name'                                      => 'required|max:200|regex:/^[\pL\s\-]+$/u',
            'password'                                  => 'required|min:8|max:20',
            'email'                                     => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'phone'                                     => 'required|numeric|unique:users,phone,NULL,id,deleted_at,NULL',
            'role'                                      => 'required',  
            'jk'                                        => 'required|in:pria,wanita'
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
