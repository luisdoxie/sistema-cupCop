<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Admision;
use App\Models\Gestion;
use App\Models\Grupo;

class DashboardController extends Controller
{
    public function index()
    {
        $gestionActiva = Gestion::where('estado', 'activo')->first();

        $stats = [
            'total_postulantes'  => 0,
            'grupos_habilitados' => 0,
            'admitidos'          => 0,
        ];

        if ($gestionActiva) {
            $admisiones = Admision::where('id_gestion', $gestionActiva->id);
            $stats['total_postulantes']  = $admisiones->count();
            $stats['grupos_habilitados'] = Grupo::where('id_gestion', $gestionActiva->id)->where('estado', 'activo')->count();
            $stats['admitidos']          = (clone $admisiones)->whereIn('estado', ['admitido_carrera1', 'admitido_carrera2'])->count();
        }

        return view('coordinador.dashboard', compact('gestionActiva', 'stats'));
    }
}
