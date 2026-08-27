<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class EmailRequest extends FormRequest
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
            'email'                                     => 'required|email', 
        ];
    }

    public function messages()
    {
        return [
            'email.required'                        => 'Alamat Email Harus Diisi',
            'email.email'                           => 'Alamat Email Tidak valid', 
            
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
