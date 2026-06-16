<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    protected array $modules = [
        'Seguridad',
        'Admision',
        'AdministracionAcademica',
        'EjecucionAcademica',
        'Inteligencia',
        'PostulacionDocente',
    ];

    public function boot(): void
    {
        foreach ($this->modules as $module) {
            $routeFile = app_path("Modules/{$module}/Routes/web.php");
            if (file_exists($routeFile)) {
                Route::middleware('web')->group($routeFile);
            }
        }
    }
}
