@extends('layouts.admin')
@section('title', 'Reportes por Voz con IA')
@section('page-title', 'Reportes por Voz — IA Grok')

@section('content')
<div class="space-y-4" x-data="reporteVoz()">

    {{-- Panel de voz --}}
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col items-center gap-4">
            <p class="text-sm text-gray-500 text-center">Haz clic en el micrófono, habla tu consulta y confirma para ejecutarla.</p>

            {{-- Botón micrófono --}}
            <button @click="toggleMic()"
                    :class="escuchando ? 'bg-red-600 hover:bg-red-700 ring-4 ring-red-300' : 'bg-blue-600 hover:bg-blue-700'"
                    class="w-20 h-20 rounded-full text-white flex items-center justify-center transition-all shadow-lg focus:outline-none">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4M12 3a4 4 0 014 4v4a4 4 0 01-8 0V7a4 4 0 014-4z"/>
                </svg>
            </button>

            {{-- Animación de onda --}}
            <div x-show="escuchando" class="flex items-end gap-1 h-8">
                <template x-for="i in 5" :key="i">
                    <div class="w-2 bg-blue-500 rounded-full animate-pulse"
                         :style="`height: ${Math.random() * 24 + 8}px; animation-delay: ${i * 0.1}s`"></div>
                </template>
            </div>

            <p x-show="escuchando" class="text-sm text-red-600 font-medium animate-pulse">Escuchando...</p>

            {{-- Texto transcrito --}}
            <div x-show="textoTranscrito" class="w-full max-w-xl">
                <label class="block text-xs font-medium text-gray-600 mb-1">Texto reconocido (puedes editarlo):</label>
                <textarea x-model="textoTranscrito" rows="2"
                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                <div class="flex gap-2 mt-2">
                    <button @click="consultar()"
                            :disabled="cargando"
                            class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white text-sm px-4 py-1.5 rounded">
                        <span x-show="!cargando">Consultar</span>
                        <span x-show="cargando">Procesando...</span>
                    </button>
                    <button @click="textoTranscrito = ''"
                            class="text-sm text-gray-500 hover:text-gray-700 px-3 py-1.5 rounded border border-gray-300">
                        Limpiar
                    </button>
                </div>
            </div>

            {{-- Error --}}
            <div x-show="error" class="w-full max-w-xl bg-red-50 border border-red-200 rounded p-3">
                <p class="text-sm text-red-700" x-text="error"></p>
            </div>
        </div>
    </div>

    {{-- SQL generado --}}
    <div x-show="sqlGenerado" class="bg-gray-900 rounded-lg shadow p-4">
        <p class="text-xs text-gray-400 mb-1 font-mono">SQL generado por Grok:</p>
        <pre class="text-green-400 text-xs font-mono whitespace-pre-wrap" x-text="sqlGenerado"></pre>
    </div>

    {{-- Resultados --}}
    <div x-show="resultados.length > 0" class="bg-white rounded-lg shadow">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <span class="text-sm text-gray-600">
                <span x-text="resultados.length"></span> registros encontrados
            </span>
            <div class="flex gap-2">
                <button @click="exportarPdf()"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1.5 rounded">
                    PDF
                </button>
                <button @click="exportarExcel()"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1.5 rounded">
                    Excel
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <template x-for="col in columnas" :key="col">
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                x-text="col"></th>
                        </template>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="(fila, idx) in resultados" :key="idx">
                        <tr :class="idx % 2 === 0 ? 'bg-white' : 'bg-gray-50'">
                            <template x-for="col in columnas" :key="col">
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700" x-text="fila[col] ?? '—'"></td>
                            </template>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Historial --}}
    <div x-show="historial.length > 0" class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Últimas consultas de la sesión</h3>
        <div class="space-y-2">
            <template x-for="(item, idx) in historial" :key="idx">
                <div class="border border-gray-200 rounded p-3 cursor-pointer hover:bg-blue-50 transition-colors"
                     @click="cargarHistorial(item)">
                    <p class="text-sm font-medium text-gray-800" x-text="item.texto"></p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        <span x-text="item.cantidad"></span> registros
                    </p>
                </div>
            </template>
        </div>
    </div>

</div>

{{-- Formularios ocultos para exportar --}}
<form id="form-pdf" method="POST" action="{{ route('admin.reportes.voz.pdf') }}" target="_blank" style="display:none">
    @csrf
    <input type="hidden" name="sql" id="pdf-sql">
    <input type="hidden" name="texto" id="pdf-texto">
</form>
<form id="form-excel" method="POST" action="{{ route('admin.reportes.voz.excel') }}" target="_blank" style="display:none">
    @csrf
    <input type="hidden" name="sql" id="excel-sql">
</form>

@push('scripts')
<script>
function reporteVoz() {
    return {
        escuchando: false,
        textoTranscrito: '',
        sqlGenerado: '',
        resultados: [],
        columnas: [],
        historial: [],
        cargando: false,
        error: '',
        recognition: null,

        init() {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (SpeechRecognition) {
                this.recognition = new SpeechRecognition();
                this.recognition.lang = 'es-BO';
                this.recognition.continuous = false;
                this.recognition.interimResults = false;
                this.recognition.onresult = (e) => {
                    this.textoTranscrito = e.results[0][0].transcript;
                    this.escuchando = false;
                };
                this.recognition.onerror = () => { this.escuchando = false; };
                this.recognition.onend = () => { this.escuchando = false; };
            }
        },

        toggleMic() {
            if (!this.recognition) {
                this.error = 'Tu navegador no soporta Web Speech API. Usa Chrome.';
                return;
            }
            if (this.escuchando) {
                this.recognition.stop();
                this.escuchando = false;
            } else {
                this.error = '';
                this.recognition.start();
                this.escuchando = true;
            }
        },

        async consultar() {
            if (!this.textoTranscrito.trim()) return;
            this.cargando = true;
            this.error = '';
            this.resultados = [];
            this.sqlGenerado = '';
            this.columnas = [];

            try {
                const res = await fetch('{{ route("admin.reportes.voz.consultar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ texto: this.textoTranscrito }),
                });

                const json = await res.json();

                if (json.error) {
                    this.error = json.error;
                } else {
                    this.sqlGenerado = json.sql;
                    this.resultados  = json.resultados;
                    this.historial   = json.historial;
                    this.columnas    = this.resultados.length > 0 ? Object.keys(this.resultados[0]) : [];
                }
            } catch (e) {
                this.error = 'Error de conexión.';
            } finally {
                this.cargando = false;
            }
        },

        cargarHistorial(item) {
            this.textoTranscrito = item.texto;
            this.consultar();
        },

        exportarPdf() {
            document.getElementById('pdf-sql').value   = this.sqlGenerado;
            document.getElementById('pdf-texto').value = this.textoTranscrito;
            document.getElementById('form-pdf').submit();
        },

        exportarExcel() {
            document.getElementById('excel-sql').value = this.sqlGenerado;
            document.getElementById('form-excel').submit();
        },
    };
}
</script>
@endpush

@endsection
