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


// Route::get('/', function () {
//     return view('test');
// });
// Route::post('/', function () {
//     echo "test";
// });


Route::get('/health', 'HealthController@index');
Route::post('/health', 'HealthController@index');

Route::get('/form/{formId}', 'HealthController@evoluation');
Route::post('/form/{formId}', 'HealthController@evoluation');

Route::get('/behavior', 'HealthController@behavior');
Route::post('/behavior', 'HealthController@behavior');

Route::get('/volunteer', 'HealthController@volunteer');
Route::post('/volunteer', 'HealthController@volunteer');
