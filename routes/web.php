<?php


Route::get('/excel', ['uses' => 'ExcelController@index', 'as' => 'index_excel']);
Route::post('/import', ['uses' => 'ExcelController@getExcel', 'as' => 'post_excel']);
Route::get('/update_excel', ['uses' => 'ExcelController@updateExcel', 'as' => 'update_excel']);
Route::post('preview_excel', ['uses' => 'ExcelController@previewExcel', 'as' => 'preview_excel']);
Route::get('/cancel', ['uses' => 'ExcelController@cancel', 'as' => 'cancel_update']);
Route::get('/info', ['uses' => 'ExcelController@info', 'as' => 'info']);
Auth::routes();


Route::get('/home', 'HomeController@index')->name('home');


Route::get('/db', ['uses' => 'ExcelController@dbtest', 'as' => 'db_excel']);