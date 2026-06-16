<?php

use App\Modules\PostulacionDocente\Controllers\PostulacionController;
use App\Modules\PostulacionDocente\Controllers\Admin\PostulacionDocenteController;
use Illuminate\Support\Facades\Route;

// Público — sin autenticación
Route::get('/postulacion-docente', [PostulacionController::class, 'create'])->name('postulacion-docente.create');
Route::post('/postulacion-docente', [PostulacionController::class, 'store'])->name('postulacion-docente.store');
Route::get('/postulacion-docente/confirmacion', [PostulacionController::class, 'confirmacion'])->name('postulacion-docente.confirmacion');

// Admin
Route::middleware(['auth', 'rol:administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/postulaciones-docente', [PostulacionDocenteController::class, 'index'])->name('postulaciones-docente.index');
        Route::get('/postulaciones-docente/{postulacion}', [PostulacionDocenteController::class, 'show'])->name('postulaciones-docente.show');
        Route::post('/postulaciones-docente/{postulacion}/aprobar', [PostulacionDocenteController::class, 'aprobar'])->name('postulaciones-docente.aprobar');
        Route::post('/postulaciones-docente/{postulacion}/rechazar', [PostulacionDocenteController::class, 'rechazar'])->name('postulaciones-docente.rechazar');
    });
