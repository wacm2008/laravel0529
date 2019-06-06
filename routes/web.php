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
//登录
Route::get('/login','PracticaController@login');
Route::post('/logindo','PracticaController@logindo')->middleware('Num');
Route::post('/show','PracticaController@show');
//退出
Route::get('/logout','PracticaController@logout');
//cdn img
Route::get('/cdn_img','PracticaController@cdnImg');