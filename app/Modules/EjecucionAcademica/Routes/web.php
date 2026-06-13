<?php

use App\Modules\EjecucionAcademica\Controllers\Docente\AsistenciaController as DocenteAsistencia;
use App\Modules\EjecucionAcademica\Controllers\Docente\ClaseController;
use App\Modules\EjecucionAcademica\Controllers\Docente\NotaController;
use App\Modules\EjecucionAcademica\Controllers\Estudiante\AsistenciaController as EstudianteAsistencia;
use App\Modules\EjecucionAcademica\Controllers\ExamenController;
use App\Modules\EjecucionAcademica\Controllers\ReporteAsistenciaController;
use Illuminate\Support\Facades\Route;

// Exámenes y reporte asistencia (solo administrador)
Route::middleware(['auth', 'rol:administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/examenes', [ExamenController::class, 'index'])->name('examenes.index');
        Route::get('/examenes/{grupo}', [ExamenController::class, 'porGrupo'])->name('examenes.porGrupo');
        Route::post('/grupos/{grupo}/activar', [ExamenController::class, 'activarGrupo'])->name('grupos.activar');
        Route::post('/examenes/{examen}/estado', [ExamenController::class, 'cambiarEstado'])->name('examenes.estado');
        Route::patch('/examenes/{examen}/fecha', [ExamenController::class, 'actualizarFecha'])->name('examenes.fecha');

        Route::get('/reportes/asistencia', [ReporteAsistenciaController::class, 'index'])->name('reportes.asistencia');
        Route::get('/reportes/asistencia/pdf', [ReporteAsistenciaController::class, 'pdf'])->name('reportes.asistencia.pdf');
        Route::get('/reportes/asistencia/excel', [ReporteAsistenciaController::class, 'excel'])->name('reportes.asistencia.excel');
    });

// Docente: notas, clases, asistencia
Route::middleware(['auth', 'rol:docente'])
    ->prefix('docente')
    ->name('docente.')
    ->group(function () {
        Route::get('/notas', [NotaController::class, 'index'])->name('notas.index');
        Route::get('/notas/{grupo}', [NotaController::class, 'planilla'])->name('notas.planilla');
        Route::post('/notas/{grupo}', [NotaController::class, 'guardar'])->name('notas.guardar');

        Route::get('/clases', [ClaseController::class, 'index'])->name('clases.index');
        Route::get('/clases/nueva', [ClaseController::class, 'create'])->name('clases.create');
        Route::post('/clases', [ClaseController::class, 'store'])->name('clases.store');
        Route::patch('/clases/{clase}/estado', [ClaseController::class, 'cambiarEstado'])->name('clases.estado');
        Route::get('/clases/verificar-aula', [ClaseController::class, 'verificarAula'])->name('clases.verificar-aula');

        Route::get('/asistencia/{clase}', [DocenteAsistencia::class, 'paseLista'])->name('asistencia.pase-lista');
        Route::post('/asistencia/{clase}', [DocenteAsistencia::class, 'guardar'])->name('asistencia.guardar');
    });

// Estudiante: asistencia
Route::middleware(['auth', 'rol:estudiante'])
    ->prefix('estudiante')
    ->name('estudiante.')
    ->group(function () {
        Route::get('/asistencia', [EstudianteAsistencia::class, 'index'])->name('asistencia');
    });
