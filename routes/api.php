<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas (sem autenticação)
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Rotas Protegidas (requerem token Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Dados do usuário autenticado
    Route::get('/user', [AuthController::class, 'me']);

    // Logout (revoga o token atual)
    Route::post('/logout', [AuthController::class, 'logout']);

    // CRUD REST de Tarefas
    Route::apiResource('tasks', TaskController::class);
});