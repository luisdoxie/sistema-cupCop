<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postulación Enviada — CUP FICCT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-slate-900 text-white min-h-screen flex items-center justify-center px-6">

<div class="text-center max-w-md mx-auto">

    {{-- Ícono check --}}
    <div class="inline-flex items-center justify-center w-20 h-20 bg-green-900/60 border-2 border-green-500 rounded-full mb-8">
        <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1 class="text-3xl font-bold text-white mb-4">¡Postulación Enviada!</h1>

    @if(session('postulacion_nombre'))
    <p class="text-slate-400 mb-2 text-sm">
        Gracias, <span class="text-white font-medium">{{ session('postulacion_nombre') }}</span>.
    </p>
    @endif

    <p class="text-slate-400 text-sm mb-8 leading-relaxed">
        Tu postulación ha sido recibida correctamente. El equipo administrativo de la FICCT
        revisará tu información y las materias solicitadas. Recibirás respuesta a través del
        correo electrónico que proporcionaste.
    </p>

    <a href="{{ route('home') }}"
       class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Volver al inicio
    </a>
</div>

</body>
</html>
