<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Profile;
use App\Models\Twitter;
use App\Models\Channel;

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

Route::get('/profile', function () {
    $profiles = Profile::with(['twitters', 'channels'])->get();
    return Inertia::render('Profile/Index', ['profiles' => $profiles]);
})->name('profile');

Route::get('/twitter', function () {
    $twitters = Twitter::all();
    return Inertia::render('Twitter/Index', ['twitters' => $twitters]);
})->name('twitter');

Route::get('/youtube', function () {
    $youtubes = Channel::all();
    return Inertia::render('Youtube/Index', ['youtubes' => $youtubes]);
})->name('youtube');

// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->name('dashboard');
