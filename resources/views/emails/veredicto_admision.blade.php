@component('mail::message')
# Sistema CUP — FICCT Bolivia

Estimado/a **{{ $admision->estudiante->persona->nombre }} {{ $admision->estudiante->persona->apellido }}**,

Le informamos el **resultado final** de su proceso de admisión al curso pre-universitario.

---

@php
$estado = $admision->estado;
$admitido = in_array($estado, ['admitido_carrera1', 'admitido_carrera2']);
@endphp

@if($admitido)
@component('mail::panel')
## ¡Felicitaciones! Ha sido ADMITIDO/A

- **Gestión:** {{ $admision->gestion->nombre ?? 'N/A' }}
- **Carrera asignada:** {{ $estado === 'admitido_carrera1' ? ($admision->carrera1->nombre ?? 'Carrera 1') : ($admision->carrera2->nombre ?? 'Carrera 2') }}
- **Promedio final:** {{ number_format($admision->promedio_final ?? 0, 2) }}
@endcomponent

Pronto recibirá más información sobre el proceso de matriculación.

@component('mail::button', ['url' => config('app.url') . '/estudiante/resultados', 'color' => 'blue'])
Ver Mis Resultados
@endcomponent

@elseif($estado === 'reprobado')
@component('mail::panel')
## Resultado: No aprobó el curso

- **Gestión:** {{ $admision->gestion->nombre ?? 'N/A' }}
- **Promedio final:** {{ number_format($admision->promedio_final ?? 0, 2) }}
- **Resultado:** Reprobado
@endcomponent

Lamentamos informarle que no alcanzó el promedio mínimo requerido. Podrá volver a postular en la siguiente gestión.

@else
@component('mail::panel')
## Resultado: Sin cupo disponible

- **Gestión:** {{ $admision->gestion->nombre ?? 'N/A' }}
- **Resultado:** Sin cupo en las carreras solicitadas
@endcomponent

Lamentamos informarle que no hubo cupo disponible en las carreras de su preferencia. Puede postular a otras carreras en la siguiente gestión.
@endif

---
**FICCT — Facultad Integral del Chaco**
Universidad Autónoma Gabriel René Moreno
Yacuiba, Bolivia | admisiones@ficct.edu.bo

@endcomponent
