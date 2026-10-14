<?php

//use App\Http\Controllers\Backend\CustomerController;

// All route names are prefixed with 'admin.'.
//Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::group([
    // stock_purchase_role was missing entirely — the role exists (checked by
    // isStockPurchase() in items/stock.blade.php, held by real staff
    // accounts) but without it here those users get 403'd out of every
    // Purchase route. sale.php's equivalent group already includes its own
    // stock_saleorder_role; this brings purchase.php in line with that.
    'middleware' => 'role:'. config('access.users.admin_role') . '|' . config('access.users.service_enginner_role') . '|' . config('access.users.stock_admin_role') . '|' . config('access.users.stock_purchase_role') ,
], function () {

    Route::resource('stock/purchase','PurchaseController');
    Route::get('stock/report_purchase','PurchaseController@report_purchase')->name('stock.report_purchase');
    Route::get('stock/purchase/create/{sid}','PurchaseController@create')->name('create.rma');
});
