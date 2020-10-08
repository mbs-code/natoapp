<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/tags', [APIController::class, 'tags'])->name('api.tags');
Route::get('/twitters', [APIController::class, 'twitters'])->name('api.twitters');
Route::get('/youtubes', [APIController::class, 'youtubes'])->name('api.youtubes');
Route::get('/videos', [APIController::class, 'videos'])->name('api.videos');

Route::post('/debug/toast', [DebugController::class, 'toast'])->name('debug.toast');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
