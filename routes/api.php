<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuAuditController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\DendaController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('buku/{id}/audits', [BukuAuditController::class, 'index']);
});

Route::apiResource('users', UserController::class);
Route::apiResource('buku', BukuController::class);
Route::apiResource('peminjaman', PeminjamanController::class);
Route::apiResource('denda', DendaController::class);


