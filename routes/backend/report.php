<?php

Route::get('report/service','ServiceController@report')->name('service.report');
Route::post('report/service','ServiceController@report')->name('service.report');

Route::get('report/agin','ServiceController@report_agin')->name('agin.report');
Route::post('report/agin','ServiceController@report_agin')->name('agin.report');

Route::get('report/rma','ServiceController@report_rma')->name('rma.report');
Route::post('report/rma','ServiceController@report_rma')->name('rma.report');

Route::get('report/shipping','ServiceController@report_shipping')->name('shipping.report');
Route::post('report/shipping','ServiceController@report_shipping')->name('shipping.report');

Route::get('report/transfer','ServiceController@report_transfer')->name('transfer.report');
Route::post('report/transfer','ServiceController@report_transfer')->name('transfer.report');

Route::get('report/enginner','ServiceController@report_enginner')->name('enginner.report');
Route::post('report/enginner','ServiceController@report_enginner')->name('enginner.report');

Route::get('report/cash','ServiceController@report_cash')->name('cash.report');
Route::post('report/cash','ServiceController@report_cash')->name('cash.report');

Route::get('report/selling','ServiceController@report_selling')->name('selling.report');
Route::post('report/selling','ServiceController@report_selling')->name('selling.report');

Route::get('report/revenue','ServiceController@report_revenue')->name('revenue.report');
Route::post('report/revenue','ServiceController@report_revenue')->name('revenue.report');
