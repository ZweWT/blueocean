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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');


Route::get('/data', function () {
    return view('user');
});

Route::get('users/list', [UserController::class, 'getUsers'])->name('users.list');
Route::get('register', [UserController::class, 'create'])->name('register');

Route::post('register', [UserController::class, 'store']);
Route::post('update/{id}', [UserController::class, 'update'])->name('update');
Route::get('delete/user/{id}', [UserController::class, 'destroy']);
Route::get('edit/user/{id}', [UserController::class, 'edit']);

require __DIR__.'/auth.php';
