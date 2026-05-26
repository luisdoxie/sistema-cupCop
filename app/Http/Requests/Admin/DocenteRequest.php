<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $docenteId = $this->route('docente') ? $this->route('docente')->id : null;
        $personaId = $this->route('docente') ? $this->route('docente')->id_persona : null;

        $ciRule = $docenteId
            ? ['required', 'string', 'max:20', Rule::unique('persona', 'ci')->ignore($personaId)]
            : ['required', 'string', 'max:20', 'unique:persona,ci'];

        return [
            'ci'                    => $ciRule,
            'nombre'                => ['required', 'string', 'max:100'],
            'apellido'              => ['required', 'string', 'max:100'],
            'sexo'                  => ['required', 'in:M,F'],
            'correo'                => ['nullable', 'email', 'max:150'],
            'telefono'              => ['nullable', 'string', 'max:20'],
            'direccion'             => ['nullable', 'string', 'max:255'],
            'especialidad'          => ['required', 'string', 'max:150'],
            'grado_academico'       => ['required', 'string', 'max:100'],
            'diplomado_educacion'   => ['required', 'accepted'],
            'anios_experiencia'     => ['required', 'integer', 'min:4'],
            'max_grupos'            => ['required', 'integer', 'min:1', 'max:10'],
            'estado'                => ['required', 'in:activo,inactivo'],
        ];
    }

    public function messages(): array
    {
        return [
            'ci.required'                  => 'El CI es obligatorio.',
            'ci.unique'                    => 'Ya existe una persona con ese CI.',
            'nombre.required'              => 'El nombre es obligatorio.',
            'apellido.required'            => 'El apellido es obligatorio.',
            'sexo.required'                => 'El sexo es obligatorio.',
            'especialidad.required'        => 'La especialidad es obligatoria.',
            'grado_academico.required'     => 'El grado académico es obligatorio.',
            'diplomado_educacion.required' => 'El campo diplomado es obligatorio.',
            'diplomado_educacion.accepted' => 'El docente debe tener diplomado en educación.',
            'anios_experiencia.required'   => 'Los años de experiencia son obligatorios.',
            'anios_experiencia.min'        => 'Se requieren mínimo 4 años de experiencia.',
            'max_grupos.required'          => 'El máximo de grupos es obligatorio.',
            'max_grupos.min'               => 'El mínimo es 1 grupo.',
            'max_grupos.max'               => 'El máximo es 10 grupos.',
        ];
    }
}
