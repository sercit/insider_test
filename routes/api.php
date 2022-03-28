<?php

use App\Http\Controllers\FixtureController;
use App\Http\Controllers\TournamentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('tournament', [TournamentController::class, 'getActive']);
Route::get('tournament/{id}/clubs', [TournamentController::class, 'getClubs']);
Route::post('tournament/{id}/generate', [TournamentController::class, 'generate']);
Route::post('tournament/{id}/reset', [TournamentController::class, 'reset']);
Route::get('tournament/{id}/fixture', [FixtureController::class, 'getAll']);
Route::post('tournament/{id}/fixture/playAll', [FixtureController::class, 'playAll']);
Route::get('fixture/{id}', [FixtureController::class, 'get']);
Route::post('fixture/{id}', [FixtureController::class, 'play']);
Route::get('tournament/{id}/fixture/active', [FixtureController::class, 'getActive']);
Route::get('tournament/{id}/table', [TournamentController::class, 'getTable']);

