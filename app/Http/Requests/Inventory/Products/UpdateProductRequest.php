<?php

namespace App\Http\Requests\Inventory\Products;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name'                     => 'required|min:2|max:200',
            'category.id'              => 'required',
            'barcode_type'             => 'required',
            'is_variant'               => 'required|boolean',
            'is_stock'                 => 'required|boolean',
            'alert_qty'                => 'required_if:is_stock,true',

            'weight'                   => 'numeric',
            'brand.id'                 => 'nullable',
            'description'              => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'name.required'                 => __('merchant/products.product.info_general_name_required'),
            'name.min'                      => __('merchant/products.product.info_general_name_min'),
            'name.max'                      => __('merchant/products.product.info_general_name_max'),
            'alert_qty.required_if'         => __('merchant/products.product.info_general_alert_qty_required_if'),
            'category.id.required'          => __('merchant/products.product.info_general_category_id_required'),
            'barcode_type.required'         => __('merchant/products.product.info_general_barcode_type_required'),
            'is_variant.required'           => __('merchant/products.product.info_general_is_variant_required'),
            'is_variant.boolean'            => __('merchant/products.product.info_general_is_variant_boolean'),

            'weight.numeric'                => __('merchant/products.product.other_detail_weight_numeric'),

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
