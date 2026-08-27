<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class TypeAccountUpdateRequest extends FormRequest
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

        $id = collect(request()->segments())->last();
 
        return [
            'name'                                      => 'required|string|unique:account_types,name,' . ($id ?? '') . ',id,deleted_at,NULL',
            'coa_code'                                  => 'required|string|unique:account_types,coa_code,' . ($id ?? '') . ',id,deleted_at,NULL',
            'price'                                     => 'required|boolean',
            'modal'                                     => 'required|boolean',
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
