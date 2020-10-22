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

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', function () {
    return view('index');
});

Route::get('/socket.io', 'IM\SocketIOController@upgrade');
Route::post('/socket.io', 'IM\SocketIOController@ok');

Route::post('/register','External\TempUserController@register');
