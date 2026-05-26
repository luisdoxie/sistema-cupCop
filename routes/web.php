<?php

use App\Http\Controllers\Admin\AsignacionController;
use App\Http\Controllers\Admin\ReporteAsistenciaController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\AulaController;
use App\Http\Controllers\Admin\CargaMasivaController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\DocenteController;
use App\Http\Controllers\Admin\ExamenController;
use App\Http\Controllers\Admin\GestionController;
use App\Http\Controllers\Admin\GrupoController;
use App\Http\Controllers\Admin\ResultadosController;
use App\Http\Controllers\Admin\VerificacionDocumentosController;
use App\Http\Controllers\Docente\AsistenciaController as DocenteAsistencia;
use App\Http\Controllers\Docente\ClaseController;
use App\Http\Controllers\Docente\NotaController;
use App\Http\Controllers\Estudiante\AsistenciaController as EstudianteAsistencia;
use App\Http\Controllers\Coordinador\DashboardController as CoordinadorDashboard;
use App\Http\Controllers\Docente\DashboardController as DocenteDashboard;
use App\Http\Controllers\Estudiante\DashboardController as EstudianteDashboard;
use App\Http\Controllers\Inscripcion\Paso1Controller;
use App\Http\Controllers\Inscripcion\Paso2Controller;
use App\Http\Controllers\Inscripcion\Paso3Controller;
use App\Http\Controllers\Inscripcion\Paso4Controller;
use App\Http\Controllers\Inscripcion\Paso5Controller;
use App\Http\Controllers\Inscripcion\StripeWebhookController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de administrador
Route::middleware(['auth', 'rol:administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Gestiones
        Route::get('/gestiones', [GestionController::class, 'index'])->name('gestiones.index');
        Route::get('/gestiones/crear', [GestionController::class, 'create'])->name('gestiones.create');
        Route::post('/gestiones', [GestionController::class, 'store'])->name('gestiones.store');
        Route::get('/gestiones/{gestion}/editar', [GestionController::class, 'edit'])->name('gestiones.edit');
        Route::put('/gestiones/{gestion}', [GestionController::class, 'update'])->name('gestiones.update');
        Route::post('/gestiones/{gestion}/cerrar', [GestionController::class, 'cerrar'])->name('gestiones.cerrar');

        // Grupos
        Route::get('/grupos', [GrupoController::class, 'index'])->name('grupos.index');
        Route::get('/grupos/crear', [GrupoController::class, 'create'])->name('grupos.create');
        Route::post('/grupos', [GrupoController::class, 'store'])->name('grupos.store');
        Route::post('/grupos/calcular', [GrupoController::class, 'calcularNecesarios'])->name('grupos.calcular');
        Route::post('/grupos/asignar', [GrupoController::class, 'asignarPostulantes'])->name('grupos.asignar');

        // Docentes
        Route::get('/docentes', [DocenteController::class, 'index'])->name('docentes.index');
        Route::get('/docentes/crear', [DocenteController::class, 'create'])->name('docentes.create');
        Route::post('/docentes', [DocenteController::class, 'store'])->name('docentes.store');
        Route::get('/docentes/{docente}', [DocenteController::class, 'show'])->name('docentes.show');
        Route::get('/docentes/{docente}/editar', [DocenteController::class, 'edit'])->name('docentes.edit');
        Route::put('/docentes/{docente}', [DocenteController::class, 'update'])->name('docentes.update');

        // Asignaciones académicas
        Route::get('/asignaciones', [AsignacionController::class, 'index'])->name('asignaciones.index');
        Route::get('/asignaciones/crear', [AsignacionController::class, 'create'])->name('asignaciones.create');
        Route::post('/asignaciones', [AsignacionController::class, 'store'])->name('asignaciones.store');
        Route::get('/asignaciones/verificar-horario', [AsignacionController::class, 'verificarHorario'])->name('asignaciones.verificar-horario');

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

        // Exámenes
        Route::get('/examenes', [ExamenController::class, 'index'])->name('examenes.index');
        Route::get('/examenes/{grupo}', [ExamenController::class, 'porGrupo'])->name('examenes.porGrupo');
        Route::post('/grupos/{grupo}/activar', [ExamenController::class, 'activarGrupo'])->name('grupos.activar');
        Route::post('/examenes/{examen}/estado', [ExamenController::class, 'cambiarEstado'])->name('examenes.estado');

        // Reportes
        Route::get('/reportes/postulantes',        [ReporteController::class, 'postulantes'])->name('reportes.postulantes');
        Route::get('/reportes/postulantes/pdf',    [ReporteController::class, 'postulantePdf'])->name('reportes.postulantes.pdf');
        Route::get('/reportes/postulantes/excel',  [ReporteController::class, 'postulantesExcel'])->name('reportes.postulantes.excel');
        Route::get('/reportes/postulantes/csv',    [ReporteController::class, 'postulantesCSV'])->name('reportes.postulantes.csv');

        Route::get('/reportes/notas',              [ReporteController::class, 'notas'])->name('reportes.notas');
        Route::get('/reportes/notas/pdf',          [ReporteController::class, 'notasPdf'])->name('reportes.notas.pdf');
        Route::get('/reportes/notas/excel',        [ReporteController::class, 'notasExcel'])->name('reportes.notas.excel');

        Route::get('/reportes/estadisticas',       [ReporteController::class, 'estadisticas'])->name('reportes.estadisticas');
        Route::get('/reportes/estadisticas/pdf',   [ReporteController::class, 'estadisticasPdf'])->name('reportes.estadisticas.pdf');
        Route::get('/reportes/estadisticas/excel', [ReporteController::class, 'estadisticasExcel'])->name('reportes.estadisticas.excel');

        Route::get('/reportes/grupos',             [ReporteController::class, 'grupos'])->name('reportes.grupos');
        Route::get('/reportes/grupos/pdf',         [ReporteController::class, 'gruposPdf'])->name('reportes.grupos.pdf');
        Route::get('/reportes/grupos/excel',       [ReporteController::class, 'gruposExcel'])->name('reportes.grupos.excel');

        Route::get('/reportes/docentes',           [ReporteController::class, 'docentes'])->name('reportes.docentes');
        Route::get('/reportes/docentes/pdf',       [ReporteController::class, 'docentesPdf'])->name('reportes.docentes.pdf');
        Route::get('/reportes/docentes/excel',     [ReporteController::class, 'docentesExcel'])->name('reportes.docentes.excel');

        Route::get('/reportes/gestiones',          [ReporteController::class, 'gestiones'])->name('reportes.gestiones');
        Route::get('/reportes/gestiones/pdf',      [ReporteController::class, 'gestionesPdf'])->name('reportes.gestiones.pdf');
        Route::get('/reportes/gestiones/excel',    [ReporteController::class, 'gestionesExcel'])->name('reportes.gestiones.excel');
        Route::get('/reportes/gestiones/txt',      [ReporteController::class, 'gestionesTxt'])->name('reportes.gestiones.txt');

        // Reporte asistencia
        Route::get('/reportes/asistencia',         [ReporteAsistenciaController::class, 'index'])->name('reportes.asistencia');
        Route::get('/reportes/asistencia/pdf',     [ReporteAsistenciaController::class, 'pdf'])->name('reportes.asistencia.pdf');
        Route::get('/reportes/asistencia/excel',   [ReporteAsistenciaController::class, 'excel'])->name('reportes.asistencia.excel');
    });

// Rutas de documentos (coordinador + administrador)
Route::middleware(['auth', 'rol:coordinador,administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/documentos', [VerificacionDocumentosController::class, 'index'])->name('documentos.index');
        Route::get('/documentos/{admision}', [VerificacionDocumentosController::class, 'show'])->name('documentos.show');
        Route::post('/documentos/{documento}/verificar', [VerificacionDocumentosController::class, 'verificar'])->name('documentos.verificar');
        Route::post('/documentos/{documento}/rechazar', [VerificacionDocumentosController::class, 'rechazar'])->name('documentos.rechazar');

        // Resultados y admisión final
        Route::get('/resultados', [ResultadosController::class, 'index'])->name('resultados.index');
        Route::post('/resultados/procesar', [ResultadosController::class, 'procesar'])->name('resultados.procesar');
        Route::get('/resultados/exportar-pdf', [ResultadosController::class, 'exportarPdf'])->name('resultados.pdf');
    });

// Rutas de coordinador (acceso: coordinador y administrador)
Route::middleware(['auth', 'rol:coordinador,administrador'])
    ->prefix('coordinador')
    ->name('coordinador.')
    ->group(function () {
        Route::get('/dashboard', [CoordinadorDashboard::class, 'index'])->name('dashboard');
    });

// Rutas de docente
Route::middleware(['auth', 'rol:docente'])
    ->prefix('docente')
    ->name('docente.')
    ->group(function () {
        Route::get('/dashboard', [DocenteDashboard::class, 'index'])->name('dashboard');

        // Notas
        Route::get('/notas',          [NotaController::class, 'index'])->name('notas.index');
        Route::get('/notas/{grupo}',  [NotaController::class, 'planilla'])->name('notas.planilla');
        Route::post('/notas/{grupo}', [NotaController::class, 'guardar'])->name('notas.guardar');

        // Clases
        Route::get('/clases',                      [ClaseController::class, 'index'])->name('clases.index');
        Route::get('/clases/nueva',                [ClaseController::class, 'create'])->name('clases.create');
        Route::post('/clases',                     [ClaseController::class, 'store'])->name('clases.store');
        Route::patch('/clases/{clase}/estado',     [ClaseController::class, 'cambiarEstado'])->name('clases.estado');
        Route::get('/clases/verificar-aula',       [ClaseController::class, 'verificarAula'])->name('clases.verificar-aula');

        // Asistencia
        Route::get('/asistencia/{clase}',          [DocenteAsistencia::class, 'paseLista'])->name('asistencia.pase-lista');
        Route::post('/asistencia/{clase}',         [DocenteAsistencia::class, 'guardar'])->name('asistencia.guardar');
    });

// Rutas de estudiante
Route::middleware(['auth', 'rol:estudiante'])
    ->prefix('estudiante')
    ->name('estudiante.')
    ->group(function () {
        Route::get('/dashboard', [EstudianteDashboard::class, 'index'])->name('dashboard');
        Route::get('/resultados', [EstudianteDashboard::class, 'resultados'])->name('resultados');
        Route::get('/asistencia', [EstudianteAsistencia::class, 'index'])->name('asistencia');
    });

// ─── Módulo de Inscripción en 5 pasos ─────────────────────────────────────────

// Paso 1: Registro (público, sin auth)
Route::get('/inscripcion/paso/1', [Paso1Controller::class, 'create'])
    ->name('inscripcion.paso1.create');
Route::post('/inscripcion/paso/1', [Paso1Controller::class, 'store'])
    ->name('inscripcion.paso1.store');

// Pasos 2-5: protegidos con auth + middleware inscripcion
Route::middleware(['auth', 'inscripcion.step:2'])->group(function () {
    Route::get('/inscripcion/paso/2', [Paso2Controller::class, 'create'])
        ->name('inscripcion.paso2.create');
    Route::post('/inscripcion/paso/2', [Paso2Controller::class, 'store'])
        ->name('inscripcion.paso2.store');
});

Route::middleware(['auth', 'inscripcion.step:3'])->group(function () {
    Route::get('/inscripcion/paso/3', [Paso3Controller::class, 'create'])
        ->name('inscripcion.paso3.create');
    Route::post('/inscripcion/paso/3', [Paso3Controller::class, 'store'])
        ->name('inscripcion.paso3.store');
});

Route::middleware(['auth', 'inscripcion.step:4'])->group(function () {
    Route::get('/inscripcion/paso/4', [Paso4Controller::class, 'create'])
        ->name('inscripcion.paso4.create');
    Route::post('/inscripcion/paso/4', [Paso4Controller::class, 'crearSesionStripe'])
        ->name('inscripcion.paso4.pagar');
    Route::get('/inscripcion/pago/exito', [Paso4Controller::class, 'exito'])
        ->name('inscripcion.pago.exito');
    Route::get('/inscripcion/pago/cancelado', [Paso4Controller::class, 'cancelado'])
        ->name('inscripcion.pago.cancelado');
});

Route::middleware(['auth', 'inscripcion.step:5'])->group(function () {
    Route::get('/inscripcion/paso/5', [Paso5Controller::class, 'index'])
        ->name('inscripcion.paso5');
});

// Webhook de Stripe (sin CSRF, excluido en bootstrap/app.php)
Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle'])
    ->name('webhook.stripe');

// ──────────────────────────────────────────────────────────────────────────────

require __DIR__.'/auth.php';
