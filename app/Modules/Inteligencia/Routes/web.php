<?php

use App\Modules\Inteligencia\Controllers\Admin\DashboardController as AdminDashboard;
use App\Modules\Inteligencia\Controllers\Admin\ReporteController;
use App\Modules\Inteligencia\Controllers\Admin\ReporteVozController;
use App\Modules\Inteligencia\Controllers\Coordinador\DashboardController as CoordinadorDashboard;
use App\Modules\Inteligencia\Controllers\Docente\DashboardController as DocenteDashboard;
use App\Modules\Inteligencia\Controllers\Estudiante\DashboardController as EstudianteDashboard;
use Illuminate\Support\Facades\Route;

// Administrador: dashboard y reporte voz (solo admin)
Route::middleware(['auth', 'rol:administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Reporte por voz con IA
        Route::get('/reportes/voz', [ReporteVozController::class, 'index'])->name('reportes.voz');
        Route::post('/reportes/voz/consultar', [ReporteVozController::class, 'consultar'])->name('reportes.voz.consultar');
        Route::post('/reportes/voz/pdf', [ReporteVozController::class, 'exportarPdf'])->name('reportes.voz.pdf');
        Route::post('/reportes/voz/excel', [ReporteVozController::class, 'exportarExcel'])->name('reportes.voz.excel');
    });

// Reportes (coordinador y administrador)
Route::middleware(['auth', 'rol:coordinador,administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/reportes/postulantes', [ReporteController::class, 'postulantes'])->name('reportes.postulantes');
        Route::get('/reportes/postulantes/pdf', [ReporteController::class, 'postulantePdf'])->name('reportes.postulantes.pdf');
        Route::get('/reportes/postulantes/excel', [ReporteController::class, 'postulantesExcel'])->name('reportes.postulantes.excel');
        Route::get('/reportes/postulantes/csv', [ReporteController::class, 'postulantesCSV'])->name('reportes.postulantes.csv');

        Route::get('/reportes/notas', [ReporteController::class, 'notas'])->name('reportes.notas');
        Route::get('/reportes/notas/pdf', [ReporteController::class, 'notasPdf'])->name('reportes.notas.pdf');
        Route::get('/reportes/notas/excel', [ReporteController::class, 'notasExcel'])->name('reportes.notas.excel');

        Route::get('/reportes/estadisticas', [ReporteController::class, 'estadisticas'])->name('reportes.estadisticas');
        Route::get('/reportes/estadisticas/pdf', [ReporteController::class, 'estadisticasPdf'])->name('reportes.estadisticas.pdf');
        Route::get('/reportes/estadisticas/excel', [ReporteController::class, 'estadisticasExcel'])->name('reportes.estadisticas.excel');

        Route::get('/reportes/grupos', [ReporteController::class, 'grupos'])->name('reportes.grupos');
        Route::get('/reportes/grupos/pdf', [ReporteController::class, 'gruposPdf'])->name('reportes.grupos.pdf');
        Route::get('/reportes/grupos/excel', [ReporteController::class, 'gruposExcel'])->name('reportes.grupos.excel');

        Route::get('/reportes/docentes', [ReporteController::class, 'docentes'])->name('reportes.docentes');
        Route::get('/reportes/docentes/pdf', [ReporteController::class, 'docentesPdf'])->name('reportes.docentes.pdf');
        Route::get('/reportes/docentes/excel', [ReporteController::class, 'docentesExcel'])->name('reportes.docentes.excel');

        Route::get('/reportes/gestiones', [ReporteController::class, 'gestiones'])->name('reportes.gestiones');
        Route::get('/reportes/gestiones/pdf', [ReporteController::class, 'gestionesPdf'])->name('reportes.gestiones.pdf');
        Route::get('/reportes/gestiones/excel', [ReporteController::class, 'gestionesExcel'])->name('reportes.gestiones.excel');
        Route::get('/reportes/gestiones/txt', [ReporteController::class, 'gestionesTxt'])->name('reportes.gestiones.txt');
    });

// Coordinador: dashboard
Route::middleware(['auth', 'rol:coordinador,administrador'])
    ->prefix('coordinador')
    ->name('coordinador.')
    ->group(function () {
        Route::get('/dashboard', [CoordinadorDashboard::class, 'index'])->name('dashboard');
    });

// Docente: dashboard
Route::middleware(['auth', 'rol:docente'])
    ->prefix('docente')
    ->name('docente.')
    ->group(function () {
        Route::get('/dashboard', [DocenteDashboard::class, 'index'])->name('dashboard');
    });

// Estudiante: dashboard, horario, resultados
Route::middleware(['auth', 'rol:estudiante'])
    ->prefix('estudiante')
    ->name('estudiante.')
    ->group(function () {
        Route::get('/dashboard', [EstudianteDashboard::class, 'index'])->name('dashboard');
        Route::get('/horario', [EstudianteDashboard::class, 'horario'])->name('horario');
        Route::get('/resultados', [EstudianteDashboard::class, 'resultados'])->name('resultados');
    });
