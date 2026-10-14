<?php

//use App\Http\Controllers\Backend\CustomerController;

// All route names are prefixed with 'admin.'.
//Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::group([
    'middleware' => 'role:'. config('access.users.admin_role') . '|' . config('access.users.stock_saleorder_role') . '|' . config('access.users.stock_admin_role') ,
], function () {
    Route::resource('stock/saleorder','SaleController');
    Route::get('stock/report_sale','SaleController@report_sale')->name('stock.report_sale');
});
