<?php

use App\Modules\Admision\Controllers\Paso1Controller;
use App\Modules\Admision\Controllers\Paso2Controller;
use App\Modules\Admision\Controllers\Paso3Controller;
use App\Modules\Admision\Controllers\Paso4Controller;
use App\Modules\Admision\Controllers\Paso5Controller;
use App\Modules\Admision\Controllers\ResultadosController;
use App\Modules\Admision\Controllers\StripeWebhookController;
use App\Modules\Admision\Controllers\VerificacionDocumentosController;
use Illuminate\Support\Facades\Route;

// Paso 1: Registro (público, sin auth)
Route::get('/inscripcion/paso/1', [Paso1Controller::class, 'create'])->name('inscripcion.paso1.create');
Route::post('/inscripcion/paso/1', [Paso1Controller::class, 'store'])->name('inscripcion.paso1.store');

Route::middleware(['auth', 'inscripcion.step:2'])->group(function () {
    Route::get('/inscripcion/paso/2', [Paso2Controller::class, 'create'])->name('inscripcion.paso2.create');
    Route::post('/inscripcion/paso/2', [Paso2Controller::class, 'store'])->name('inscripcion.paso2.store');
});

Route::middleware(['auth', 'inscripcion.step:3'])->group(function () {
    Route::get('/inscripcion/paso/3', [Paso3Controller::class, 'create'])->name('inscripcion.paso3.create');
    Route::post('/inscripcion/paso/3', [Paso3Controller::class, 'store'])->name('inscripcion.paso3.store');
});

Route::middleware(['auth', 'inscripcion.step:4'])->group(function () {
    Route::get('/inscripcion/paso/4', [Paso4Controller::class, 'create'])->name('inscripcion.paso4.create');
    Route::post('/inscripcion/paso/4', [Paso4Controller::class, 'crearSesionStripe'])->name('inscripcion.paso4.pagar');
    Route::get('/inscripcion/pago/exito', [Paso4Controller::class, 'exito'])->name('inscripcion.pago.exito');
    Route::get('/inscripcion/pago/cancelado', [Paso4Controller::class, 'cancelado'])->name('inscripcion.pago.cancelado');
});

Route::middleware(['auth', 'inscripcion.step:5'])->group(function () {
    Route::get('/inscripcion/paso/5', [Paso5Controller::class, 'index'])->name('inscripcion.paso5');
});

// Webhook de Stripe (sin CSRF — excluido en bootstrap/app.php)
Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle'])->name('webhook.stripe');

// Verificación de documentos y admisión final (coordinador + administrador)
Route::middleware(['auth', 'rol:coordinador,administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/documentos', [VerificacionDocumentosController::class, 'index'])->name('documentos.index');
        Route::get('/documentos/{admision}', [VerificacionDocumentosController::class, 'show'])->name('documentos.show');
        Route::post('/documentos/{documento}/verificar', [VerificacionDocumentosController::class, 'verificar'])->name('documentos.verificar');
        Route::post('/documentos/{documento}/rechazar', [VerificacionDocumentosController::class, 'rechazar'])->name('documentos.rechazar');

        Route::get('/resultados', [ResultadosController::class, 'index'])->name('resultados.index');
        Route::post('/resultados/procesar', [ResultadosController::class, 'procesar'])->name('resultados.procesar');
        Route::get('/resultados/exportar-pdf', [ResultadosController::class, 'exportarPdf'])->name('resultados.pdf');
        Route::post('/resultados/enviar-veredictos', [ResultadosController::class, 'enviarVeredictos'])->name('resultados.enviar-veredictos');
    });
