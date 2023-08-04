<?php

use Illuminate\Support\Facades\Route;

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
    return view('auth/login');
});
Route::get('/admin/administrador','App\Http\Controllers\micrositioController@administrador')->middleware(['auth'])->name('dashboard');
Route::post('/admin/search','App\Http\Controllers\micrositioController@search')->middleware(['auth'])->name('search');
Route::post('/sendmail','App\Http\Controllers\micrositioController@sendMail')->name('sendMail');
Route::post('/admin/certificado','App\Http\Controllers\micrositioController@certificado')->middleware(['auth'])->name('certificado');
Route::get('/{url}','App\Http\Controllers\micrositioController@broker');

Route::post('/admin/update','App\Http\Controllers\micrositioController@update')->name('update');

Route::get('/admin/dashboard/{id}','App\Http\Controllers\micrositioController@dashboard')->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
