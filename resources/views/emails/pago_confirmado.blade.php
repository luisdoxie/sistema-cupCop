@component('mail::message')
# Sistema CUP — FICCT Bolivia

Estimado/a **{{ $admision->estudiante->persona->nombre }} {{ $admision->estudiante->persona->apellido }}**,

¡Su pago ha sido **confirmado exitosamente**! Ya puede continuar con el proceso de inscripción.

---

@component('mail::panel')
**Detalles de su inscripción**

- **Gestión:** {{ $admision->gestion->nombre ?? 'N/A' }}
- **Carrera 1:** {{ $admision->carrera1->nombre ?? 'N/A' }}
- **Carrera 2:** {{ $admision->carrera2->nombre ?? 'N/A' }}
- **Estado:** Pago confirmado ✓
@endcomponent

El siguiente paso es completar la verificación de sus documentos. Nuestro equipo los revisará a la brevedad.

@component('mail::button', ['url' => config('app.url'), 'color' => 'blue'])
Ingresar al Sistema
@endcomponent

---
**FICCT — Facultad Integral del Chaco**
Universidad Autónoma Gabriel René Moreno
Yacuiba, Bolivia | admisiones@ficct.edu.bo

@endcomponent
