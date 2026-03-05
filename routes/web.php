<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ParoquiaController;
use App\Http\Controllers\PublicoController;
use Illuminate\Support\Facades\Route;

// --- Área Pública ---
Route::get('/', [PublicoController::class, 'index'])->name('publico.index');

// --- Autenticação ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/registro', [AuthController::class, 'showRegistroForm'])->name('registro');
    Route::post('/registro', [AuthController::class, 'registro']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// --- Área da Paróquia (logada) ---
Route::middleware('auth')->prefix('paroquia')->name('paroquia.')->group(function () {
    Route::get('/dashboard', [ParoquiaController::class, 'dashboard'])->name('dashboard');

    // Igrejas
    Route::post('/igrejas', [ParoquiaController::class, 'criarIgreja'])->name('igrejas.criar');
    Route::put('/igrejas/{igreja}', [ParoquiaController::class, 'editarIgreja'])->name('igrejas.editar');
    Route::delete('/igrejas/{igreja}', [ParoquiaController::class, 'excluirIgreja'])->name('igrejas.excluir');

    // Horários de Missa
    Route::post('/igrejas/{igreja}/horarios', [ParoquiaController::class, 'criarHorario'])->name('horarios.criar');
    Route::put('/horarios/{horario}', [ParoquiaController::class, 'editarHorario'])->name('horarios.editar');
    Route::delete('/horarios/{horario}', [ParoquiaController::class, 'excluirHorario'])->name('horarios.excluir');
});

// --- Área do Admin ---
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::patch('/paroquias/{paroquia}/aprovar', [AdminController::class, 'aprovar'])->name('aprovar');
    Route::patch('/paroquias/{paroquia}/rejeitar', [AdminController::class, 'rejeitar'])->name('rejeitar');
});
