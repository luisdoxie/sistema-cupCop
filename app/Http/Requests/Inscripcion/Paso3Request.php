<?php

namespace App\Http\Requests\Inscripcion;

use Illuminate\Foundation\Http\FormRequest;

class Paso3Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'certificado_nacimiento' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'fotocopia_carnet'       => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'libreta_colegio'        => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'titulo_bachiller'       => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'certificado_nacimiento.required' => 'El certificado de nacimiento es obligatorio.',
            'certificado_nacimiento.file'     => 'El certificado de nacimiento debe ser un archivo.',
            'certificado_nacimiento.mimes'    => 'El certificado de nacimiento debe ser PDF, JPG o PNG.',
            'certificado_nacimiento.max'      => 'El certificado de nacimiento no debe superar 5MB.',
            'fotocopia_carnet.required'       => 'La fotocopia del carnet de identidad es obligatoria.',
            'fotocopia_carnet.file'           => 'La fotocopia del carnet debe ser un archivo.',
            'fotocopia_carnet.mimes'          => 'La fotocopia del carnet debe ser PDF, JPG o PNG.',
            'fotocopia_carnet.max'            => 'La fotocopia del carnet no debe superar 5MB.',
            'libreta_colegio.required'        => 'La libreta del colegio es obligatoria.',
            'libreta_colegio.file'            => 'La libreta del colegio debe ser un archivo.',
            'libreta_colegio.mimes'           => 'La libreta del colegio debe ser PDF, JPG o PNG.',
            'libreta_colegio.max'             => 'La libreta del colegio no debe superar 5MB.',
            'titulo_bachiller.file'           => 'El título de bachiller debe ser un archivo.',
            'titulo_bachiller.mimes'          => 'El título de bachiller debe ser PDF, JPG o PNG.',
            'titulo_bachiller.max'            => 'El título de bachiller no debe superar 5MB.',
        ];
    }
}
