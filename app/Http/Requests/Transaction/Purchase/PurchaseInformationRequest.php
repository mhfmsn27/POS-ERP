<?php

namespace App\Http\Requests\Transaction\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseInformationRequest extends FormRequest
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
            'general_information.supplier.id'               => 'required',
        ];
    }

    public function messages()
    {
        return [
            'general_information.supplier.id.required'      => 'Informasi Supplier Harus Diisi',
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
