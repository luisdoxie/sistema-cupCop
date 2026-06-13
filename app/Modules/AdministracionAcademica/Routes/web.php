<?php

use App\Modules\AdministracionAcademica\Controllers\AsignacionController;
use App\Modules\AdministracionAcademica\Controllers\AulaController;
use App\Modules\AdministracionAcademica\Controllers\CargaMasivaController;
use App\Modules\AdministracionAcademica\Controllers\ClaseProgramadaController;
use App\Modules\AdministracionAcademica\Controllers\ConfiguracionController;
use App\Modules\AdministracionAcademica\Controllers\DocenteController;
use App\Modules\AdministracionAcademica\Controllers\GestionController;
use App\Modules\AdministracionAcademica\Controllers\GrupoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'rol:administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Gestiones
        Route::get('/gestiones', [GestionController::class, 'index'])->name('gestiones.index');
        Route::get('/gestiones/crear', [GestionController::class, 'create'])->name('gestiones.create');
        Route::post('/gestiones', [GestionController::class, 'store'])->name('gestiones.store');
        Route::get('/gestiones/{gestion}', [GestionController::class, 'show'])->name('gestiones.show');
        Route::put('/gestiones/{gestion}/cupos/{carreraGestion}', [GestionController::class, 'actualizarCupo'])->name('gestiones.cupos.update');
        Route::get('/gestiones/{gestion}/editar', [GestionController::class, 'edit'])->name('gestiones.edit');
        Route::put('/gestiones/{gestion}', [GestionController::class, 'update'])->name('gestiones.update');
        Route::post('/gestiones/{gestion}/cerrar', [GestionController::class, 'cerrar'])->name('gestiones.cerrar');

        // Grupos
        Route::get('/grupos', [GrupoController::class, 'index'])->name('grupos.index');
        Route::get('/grupos/crear', [GrupoController::class, 'create'])->name('grupos.create');
        Route::post('/grupos', [GrupoController::class, 'store'])->name('grupos.store');
        Route::post('/grupos/calcular', [GrupoController::class, 'calcularNecesarios'])->name('grupos.calcular');
        Route::post('/grupos/asignar', [GrupoController::class, 'asignarPostulantes'])->name('grupos.asignar');
        Route::get('/grupos/{grupo}/editar', [GrupoController::class, 'edit'])->name('grupos.edit');
        Route::put('/grupos/{grupo}', [GrupoController::class, 'update'])->name('grupos.update');
        Route::delete('/grupos/{grupo}', [GrupoController::class, 'destroy'])->name('grupos.destroy');

        // Docentes
        Route::get('/docentes', [DocenteController::class, 'index'])->name('docentes.index');
        Route::get('/docentes/crear', [DocenteController::class, 'create'])->name('docentes.create');
        Route::post('/docentes', [DocenteController::class, 'store'])->name('docentes.store');
        Route::get('/docentes/{docente}', [DocenteController::class, 'show'])->name('docentes.show');
        Route::get('/docentes/{docente}/editar', [DocenteController::class, 'edit'])->name('docentes.edit');
        Route::put('/docentes/{docente}', [DocenteController::class, 'update'])->name('docentes.update');
        Route::delete('/docentes/{docente}', [DocenteController::class, 'destroy'])->name('docentes.destroy');

        // Asignaciones académicas
        Route::get('/asignaciones', [AsignacionController::class, 'index'])->name('asignaciones.index');
        Route::get('/asignaciones/crear', [AsignacionController::class, 'create'])->name('asignaciones.create');
        Route::post('/asignaciones', [AsignacionController::class, 'store'])->name('asignaciones.store');
        Route::get('/asignaciones/verificar-horario', [AsignacionController::class, 'verificarHorario'])->name('asignaciones.verificar-horario');
        Route::delete('/asignaciones/{asignacion}', [AsignacionController::class, 'destroy'])->name('asignaciones.destroy');

        // Clases programadas
        Route::get('/clases', [ClaseProgramadaController::class, 'index'])->name('clases.index');
        Route::post('/clases/generar', [ClaseProgramadaController::class, 'generar'])->name('clases.generar');
        Route::delete('/clases/limpiar', [ClaseProgramadaController::class, 'limpiar'])->name('clases.limpiar');

        // Aulas
        Route::get('/aulas', [AulaController::class, 'index'])->name('aulas.index');
        Route::get('/aulas/crear', [AulaController::class, 'create'])->name('aulas.create');
        Route::post('/aulas', [AulaController::class, 'store'])->name('aulas.store');
        Route::get('/aulas/{aula}/editar', [AulaController::class, 'edit'])->name('aulas.edit');
        Route::put('/aulas/{aula}', [AulaController::class, 'update'])->name('aulas.update');

        // Carga masiva
        Route::get('/carga-masiva', [CargaMasivaController::class, 'index'])->name('carga-masiva.index');
        Route::get('/carga-masiva/plantilla/{tipo}', [CargaMasivaController::class, 'descargarPlantilla'])->name('carga-masiva.plantilla');
        Route::post('/carga-masiva', [CargaMasivaController::class, 'subir'])->name('carga-masiva.subir');
        Route::get('/carga-masiva/{lote}', [CargaMasivaController::class, 'verResultado'])->name('carga-masiva.resultado');

        // Configuración
        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion');
        Route::post('/configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');
    });
