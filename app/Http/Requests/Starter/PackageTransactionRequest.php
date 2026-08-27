<?php

namespace App\Http\Requests\Starter;

use Illuminate\Foundation\Http\FormRequest;

class PackageTransactionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'store.id'      => 'required',
            'package.id'    => 'required'
        ];
    }

    public function messages()
    {
        return [
            'store.id.required'      => 'Informasi Toko atau Cabang harus di isi',
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
