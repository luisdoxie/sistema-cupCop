@component('mail::message')
# Sistema CUP — FICCT Bolivia

Estimado/a **{{ $admision->estudiante->persona->nombre }} {{ $admision->estudiante->persona->apellido }}**,

¡Felicitaciones! Ha sido asignado/a a un **grupo de estudio** para el curso pre-universitario.

---

@component('mail::panel')
**Información de su grupo**

- **Gestión:** {{ $admision->gestion->nombre ?? 'N/A' }}
- **Grupo:** {{ $admision->grupo->nombre ?? 'N/A' }}
- **Estado:** Cursando ✓
@endcomponent

Ingrese al sistema para consultar su horario de clases y toda la información de su grupo.

@component('mail::button', ['url' => config('app.url') . '/estudiante/dashboard', 'color' => 'blue'])
Ver Mi Horario
@endcomponent

Le deseamos mucho éxito en el curso pre-universitario.

---
**FICCT — Facultad Integral del Chaco**
Universidad Autónoma Gabriel René Moreno
Yacuiba, Bolivia | admisiones@ficct.edu.bo

@endcomponent
