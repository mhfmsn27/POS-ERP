<?php

namespace App\Http\Requests\Transaction\SaleReturn;

use Illuminate\Foundation\Http\FormRequest;

class SaleReturnRequest extends FormRequest
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
        return [
            'items'                   => 'required|array|min:1',
            'items.*.variation_id'    => 'required',
            'items.*.id'              => 'required',
            'items.*.return_qty'      => 'required|numeric|min:1',
        ];
    }

    public function messages()
    {
        return [
            'items.*.return_qty.min'                 => 'Qty Harus Lebih Dari 0',
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
