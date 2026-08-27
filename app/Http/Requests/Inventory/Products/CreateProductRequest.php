<?php

namespace App\Http\Requests\Inventory\Products;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
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
            'info_general.name'                     => 'required|min:2|max:200',
            'info_general.category.id'              => 'required',
            'info_general.barcode_type'             => 'required',
            'info_general.is_variant'               => 'required|boolean',
            'info_general.is_stock'                 => 'required|boolean',
            'info_general.alert_qty'                => 'required_if:info_general.is_stock,true',
            'info_general.is_account'               => 'required|boolean',

            'info_general.supply.id'                => 'required_if:info_general.account,true',
            'info_general.sale.id'                  => 'required_if:info_general.account,true',
            'info_general.return_sale.id'           => 'required_if:info_general.account,true',
            'info_general.discount.id'              => 'required_if:info_general.account,true',
            // 'info_general.sent.id'                  => 'required_if:info_general.account,true',
            'info_general.cost.id'                  => 'required_if:info_general.account,true',
            'info_general.retur_purchase.id'        => 'required_if:info_general.account,true',
            // 'info_general.supplier_debt.id'         => 'required_if:info_general.account,true',

            'other_detail.weight'                   => 'numeric',
            'other_detail.brand.id'                 => 'nullable',
            'other_detail.description'              => 'nullable',

            'variations'                            => 'required|array|min:1',
            'variations.*.name'                     => 'required_if:info_general.is_variant,true',
            'variations.*.barcode'                  => 'nullable',
            'variations.*.purchase_price'           => 'required|numeric',
            'variations.*.selling_price'            => 'required|numeric',
            'variations.*.grocery'                  => 'numeric', 
            'variations.*.unit'                     => 'required',   
            'variations.*.rak.id'                   => 'nullable',

            'variations.*.stock'                    => 'required|numeric',

        ];
    }

    public function messages()
    {
        return [
            'info_general.name.required'                 => __('merchant/products.product.info_general_name_required'),
            'info_general.name.min'                      => __('merchant/products.product.info_general_name_min'),
            'info_general.name.max'                      => __('merchant/products.product.info_general_name_max'),
            'info_general.alert_qty.required_if'         => __('merchant/products.product.info_general_alert_qty_required_if'),
            'info_general.category.id.required'          => __('merchant/products.product.info_general_category_id_required'),
            'info_general.barcode_type.required'         => __('merchant/products.product.info_general_barcode_type_required'),
            'info_general.is_variant.required'           => __('merchant/products.product.info_general_is_variant_required'),
            'info_general.is_variant.boolean'            => __('merchant/products.product.info_general_is_variant_boolean'),

            'other_detail.weight.numeric'                => __('merchant/products.product.other_detail_weight_numeric'),

            'media.required'                            => __('merchant/products.product.media_required'), 
            'media.max'                                 => __('merchant/products.product.media_max'),

            'variations.required'                       => __('merchant/products.product.variations_required'),
            'variations.*.name.required_if'             => __('merchant/products.product.variations___name_required_if'),
            'variations.*.purchase_price.required'      => __('merchant/products.product.variations___purchase_price_required_if'),
            'variations.*.purchase_price.numeric'       => __('merchant/products.product.variations___purchase_price_numeric'),
            'variations.*.selling_price.required'       => __('merchant/products.product.variations___selling_price_required'),
            'variations.*.selling_price.numeric'        => __('merchant/products.product.variations___selling_price_numeric'),
            'variations.*.grocery.numeric'              => __('merchant/products.product.variations___grocery_price_numeric'), 

            'variations.*.stock.required_if'            => __('merchant/settings.key.stock_transfer_key_required'),
            'variations.*.stock.numeric'                => __('merchant/products.product.variations___stock_numeric'),


            'variations.*.unit.required'                => __('merchant/products.product.variations___unit_required'), 
            'variations.*.tax.required_if'              => __('merchant/products.product.variations___tax_required_if'),
            'variations.*.tax.numeric'                  => __('merchant/products.product.variations___tax_numeric'),


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
