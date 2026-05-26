<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Inscripción - {{ config('app.name', 'CUP') }}</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <!-- Header -->
    <header class="bg-blue-700 text-white shadow">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="text-xl font-bold">CUP &mdash; Proceso de Inscripción</span>
            </div>
            @auth
                <div class="flex items-center space-x-4 text-sm">
                    <span>{{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="underline hover:text-blue-200">Salir</button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <!-- Barra de progreso -->
    @isset($paso)
    <div class="bg-white border-b shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-5">
            <p class="text-center text-sm text-gray-500 mb-4">Paso {{ $paso }} de 5</p>
            <ol class="flex items-center justify-center space-x-2 sm:space-x-6">
                @foreach ([
                    1 => 'Registro',
                    2 => 'Carreras',
                    3 => 'Documentos',
                    4 => 'Pago',
                    5 => 'Confirmacion'
                ] as $num => $label)
                    <li class="flex items-center">
                        @if($num < $paso)
                            {{-- Completado --}}
                            <span class="flex items-center justify-center w-9 h-9 rounded-full bg-green-500 text-white text-sm font-bold shadow">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                        @elseif($num === $paso)
                            {{-- Activo --}}
                            <span class="flex items-center justify-center w-9 h-9 rounded-full bg-blue-600 text-white text-sm font-bold shadow ring-4 ring-blue-200">
                                {{ $num }}
                            </span>
                        @else
                            {{-- Pendiente --}}
                            <span class="flex items-center justify-center w-9 h-9 rounded-full bg-gray-200 text-gray-500 text-sm font-bold">
                                {{ $num }}
                            </span>
                        @endif
                        <span class="hidden sm:block ml-2 text-xs {{ $num === $paso ? 'text-blue-700 font-semibold' : ($num < $paso ? 'text-green-600' : 'text-gray-400') }}">
                            {{ $label }}
                        </span>
                        @if($num < 5)
                            <div class="hidden sm:block w-8 h-0.5 mx-2 {{ $num < $paso ? 'bg-green-400' : 'bg-gray-200' }}"></div>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
    @endisset

    <!-- Mensajes flash -->
    <div class="max-w-4xl mx-auto px-4 mt-4 space-y-2">
        @if(session('success'))
            <div class="bg-green-50 border border-green-300 text-green-800 rounded-lg px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-300 text-red-800 rounded-lg px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif
        @if(session('info'))
            <div class="bg-blue-50 border border-blue-300 text-blue-800 rounded-lg px-4 py-3 text-sm">
                {{ session('info') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg px-4 py-3 text-sm">
                {{ session('warning') }}
            </div>
        @endif
    </div>

    <!-- Contenido principal -->
    <main class="max-w-4xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-4xl mx-auto px-4 py-4 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} Centro Universitario Politecnico &mdash; Sistema de Inscripcion
        </div>
    </footer>

</body>
</html>
