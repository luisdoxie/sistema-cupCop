<?php

namespace App\Http\Requests\Inscripcion;

use App\Models\CarreraGestion;
use App\Models\Gestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class Paso2Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_carrera1' => ['required', 'integer', 'exists:carrera,id'],
            'id_carrera2' => ['required', 'integer', 'exists:carrera,id', 'different:id_carrera1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $gestion = Gestion::where('estado', 'activo')->first();

            if (! $gestion) {
                $v->errors()->add('id_carrera1', 'No hay una gestión activa disponible para inscripción.');
                return;
            }

            $carrera1Id = $this->input('id_carrera1');
            $carrera2Id = $this->input('id_carrera2');

            $cg1 = CarreraGestion::where('id_gestion', $gestion->id)
                ->where('id_carrera', $carrera1Id)
                ->where('cupo_disponible', '>', 0)
                ->first();

            if (! $cg1) {
                $v->errors()->add('id_carrera1', 'La primera carrera seleccionada no tiene cupos disponibles.');
            }

            $cg2 = CarreraGestion::where('id_gestion', $gestion->id)
                ->where('id_carrera', $carrera2Id)
                ->where('cupo_disponible', '>', 0)
                ->first();

            if (! $cg2) {
                $v->errors()->add('id_carrera2', 'La segunda carrera seleccionada no tiene cupos disponibles.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'id_carrera1.required'   => 'Debe seleccionar la primera carrera.',
            'id_carrera1.exists'     => 'La primera carrera seleccionada no es válida.',
            'id_carrera2.required'   => 'Debe seleccionar la segunda carrera.',
            'id_carrera2.exists'     => 'La segunda carrera seleccionada no es válida.',
            'id_carrera2.different'  => 'La segunda carrera debe ser diferente a la primera.',
        ];
    }
}
