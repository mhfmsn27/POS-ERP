<?php

namespace App\Http\Controllers\Api\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\BusinessRequest;
use App\Http\Resources\Merchant\Store\StoreListResource;
use App\Models\Account\Account;
use App\Models\Admin\Printer;
use App\Models\Admin\Store;
use App\Observers\Account\TaxObserver;
use App\Observers\CashIntOut\CategoryObserver;
use App\Observers\Hrm\DepartmentObserver;
use App\Observers\Inventory\UnitObserver;
use App\Observers\Master\PaymentMethodObserver;
use App\Observers\Master\PrinterObserver;
use App\Observers\Master\TermPaymentObserver;
use App\Observers\Notification\NotificationObserver;
use App\Observers\Saas\StoreObserver;
use App\Observers\Setting\SettingObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class StoreController extends Controller
{
    protected $storeObserver;
    protected $printerObserver;
    protected $settingObserver;
    protected $categoryObserver;
    protected $termPaymentObserver;
    protected $departmentObserver;
    protected $unitObservser;
    protected $paymentMethodObserver;
    protected $taxObserver;
    protected $notificationObserver;
    public function __construct(NotificationObserver $notificationObserver, StoreObserver $storeObserver, PrinterObserver $printerObserver, SettingObserver $settingObserver, CategoryObserver $categoryObserver, TermPaymentObserver $termPaymentObserver, DepartmentObserver $departmentObserver, UnitObserver $unitObserver, PaymentMethodObserver $paymentMethodObserver, TaxObserver $taxObserver)
    {
        $this->storeObserver            = $storeObserver;
        $this->printerObserver          = $printerObserver;
        $this->settingObserver          = $settingObserver;
        $this->categoryObserver         = $categoryObserver;
        $this->termPaymentObserver      = $termPaymentObserver;
        $this->departmentObserver       = $departmentObserver;
        $this->unitObservser            = $unitObserver;
        $this->paymentMethodObserver    = $paymentMethodObserver;
        $this->taxObserver              = $taxObserver;
        $this->notificationObserver     = $notificationObserver;
    }

    public function index(Request $request)
    {
        $stores         = $this->storeObserver->getData($request)->where(function ($q) {
            return auth()->user()->store_id != '0' ? $q->whereIn('id', explode(",", auth()->user()->store_id)) : '';
        })->get(['id', 'name', 'address', 'phone', 'email']);


        return response()->json([
            'stores'  => StoreListResource::collection($stores),
        ], 200);
    }

    public function detail()
    {
        $stores         = Store::find(my_store());
        $printers       = Printer::orderBy('name', 'asc')->get(['id', 'name']);

        return response()->json([
            'store'         => array(
                'printer_id'        => $stores->printer_id,
                'name'              => $stores->name,
                'email'             => $stores->email,
                'phone'             => $stores->phone,
                'zip_code'          => $stores->zip_code,
                'tax_option'        => $stores->tax_option,
                'tax_one'           => (float)$stores->tax_one,
                'tax_two'           => (float)$stores->tax_two,
                'tax_tree'          => (float)$stores->tax_tree,
                'warehouse_default_id'  => $stores->warehouse_default_id,
                'shift_register'    => $stores->shift_register,
                'accountant_use'    => $stores->accountant_use,
                'address'           => $stores->address,
                'footer_text'       => $stores->footer_text
            ),
            'printers'      => $printers
        ], 200);
    }

    public function createStore(BusinessRequest $request)
    {

        try {

            DB::beginTransaction();

            $store = $this->storeObserver->createData($request);
            if (!$store) {
                throw new \Exception("Failed to create store data");
            }

            $printer = $this->printerObserver->createDefault($store);
            if (!$printer) {
                throw new \Exception("Failed to create printer data");
            }

            $department = $this->departmentObserver->createDefault($store);
            if (!$department) {
                throw new \Exception("Failed to create department data");
            }

            $designation = $this->departmentObserver->createDesignationDefault($department);
            if (!$designation) {
                throw new \Exception("Failed to create designation data");
            }

            $hrmSetting = $this->settingObserver->createHrmDefault($store);
            if (!$hrmSetting) {
                throw new \Exception("Failed to create Hrm Setting data");
            }

            $category = $this->categoryObserver->createDefault($store);
            if (!$category) {
                throw new \Exception("Failed to create Category data");
            }

            $termPayemnt = $this->termPaymentObserver->createDefault($store);
            if (!$termPayemnt) {
                throw new \Exception("Failed to create payment data");
            }

            $method = $this->paymentMethodObserver->createAutomatic(Account::where("coa", "110101")->first());
            if (!$method) {
                throw new \Exception("Failed to create method data");
            }

            $method2 = $this->paymentMethodObserver->createAutomatic(Account::where("coa", "110102")->first());
            if (!$method2) {
                throw new \Exception("Failed to create method 2 data");
            }

            if ($store->tax_option == 'active') {
                $this->taxObserver->createDefault($store);
                $store->update([
                    'tax'       => 11,
                    'tax_one'   => 11,
                    'tax_two'   => 2,
                    'tax_tree'  => 2.5
                ]);
            }

            $store->update([
                'printer_id'        => $printer->id,
            ]);


            DB::commit();

            $templates  = $this->notificationObserver->getTemplate('store_tempate');

            if ($templates) {
                $message = str_replace(
                    ['{name}', '{business_name}', '{storename}'],
                    [($store->merchant->owner->name ?? ''), ($store->merchant->name ?? ''), $store->name],
                    $templates->message
                );

                $this->notificationObserver->sendMessage($message);
            }

            return response()->json([
                'status'    => true,
                'message'   => 'Toko atau Cabang berhasil di tambahkan',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'    => true,
                'message'   => $e->getMessage(),
            ], 422);
        }
    }

    public function update(Request $request)
    {

        abort_if(Gate::denies('store_sett'), 403);

        $store = Store::find(my_store());
        $store = $this->storeObserver->updateData($request, $store, '');

        return response()->json([
            'status'    => true,
            'message'   => 'Toko atau Cabang berhasil di simpan',
        ], 200);
    }

    public function delete(Request $request)
    {
        abort_if(Gate::denies('store_sett'), 403);

        $store  = Store::find(my_store());

        if ($store->two_factor_code) {
            if ($store->two_factor_expires_at->lt(now())) {

                $store->resetTwoFactorCode();

                return response()->json([
                    'status' => false,
                    'message' => 'Kode dua faktor telah kedaluwarsa. Silakan Coba kembali.',
                ], 419);
            }
        }

        if ($store->two_factor_code != $request->code) {
            return response()->json([
                'status' => false,
                'message' => 'Kode dua faktor Salah.',
            ], 419);
        }


        $store->delete();


        return response()->json([
            'status'    => true,
            'message'   => 'Toko atau Cabang berhasil di hapus',
        ], 200);
    }

    public function sendOtp()
    {
        $stores     = Store::find(my_store());

        if ($stores) {
            $stores->generateTwoFactorCode();

            $templates  = $this->notificationObserver->getTemplate('delete_store_template');

            if ($templates) {
                $message = str_replace(
                    ['{name}', '{code}', '{expire_date}'],
                    [($stores->merchant->owner->name ?? ''), ($stores->two_factor_code ?? ''), $stores->two_factor_expires_at],
                    $templates->message
                );

                $this->notificationObserver->sendMessage($message, $stores->merchant->owner->phone ?? '');
            }

            return response()->json([
                'status'    => true,
                'message'   => 'Otp berhasil kami kirimkan',
            ], 200);
        }
    }
}
