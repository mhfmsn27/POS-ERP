<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PrinterController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SettingsHrmController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\TaxrateController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Api\Master\CourierController;
use App\Http\Controllers\Api\Rma\RmaController;
use App\Http\Controllers\Api\Transaction\PurchaseController;
use App\Http\Controllers\Api\Transaction\PurchasePaymentController;
use App\Http\Controllers\Api\Transaction\SalesController;
use App\Http\Controllers\Auth\PackageController;
use App\Http\Controllers\Auth\PackageTransactionController;
use App\Http\Controllers\Hrm\AttendanceController;
use App\Http\Controllers\Hrm\DepartmentController;
use App\Http\Controllers\Hrm\DesignationController;
use App\Http\Controllers\Hrm\EmployeeController;
use App\Http\Controllers\Notification\DeviceController;
use App\Http\Controllers\Notification\TemplateController;
use App\Http\Controllers\Pos\ShiftRegisterController;
use App\Http\Controllers\Salary\AllowanceController;
use App\Http\Controllers\Salary\CuttingSalaryController;
use App\Http\Controllers\Transaction\SellController; 
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'is_merchant'])->group(function () {

   
    // Store Route
    Route::prefix('store')->group(function () {
        Route::get('choose-store', [StoreController::class, 'index'])->name('store.choose');
        Route::get('choose/{store}', [StoreController::class, 'choose'])->name('choose.store');
        Route::get('create', [StoreController::class, 'create'])->name('store.create');
        Route::post("create-data", [StoreController::class, 'createData'])->name('store.add');
        Route::middleware('store')->group(function () {
            Route::post('store/{any}', [StoreController::class, 'store'])->name('store.store');
            Route::get('update', [StoreController::class, 'update'])->name('store.update');
            Route::get('delete/{store}', [StoreController::class, 'delete'])->name('store.delete');
        });

        Route::prefix('transactions')->group(function () {
            Route::get('order/{store}', [PackageTransactionController::class, 'order'])->name('store.order');
        });
    });

    Route::prefix('packages')->group(function () {
        Route::get('/', [PackageController::class, 'index'])->name('choose.package');
    });

    Route::prefix('transaction-package')->group(function () {
        Route::get('/', [PackageTransactionController::class, 'index'])->name('package.order');
        Route::post('store/{store}', [PackageTransactionController::class, 'transaction'])->name('package.order.store');
        Route::post('add-payment/{transaction}', [PackageTransactionController::class, 'addPayment']);
        Route::get('delete/{transaction}', [PackageTransactionController::class, 'deleteTransaction'])->name('package.order.delete');
    });

    Route::prefix('notifications')->group(function () {
        Route::prefix('device')->group(function () {
            Route::get('/', [DeviceController::class, 'index'])->name('device');
            Route::get('create', [DeviceController::class, 'create'])->name('device.create');
            Route::get('update/{device}', [DeviceController::class, 'update'])->name('device.update');
            Route::get('delete/{device}', [DeviceController::class, 'delete'])->name('device.delete');
            Route::post('store', [DeviceController::class, 'store'])->name('device.store');
            Route::post('edit/{device}', [DeviceController::class, 'edit'])->name('device.edit');
        });

        Route::prefix('template')->group(function () {
            Route::get('/', [TemplateController::class, 'index'])->name('template');
            Route::get('create', [TemplateController::class, 'create'])->name('template.create');
            Route::get('update/{template}', [TemplateController::class, 'update'])->name('template.update');
            Route::get('delete/{template}', [TemplateController::class, 'delete'])->name('template.delete');
            Route::post('store', [TemplateController::class, 'store'])->name('template.store');
            Route::post('edit/{template}', [TemplateController::class, 'edit'])->name('template.edit');
        });
    });

    /**
     *  Shift Register Route
     */
    Route::prefix('shift-register')->middleware('store')->group(function () {
        Route::get("today-transaction", [ShiftRegisterController::class, 'getTransaction']);
        Route::get('today-payment', [ShiftRegisterController::class, 'getPayment']);
        Route::get('top-product', [ShiftRegisterController::class, 'topProduct']);
        Route::get('close-register', [ShiftRegisterController::class, 'closeRegister'])->name('register.close');
        Route::get('close-in-reports/{id}', [ShiftRegisterController::class, 'close'])->name('close_in_reports');
        Route::get('detail/{id}', [ShiftRegisterController::class, 'detail'])->name('shift.detail');
        Route::get('print/{id}', [ShiftRegisterController::class, 'print'])->name('shift.print');
    });

    Route::middleware(['store'])->group(function () {

        Route::middleware('package_active')->group(function () {

            /**
             *  Preferensi Sistem
             */

            Route::prefix('preferensi')->group(function () {

                // General Settings
                Route::get('settings', [SettingController::class, 'index'])->name('sett.index');
                Route::post("key-store", [SettingController::class, 'keySetting']);
                Route::post("hrm-store", [SettingsHrmController::class, 'store'])->name('hrm.store');
                Route::post('notification',[SettingController::class,'store'])->name('notification.store');

                // Role
                Route::prefix('roles')->group(function () {
                    Route::get('/', [RoleController::class, 'index'])->name('role.index');
                    Route::get('create', [RoleController::class, 'create'])->name('role.create');
                    Route::get('update/{role}', [RoleController::class, 'update'])->name('role.update');
                    Route::post('store/{any}', [RoleController::class, 'store'])->name('role.store');
                    Route::get("delete/{role}", [RoleController::class, 'delete'])->name("role.delete");
                    Route::get('role-permission-delete/{id}/{role}', [RoleController::class, 'deletePermission']);
                });

                // Users
                Route::prefix('users')->group(function () {
                    Route::get('/', [UsersController::class, 'index'])->name('user.index');
                    Route::get('create', [UsersController::class, 'create'])->name('user.create');
                    Route::get('update/{users}', [UsersController::class, 'update'])->name('user.update');
                    Route::get('delete/{user}', [UsersController::class, 'delete'])->name('user.delete');
                    Route::post('user-store', [UsersController::class, 'store'])->name('user.store');
                    Route::post('user-update', [UsersController::class, 'edit'])->name('user.edit');
                });

                // Default Accountant
                Route::get('account-default/{vue?}', function () {
                    return view('vue', ["page" => "POSHUB - Pengaturan Akun Default"]);
                })->where('vue', '^(?!setup|update|password).*$')->name("account.default");
            });


            /**
             *  Company Module
             */

            Route::prefix('company')->group(function () {

                // Master Data
                Route::get('master-data/{vue?}', function () {
                    return view('vue', ["page" => "POSHUB - Module Master Data"]);
                })->where('vue', '^(?!setup|update|password).*$')->name("master_data.module");

                // Courier
                Route::prefix('couriers')->group(function () {
                    Route::get('/', [CourierController::class, 'index'])->name('courier.index');
                    Route::get('create', [CourierController::class, 'create'])->name('courier.create');
                    Route::get('update/{courier}', [CourierController::class, 'update'])->name('courier.update');
                    Route::get('delete/{courier}', [CourierController::class, 'delete'])->name('courier.delete');
                    Route::post('store', [CourierController::class, 'store'])->name('courier.store');
                    Route::post('edit/{courier}', [CourierController::class, 'edit'])->name('courier.edit');
                });

                // Printer Crud
                Route::prefix('printers')->group(function () {
                    Route::get('/', [PrinterController::class, 'index'])->name('printer.index');
                    Route::get('create', [PrinterController::class, 'create'])->name('printer.create');
                    Route::get('update/{printer}', [PrinterController::class, 'update'])->name('printer.update');
                    Route::get('delete/{printer}', [PrinterController::class, 'delete'])->name('printer.delete');
                    Route::post('store/{any}', [PrinterController::class, 'store'])->name('printer.store');
                });

                // Taxrate Crud
                Route::prefix('taxrates')->group(function () {
                    Route::get('/', [TaxrateController::class, 'index'])->name('taxrate.index');
                    Route::get('create', [TaxrateController::class, 'create'])->name('taxrate.create');
                    Route::get('update/{taxrate}', [TaxrateController::class, 'update'])->name('taxrate.update');
                    Route::get('delete/{taxrate}', [TaxrateController::class, 'delete'])->name('taxrate.delete');
                    Route::post('store/{any}', [TaxrateController::class, 'store'])->name('taxrate.store');
                });

                // department
                Route::prefix('departments')->group(function () {
                    Route::get('/', [DepartmentController::class, 'index'])->name('department.index');
                    Route::get('delete/{department}', [DepartmentController::class, 'delete'])->name('department.delete');
                    Route::post('store/{any}', [DepartmentController::class, 'store'])->name('department.store');
                });

                // designation
                Route::prefix('designations')->group(function () {
                    Route::get('/', [DesignationController::class, 'index'])->name('designation.index');
                    Route::get('create', [DesignationController::class, 'create'])->name('designation.create');
                    Route::get('update/{designation}', [DesignationController::class, 'update'])->name('designation.update');
                    Route::get('delete/{designation}', [DesignationController::class, 'delete'])->name('designation.delete');
                    Route::post('store/{any}', [DesignationController::class, 'store'])->name('designation.store');
                });

                // Employees
                Route::prefix('employees')->group(function () {
                    Route::get('/', [EmployeeController::class, 'index'])->name('employee.index');
                    Route::get('create', [EmployeeController::class, 'create'])->name('employee.create');
                    Route::get('update/{employee}', [EmployeeController::class, 'update'])->name('employee.update');
                    Route::get('delete/{employee}', [EmployeeController::class, 'delete'])->name('employee.delete');
                    Route::get('kasbon-history/{employee}', [EmployeeController::class, 'history'])->name('employee.history');
                    Route::post('store/{any}', [EmployeeController::class, 'store'])->name('employee.store');
                });
            });

            /**
             *  Buku Besar Module
             */

            Route::prefix('buku-besar')->group(function () {

                // Accounting
                Route::get('accounting/{vue?}', function () {
                    return view('vue', ["page" => "POSHUB - Module Akuntansi"]);
                })->where('vue', '^(?!setup|update|password).*$')->name("accounting.module");

                // Jurnal
                Route::get('jurnal/{vue?}', function () {
                    return view('vue', ["page" => "POSHUB - Jurnal Umum"]);
                })->where('vue', '^(?!setup|update|password).*$')->name("jurnal.module");


                // Allowance Route
                Route::prefix('allowances')->group(function () {
                    Route::get('/', [AllowanceController::class, 'index'])->name('allowance.index');
                    Route::get('create', [AllowanceController::class, 'create'])->name('allowance.create');
                    Route::get('update/{allowance}', [AllowanceController::class, 'update'])->name('allowance.update');
                    Route::get('delete/{allowance}', [AllowanceController::class, 'delete'])->name('allowance.delete');
                    Route::post('store/{any}', [AllowanceController::class, 'store'])->name('allowance.store');
                    Route::get('get-designation/{department}', [AllowanceController::class, 'getDesignation']);
                });

                // Cutting / Deduction
                Route::prefix('cutting')->group(function () {
                    Route::get('/', [CuttingSalaryController::class, 'index'])->name('cutting.index');
                    Route::get('create', [CuttingSalaryController::class, 'create'])->name('cutting.create');
                    Route::get('update/{cutting}', [CuttingSalaryController::class, 'update'])->name('cutting.update');
                    Route::get('delete/{cutting}', [CuttingSalaryController::class, 'delete'])->name('cutting.delete');
                    Route::post('store/{any}', [CuttingSalaryController::class, 'store'])->name('cutting.store');
                });

                // Gaji dan kasbon pegawai
                Route::get('salaries/{vue?}', function () {
                    return view('vue', ["page" => "POSHUB - Gaji dan Kasbon Pegawai"]);
                })->where('vue', '^(?!setup|update|password).*$')->name("hrm.module");
            });


            /**
             *  Kas dan Bank
             */

            Route::get('cash-bank/{vue?}', function () {
                return view('vue', ["page" => "POSHUB - Kas dan Bank"]);
            })->where('vue', '^(?!setup|update|password).*$')->name("cash_bank.module");


            /**
             *  Sales Route
             */

            Route::get('sales/{vue?}', function () {
                return view('vue', ["page" => "POSHUB - Module Penjualan"]);
            })->where('vue', '^(?!setup|update|password).*$')->name("sales.module");


            /**
             *  Rma Route
             */

            Route::get('rma/{vue?}', function () {
                return view('vue', ["page" => "POSHUB - Module Rma"]);
            })->where('vue', '^(?!setup|update|password).*$')->name("rma.module");


            /**
             *  Purchase Route
             */

            Route::get('purchase/{vue?}', function () {
                return view('vue', ["page" => "POSHUB - Module Pembelian"]);
            })->where('vue', '^(?!setup|update|password).*$')->name("purchase.module");


            /**
             *  Inventori Route
             */

            Route::get('inventori/{vue?}', function () {
                return view('vue', ["page" => "POSHUB - Module Master Data"]);
            })->where('vue', '^(?!setup|update|password).*$')->name("inventori.module");


            /**
             *  Taxrate
             */
            Route::get('reports/{vue?}', function () {
                return view('vue', ["page" => "POSHUB - Laporan"]);
            })->where('vue', '^(?!setup|update|password).*$')->name("reports.module");


            

            Route::get('/home', [AdminController::class, 'index'])->name('index');

            Route::prefix('auth')->group(function () {
                Route::get("profile", [AdminController::class, 'myProfile'])->name('profile');
                Route::post("change-profile", [AdminController::class, 'changeProfile'])->name('change.profile');
                Route::post("change-pass", [AdminController::class, 'changePassword'])->name('change.password');
            });

            Route::get('income-expense', [AdminController::class, 'incomeAndExpense']);
            Route::get("transaction-data", [AdminController::class, 'transactionData']);
            Route::get('sell-month', [AdminController::class, 'sellmonth']);
            Route::get('five-top-product', [AdminController::class, 'topProduct']);

            Route::prefix('hrm')->group(function () {
                Route::get('checkint', [AttendanceController::class, 'checkint'])->name('attendance.check');
                Route::get('checkout', [AttendanceController::class, 'checkout'])->name('attendance.checkout');
                Route::get('get-designation/{department}', [AllowanceController::class, 'getDesignation']);
            });


            Route::prefix('attendance')->group(function () {
                Route::get('today', [AttendanceController::class, 'today'])->name('attendance.today');
            });

            /**
             *  Sell Route
             */
            Route::prefix('sell')->group(function () {
                Route::get('detail/{id}', [SellController::class, 'detail'])->name('sell.detail');
                Route::get('print/{id}', [SellController::class, 'print'])->name('sell.print');
            });

            /**
             *  Taxes
             */

            Route::get('taxes/{vue?}', function () {
                return view('vue', ["page" => "POSHUB - Smartlink Tax"]);
            })->where('vue', '^(?!setup|update|password).*$')->name("taxes.module");
        });
    });

    Route::prefix('prints')->group(function () {
        Route::get('rma/{transaction}', [RmaController::class, 'print']);
        Route::get('faktur-pengiriman/{transaction}', [SalesController::class, 'printPengiriman']);
        Route::get('faktur-penjualan/{transaction}', [SalesController::class, 'print']);
        Route::get('label-pengiriman/{transaction}', [SalesController::class, 'label']);
        Route::get('pembayaran-pembelian/{transaction}', [PurchasePaymentController::class, 'print']);
        Route::get('penerimaan/{transaction}', [PurchaseController::class, 'penerimaanprint']);
    });
});
