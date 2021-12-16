<?php

use App\Http\Controllers\ListAPIController;
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

Route::get("/api/show/{any?}", [ListAPIController::class, "show"])->where("any", ".*");
Route::post("/api/delete", [ListAPIController::class, "destroy"]);
Route::post("/api/rename", [ListAPIController::class, "update"]);
Route::post("/api/upload", [ListAPIController::class, "store"]);
Route::post("/api/chunk-upload", [ListAPIController::class, "chunk_upload"]);
