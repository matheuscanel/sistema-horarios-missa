<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ParoquiaController;
use App\Http\Controllers\Api\PublicoController;
use Illuminate\Support\Facades\Route;

// =============================================
// ROTAS PÚBLICAS (sem autenticação)
// =============================================

// Página pública — lista igrejas aprovadas com horários
Route::get('/igrejas', [PublicoController::class, 'index']);

// Autenticação
Route::post('/login', [AuthController::class, 'login']);
Route::post('/registro', [AuthController::class, 'registro']);

// =============================================
// ROTAS PROTEGIDAS (requer autenticação via Sanctum)
// =============================================

Route::middleware('auth:sanctum')->group(function () {

    // Dados do usuário logado
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- Área da Paróquia ---
    Route::prefix('paroquia')->group(function () {
        Route::get('/dashboard', [ParoquiaController::class, 'dashboard']);

        // CRUD de Igrejas
        Route::post('/igrejas', [ParoquiaController::class, 'criarIgreja']);
        Route::put('/igrejas/{igreja}', [ParoquiaController::class, 'editarIgreja']);
        Route::delete('/igrejas/{igreja}', [ParoquiaController::class, 'excluirIgreja']);

        // CRUD de Horários de Missa
        Route::post('/igrejas/{igreja}/horarios', [ParoquiaController::class, 'criarHorario']);
        Route::put('/horarios/{horario}', [ParoquiaController::class, 'editarHorario']);
        Route::delete('/horarios/{horario}', [ParoquiaController::class, 'excluirHorario']);
    });

    // --- Área do Admin (requer tipo 'admin') ---
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/paroquias', [AdminController::class, 'index']);
        Route::patch('/paroquias/{paroquia}/aprovar', [AdminController::class, 'aprovar']);
        Route::patch('/paroquias/{paroquia}/rejeitar', [AdminController::class, 'rejeitar']);
        Route::delete('/paroquias/{paroquia}', [AdminController::class, 'remover']);
    });
});
