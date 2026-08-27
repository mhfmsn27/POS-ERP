<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\BusinessRequest;
use App\Models\Account\Account;
use App\Models\Admin\KeySetting;
use App\Models\Admin\Setting;
use App\Models\Admin\SettingsHrm;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Observers\Account\TaxObserver;
use App\Observers\CashIntOut\CategoryObserver;
use App\Observers\Hrm\DepartmentObserver;
use App\Observers\Inventory\UnitObserver;
use App\Observers\Master\PaymentMethodObserver;
use App\Observers\Master\PrinterObserver;
use App\Observers\Master\TermPaymentObserver;
use App\Observers\Saas\MerchantObserver;
use App\Observers\Saas\StoreObserver;
use App\Observers\Setting\SettingObserver;
use Illuminate\Support\Facades\DB;

class BusinessRegisterController extends Controller
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
    protected $merchantObserver;
    public function __construct(MerchantObserver $merchantObserver, StoreObserver $storeObserver, PrinterObserver $printerObserver, SettingObserver $settingObserver, CategoryObserver $categoryObserver, TermPaymentObserver $termPaymentObserver, DepartmentObserver $departmentObserver, UnitObserver $unitObserver, PaymentMethodObserver $paymentMethodObserver, TaxObserver $taxObserver)
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
        $this->merchantObserver         = $merchantObserver;
    }

    public function index()
    {
        return view('auth.business_register', ['page' => 'Registrasi Bisnis dan Toko']);
    }

    public function businessCreate(BusinessRequest $request)
    {
        try {

            DB::beginTransaction();

            $user           = User::where("id", auth()->user()->id)->first(['id', 'name', 'merchant_id', 'store_id', 'role']);
            $merchant       = $this->merchantObserver->createData($request, $user);

            $user->update([
                'merchant_id'   => $merchant->id,
                'store_id'      => 0
            ]);

            $store = $this->storeObserver->createData($request, $merchant->id);


            $roleAccess = Role::create([
                'name'          => 'Super Admin - ' . strtolower(preg_replace("/[^0-9a-zA-Z]/", "-", $request->name)),
                'guard_name'    => 'web',
                'merchant_id'   => $merchant->id
            ]);

            $roleAccess->give_model_has_role_data($user->id);

            foreach (Permission::all() as $p) {
                $roleAccess->give_permission_data($p->id);
            }

            $user->update([
                'role'          => $roleAccess->id,
            ]);

            Setting::create([
                'name'                          => 'POSHUB ACCOUNTING',
                'logo'                          => 'uploads/logo.webp',
                'default_email'                 => 'admin@poshub.id',
                'default_phone'                 => '628123456789',
                'merchant_id'                   => $merchant->id
            ]);

            KeySetting::create([
                'purchase_key'          => 'PO',
                'purchase_return_key'   => 'PO_RTN',
                'sell_key'              => 'SL',
                'sell_return_key'       => 'SL_RTN',
                'adjustment_key'        => 'AS',
                'stock_transfer_key'    => 'ST',
                'expense_key'           => 'EP',
                'purchase_payment_key'  => 'PO_PAY',
                'sell_payment_key'      => 'SL_PAY',
                'expense_payment_key'   => 'EP_PAY',
                'merchant_id'           => $merchant->id
            ]);

            SettingsHrm::create([
                'min_check_int'     => '06:00',
                'max_check_int'     => '08:00',
                'min_check_out'     => '20:00',
                'attendance_in_late'    => 'yes',
                'attendance_to_salary'  => 'no',
                'attendance_to_cutting' => 'no',
                'salary_tax'            => '0',
                'merchant_id'       => $merchant->id
            ]);

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

            $category = $this->categoryObserver->createDefault($store);
            if (!$category) {
                throw new \Exception("Failed to create Category data");
            }

            $termPayemnt = $this->termPaymentObserver->createDefault($store);
            if (!$termPayemnt) {
                throw new \Exception("Failed to create payment data");
            }

            if ($store->accountant_use == 'yes') {
                $method = $this->paymentMethodObserver->createAutomatic(Account::where("coa", "110101")->first());
                if (!$method) {
                    throw new \Exception("Failed to create method data");
                }
    
                $method2 = $this->paymentMethodObserver->createAutomatic(Account::where("coa", "110102")->first());
                if (!$method2) {
                    throw new \Exception("Failed to create method 2 data");
                }
            }

            
 
            if ($store->tax_option == 'active') {
                $this->taxObserver->createDefault($store,$merchant->id);
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

            return redirect()->route('store.choose');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'file'      => $e->getFile(),
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 409);
        }
    }
}
