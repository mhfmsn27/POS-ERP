<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class RakRequest extends FormRequest
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
            'rak'                               => 'required',
            'floor'                             => 'required',
            'room'                              => 'required',

        ];
    }

    public function messages()
    {
        return [
            'rak.required'                      => __('merchant/products.rak.rak_required'),
            'floor.required'                    => __('merchant/products.rak.floor_required'),
            'room.required'                     => __('merchant/products.rak.room_required'), 
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
