<?php

use Illuminate\Support\Facades\Route;

Route::prefix('app')->middleware(['auth', 'store'])->group(function () {

    // Accounting Route
    include('modules/account.php');

    // Master Data Route
    include('modules/master.php');

    // Master Data Inventori
    include('modules/inventori.php');

    // CRM
    include('modules/crm.php');

    // HRM
    include('modules/hrm.php');

    // Transactions
    include('modules/transactions.php');

    // Reports
    include('modules/reports.php');

    // Settings
    include('modules/settings.php');

    // Taxes
    include('modules/taxes.php');

    // Enterprise Features (Fiscal Periods, In-Transit Transfers, Loyalty Points, Fraud Audit)
    include('modules/enterprise.php');
});
