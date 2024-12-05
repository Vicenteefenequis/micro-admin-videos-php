<?php

use App\Http\Controllers\Api\{CastMemberController, CategoryController, GenreController};
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
Route::apiResource('/categories', CategoryController::class);
Route::apiResource('/genres', GenreController::class);
Route::apiResource('/cast_members', CastMemberController::class);


Route::get('/', function () {
    return response()->json(['message' => 'success']);
});
