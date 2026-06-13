<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') return;

        // Fix turnos: B → Tarde, E/F → Noche
        $turnos     = DB::table('turno')->pluck('id', 'nombre');
        $turnoTarde = $turnos['Tarde'] ?? null;
        $turnoNoche = $turnos['Noche'] ?? null;

        if ($turnoTarde) {
            DB::statement("
                UPDATE materia_grupo SET id_turno = ?
                WHERE id_grupo IN (SELECT id FROM grupo WHERE paralelo = 'B')
            ", [$turnoTarde]);
        }
        if ($turnoNoche) {
            DB::statement("
                UPDATE materia_grupo SET id_turno = ?
                WHERE id_grupo IN (SELECT id FROM grupo WHERE paralelo IN ('E','F'))
            ", [$turnoNoche]);
        }

        // Fix 450 personas Demo creadas por migración 000007 antes del fix de nombres
        $nombresM  = ['Juan','Luis','Carlos','Pedro','Miguel','Diego','Marco','Pablo','Oscar','Raul','Victor','Ruben','Andres','Felipe','Jorge'];
        $nombresF  = ['Maria','Ana','Laura','Sofia','Diana','Rosa','Claudia','Elena','Isabel','Carla','Fanny','Wendy','Valeria','Paola','Sandra'];
        $apellidos = ['Garcia','Lopez','Martinez','Rodriguez','Gomez','Perez','Sanchez','Torres','Ramirez','Flores','Condori','Mamani','Quispe','Torrez','Copa','Vargas','Mendoza','Rojas','Salazar','Cruz'];

        $personas = DB::table('persona')
            ->where('nombre', 'Demo')
            ->where('apellido', 'like', 'Est%')
            ->orderBy('id')
            ->get(['id', 'ci']);

        foreach ($personas as $i => $p) {
            $sexo   = ($i % 2 === 0) ? 'M' : 'F';
            $nom    = $sexo === 'M' ? $nombresM[$i % count($nombresM)] : $nombresF[$i % count($nombresF)];
            $ape1   = $apellidos[$i % count($apellidos)];
            $ape2   = $apellidos[($i + 7) % count($apellidos)];
            $correo = strtolower($nom . '.' . $ape1 . $p->ci) . '@ficct.edu.bo';

            DB::table('persona')->where('id', $p->id)->update([
                'nombre'   => $nom,
                'apellido' => $ape1 . ' ' . $ape2,
                'sexo'     => $sexo,
                'correo'   => $correo,
            ]);
        }
    }

    public function down(): void {}
};
