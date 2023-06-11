<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
})->name('main');
Route::post('save-location', [LocationController::class, 'saveLocation']);

Route::get('auth/{provider}', [AuthController::class, 'redirect']);
Route::get('auth/{provider}/callback', [AuthController::class, 'handleCallback']);
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('home', [HomeController::class, 'home'])->name('home');
    Route::get('logout', [AuthController::class, 'logout']);
});
