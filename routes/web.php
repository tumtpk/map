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


Route::get('/', 'MapController@index');
Route::post('/', 'MapController@index');

Route::get('/behavior', 'MapController@behavior');
Route::post('/behavior', 'MapController@behavior');

Route::get('/adl', 'MapController@adl');
Route::post('/adl', 'MapController@adl');

Route::get('/osteoarthritis', 'MapController@osteoarthritis');
Route::post('/osteoarthritis', 'MapController@osteoarthritis');
