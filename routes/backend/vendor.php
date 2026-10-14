<?php

//use App\Http\Controllers\Backend\CustomerController;

// All route names are prefixed with 'admin.'.
//Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('setup/vendor','VendorController');
