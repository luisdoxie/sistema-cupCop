<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfigSistema;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $configs = ConfigSistema::all();

        return view('admin.configuracion.index', compact('configs'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'configs'   => 'required|array',
            'configs.*' => 'nullable|string|max:255',
        ]);

        foreach ($data['configs'] as $clave => $valor) {
            ConfigSistema::where('clave', $clave)->update(['valor' => $valor]);
        }

        return redirect()->route('admin.configuracion')
            ->with('success', 'Configuración actualizada exitosamente.');
    }
}
