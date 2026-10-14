<?php
Route::resource('setup/category','CategoryController');
Route::post('setup/category/fetch','CategoryController@fetch')->name('category.fetch');
