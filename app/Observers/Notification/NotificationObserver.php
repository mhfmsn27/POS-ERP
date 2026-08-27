<?php

namespace App\Observers\Notification;

use App\Models\Admin\NotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationObserver
{

    protected $deviceObserver;

    public function __construct(DeviceObserver $deviceObserver)
    {
        $this->deviceObserver       = $deviceObserver;
    }

    public function get()
    {
        return NotificationSetting::first();
    }

    public function create(Request $request)
    {
        return NotificationSetting::create([
            'type'          => $request->type,
            'user_register' => $request->register,
            'user_add'      => $request->add,
            'add_store'             => $request->add_store,
            'ecommerce_order'       => $request->ecommerce_order,
            'ecommerce_payment'     => $request->ecommerce_payment,
            'ecommerce_shipping'    => $request->ecommerce_shipping,
            'ecommerce_received'    => $request->ecommerce_received,
            'rma_add'               => $request->rma_add,
            'rma_progress'          => $request->rma_progress,
            'phone'                 => $request->phone,
            'package_buy'           => $request->package,
            'package_payment'       => $request->package_payment,
            'delete_store'          => $request->delete_store
        ]);
    }

    public function update(Request $request, NotificationSetting $setting)
    {
        $setting->update([
            'type'          => $request->type,
            'user_register' => $request->register,
            'user_add'      => $request->add,
            'add_store'             => $request->add_store,
            'ecommerce_order'       => $request->ecommerce_order,
            'ecommerce_payment'     => $request->ecommerce_payment,
            'ecommerce_shipping'    => $request->ecommerce_shipping,
            'ecommerce_received'    => $request->ecommerce_received,
            'rma_add'               => $request->rma_add,
            'rma_progress'          => $request->rma_progress,
            'phone'                 => $request->phone,
            'package_buy'           => $request->package,
            'package_payment'       => $request->package_payment,
            'delete_store'          => $request->delete_store
        ]);
    }

    public function getTemplate(String $type, $settings = null)
    {
        $settings   = $settings == null ? $this->get() : $settings;
        $general    = NotificationSetting::withoutGlobalScopes()->where('store_id', null)->first();

        if ($type == 'delete_store_template') {

            $template   = null;
            if (($settings != null ? $settings->type : 'general') == 'personal') {
                $template   = $settings->delete_store_template;
            }

            if (!$template) {
                $template   = $general->delete_store_template()->withoutGlobalScopes()->first();
            }

            return $template;
        }
        
        if ($type == 'registration_template') {

            $template   = null;
            if (($settings != null ? $settings->type : 'general') == 'personal') {
                $template   = $settings->registration_template;
            }

            if (!$template) {
                $template   = $general->registration_template()->withoutGlobalScopes()->first();
            }

            return $template;
        }

        if ($type == 'user_template') {

            $template   = null;
            if (($settings != null ? $settings->type : 'general') == 'personal') {
                $template   = $settings->user_template;
            }

            if (!$template) {
                $template   = $general->user_template()->withoutGlobalScopes()->first();
            }

            return $template;
        }

        if ($type == 'store_tempate') {

            $template   = null;
            if (($settings != null ? $settings->type : 'general') == 'personal') {
                $template   = $settings->store_tempate;
            }

            if (!$template) {
                $template   = $general->store_tempate()->withoutGlobalScopes()->first();
            }

            return $template;
        }

        if ($type == 'order_template') {

            $template   = null;
            if (($settings != null ? $settings->type : 'general') == 'personal') {
                $template   = $settings->order_template;
            }

            if (!$template) {
                $template   = $general->order_template()->withoutGlobalScopes()->first();
            }

            return $template;
        }

        if ($type == 'payment_template') {

            $template   = null;
            if (($settings != null ? $settings->type : 'general') == 'personal') {
                $template   = $settings->payment_template;
            }

            if (!$template) {
                $template   = $general->payment_template()->withoutGlobalScopes()->first();
            }

            return $template;
        }

        if ($type == 'shipping_template') {

            $template   = null;
            if (($settings != null ? $settings->type : 'general') == 'personal') {
                $template   = $settings->shipping_template;
            }

            if (!$template) {
                $template   = $general->shipping_template()->withoutGlobalScopes()->first();
            }

            return $template;
        }

        if ($type == 'received_template') {

            $template   = null;
            if (($settings != null ? $settings->type : 'general') == 'personal') {
                $template   = $settings->received_template;
            }

            if (!$template) {
                $template   = $general->received_template()->withoutGlobalScopes()->first();
            }

            return $template;
        }

        if ($type == 'rma_template') {

            $template   = null;
            if (($settings != null ? $settings->type : 'general') == 'personal') {
                $template   = $settings->rma_template;
            }

            if (!$template) {
                $template   = $general->rma_template()->withoutGlobalScopes()->first();
            }

            return $template;
        }

        if ($type == 'rma_process_template') {

            $template   = null;
            if (($settings != null ? $settings->type : 'general') == 'personal') {
                $template   = $settings->rma_process_template;
            }

            if (!$template) {
                $template   = $general->rma_process_template()->withoutGlobalScopes()->first();
            }

            return $template;
        }

        if ($type == 'package_template') {

            $template   = null;
            if (($settings != null ? $settings->type : 'general') == 'personal') {
                $template   = $settings->package_template;
            }

            if (!$template) {
                $template   = $general->package_template()->withoutGlobalScopes()->first();
            }

            return $template;
        }

        if ($type == 'payment_package_template') {

            $template   = null;
            if (($settings != null ? $settings->type : 'general') == 'personal') {
                $template   = $settings->payment_package_template;
            }

            if (!$template) {
                $template   = $general->payment_package_template()->withoutGlobalScopes()->first();
            }

            return $template;
        }
    }

    public function sendMessage($message, ?string $phone = '')
    {
        $settings = $this->get();

        if (($settings != null ? $settings->type : 'general') == 'general') {
            $settings = NotificationSetting::withoutGlobalScopes()->where('store_id', null)->first();
        }

        if ($settings && $phone != '-') {
            $device = $this->deviceObserver->getDevice($settings);
            if ($device && !empty($device->deviceid) && !empty($device->apikey)) {
                $targetPhone = ($phone == '' || $phone == null) ? ($settings->phone ?? '') : $phone;
                if (!empty($targetPhone) && $targetPhone != '-') {
                    \App\Jobs\SendWhatsappNotificationJob::dispatch(
                        (string)$message,
                        (string)$targetPhone,
                        (string)$device->deviceid,
                        (string)$device->apikey
                    );
                }
            }
        }
    }
}
