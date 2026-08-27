<?php

namespace App;

use App\Models\Account\AccountTransaction;
use App\Models\Admin\KeySetting;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;

class Helper
{
    public static function getFileExtension(string $decodedB64)
    {
        $fileInfo = finfo_buffer($finfo = finfo_open(), $decodedB64, FILEINFO_MIME_TYPE);
        finfo_close($finfo);
        return substr($fileInfo, strpos($fileInfo, '/') + 1);
    }

    public static function generateResizedImage($image)
    {
        $image = \Intervention\Image\ImageManagerStatic::make($image);
        $image->backup();

        $image->fit(150, 150);
        $imageSmall = (string) $image->encode();
        $image->reset();

        $image->fit(300, 300);
        $imageMedium = (string) $image->encode();
        $image->reset();

        $result = [
            'small' => $imageSmall,
            'medium' => $imageMedium,
            'normal' => $image,
        ];

        return $result;
    }

    public static function fresh_aprice($price)
    {
        return strtolower(preg_replace("/[^0-9\.]/", "", $price));
    }

    public static function transactionKey($key, $invoice)
    {
        $keySetting = KeySetting::first();

        $invoiceRef = $key;

        if ($key == 'PO') {
            $keySetting == null ? $invoiceRef = 'PM' : $invoiceRef = $keySetting->purchase_key;
        }

        if ($key == 'PO_RTN') {
            $keySetting == null ? $invoiceRef = 'PM_RTN' : $invoiceRef = $keySetting->purchase_return_key;
        }

        if ($key == 'EP') {
            $keySetting == null ? $invoiceRef = 'EP' : $invoiceRef = $keySetting->expense_key;
        }

        if ($key == 'SL') {
            $keySetting == null ? $invoiceRef = 'SL' : $invoiceRef = $keySetting->sell_key;
        }

        if ($key == 'SL_RTN') {
            $keySetting == null ? $invoiceRef = 'SL_RTN' : $invoiceRef = $keySetting->sell_return_key;
        }

        if ($key == 'AS') {
            $keySetting == null ? $invoiceRef = 'AS' : $invoiceRef = $keySetting->adjustment_key;
        }

        if ($key == 'ST') {
            $keySetting == null ? $invoiceRef = 'ST' : $invoiceRef = $keySetting->stock_transfer_key;
        }

        if ($key == 'PT') {
            $invoiceRef = 'ST';
        }


        $invoiceNumber =  $invoiceRef . '' . date("Ymd") . '/' . $invoice;
        return $invoiceNumber;
    }

    public static function accountKey($key, $invoice)
    {

 
        $invoiceRef = 'OT';
        if ($key == 'deposit') {
            $invoiceRef = 'DP';
        }

        if ($key == 'deposit_equitas') {
            $invoiceRef = 'DE';
        }

        if ($key == 'transfer_dana') {
            $invoiceRef = 'TD';
        }

        if ($key == 'received_dana') {
            $invoiceRef = 'RD';
        }

        if ($key == 'first_stock' || $key == 'deposit_stock_product') {
            $invoiceRef = 'FS';
        }

        if ($key == 'due_supplier') {
            $invoiceRef = 'DS';
        }

        if ($key == 'pay_supplier_faktur') {
            $invoiceRef = 'PAYSF';
        }

        if ($key == 'received_product_from_supplier') {
            $invoiceRef = 'RCP';
        }

        if ($key == 'saldo_supplier') {
            $invoiceRef = 'SS';
        }

        if ($key == 'saldo_customer') {
            $invoiceRef = 'SC';
        }

        if ($key == 'add_stock') {
            $invoiceRef = 'SO-ADD';
        }

        if ($key == 'min_stock') {
            $invoiceRef = 'SO-MIN';
        }

        if ($key == 'received_to_faktur') {
            $invoiceRef = 'RTF';
        }

        if ($key == 'saldo_supplier') {
            $invoiceRef = 'SS';
        }

        if ($key == 'return_purchase') {
            $invoiceRef = 'RP';
        }

        if($key == 'sent_product_to_customer') {
            $invoiceRef = 'SENTP';
        }

        if($key == 'due_customer') {
            $invoiceRef = 'DC';
        }

        if($key == 'sale_faktur') {
            $invoiceRef = 'SF';
        }

        if($key == 'pay_customer_faktur') {
            $invoiceRef = 'PAYCS';
        }

        if($key == 'return_sell') {
            $invoiceRef = 'RS';
        }

        if($key == 'expense') {
            $invoiceRef = 'EX';
        }

        if($key == 'cash_int') {
            $invoiceRef = 'CI';
        }

        if($key == 'kasbon') {
            $invoiceRef = 'KA';
        }

        if($key == 'salary') {
            $invoiceRef = 'SR';
        }

        if($key == 'pay_salary') {
            $invoiceRef = 'PS';
        }

        if($key == 'sell_discount') {
            $invoiceRef = 'SD';
        }

        if($key == 'commission') {
            $invoiceRef = 'CP';
        }

        if($key == 'tax_input') {
            $invoiceRef = 'TAXINT';
        }

        if($key == 'tax_output') {
            $invoiceRef = 'TAXOUT';
        }

        if($key == 'goverment_tax') {
            $invoiceRef = 'GTAX';
        }

        if($key == 'service_tax') {
            $invoiceRef = 'STAX';
        }

        if($key == 'spt_tax') {
            $invoiceRef = 'SPT';
        }
        

        $invoiceNumber =  $invoiceRef . '' . date("Ymd") . '/' . $invoice;
        return $invoiceNumber;
    }

    public static function createAccount($type, $request, $payment)
    {
        $data = new AccountTransaction();
        $data->account_id = $request->account_id;
        $data->created_by = Auth::user()->id;
        $data->type = $type;
        $data->amount = $payment->amount;
        $data->operation_date = $payment->created_at;
        if ($payment->transaction_type == 'transaction') {
            $data->sub_type = $payment->transaction->type ?? '';
        } else {
            $data->sub_type = 'expense';
        }

        $data->transaction_id = $payment->transaction_id;
        $data->transaction_payment_id = $payment->id;
        $data->save();

        $payment->account_id = $data->id;
        $payment->save();
    }

    public static function setTimeZoneLocal($date)
    {
        $tz     = new DateTimeZone('Asia/Jakarta');
        $date   = new DateTime($date);
        $date->setTimezone($tz); 
        return $date->format("Y-m-d");
    }
}
