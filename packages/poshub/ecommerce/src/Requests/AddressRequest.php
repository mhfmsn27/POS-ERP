<?php

namespace Poshub\Ecommerce\Requests;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'name'                                      => 'required|string|max:200',
            'sub_district_id'                           => 'required|numeric|min:1',
            'address'                                   => 'required|string|min:20',
            'postal_code'                               => 'required',
            'phone'                                     => 'required',
            'default'                                   => 'required|in:yes,no', 
        ];
    }

    public function messages()
    {
        return [
            'name.required'                             => 'Nama Harus Diisi',
            'name.max'                                  => 'Inputan Nama Tidak boleh lebih dari 200 Karakter',

            'sub_district_id.required'                  => 'Kecamatan Harus Diisi',
            'address.required'                          => 'Alamat Lengkap Harus Diisi',
            'address.min'                               => 'Alamat harus diisi lebih dari 100 karakter',
            
            'postal_code.required'                      => 'Kode Pos Harus Diisi',
            'phone.required'                            => 'Nomor Ponsel harus diisi',  
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = new JsonResponse([
            'message' => $validator->errors()->first(),
            'status' => false,
        ], 200);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
