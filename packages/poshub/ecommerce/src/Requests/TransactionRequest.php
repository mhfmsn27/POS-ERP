<?php

namespace Poshub\Ecommerce\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
                  'details'                           => 'required|array|min:1',
                  'details.*.cart'                    => 'required|numeric|min:1',
                  'ongkir.code'                       => 'required',
                  'ongkir.service'                    => 'required',
                  'ongkir.from'                       => 'required|numeric|min:1'
            ];
      }

      public function messages()
      {
            return [
                  'details.min'                             => 'Tidak ada produk yang akan anda checkout',
                  'details.*.cart.required'                 => 'ID Keranjang produk tidak boleh kosong, jangan menghapusnya guys',

                  'ongkir.code.required'                    => 'Silahkan pilih layanan ongkos kirim terlebih dahulu',
                  'ongkir.service.required'                 => 'Silahkan pilih layanan ongkos kirim terlebih dahulu',
                  'ongkir.from'                             => 'Silahkan pilih alamat pengiriman terlebih dahulu', 
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
