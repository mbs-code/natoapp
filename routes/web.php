<?php

use App\Models\Twitter;
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

// !!! call "php artisan ziggy:generate" !!!

Route::get('/', function () {
    return Inertia\Inertia::render('Home');
    // return view('welcome');
})->name('home');

Route::get('/twitter', function () {
    $twitters = Twitter::all();
    return Inertia\Inertia::render('Twitter/Index', ['twitters' => $twitters]);
})->name('twitter');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia\Inertia::render('Dashboard');
})->name('dashboard');
