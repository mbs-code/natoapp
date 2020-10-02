<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TwitterController;
use App\Http\Controllers\YoutubeController;
use App\Http\Controllers\DebugController;

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
    return Inertia::render('Home');
})->name('home');

Route::resource('profiles', ProfileController::class);
Route::resource('twitters', TwitterController::class,
    ['only' => ['index', 'store', 'update', 'destroy']]
);
Route::resource('youtubes', YoutubeController::class,
    ['only' => ['index', 'store', 'update', 'destroy']]
);

Route::post('/debug/toast', [DebugController::class, 'toast'])->name('debug.toast');

// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->name('dashboard');
