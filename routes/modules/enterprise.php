<?php

use App\Http\Controllers\Api\Enterprise\EnterpriseFeaturesController;
use Illuminate\Support\Facades\Route;

Route::prefix('enterprise')->group(function () {
    // 1. Fiscal Accounting Periods
    Route::post('fiscal-periods/lock', [EnterpriseFeaturesController::class, 'updatePeriodStatus']);
    Route::post('fiscal-periods/year-end-close', [EnterpriseFeaturesController::class, 'closeFiscalYear']);

    // 2. Inter-Store Stock Transfers In-Transit
    Route::post('transfers/create', [EnterpriseFeaturesController::class, 'createTransfer']);
    Route::post('transfers/{id}/dispatch', [EnterpriseFeaturesController::class, 'dispatchTransfer']);
    Route::post('transfers/{id}/receive', [EnterpriseFeaturesController::class, 'receiveTransfer']);

    // 3. Customer Loyalty & VIP Memberships
    Route::get('loyalty/customer/{id}', [EnterpriseFeaturesController::class, 'getCustomerLoyalty']);
    Route::post('loyalty/redeem', [EnterpriseFeaturesController::class, 'redeemLoyaltyPoints']);

    // 4. Anti-Fraud & Forensic Security
    Route::get('security/anomalies', [EnterpriseFeaturesController::class, 'getSecurityAnomalies']);
    Route::post('security/log-drawer-kick', [EnterpriseFeaturesController::class, 'logDrawerKick']);

    // 5. Shift Register Z-Report & Audit Close
    Route::get('shifts/{id}/z-report', [\App\Http\Controllers\Pos\ShiftRegisterController::class, 'zReport']);
    Route::post('shifts/{id}/close', [\App\Http\Controllers\Pos\ShiftRegisterController::class, 'close']);

    // 6. Smart Inventory Forecasting & Reorder Predictor
    Route::get('forecasting/inventory', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'getInventoryForecast']);

    // 7. FEFO Batch & Expiry Management
    Route::post('batches/create', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'createBatch']);
    Route::get('batches/expiring', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'getExpiringBatches']);

    // 8. Consolidated Multi-Branch Financial Statements
    Route::get('financials/consolidated-income-statement', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'getConsolidatedIncomeStatement']);

    // 9. CRMHUB Omnichannel WhatsApp Receipts & Shift Alerts
    Route::post('receipts/send-digital', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'sendDigitalReceipt']);
    Route::post('receipts/send-z-report', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'sendShiftZReport']);

    // 10. Real-time Customer-Facing Dual Screen Display
    Route::post('customer-display/update', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'updateCustomerDisplay']);
    Route::get('customer-display/{token}', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'getCustomerDisplay']);

    // 11. Auto-Bank Reconciliation & Smart Parser
    Route::post('bank-recon/import', [\App\Http\Controllers\Api\Enterprise\StrategicEnterpriseController::class, 'importBankStatement']);
    Route::post('bank-recon/auto-match', [\App\Http\Controllers\Api\Enterprise\StrategicEnterpriseController::class, 'autoMatchBankStatement']);

    // 12. DJP E-Faktur Generator & Tax Validation
    Route::post('tax/export-efaktur', [\App\Http\Controllers\Api\Enterprise\StrategicEnterpriseController::class, 'exportEfaktur']);
    Route::post('tax/validate-npwp', [\App\Http\Controllers\Api\Enterprise\StrategicEnterpriseController::class, 'validateTaxNumber']);

    // 13. Dynamic QRIS POS Generator & Status
    Route::post('qris/generate', [\App\Http\Controllers\Api\Enterprise\StrategicEnterpriseController::class, 'generateQris']);
    Route::get('qris/status/{invoice}', [\App\Http\Controllers\Api\Enterprise\StrategicEnterpriseController::class, 'checkQrisStatus']);

    // 14. Kitchen Display System (KDS)
    Route::post('kds/tickets/create', [\App\Http\Controllers\Api\Enterprise\StrategicEnterpriseController::class, 'createKdsTicket']);
    Route::get('kds/tickets/active', [\App\Http\Controllers\Api\Enterprise\StrategicEnterpriseController::class, 'getActiveKdsTickets']);
    Route::post('kds/tickets/{id}/status', [\App\Http\Controllers\Api\Enterprise\StrategicEnterpriseController::class, 'updateKdsStatus']);

    // 15. AI Cash Flow Predictor 30/60/90 Days
    Route::get('cashflow/forecast', [\App\Http\Controllers\Api\Enterprise\StrategicEnterpriseController::class, 'getCashFlowForecast']);

    // 16. E-Commerce Flash Sales, Abandoned Carts, and Stock Reservations
    Route::post('ecommerce/flash-sale/create', [\App\Http\Controllers\Api\Enterprise\EcommerceEnterpriseController::class, 'createFlashSale']);
    Route::get('ecommerce/flash-sale/active', [\App\Http\Controllers\Api\Enterprise\EcommerceEnterpriseController::class, 'getActiveFlashSales']);
    Route::post('ecommerce/abandoned-cart/track', [\App\Http\Controllers\Api\Enterprise\EcommerceEnterpriseController::class, 'trackCart']);
    Route::post('ecommerce/abandoned-cart/process', [\App\Http\Controllers\Api\Enterprise\EcommerceEnterpriseController::class, 'processAbandonedCarts']);
    Route::post('ecommerce/stock-reservation/release-expired', [\App\Http\Controllers\Api\Enterprise\EcommerceEnterpriseController::class, 'releaseExpiredReservations']);

    // 17. Fixed Assets & Monthly Depreciation
    Route::post('assets/register', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'registerAsset']);
    Route::post('assets/depreciate-monthly', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'processDepreciation']);

    // 18. Department Budgeting vs Actual Variance
    Route::post('budgeting/set', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'setBudget']);
    Route::get('budgeting/variance', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'getBudgetVariance']);

    // 19. Manufacturing, Bill of Materials (BOM) & Work Orders
    Route::post('manufacturing/bom/create', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'createBom']);
    Route::post('manufacturing/work-orders/create', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'createWorkOrder']);
    Route::post('manufacturing/work-orders/{id}/execute', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'executeWorkOrder']);

    // 20. HRM Payroll PPh 21 TER & BPJS Calculator
    Route::post('payroll/calculate', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'calculatePayroll']);

    // 21. Serial Number / IMEI Tracking
    Route::post('inventory/serial-numbers/register', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'registerSerialNumbers']);
    Route::get('inventory/serial-numbers/lookup/{sn}', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'lookupSerial']);

    // 22. Warehouse Bin Locations
    Route::post('inventory/bins/create', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'createBin']);
    Route::get('inventory/bins', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'getBins']);

    // 23. RMA, Warranty & Service Center Tracker
    Route::post('rma/tickets/create', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'createRmaTicket']);
    Route::post('rma/tickets/{id}/status', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'updateRmaStatus']);

    // 24. CRMHUB Omnichannel Retention & Safe Broadcast
    Route::post('retention/birthday-greetings', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'sendBirthdayGreetings']);
    Route::post('retention/safe-broadcast', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'sendSafeBroadcast']);

    // 25. Offline POS Sync & Universal ESC/POS Printing
    Route::post('pos/offline-sync', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'syncOfflineTransactions']);
    Route::get('pos/escpos-receipt/{id}', [\App\Http\Controllers\Api\Enterprise\FullSpectrumEnterpriseController::class, 'getEscPosReceipt']);

    // 26. Wholesale B2B & Dynamic Tier Pricing
    Route::post('wholesale/tier/set', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'setTierPrice']);
    Route::get('wholesale/tiers/{productId}', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'getWholesaleTiers']);

    // 27. Digital Gift Cards & Prepaid Store Credit
    Route::post('gift-cards/issue', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'issueGiftCard']);
    Route::post('gift-cards/check-balance', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'checkGiftCardBalance']);
    Route::post('gift-cards/redeem', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'redeemGiftCard']);

    // 28. Accounting Period Closing & Lock
    Route::post('period-closing/close', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'closePeriod']);

    // 29. Cashier Fraud & Anomaly Detection
    Route::post('fraud/log-anomaly', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'logFraudAnomaly']);
    Route::get('fraud/scan', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'scanFraud']);

    // 30. Inventory Reorder AI & EOQ
    Route::post('reorder/generate', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'generateReorders']);
    Route::get('reorder/pending', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'getPendingReorders']);

    // 31. Delivery Driver Dispatch & e-POD
    Route::post('logistics/dispatch', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'dispatchDelivery']);
    Route::post('logistics/epod/{id}', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'submitEpod']);

    // 32. Split Bill & Multi-Tender Payment
    Route::post('pos/split-bill', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'splitBill']);
    Route::post('pos/multi-tender', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'settleMultiTender']);

    // 33. Resto Table Management & Live Occupancy
    Route::post('resto/tables/create', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'createTable']);
    Route::get('resto/tables', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'getTables']);
    Route::post('resto/tables/{id}/status', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'updateTableStatus']);

    // 34. CRMHUB Omnichannel Interactive WhatsApp Bot
    Route::post('crmhub/bot-webhook', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'handleBotWebhook']);

    // 35. Service Appointments & Booking
    Route::post('appointments/book', [\App\Http\Controllers\Api\Enterprise\FrontierEnterpriseController::class, 'bookAppointment']);
    Route::post('appointments/send-reminders', [\App\Http\Controllers\Api\Enterprise\FrontierEnterpriseController::class, 'sendAppointmentReminders']);
    Route::get('appointments', [\App\Http\Controllers\Api\Enterprise\FrontierEnterpriseController::class, 'getAppointments']);
    Route::post('appointments/{id}/status', [\App\Http\Controllers\Api\Enterprise\FrontierEnterpriseController::class, 'updateAppointmentStatus']);

    // 36. Consignment & Revenue Share Settlement
    Route::post('consignment/products/register', [\App\Http\Controllers\Api\Enterprise\FrontierEnterpriseController::class, 'registerConsignmentProduct']);
    Route::post('consignment/settlements/generate', [\App\Http\Controllers\Api\Enterprise\FrontierEnterpriseController::class, 'generateConsignmentSettlement']);
    Route::get('consignment/settlements', [\App\Http\Controllers\Api\Enterprise\FrontierEnterpriseController::class, 'getConsignmentSettlements']);
    Route::post('consignment/settlements/{id}/status', [\App\Http\Controllers\Api\Enterprise\FrontierEnterpriseController::class, 'updateConsignmentSettlementStatus']);

    // 37. Smart Promotion Engine (Kombo, BOGO, Threshold)
    Route::post('promotions/create', [\App\Http\Controllers\Api\Enterprise\FrontierEnterpriseController::class, 'createPromotion']);
    Route::post('promotions/evaluate-cart', [\App\Http\Controllers\Api\Enterprise\FrontierEnterpriseController::class, 'evaluateCartPromotions']);
    Route::get('promotions', [\App\Http\Controllers\Api\Enterprise\FrontierEnterpriseController::class, 'getPromotions']);
    Route::post('promotions/{id}/toggle', [\App\Http\Controllers\Api\Enterprise\FrontierEnterpriseController::class, 'togglePromotion']);

    // 38. Executive C-Level BI Analytics & WhatsApp Morning Briefing
    Route::get('executive/briefing/snapshot', [\App\Http\Controllers\Api\Enterprise\FrontierEnterpriseController::class, 'getExecutiveSnapshot']);
    Route::post('executive/briefing/send-whatsapp', [\App\Http\Controllers\Api\Enterprise\FrontierEnterpriseController::class, 'sendExecutiveBriefing']);

    // =========================================================================
    // TIER-1 STRATEGIC ENTERPRISE ERP MODULES (ACCURATE, HASHMICRO, ZAHIR & KLEDO)
    // =========================================================================

    // 39. Modul Pajak & Kepatuhan e-Faktur DJP (CSV Export, NSFP & Withholding Tax)
    Route::post('tax/efaktur-csv', [\App\Http\Controllers\Api\Enterprise\TierOneEnterpriseErpController::class, 'exportEfakturCsv']);
    Route::post('tax/allocate-nsfp', [\App\Http\Controllers\Api\Enterprise\TierOneEnterpriseErpController::class, 'allocateNsfp']);
    Route::post('tax/calculate-withholding', [\App\Http\Controllers\Api\Enterprise\TierOneEnterpriseErpController::class, 'calculateTaxWithholding']);

    // 40. Executive Financial Health, 8 Rasio Finansial & AR/AP Aging Schedule
    Route::get('analytics/financial-health', [\App\Http\Controllers\Api\Enterprise\TierOneEnterpriseErpController::class, 'getFinancialHealth']);
    Route::get('analytics/aging-schedule', [\App\Http\Controllers\Api\Enterprise\TierOneEnterpriseErpController::class, 'getAgingSchedule']);
    Route::get('analytics/cashflow-forecast', [\App\Http\Controllers\Api\Enterprise\TierOneEnterpriseErpController::class, 'getCashFlowForecast']);

    // 41. Cost Center, Departemen & Akuntansi Proyek (Project P&L & Recurring Amortization)
    Route::get('accounting/project-pnl', [\App\Http\Controllers\Api\Enterprise\TierOneEnterpriseErpController::class, 'getProjectPnl']);
    Route::post('accounting/amortization/create', [\App\Http\Controllers\Api\Enterprise\TierOneEnterpriseErpController::class, 'createAmortization']);

    // 42. Multi-Satuan Berjenjang (Multi-Tier UoM) & Manufaktur Absorption HPP
    Route::post('inventory/uom/convert', [\App\Http\Controllers\Api\Enterprise\TierOneEnterpriseErpController::class, 'convertUom']);
    Route::get('inventory/uom/tiered-prices', [\App\Http\Controllers\Api\Enterprise\TierOneEnterpriseErpController::class, 'getTieredUomPrices']);
    Route::post('manufacturing/absorption-costing', [\App\Http\Controllers\Api\Enterprise\TierOneEnterpriseErpController::class, 'calculateManufacturingCost']);

    // 43. Rekonsiliasi Bank Otomatis & Kas Kecil (Petty Cash Imprest/Fluctuating)
    Route::post('cashbank/bank-statement/parse', [\App\Http\Controllers\Api\Enterprise\TierOneEnterpriseErpController::class, 'parseBankStatement']);
    Route::post('cashbank/bank-statement/auto-match', [\App\Http\Controllers\Api\Enterprise\TierOneEnterpriseErpController::class, 'autoMatchBank']);
    Route::post('cashbank/petty-cash/record', [\App\Http\Controllers\Api\Enterprise\TierOneEnterpriseErpController::class, 'recordPettyCash']);

    // 44. Portal B2B Mandiri Pelanggan & Vendor (Plafon Kredit & PO Confirmation)
    Route::get('b2b/customer/{id}/profile', [\App\Http\Controllers\Api\Enterprise\TierOneEnterpriseErpController::class, 'getB2bCustomerPortal']);
    Route::get('b2b/vendor/{id}/orders', [\App\Http\Controllers\Api\Enterprise\TierOneEnterpriseErpController::class, 'getVendorPortal']);
    Route::post('b2b/vendor/{id}/dispatch-confirm', [\App\Http\Controllers\Api\Enterprise\TierOneEnterpriseErpController::class, 'confirmVendorDispatch']);
});
