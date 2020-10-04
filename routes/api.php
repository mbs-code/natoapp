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

Route::get('/api/tags', [APIController::class, 'tags'])->name('api.tags');
Route::get('/api/twitters', [APIController::class, 'twitters'])->name('api.twitters');
Route::get('/api/youtubes', [APIController::class, 'youtubes'])->name('api.youtubes');

Route::post('/debug/toast', [DebugController::class, 'toast'])->name('debug.toast');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
