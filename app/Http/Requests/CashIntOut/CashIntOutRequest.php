<?php

namespace App\Http\Requests\CashIntOut;

use Illuminate\Foundation\Http\FormRequest;

class CashIntOutRequest extends FormRequest
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
        return [
            'items'                                     => 'required|array|min:1',
            'category.id'                               => 'required',
            'method.id'                                 => 'required',
            'summary.subtotal'                          => 'required|numeric|min:1'
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
