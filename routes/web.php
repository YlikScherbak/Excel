<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/excel', ['uses' => 'ExcelController@index', 'as' => 'index_excel']);
Route::post('import', ['uses' => 'ExcelController@getExcel', 'as' => 'post_excel']);
Route::post('/update_excel', ['uses' => 'ExcelController@updateExcel', 'as' => 'update_excel']);
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
