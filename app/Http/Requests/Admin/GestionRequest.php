<?php

namespace App\Http\Requests\Admin;

use App\Models\Gestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $gestionId = $this->route('gestion') ? $this->route('gestion')->id : null;

        $rules = [
            'nombre'       => ['required', 'string', 'max:100', Rule::unique('gestion', 'nombre')->ignore($gestionId)],
            'anio'         => ['required', 'integer', 'min:2000', 'max:2100'],
            'semestre'     => ['required', 'in:1,2'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after:fecha_inicio'],
            'estado'       => ['required', 'in:activo,cerrado,planificado'],
        ];

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('estado') === 'activo') {
                $gestionId = $this->route('gestion') ? $this->route('gestion')->id : null;
                $existe = Gestion::where('estado', 'activo')
                    ->when($gestionId, fn($q) => $q->where('id', '!=', $gestionId))
                    ->exists();

                if ($existe) {
                    $validator->errors()->add('estado', 'Ya existe una gestión activa. Ciérrela antes de activar otra.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'nombre.required'       => 'El nombre es obligatorio.',
            'nombre.unique'         => 'Ya existe una gestión con ese nombre.',
            'anio.required'         => 'El año es obligatorio.',
            'semestre.required'     => 'El semestre es obligatorio.',
            'semestre.in'           => 'El semestre debe ser 1 o 2.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required'    => 'La fecha de fin es obligatoria.',
            'fecha_fin.after'       => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'estado.required'       => 'El estado es obligatorio.',
        ];
    }
}
