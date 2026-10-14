<?php

//use App\Http\Controllers\Backend\CustomerController;

// All route names are prefixed with 'admin.'.
//Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
// Was commented out entirely — ItemController's own store()/update()
// success redirects call route('admin.item.create')/('admin.item.index'),
// which were broken with no route registered at all.
Route::resource('setup/item','ItemController');
//Route::get('stockcontrol/index','ItemController@stock')->name('stock.control');
Route::post('setup/item/fetchstock','ItemController@fetchstock')->name('item.fetchstock');
