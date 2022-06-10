<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

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
    return view('home');
});
Route::get('/home', function () {
    return view('home');
})->name("home");

Route::post('/partida', [Controller::class, 'empezar'])->name("partida");
Route::post('/jugar', [Controller::class, 'jugar'])->name("jugar");
Route::post('/reset', [Controller::class, 'reset'])->name("reset");
