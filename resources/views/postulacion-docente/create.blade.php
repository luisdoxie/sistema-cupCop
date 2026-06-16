<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postulación Docente — CUP FICCT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-slate-900 text-white min-h-screen">

{{-- Navbar --}}
<nav class="bg-slate-950 border-b border-slate-800 px-6 py-4">
    <div class="max-w-4xl mx-auto flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3z"/>
                </svg>
            </div>
            <div class="leading-tight">
                <span class="font-bold text-white text-sm">Sistema CUP</span>
                <span class="block text-blue-400 text-xs">FICCT · UAGRM</span>
            </div>
        </a>
        <a href="{{ route('home') }}" class="text-slate-400 hover:text-white text-sm transition">
            ← Volver al inicio
        </a>
    </div>
</nav>

{{-- Contenido --}}
<div class="max-w-3xl mx-auto px-6 py-12">

    {{-- Header --}}
    <div class="text-center mb-10">
        <div class="inline-flex items-center gap-2 bg-green-900/60 border border-green-700 rounded-full px-4 py-1.5 text-green-300 text-xs font-medium mb-6">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
            Convocatoria Docente 2026 — Abierta
        </div>
        <h1 class="text-3xl font-extrabold text-white mb-3">Postulación Docente</h1>
        <p class="text-slate-400 text-sm max-w-xl mx-auto">
            Completa el formulario para postularte como docente del CUP FICCT. El equipo administrativo
            revisará tu postulación y se pondrá en contacto contigo.
        </p>
    </div>

    {{-- Errores --}}
    @if($errors->any())
    <div class="bg-red-900/50 border border-red-700 rounded-xl p-4 mb-6">
        <ul class="text-sm text-red-300 space-y-1">
            @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Formulario --}}
    <form method="POST" action="{{ route('postulacion-docente.store') }}" enctype="multipart/form-data"
          class="bg-slate-800 border border-slate-700 rounded-2xl p-8 space-y-8">
        @csrf

        {{-- Datos personales --}}
        <div>
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-5 pb-2 border-b border-slate-700">
                Datos Personales
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">
                        Carnet de Identidad <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="ci" value="{{ old('ci') }}" required
                           class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                           placeholder="Ej: 12345678">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">
                        Sexo <span class="text-red-400">*</span>
                    </label>
                    <select name="sexo" required
                            class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">Seleccionar</option>
                        <option value="M" {{ old('sexo') === 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('sexo') === 'F' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">
                        Nombre(s) <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required
                           class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">
                        Apellido(s) <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="apellido" value="{{ old('apellido') }}" required
                           class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Correo Electrónico</label>
                    <input type="email" name="correo" value="{{ old('correo') }}"
                           class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                           placeholder="ejemplo@correo.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}"
                           class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                           placeholder="+591 ...">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion') }}"
                           class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

            </div>
        </div>

        {{-- Perfil profesional --}}
        <div>
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-5 pb-2 border-b border-slate-700">
                Perfil Profesional
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Grado Académico</label>
                    <input type="text" name="grado_academico" value="{{ old('grado_academico') }}"
                           class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                           placeholder="Ej: Licenciatura en Informática, Ingeniería en Sistemas...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">
                        Años de Experiencia Docente <span class="text-red-400">*</span>
                    </label>
                    <input type="number" name="anios_experiencia" value="{{ old('anios_experiencia', 4) }}"
                           min="4" required
                           class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <p class="text-xs text-slate-500 mt-1">Mínimo 4 años requeridos</p>
                </div>

                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="diplomado_educacion" value="1"
                               {{ old('diplomado_educacion') ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-slate-500 bg-slate-700 text-blue-500 focus:ring-blue-500">
                        <span class="text-sm text-slate-300">Diplomado en Educación Superior</span>
                    </label>
                </div>

            </div>
        </div>

        {{-- Materias --}}
        <div>
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-2 pb-2 border-b border-slate-700">
                Materias que puede impartir <span class="text-red-400">*</span>
            </h2>
            <p class="text-xs text-slate-500 mb-4">Selecciona una o más materias en las que tienes formación y experiencia.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($materias as $materia)
                @php $checked = in_array($materia->id, old('materias', [])); @endphp
                <label class="flex items-center gap-3 bg-slate-700/50 border rounded-xl p-4 cursor-pointer hover:border-blue-500 transition-colors
                              {{ $checked ? 'border-blue-500 bg-blue-900/20' : 'border-slate-600' }}">
                    <input type="checkbox" name="materias[]" value="{{ $materia->id }}"
                           {{ $checked ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-slate-500 bg-slate-700 text-blue-500 focus:ring-blue-500">
                    <div>
                        <p class="text-white text-sm font-medium">{{ $materia->nombre }}</p>
                        <p class="text-slate-400 text-xs">{{ $materia->sigla }}</p>
                    </div>
                </label>
                @endforeach
            </div>
            @error('materias')
            <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- CV --}}
        <div>
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-5 pb-2 border-b border-slate-700">
                Documentación
            </h2>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Curriculum Vitae
                    <span class="text-slate-500 font-normal">(PDF, DOC o DOCX — máx. 5 MB)</span>
                </label>
                <input type="file" name="cv" accept=".pdf,.doc,.docx"
                       class="block w-full text-sm text-slate-400
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-lg file:border-0
                              file:text-sm file:font-medium
                              file:bg-blue-600 file:text-white
                              hover:file:bg-blue-500 file:cursor-pointer">
                <p class="text-xs text-slate-500 mt-1">Opcional, pero recomendado para agilizar la revisión.</p>
            </div>
        </div>

        {{-- Enviar --}}
        <div class="pt-2">
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-6 rounded-xl transition-colors shadow-lg shadow-blue-900/40">
                Enviar Postulación
            </button>
            <p class="text-center text-slate-500 text-xs mt-3">
                Una vez enviada, el equipo administrativo revisará tu postulación y se pondrá en contacto contigo.
            </p>
        </div>

    </form>
</div>

</body>
</html>
