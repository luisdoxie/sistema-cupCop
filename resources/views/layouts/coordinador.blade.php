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
           class="fixed lg:static inset-y-0 left-0 z-40 w-72 bg-teal-900 text-white flex flex-col transition-all duration-300 min-h-screen lg:translate-x-0">

        <div class="flex items-center justify-between p-4 border-b border-teal-700 flex-shrink-0">
            <span x-show="sidebar" class="font-bold text-lg truncate">CUP Coordinador</span>
            <button @click="sidebar = !sidebar" class="text-teal-300 hover:text-white focus:outline-none flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 py-4 overflow-y-auto">
            @php
                $items = [
                    ['route' => 'coordinador.dashboard',  'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label' => 'Dashboard'],
                    ['route' => 'admin.documentos.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Documentos'],
                    ['route' => 'admin.resultados.index', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Admisión Final'],
                ];
                $reportes = [
                    ['route' => 'admin.reportes.postulantes',  'label' => 'Postulantes'],
                    ['route' => 'admin.reportes.notas',        'label' => 'Notas'],
                    ['route' => 'admin.reportes.estadisticas', 'label' => 'Estadísticas'],
                    ['route' => 'admin.reportes.grupos',       'label' => 'Grupos'],
                    ['route' => 'admin.reportes.docentes',     'label' => 'Docentes'],
                    ['route' => 'admin.reportes.gestiones',    'label' => 'Gestiones'],
                    ['route' => 'admin.reportes.asistencia',   'label' => 'Asistencia'],
                ];
            @endphp
            @foreach($items as $item)
            <a href="{{ route($item['route']) }}"
               @click="if(isMobile) sidebar = false"
               class="flex items-center gap-3 px-4 py-2.5 hover:bg-teal-700 transition-colors {{ request()->routeIs($item['route']) ? 'bg-teal-700 border-l-4 border-teal-300' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                </svg>
                <span x-show="sidebar" class="text-sm truncate">{{ $item['label'] }}</span>
            </a>
            @endforeach

            {{-- Reportes --}}
            <div x-data="{ open: {{ request()->routeIs('admin.reportes.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="flex items-center gap-3 px-4 py-2.5 w-full hover:bg-teal-700 transition-colors {{ request()->routeIs('admin.reportes.*') ? 'bg-teal-700 border-l-4 border-teal-300' : '' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span x-show="sidebar" class="text-sm truncate flex-1 text-left">Reportes</span>
                    <svg x-show="sidebar" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open && sidebar" class="bg-teal-950">
                    @foreach($reportes as $rep)
                    <a href="{{ route($rep['route']) }}"
                       @click="if(isMobile) sidebar = false"
                       class="flex items-center gap-3 pl-10 pr-4 py-2 hover:bg-teal-800 transition-colors text-teal-200 {{ request()->routeIs($rep['route']) ? 'bg-teal-800 text-white' : '' }}">
                        <span class="text-xs">{{ $rep['label'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </nav>

        <div class="p-4 border-t border-teal-700 flex-shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 w-full text-teal-300 hover:text-white transition-colors">
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
                <h1 class="text-lg font-semibold text-gray-800 truncate">@yield('page-title', 'Panel Coordinador')</h1>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                <span class="hidden sm:block text-sm text-gray-600 truncate max-w-32">{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</span>
                <span class="bg-teal-100 text-teal-800 text-xs font-medium px-2 py-0.5 rounded whitespace-nowrap">Coordinador</span>
            </div>
        </header>
        <main class="flex-1 overflow-y-auto p-4 lg:p-6">
            @yield('content')
        </main>
    </div>
</div>
@endsection
