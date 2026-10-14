<?php

//use App\Http\Controllers\Backend\CustomerController;

// All route names are prefixed with 'admin.'.
//Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
//Route::get('service/report/pdf','ServiceController@pdfreport')->name('service.pdfreport');


Route::match(array('PUT', 'PATCH'), "service/{service}/edit", array(
      'uses' => 'ServiceController@edit',
      'as' => 'service.edit'
));

Route::get('service/rma_paid','ServiceController@rma_paid')->name('service.rma_paid');

Route::get('transfer/clearance','ServiceController@transfer_clearance')->name('service.transfer_clearance');

Route::get('service/calling','ServiceController@calling')->name('service.calling');

Route::post('service/getServices','ServiceController@getServices')->name('service.getServices');

Route::post('service/suggest','ServiceController@suggesstion')->name('service.suggesstion');

/*
Route::get('service/{service}/edit', function () {
    //return abort('500');
    dd($errors->any());

        return redirect(route('admin.service.index'))
            ->withFlashDanger('You have no permission to copy and paste link address.');


});
 */
//-------------------------Service Shipping -----------------------
Route::delete('shipping/destroy/{shipping}','ServiceController@shipping_destroy')->name('service.shipping.destroy');
Route::get('shipping/{shipping}','ServiceController@shipping')->name('service.shipping');
Route::post('shipping/{shipping}','ServiceController@shipping')->name('service.shipping');
Route::get('shipping/{shipping}/edit','ServiceController@shipping')->name('service.shipping.edit');
Route::patch('shipping/{shipping}/edit','ServiceController@shipping')->name('service.shipping.edit');
//-------------------------Service Transfer -----------------------
Route::delete('transfer/destroy/{shipping}','ServiceController@transfer_destroy')->name('service.transfer.destroy');
Route::get('transfer/{transfer}','ServiceController@transfer')->name('service.transfer');
Route::post('transfer/{transfer}','ServiceController@transfer')->name('service.transfer');
Route::get('transfer/{transfer}/edit','ServiceController@transfer')->name('service.transfer.edit');
Route::patch('transfer/{transfer}/edit','ServiceController@transfer')->name('service.transfer.edit');
//------------------------- Cash Transfer -----------------------------
Route::get('service/cash_transfer','ServiceController@cash_transfer')->name('service.cash_transfer');

//Route::get('service/shipping/{shipping}/edit','ServiceController@shipping')->name('service.shipping.edit');
//Route::post('service/shipping','ServiceController@saveshipping')->name('service.saveshipping');
Route::delete('service/{service}','ServiceController@destroy')->name('service.destroy');
Route::resource('service','ServiceController');
Route::get('service/receipt/{service}','ServiceController@receipt')->name('service.receipt');
Route::get('service/invoice/{service}','ServiceController@invoice')->name('service.invoice');
Route::post('service/assignto/{service}','ServiceController@assignto')->name('service.assignto');
Route::post('service/customerphone','ServiceController@getcustomerphone')->name('service.getcustomerphone');
Route::post('service/media', 'ServiceController@storeMedia')->name('service.storeMedia');
Route::resource('serviceshipping','ServiceShippingController');
