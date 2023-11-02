<?php

use App\Http\Controllers\AutheManager;
use App\Http\Controllers\PhotoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');;

Route::get('/login', [AutheManager::class,'login'])->name('login');
Route::post('/login', [AutheManager::class,'loginPost'])->name('login.post');
Route::get('/register', [AutheManager::class,'register'])->name('register');
Route::post('/register', [AutheManager::class,'registerPost'])->name('register.post');
Route::get('/logout', [AutheManager::class,'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/gallery', [PhotoController::class, 'index']);
    Route::post('/gallery', [PhotoController::class, 'upload']);
    Route::delete('/gallery/{photo}', [PhotoController::class, 'delete'])->name('photo.delete');

});
