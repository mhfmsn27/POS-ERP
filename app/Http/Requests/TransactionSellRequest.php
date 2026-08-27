<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;


class TransactionSellRequest extends FormRequest
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
            'customer_id'                       => 'required|min:1',
            'discount'                          => 'required',
            'tax'                               => 'required',
            'shipping'                          => 'required',
            'other_price'                       => 'required',
            'fixtotal'                          => 'required',
            'payment_methode'                   => 'required',
            'on_pay'                            => 'required',
            'payment_service'                   => 'required',

            'details'                           => 'required|array|min:1',
            'details.*.variation_id'            => 'required|min:1',
            'details.*.product_id'              => 'required|min:1',
            'details.*.qty'                     => 'required|min:1',
            'details.*.unit_cost'               => 'required',
            'details.*.subtotal'                => 'required',
        ];
    }


    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {

        $response =  response()->json([
            'errors' => $validator->errors(),
            'status' => 'error'
        ], 200);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
