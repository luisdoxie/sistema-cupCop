@component('mail::message')
# Sistema CUP — FICCT Bolivia

Estimado/a **{{ $documento->admision->estudiante->persona->nombre }} {{ $documento->admision->estudiante->persona->apellido }}**,

Lamentamos informarle que uno de sus documentos ha sido **rechazado** por el equipo de verificación.

---

**Documento:** {{ ucfirst(str_replace('_', ' ', $documento->tipo_documento)) }}

**Motivo del rechazo:**
{{ $observacion }}

---

Por favor, ingrese al sistema y suba nuevamente el documento corregido a la brevedad posible para no retrasar su proceso de admisión.

@component('mail::button', ['url' => config('app.url') . '/inscripcion/paso/3', 'color' => 'blue'])
Subir Documento Corregido
@endcomponent

Si tiene dudas, puede comunicarse con la oficina de admisiones de la FICCT.

---
**FICCT — Facultad Integral del Chaco**
Universidad Autónoma Gabriel René Moreno
Yacuiba, Bolivia | admisiones@ficct.edu.bo

@endcomponent
