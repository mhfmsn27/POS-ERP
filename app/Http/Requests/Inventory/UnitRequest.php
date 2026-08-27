<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UnitRequest extends FormRequest
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
            'name'                                      => 'required',
            'is_root_parent'                            => 'required|boolean',
            'parent.id'                                 => 'required_if:is_root_parent,true',
            'value'                                     => 'required_if:is_root_parent,true',
            'code'                                      => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required'                             => __('merchant/products.unit.name_required'),
            'code.required'                             => __('merchant/products.unit.code_required'),
            'is_root_parent.required'                   => __('merchant/products.unit.is_root_parent_required'),
            'parent.id.required_if'                     => __('merchant/products.unit.parent_id_required_if'),
            'value.required_if'                         => __('merchant/products.unit.value_required_id')
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
