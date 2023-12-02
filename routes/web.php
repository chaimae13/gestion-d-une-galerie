<?php

use App\Http\Controllers\AutheManager;
use App\Http\Controllers\GetApi;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\Theme;
use App\Http\Controllers\ThemeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the 'web' middleware group. Make something great!
|
*/

Route::get('/login', [AutheManager::class,'login'])->name('login');
Route::post('/login', [AutheManager::class,'loginPost'])->name('login.post');
Route::get('/register', [AutheManager::class,'register'])->name('register');
Route::post('/register', [AutheManager::class,'registerPost'])->name('register.post');
Route::get('/logout', [AutheManager::class,'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [AutheManager::class,'home'])->name('welcome');
    Route::get('/gallery', [PhotoController::class, 'index'])->name('gallery');
    Route::post('/gallery', [PhotoController::class, 'upload']);
    Route::delete('/gallery/{photo}', [PhotoController::class, 'delete'])->name('photo.delete');
});


Route::get('/getInfo/{photo_id}', [PhotoController::class, 'viewJSON'])->name('getInfo');
Route::post('/themes', [ThemeController::class,'add']);
// Route::get('/getImagesByTheme/{themeId}', [PhotoController::class,'getImagesByTheme']);

Route::get('/editer-photo/{id}',[PhotoController::class,'edit'])->name('photo.edit');
Route::post('/photos/{id}/update', [PhotoController::class,'update'])->name('photo.update');
Route::post('/photo/{id}/change-scale', [PhotoController::class,'changeScale'])->name('photo.changeScale');


