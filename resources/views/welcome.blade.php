<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convocatoria 2025 — CUP | FICCT - UAGRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Patrón de cuadrícula tipo circuito en el hero */
        .circuit-bg {
            background-color: #0f172a;
            background-image:
                linear-gradient(rgba(59,130,246,0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59,130,246,0.07) 1px, transparent 1px),
                radial-gradient(circle at 20% 50%, rgba(37,99,235,0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(99,102,241,0.12) 0%, transparent 40%),
                radial-gradient(circle at 60% 80%, rgba(14,165,233,0.10) 0%, transparent 40%);
            background-size: 40px 40px, 40px 40px, 100% 100%, 100% 100%, 100% 100%;
        }

        /* Puntos de nodo sobre la cuadrícula */
        .circuit-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(96,165,250,0.35) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        /* Animaciones de entrada */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up    { animation: fadeUp 0.7s ease both; }
        .fade-up-d1 { animation: fadeUp 0.7s ease 0.15s both; }
        .fade-up-d2 { animation: fadeUp 0.7s ease 0.30s both; }
        .fade-up-d3 { animation: fadeUp 0.7s ease 0.45s both; }

        /* Navbar con vidrio cuando hay scroll */
        .navbar-scroll { background: rgba(15,23,42,0.95); backdrop-filter: blur(12px); }

        /* Línea de color animada en los cards de carreras */
        .career-card::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0;
            width: 0; height: 3px;
            background: linear-gradient(90deg, #3b82f6, #818cf8);
            transition: width 0.4s ease;
            border-radius: 0 0 12px 12px;
        }
        .career-card:hover::after { width: 100%; }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-slate-900 text-white"
      x-data="landingApp()"
      @keydown.escape.window="modalOpen = false">

{{-- ══════════════════════════════════════════════
     NAVBAR FIJO
══════════════════════════════════════════════ --}}
<nav id="navbar"
     class="fixed top-0 inset-x-0 z-40 transition-all duration-300"
     :class="scrolled ? 'navbar-scroll shadow-lg' : 'bg-transparent'">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

        {{-- Logo --}}
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center shadow">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/>
                </svg>
            </div>
            <div class="leading-tight">
                <span class="font-bold text-white text-sm">Sistema CUP</span>
                <span class="block text-blue-400 text-xs">FICCT · UAGRM</span>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('inscripcion.paso1.create') }}"
               class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-blue-500 text-blue-300 hover:bg-blue-900/50 text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Inscripciones
            </a>
            <button @click="modalOpen = true"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium transition shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Perfil
            </button>
        </div>
    </div>
</nav>

{{-- ══════════════════════════════════════════════
     HERO — pantalla completa con fondo circuito
══════════════════════════════════════════════ --}}
<section class="relative min-h-screen flex items-center justify-center overflow-hidden circuit-bg">
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center py-32">

        {{-- Chip "Inscripciones abiertas" --}}
        <div class="fade-up inline-flex items-center gap-2 bg-blue-900/60 border border-blue-700 rounded-full px-4 py-1.5 text-blue-300 text-xs font-medium mb-8 backdrop-blur-sm">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
            Convocatoria 2025 — Inscripciones Abiertas
        </div>

        {{-- Nombre de la facultad --}}
        <h1 class="fade-up-d1 text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-4">
            Facultad de Ingeniería en<br>
            <span class="bg-gradient-to-r from-blue-400 via-indigo-400 to-cyan-400 bg-clip-text text-transparent">
                Ciencias de la Computación
            </span><br>
            y Telecomunicaciones
        </h1>

        {{-- Universidad --}}
        <p class="fade-up-d2 text-blue-300 text-lg mb-10 font-medium">
            Universidad Autónoma Gabriel René Moreno &mdash; FICCT
        </p>

        {{-- Botones principales --}}
        <div class="fade-up-d3 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('inscripcion.paso1.create') }}"
               class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl shadow-lg shadow-blue-900/40 transition text-base">
                Inscríbete ahora
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            <a href="#descripcion"
               class="inline-flex items-center justify-center gap-2 px-8 py-3.5 border border-slate-600 hover:border-blue-500 text-slate-300 hover:text-white font-semibold rounded-xl transition text-base">
                Más información
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- Indicador de scroll --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1 text-slate-500 text-xs z-10">
        <span>Desplaza</span>
        <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>
</section>

{{-- ══════════════════════════════════════════════
     DESCRIPCIÓN DE LA FACULTAD
══════════════════════════════════════════════ --}}
<section id="descripcion" class="bg-slate-800/60 border-y border-slate-700/50">
    <div class="max-w-5xl mx-auto px-6 py-20">
        <div class="flex flex-col lg:flex-row gap-12 items-start">

            {{-- Etiqueta lateral --}}
            <div class="lg:w-48 flex-shrink-0">
                <span class="inline-block bg-blue-900/60 border border-blue-700 text-blue-400 text-xs font-semibold px-3 py-1.5 rounded-full uppercase tracking-widest">
                    Sobre el CUP
                </span>
                <div class="mt-4 w-12 h-1 bg-gradient-to-r from-blue-500 to-indigo-500 rounded"></div>
            </div>

            {{-- Texto institucional --}}
            <div class="flex-1 space-y-5 text-slate-300 leading-relaxed">
                <p>
                    La <strong class="text-white">Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones</strong>
                    de la Universidad Autónoma Gabriel René Moreno comunica a la población interesada el lanzamiento oficial de la
                    <strong class="text-blue-400">Convocatoria 2025 del Curso Universitario Preparatorio (CUP)</strong>, dirigido a
                    quienes aspiran a formarse en las carreras de Ingeniería Informática, Ingeniería en Sistemas,
                    Ingeniería en Redes y Telecomunicaciones e Ingeniería en Robótica.
                </p>
                <p>
                    El CUP constituye un programa académico orientado a fortalecer las competencias básicas requeridas para el
                    ingreso a las ciencias de la computación y las telecomunicaciones. A través de contenidos actualizados,
                    metodología formativa y el acompañamiento de docentes con experiencia, este curso busca consolidar los
                    conocimientos indispensables para una transición adecuada hacia la educación superior en el área tecnológica.
                </p>
                <p>
                    La Facultad invita a los jóvenes que desean iniciar su formación profesional en el área tecnológica a
                    participar del proceso de inscripción, disponible conforme al cronograma y las modalidades establecidas
                    en la convocatoria oficial. La información detallada puede consultarse en las plataformas institucionales
                    de la FICCT o en sus unidades administrativas habilitadas para atención al público.
                </p>

                <a href="{{ route('inscripcion.paso1.create') }}"
                   class="inline-flex items-center gap-2 mt-2 text-blue-400 hover:text-blue-300 font-medium transition text-sm">
                    Iniciar proceso de inscripción
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
     CARRERAS DISPONIBLES
══════════════════════════════════════════════ --}}
<section class="bg-slate-900 py-20">
    <div class="max-w-6xl mx-auto px-6">

        <div class="text-center mb-14">
            <p class="text-blue-400 text-sm font-semibold uppercase tracking-widest mb-2">Oferta académica</p>
            <h2 class="text-3xl font-bold text-white">Carreras disponibles</h2>
            <p class="text-slate-400 mt-3 max-w-xl mx-auto text-sm">
                Elige la carrera que mejor se ajuste a tu perfil y objetivos profesionales.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Ingeniería Informática --}}
            <div class="career-card relative bg-slate-800 border border-slate-700 rounded-xl p-6
                        hover:border-blue-600 transition-all duration-300
                        hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-900/30 overflow-hidden">
                <div class="w-12 h-12 bg-blue-900/60 rounded-xl flex items-center justify-center mb-5 border border-blue-800">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-blue-400 bg-blue-950 px-2 py-0.5 rounded uppercase tracking-wide">INF</span>
                <h3 class="text-white font-bold mt-3 mb-2 leading-snug">Ingeniería Informática</h3>
                <p class="text-slate-400 text-xs leading-relaxed">Desarrollo de software, algoritmos y sistemas inteligentes aplicados a la industria.</p>
            </div>

            {{-- Ingeniería en Sistemas --}}
            <div class="career-card relative bg-slate-800 border border-slate-700 rounded-xl p-6
                        hover:border-indigo-600 transition-all duration-300
                        hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-900/30 overflow-hidden">
                <div class="w-12 h-12 bg-indigo-900/60 rounded-xl flex items-center justify-center mb-5 border border-indigo-800">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-indigo-400 bg-indigo-950 px-2 py-0.5 rounded uppercase tracking-wide">SI</span>
                <h3 class="text-white font-bold mt-3 mb-2 leading-snug">Ingeniería en Sistemas</h3>
                <p class="text-slate-400 text-xs leading-relaxed">Análisis, diseño e implementación de sistemas de información empresariales y corporativos.</p>
            </div>

            {{-- Redes y Telecomunicaciones --}}
            <div class="career-card relative bg-slate-800 border border-slate-700 rounded-xl p-6
                        hover:border-cyan-600 transition-all duration-300
                        hover:-translate-y-1 hover:shadow-xl hover:shadow-cyan-900/30 overflow-hidden">
                <div class="w-12 h-12 bg-cyan-900/60 rounded-xl flex items-center justify-center mb-5 border border-cyan-800">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-cyan-400 bg-cyan-950 px-2 py-0.5 rounded uppercase tracking-wide">RC</span>
                <h3 class="text-white font-bold mt-3 mb-2 leading-snug">Ingeniería en Redes y Telecomunicaciones</h3>
                <p class="text-slate-400 text-xs leading-relaxed">Infraestructura de redes, comunicaciones digitales y conectividad a escala empresarial.</p>
            </div>

            {{-- Robótica --}}
            <div class="career-card relative bg-slate-800 border border-slate-700 rounded-xl p-6
                        hover:border-violet-600 transition-all duration-300
                        hover:-translate-y-1 hover:shadow-xl hover:shadow-violet-900/30 overflow-hidden">
                <div class="w-12 h-12 bg-violet-900/60 rounded-xl flex items-center justify-center mb-5 border border-violet-800">
                    <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-violet-400 bg-violet-950 px-2 py-0.5 rounded uppercase tracking-wide">ROB</span>
                <h3 class="text-white font-bold mt-3 mb-2 leading-snug">Ingeniería en Robótica</h3>
                <p class="text-slate-400 text-xs leading-relaxed">Automatización, inteligencia artificial aplicada y diseño de sistemas robóticos avanzados.</p>
            </div>
        </div>

        {{-- CTA central --}}
        <div class="mt-14 text-center">
            <a href="{{ route('inscripcion.paso1.create') }}"
               class="inline-flex items-center gap-3 px-10 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-blue-900/40 transition text-base">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Comenzar inscripción — Convocatoria 2025
            </a>
            <p class="text-slate-500 text-xs mt-3">Proceso 100% en línea</p>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════ --}}
<footer class="bg-slate-950 border-t border-slate-800">
    <div class="max-w-6xl mx-auto px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3z"/>
                </svg>
            </div>
            <span class="text-slate-400 text-sm">
                &copy; {{ date('Y') }} <span class="text-white font-medium">Sistema CUP</span> &mdash; FICCT · UAGRM
            </span>
        </div>
        <button @click="modalOpen = true" class="text-slate-500 hover:text-blue-400 text-sm transition">
            Acceso para docentes y administrativos &rarr;
        </button>
    </div>
</footer>

{{-- ══════════════════════════════════════════════
     MODAL DE LOGIN
══════════════════════════════════════════════ --}}
<div x-cloak
     x-show="modalOpen"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @click.self="modalOpen = false">

    {{-- Fondo semitransparente --}}
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

    {{-- Tarjeta del modal --}}
    <div x-show="modalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden z-10">

        {{-- Botón cerrar --}}
        <button @click="modalOpen = false"
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition z-20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Header azul --}}
        <div class="bg-blue-900 px-8 py-7 text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-white rounded-full mb-3 shadow-lg">
                <svg class="w-8 h-8 text-blue-900" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-white">Sistema CUP</h2>
            <p class="text-blue-300 text-xs mt-1">Curso Pre-Universitarios · FICCT</p>
        </div>

        {{-- Formulario --}}
        <div class="px-8 py-7">
            <h3 class="text-lg font-semibold text-gray-800 mb-5 text-center">Iniciar Sesión</h3>

            @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3">
                <ul class="text-sm text-red-600 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (session('status'))
            <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="modal-ci" class="block text-sm font-medium text-gray-700 mb-1">
                        Carnet de Identidad (CI)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                        </span>
                        <input id="modal-ci" name="ci" type="text"
                               value="{{ old('ci') }}"
                               required autocomplete="username"
                               placeholder="Ingresa tu CI"
                               class="block w-full pl-10 pr-3 py-2.5 border {{ $errors->has('ci') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    @error('ci') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label for="modal-password" class="block text-sm font-medium text-gray-700 mb-1">
                        Contraseña
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input id="modal-password" name="password" type="password"
                               required autocomplete="current-password"
                               placeholder="••••••••"
                               class="block w-full pl-10 pr-3 py-2.5 border {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-between mb-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-600">Recordarme</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800 transition">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <button type="submit"
                        class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Iniciar Sesión
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function landingApp() {
    return {
        modalOpen: {{ ($errors->any() || session('status')) ? 'true' : 'false' }},
        scrolled: false,
        init() {
            window.addEventListener('scroll', () => {
                this.scrolled = window.scrollY > 40;
            });
        }
    }
}
</script>

{{-- ══════════════════════════════════════════════
     CHATBOT BOTPRESS
══════════════════════════════════════════════ --}}
<script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
<script>
    botpress.init({
        configUrl: "https://files.bpcontent.cloud/2026/06/13/04/20260613041314-RJDNS2HF.json"
    });
</script>

</body>
</html>
