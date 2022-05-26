<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
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
Route::middleware('auth')->group(function() {
    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index')->name('users');
        Route::get('/users/list', 'getUsers')->name('users.list');
        Route::get('/register', 'create')->name('register');
        Route::post('/register', 'store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::get('/delete/user/{id}', 'destroy');
        Route::get('/edit/user/{id}', 'edit');
    });
});

require __DIR__.'/auth.php';
