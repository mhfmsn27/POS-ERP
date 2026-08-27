<?php

namespace App\Http\Requests\Inventory\Products;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVariationRequest extends FormRequest
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
            'name'                     => 'required_if:info_general.is_variant,true',
            'purchase_price'           => 'required|numeric',
            'selling_price'            => 'required|numeric',
            'grocery'                  => 'numeric',
            'unit'                     => 'required',
            'include_tax'              => 'required|boolean',
            'tax'                      => 'required_if:include_tax,false|numeric',

        ];
    }

    public function messages()
    {
        return [

            'name.required_if'             => __('merchant/products.product.variations___name_required_if'),
            'purchase_price.required'      => __('merchant/products.product.variations___purchase_price_required_if'),
            'purchase_price.numeric'       => __('merchant/products.product.variations___purchase_price_numeric'),
            'selling_price.required'       => __('merchant/products.product.variations___selling_price_required'),
            'selling_price.numeric'        => __('merchant/products.product.variations___selling_price_numeric'),
            'grocery.numeric'              => __('merchant/products.product.variations___grocery_price_numeric'),
            'unit.required'                => __('merchant/products.product.variations___unit_required'),
            'include_tax.required'         => __('merchant/products.product.variations___include_tax_required'),
            'include_tax.boolean'          => __('merchant/products.product.variations___include_tax_boolean'),
            'tax.required_if'              => __('merchant/products.product.variations___tax_required_if'),
            'tax.numeric'                  => __('merchant/products.product.variations___tax_numeric'),


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
