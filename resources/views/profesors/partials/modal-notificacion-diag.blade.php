<div id="diagnosticModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 backdrop-blur-sm fade-in" data-backdrop="static" role="dialog" aria-modal="true">
    <div class="bg-gray-900/90 backdrop-blur-md border border-white/10 rounded-lg shadow-2xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-emerald-500/10 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">¡Informe de Diagnóstico Disponible!</h3>
                    <p class="text-xs text-emerald-400">Reportes académicos para tus secciones</p>
                </div>
            </div>
            <button type="button" onclick="cerrarModal()" class="p-1.5 bg-white/10 hover:bg-red-500/20 rounded-lg text-gray-400 hover:text-red-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5 space-y-5">

            {{-- Info Message --}}
            <div class="flex items-start space-x-3 bg-emerald-500/5 border border-emerald-500/10 rounded-lg p-4">
                <svg class="w-5 h-5 text-emerald-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm text-emerald-300 font-medium">Estimado Profesor Tutor</p>
                    <p class="text-sm text-gray-400 mt-1">
                        Se han generado informes de diagnóstico académico para las secciones que usted guía.
                    </p>
                </div>
            </div>

            {{-- Last Report --}}
            @if(isset($ultimoReporte) && $ultimoReporte)
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">Último Informe</p>
                    <div class="bg-white/5 border border-white/10 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-400">Diagnóstico:</span>
                            <span class="text-sm text-white font-medium">{{ $ultimoReporte->diagMain?->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-400">Sección:</span>
                            <span class="text-sm text-white font-medium">
                                {{ $ultimoReporte->section?->grado?->name ?? '?' }} - Sección {{ $ultimoReporte->section?->name ?? '?' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-400">Generado:</span>
                            <span class="text-sm text-white font-medium">
                                {{ optional($ultimoReporte->created_at)->format('d/m/Y') ?? 'N/A' }}
                            </span>
                        </div>
                        @if(isset($ultimoReporte->global_precision_avg))
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-400">Precisión Global:</span>
                                <span class="text-sm text-emerald-400 font-medium">{{ number_format($ultimoReporte->global_precision_avg, 1) }}%</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Secciones with Reports --}}
            @if(isset($seccionesGuia) && $seccionesGuia->count() > 0)
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">Secciones con Reportes</p>
                    <div class="space-y-2">
                        @foreach($seccionesGuia as $seccion)
                            @php
                                $reportesCount = 0;
                                if (class_exists(\App\Models\app\Instrument\SectionDiagnosticReport::class)) {
                                    $reportesCount = \App\Models\app\Instrument\SectionDiagnosticReport::where('section_id', $seccion->id)->count();
                                }
                                $gradoName = $seccion->grado->name ?? '?';
                                $seccionName = $seccion->name ?? '?';
                            @endphp
                            @if($reportesCount > 0)
                                <div class="flex items-center justify-between bg-white/5 rounded-lg px-4 py-2.5 border border-white/5">
                                    <span class="text-sm text-gray-300">{{ $gradoName }} - Sección {{ $seccionName }}</span>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        {{ $reportesCount }} {{ $reportesCount === 1 ? 'informe' : 'informes' }}
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Benefits --}}
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">¿Qué encontrará en los informes?</p>
                <div class="grid grid-cols-1 gap-2">
                    @php
                        $benefits = [
                            ['icon' => 'M9 12l2 2 4-4', 'text' => 'Análisis de fortalezas y debilidades por área'],
                            ['icon' => 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z', 'text' => 'Distribución del desempeño estudiantil'],
                            ['icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'text' => 'Recomendaciones pedagógicas específicas'],
                        ];
                    @endphp
                    @foreach($benefits as $benefit)
                        <div class="flex items-center space-x-2.5 text-sm text-gray-400">
                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $benefit['icon'] }}" />
                            </svg>
                            <span>{{ $benefit['text'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between px-6 py-4 border-t border-white/5 bg-gray-900/50 rounded-b-2xl">
            <button type="button" onclick="noMostrarMas()"
                class="text-sm text-gray-500 hover:text-gray-300 transition-colors">
                No mostrar más
            </button>
            <div class="flex items-center space-x-3">
                <button type="button" onclick="cerrarModal()"
                    class="px-4 py-2 text-sm text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg transition-colors">
                    Cerrar
                </button>
                <a href="#"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-300 text-sm font-bold">
                    Ir a Informes
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (localStorage.getItem('noMostrarNotificacionDiagnostico') === 'true') {
            const modal = document.getElementById('diagnosticModal');
            if (modal) modal.remove();
        }
    });

    function cerrarModal() {
        const modal = document.getElementById('diagnosticModal');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    function noMostrarMas() {
        localStorage.setItem('noMostrarNotificacionDiagnostico', 'true');
        cerrarModal();
    }
</script>
