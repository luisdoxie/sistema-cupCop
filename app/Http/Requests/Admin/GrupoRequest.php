<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GrupoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'       => ['required', 'string', 'max:50'],
            'paralelo'     => ['required', 'string', 'max:10'],
            'modalidad'    => ['required', 'in:presencial,virtual'],
            'cupo_maximo'  => ['required', 'integer', 'min:1', 'max:80'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'      => 'El nombre del grupo es obligatorio.',
            'paralelo.required'    => 'El paralelo es obligatorio.',
            'modalidad.required'   => 'La modalidad es obligatoria.',
            'modalidad.in'         => 'La modalidad debe ser presencial o virtual.',
            'cupo_maximo.required' => 'El cupo máximo es obligatorio.',
            'cupo_maximo.min'      => 'El cupo mínimo es 1.',
            'cupo_maximo.max'      => 'El cupo máximo es 80.',
        ];
    }
}
