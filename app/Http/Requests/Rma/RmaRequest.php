<?php

namespace App\Http\Requests\Rma;

use Illuminate\Foundation\Http\FormRequest;

class RmaRequest extends FormRequest
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
            'customer_name'             => 'required',
            'phone'                     => 'required',
            'customer.id'               => 'required',
            'items'                     => 'required|array|min:1',
            'note'                      => 'nullable',
            'estimate_date'             => 'required',
            'estimate_price'            => 'required'
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
