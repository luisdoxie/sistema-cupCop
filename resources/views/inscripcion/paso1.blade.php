@extends('layouts.inscripcion')

@section('content')
<div class="bg-white rounded-2xl shadow-md p-8 max-w-2xl mx-auto"
     x-data="{
         password: '',
         confirmPassword: '',
         get passwordsMatch() {
             return this.password.length === 0 || this.confirmPassword.length === 0 || this.password === this.confirmPassword;
         }
     }">

    <h1 class="text-2xl font-bold text-gray-800 mb-1">Crear cuenta de estudiante</h1>
    <p class="text-gray-500 text-sm mb-6">Complete sus datos personales para iniciar el proceso de inscripcion.</p>

    @if($errors->any())
        <div class="bg-red-50 border border-red-300 rounded-lg p-4 mb-6">
            <p class="font-semibold text-red-700 text-sm mb-2">Corrija los siguientes errores:</p>
            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('inscripcion.paso1.store') }}" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <!-- CI -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cédula de Identidad <span class="text-red-500">*</span></label>
                <input type="text" name="ci" value="{{ old('ci') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('ci') border-red-400 @enderror"
                       placeholder="Ej: 12345678">
                @error('ci') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Sexo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sexo <span class="text-red-500">*</span></label>
                <select name="sexo" required
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('sexo') border-red-400 @enderror">
                    <option value="">-- Seleccione --</option>
                    <option value="M" {{ old('sexo') === 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('sexo') === 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
                @error('sexo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Nombre -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre(s) <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('nombre') border-red-400 @enderror"
                       placeholder="Ej: Juan Carlos">
                @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Apellido -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Apellido(s) <span class="text-red-500">*</span></label>
                <input type="text" name="apellido" value="{{ old('apellido') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('apellido') border-red-400 @enderror"
                       placeholder="Ej: Perez Lopez">
                @error('apellido') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Correo -->
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo electronico <span class="text-red-500">*</span></label>
                <input type="email" name="correo" value="{{ old('correo') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('correo') border-red-400 @enderror"
                       placeholder="ejemplo@correo.com">
                @error('correo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Telefono -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefono</label>
                <input type="text" name="telefono" value="{{ old('telefono') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       placeholder="Ej: 70000000">
            </div>

            <!-- Ciudad -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad <span class="text-red-500">*</span></label>
                <input type="text" name="ciudad" value="{{ old('ciudad') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('ciudad') border-red-400 @enderror"
                       placeholder="Ej: La Paz">
                @error('ciudad') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Direccion -->
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Direccion</label>
                <input type="text" name="direccion" value="{{ old('direccion') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       placeholder="Ej: Av. Principal N 123">
            </div>

            <!-- Fecha de nacimiento -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de nacimiento <span class="text-red-500">*</span></label>
                <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required
                       max="{{ now()->subYears(16)->toDateString() }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('fecha_nacimiento') border-red-400 @enderror">
                @error('fecha_nacimiento') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Colegio -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Colegio de procedencia <span class="text-red-500">*</span></label>
                <input type="text" name="colegio_procedencia" value="{{ old('colegio_procedencia') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('colegio_procedencia') border-red-400 @enderror"
                       placeholder="Nombre del colegio">
                @error('colegio_procedencia') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Titulo Bachiller -->
            <div class="sm:col-span-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="titulo_bachiller" value="1"
                           {{ old('titulo_bachiller') ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Tengo título de bachiller</span>
                </label>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contrasena <span class="text-red-500">*</span></label>
                <input type="password" name="password" required minlength="8"
                       x-model="password"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('password') border-red-400 @enderror"
                       placeholder="Minimo 8 caracteres">
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Confirmar Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contrasena <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" required
                       x-model="confirmPassword"
                       :class="{'border-red-400': !passwordsMatch && confirmPassword.length > 0, 'border-green-400': passwordsMatch && confirmPassword.length > 0}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       placeholder="Repita la contrasena">
                <p x-show="!passwordsMatch && confirmPassword.length > 0"
                   class="text-red-500 text-xs mt-1">Las contrasenas no coinciden.</p>
                <p x-show="passwordsMatch && confirmPassword.length > 0"
                   class="text-green-600 text-xs mt-1">Las contrasenas coinciden.</p>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit"
                    :disabled="!passwordsMatch && confirmPassword.length > 0"
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold py-3 rounded-lg transition-colors">
                Continuar al Paso 2 &rarr;
            </button>
        </div>

        <p class="text-center text-sm text-gray-500">
            &iquest;Ya tiene cuenta?
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Iniciar sesion</a>
        </p>
    </form>
</div>
@endsection
