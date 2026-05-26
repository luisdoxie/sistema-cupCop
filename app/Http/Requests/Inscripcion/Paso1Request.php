<?php

namespace App\Http\Requests\Inscripcion;

use Illuminate\Foundation\Http\FormRequest;

class Paso1Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ci'                  => ['required', 'string', 'max:20', 'unique:persona,ci'],
            'nombre'              => ['required', 'string', 'max:100'],
            'apellido'            => ['required', 'string', 'max:100'],
            'sexo'                => ['required', 'in:M,F'],
            'telefono'            => ['nullable', 'string', 'max:20'],
            'direccion'           => ['nullable', 'string', 'max:255'],
            'correo'              => ['required', 'email', 'max:150', 'unique:persona,correo'],
            'password'            => ['required', 'string', 'min:8', 'confirmed'],
            'fecha_nacimiento'    => ['required', 'date', 'before_or_equal:' . now()->subYears(16)->toDateString()],
            'colegio_procedencia' => ['required', 'string', 'max:200'],
            'ciudad'              => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'ci.required'                  => 'El número de cédula de identidad es obligatorio.',
            'ci.unique'                    => 'Ya existe una persona registrada con ese CI.',
            'ci.max'                       => 'El CI no puede exceder 20 caracteres.',
            'nombre.required'              => 'El nombre es obligatorio.',
            'apellido.required'            => 'El apellido es obligatorio.',
            'sexo.required'                => 'El sexo es obligatorio.',
            'sexo.in'                      => 'El sexo debe ser M (Masculino) o F (Femenino).',
            'correo.required'              => 'El correo electrónico es obligatorio.',
            'correo.email'                 => 'Ingrese un correo electrónico válido.',
            'correo.unique'                => 'Ya existe una cuenta con ese correo electrónico.',
            'password.required'            => 'La contraseña es obligatoria.',
            'password.min'                 => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'           => 'Las contraseñas no coinciden.',
            'fecha_nacimiento.required'    => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date'        => 'Ingrese una fecha de nacimiento válida.',
            'fecha_nacimiento.before_or_equal' => 'Debe tener al menos 16 años para inscribirse.',
            'colegio_procedencia.required' => 'El colegio de procedencia es obligatorio.',
            'ciudad.required'              => 'La ciudad es obligatoria.',
        ];
    }
}
