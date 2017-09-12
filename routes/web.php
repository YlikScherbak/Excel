<?php


Route::get('/excel', ['uses' => 'ExcelController@index', 'as' => 'index_excel']);
Route::post('/import', ['uses' => 'ExcelController@getExcel', 'as' => 'post_excel']);
Route::post('/update_excel', ['uses' => 'ExcelController@updateExcel', 'as' => 'update_excel']);
Auth::routes();


Route::get('/home', 'HomeController@index')->name('home');


Route::get('/db', ['uses' => 'ExcelController@dbtest', 'as' => 'db_excel']);