<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admision;
use App\Models\Gestion;
use App\Models\Grupo;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $gestionActiva = Gestion::where('estado', 'activo')->first();

        $stats = [
            'total_postulantes' => 0,
            'admitidos'         => 0,
            'reprobados'        => 0,
            'grupos_habilitados'=> 0,
        ];

        $chartData = [];

        if ($gestionActiva) {
            $admisiones = Admision::where('id_gestion', $gestionActiva->id);

            $stats['total_postulantes']  = $admisiones->count();
            $stats['admitidos']          = (clone $admisiones)->whereIn('estado', ['admitido_carrera1', 'admitido_carrera2'])->count();
            $stats['reprobados']         = (clone $admisiones)->where('estado', 'reprobado')->count();
            $stats['grupos_habilitados'] = Grupo::where('id_gestion', $gestionActiva->id)->where('estado', 'activo')->count();

            $chartData = [
                'inscrito'    => (clone $admisiones)->where('estado', 'inscrito')->count(),
                'cursando'    => (clone $admisiones)->where('estado', 'cursando')->count(),
                'admitidos'   => $stats['admitidos'],
                'reprobados'  => $stats['reprobados'],
                'no_admitido' => (clone $admisiones)->where('estado', 'no_admitido')->count(),
            ];
        }

        return view('admin.dashboard', compact('gestionActiva', 'stats', 'chartData'));
    }
}
