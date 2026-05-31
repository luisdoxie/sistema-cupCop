<x-mail::message>
# Confirmacion de Inscripcion

Estimado/a **{{ $admision->estudiante->persona->nombre ?? 'Estudiante' }} {{ $admision->estudiante->persona->apellido ?? '' }}**,

Su inscripcion ha sido registrada correctamente en el **Curso Pre-Universitarios (CUP)**.

## Detalles de su Admision

| Campo | Valor |
|-------|-------|
| **N de Admision** | #{{ $admision->id }} |
| **Estado** | {{ ucfirst(str_replace('_', ' ', $admision->estado)) }} |
| **Gestion** | {{ $admision->gestion->nombre ?? 'N/A' }} |
| **Fecha de Inscripcion** | {{ \Carbon\Carbon::parse($admision->fecha)->format('d/m/Y') }} |

## Carreras Seleccionadas

- **1ra opcion:** {{ $admision->carrera1->nombre ?? 'N/A' }} ({{ $admision->carrera1->sigla ?? '' }})
- **2da opcion:** {{ $admision->carrera2->nombre ?? 'N/A' }} ({{ $admision->carrera2->sigla ?? '' }})

@if($admision->pago && $admision->pago->estado_pago === 'completado')
## Pago Registrado

| Campo | Valor |
|-------|-------|
| **Monto** | ${{ number_format($admision->pago->monto, 2) }} USD |
| **Estado del pago** | Completado |
| **Referencia** | {{ $admision->pago->referencia_transaccion }} |
| **Fecha de pago** | {{ \Carbon\Carbon::parse($admision->pago->fecha_pago)->format('d/m/Y H:i') }} |
@endif

<x-mail::button :url="config('app.url')" color="blue">
Ir al Portal
</x-mail::button>

Si tiene alguna consulta, comuniquese con la oficina de admisiones.

Atentamente,
{{ config('app.name') }}
</x-mail::message>
