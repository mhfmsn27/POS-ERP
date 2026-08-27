<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'detail'                                    => 'nullable', 
        ];
    }

    public function messages()
    {
        return [
            'name.required'                             => __('merchant/products.category.name_required'),
            'is_root_parent.required'                   => __('merchant/products.category.is_root_parent_required'),
            'parent.id.required_if'                     => __('merchant/products.category.parent_id_required_if'),
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
