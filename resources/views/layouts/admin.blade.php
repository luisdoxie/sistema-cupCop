@extends('layouts.base')

@section('body')
<div class="flex min-h-screen bg-gray-100"
     x-data="{ sidebar: window.innerWidth >= 1024, isMobile: window.innerWidth < 1024 }"
     @resize.window.debounce.300ms="isMobile = window.innerWidth < 1024; if (!isMobile) sidebar = true">

    {{-- Overlay móvil --}}
    <div x-show="sidebar && isMobile"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebar = false"
         class="fixed inset-0 z-30 bg-black/50 lg:hidden"
         style="display:none"></div>

    {{-- Sidebar --}}
    <aside :class="[
               sidebar ? 'translate-x-0' : '-translate-x-full',
               sidebar && !isMobile ? 'lg:w-64' : (!sidebar && !isMobile ? 'lg:w-16 lg:translate-x-0' : 'w-72')
           ]"
           class="fixed lg:static inset-y-0 left-0 z-40 w-72 bg-blue-900 text-white flex flex-col transition-all duration-300 min-h-screen lg:translate-x-0">

        <div class="flex items-center justify-between p-4 border-b border-blue-700 flex-shrink-0">
            <span x-show="sidebar" class="font-bold text-lg truncate">CUP Admin</span>
            <button @click="sidebar = !sidebar" class="text-blue-300 hover:text-white focus:outline-none flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 py-2 overflow-y-auto">

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
               @click="if(isMobile) sidebar = false"
               class="flex items-center gap-3 px-4 py-2.5 hover:bg-blue-700 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700 border-l-4 border-blue-300' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="sidebar" class="text-sm truncate">Dashboard</span>
            </a>

            {{-- ── Gestión Académica ── --}}
            @php $openGestion = request()->routeIs('admin.gestiones.*','admin.grupos.*','admin.docentes.*','admin.aulas.*','admin.asignaciones.*'); @endphp
            <div x-data="{ open: {{ $openGestion ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="flex items-center gap-3 px-4 py-2.5 w-full hover:bg-blue-700 transition-colors {{ $openGestion ? 'bg-blue-700 border-l-4 border-blue-300' : '' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span x-show="sidebar" class="text-sm truncate flex-1 text-left">Gestión Académica</span>
                    <svg x-show="sidebar" class="w-4 h-4 flex-shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open && sidebar"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="bg-blue-950">
                    @foreach([
                        ['route'=>'admin.gestiones.index','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','label'=>'Gestiones'],
                        ['route'=>'admin.grupos.index','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','label'=>'Grupos'],
                        ['route'=>'admin.docentes.index','icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z','label'=>'Docentes'],
                        ['route'=>'admin.aulas.index','icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4','label'=>'Aulas'],
                        ['route'=>'admin.asignaciones.index','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','label'=>'Horarios'],
                    ] as $sub)
                    <a href="{{ route($sub['route']) }}"
                       @click="if(isMobile) sidebar = false"
                       class="flex items-center gap-3 pl-8 pr-4 py-2 hover:bg-blue-800 transition-colors {{ request()->routeIs($sub['route']) ? 'bg-blue-800 text-white' : 'text-blue-200' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sub['icon'] }}"/>
                        </svg>
                        <span class="text-xs">{{ $sub['label'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- ── Ejecución ── --}}
            @php $openEjecucion = request()->routeIs('admin.clases.*','admin.examenes.*'); @endphp
            <div x-data="{ open: {{ $openEjecucion ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="flex items-center gap-3 px-4 py-2.5 w-full hover:bg-blue-700 transition-colors {{ $openEjecucion ? 'bg-blue-700 border-l-4 border-blue-300' : '' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span x-show="sidebar" class="text-sm truncate flex-1 text-left">Ejecución</span>
                    <svg x-show="sidebar" class="w-4 h-4 flex-shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open && sidebar"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="bg-blue-950">
                    @foreach([
                        ['route'=>'admin.clases.index','icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z','label'=>'Clases'],
                        ['route'=>'admin.examenes.index','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01','label'=>'Exámenes'],
                    ] as $sub)
                    <a href="{{ route($sub['route']) }}"
                       @click="if(isMobile) sidebar = false"
                       class="flex items-center gap-3 pl-8 pr-4 py-2 hover:bg-blue-800 transition-colors {{ request()->routeIs($sub['route']) ? 'bg-blue-800 text-white' : 'text-blue-200' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sub['icon'] }}"/>
                        </svg>
                        <span class="text-xs">{{ $sub['label'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- ── Admisión ── --}}
            @php $openAdmision = request()->routeIs('admin.documentos.*','admin.resultados.*','admin.carga-masiva.*'); @endphp
            <div x-data="{ open: {{ $openAdmision ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="flex items-center gap-3 px-4 py-2.5 w-full hover:bg-blue-700 transition-colors {{ $openAdmision ? 'bg-blue-700 border-l-4 border-blue-300' : '' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span x-show="sidebar" class="text-sm truncate flex-1 text-left">Admisión</span>
                    <svg x-show="sidebar" class="w-4 h-4 flex-shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open && sidebar"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="bg-blue-950">
                    @foreach([
                        ['route'=>'admin.documentos.index','icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','label'=>'Documentos'],
                        ['route'=>'admin.resultados.index','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','label'=>'Admisión Final'],
                        ['route'=>'admin.carga-masiva.index','icon'=>'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12','label'=>'Carga Masiva'],
                    ] as $sub)
                    <a href="{{ route($sub['route']) }}"
                       @click="if(isMobile) sidebar = false"
                       class="flex items-center gap-3 pl-8 pr-4 py-2 hover:bg-blue-800 transition-colors {{ request()->routeIs($sub['route']) ? 'bg-blue-800 text-white' : 'text-blue-200' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sub['icon'] }}"/>
                        </svg>
                        <span class="text-xs">{{ $sub['label'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- ── Reportes ── --}}
            <div x-data="{ open: {{ request()->routeIs('admin.reportes.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="flex items-center gap-3 px-4 py-2.5 w-full hover:bg-blue-700 transition-colors {{ request()->routeIs('admin.reportes.*') ? 'bg-blue-700 border-l-4 border-blue-300' : '' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span x-show="sidebar" class="text-sm truncate flex-1 text-left">Reportes</span>
                    <svg x-show="sidebar" class="w-4 h-4 flex-shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open && sidebar"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="bg-blue-950">
                    @foreach([
                        ['route'=>'admin.reportes.postulantes', 'label'=>'Postulantes'],
                        ['route'=>'admin.reportes.notas',       'label'=>'Notas'],
                        ['route'=>'admin.reportes.estadisticas','label'=>'Estadísticas'],
                        ['route'=>'admin.reportes.grupos',      'label'=>'Grupos'],
                        ['route'=>'admin.reportes.docentes',    'label'=>'Docentes'],
                        ['route'=>'admin.reportes.gestiones',   'label'=>'Gestiones'],
                        ['route'=>'admin.reportes.asistencia',  'label'=>'Asistencia'],
                        ['route'=>'admin.reportes.voz',         'label'=>'🎙 IA por Voz'],
                    ] as $rep)
                    <a href="{{ route($rep['route']) }}"
                       @click="if(isMobile) sidebar = false"
                       class="flex items-center gap-3 pl-10 pr-4 py-2 hover:bg-blue-800 transition-colors {{ request()->routeIs($rep['route']) ? 'bg-blue-800 text-white' : 'text-blue-200' }}">
                        <span class="text-xs">{{ $rep['label'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Configuración --}}
            <a href="{{ route('admin.configuracion') }}"
               @click="if(isMobile) sidebar = false"
               class="flex items-center gap-3 px-4 py-2.5 hover:bg-blue-700 transition-colors {{ request()->routeIs('admin.configuracion') ? 'bg-blue-700 border-l-4 border-blue-300' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                </svg>
                <span x-show="sidebar" class="text-sm truncate">Configuración</span>
            </a>

        </nav>

        <div class="p-4 border-t border-blue-700 flex-shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 w-full text-blue-300 hover:text-white transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span x-show="sidebar" class="text-sm">Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Contenido principal --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="bg-white shadow-sm px-4 py-3 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <button @click="sidebar = !sidebar" class="text-gray-500 hover:text-gray-700 focus:outline-none flex-shrink-0 lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-lg font-semibold text-gray-800 truncate">@yield('page-title', 'Panel Administrativo')</h1>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                <span class="hidden sm:block text-sm text-gray-600 truncate max-w-32">{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</span>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded whitespace-nowrap">Admin</span>
            </div>
        </header>
        <main class="flex-1 overflow-y-auto p-4 lg:p-6">
            @yield('content')
        </main>
    </div>
</div>
@endsection
