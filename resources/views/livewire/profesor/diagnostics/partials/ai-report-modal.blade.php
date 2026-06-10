{{-- AI Report Modal --}}
@if($SessionModalReport && $selectedReport)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" wire:click="$set('SessionModalReport', false)"></div>
        <div class="relative bg-gray-900 border border-white/10 rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-2xl">
            {{-- Header --}}
            <div class="sticky top-0 bg-gray-900/95 backdrop-blur-sm border-b border-white/5 px-6 py-4 flex items-center justify-between z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500/20 to-blue-500/20 flex items-center justify-center border border-white/5">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Reporte Generado por IA</h3>
                        <p class="text-[11px] text-gray-500">{{ $selectedReport->estudiant?->full_name ?? 'Estudiante' }}</p>
                    </div>
                </div>
                <button wire:click="$set('SessionModalReport', false)" class="w-7 h-7 rounded-lg bg-gray-800/50 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5 space-y-6">
                {{-- Report Status --}}
                <div class="flex items-center justify-between bg-gray-800/30 border border-white/5 rounded-xl p-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold
                            {{ $selectedReport->status === 'completed' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($selectedReport->status === 'processing' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20') }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $selectedReport->status === 'completed' ? 'bg-emerald-400' : ($selectedReport->status === 'processing' ? 'bg-amber-400 animate-pulse' : 'bg-red-400') }}"></span>
                            {{ $selectedReport->status === 'completed' ? 'Completado' : ($selectedReport->status === 'processing' ? 'Procesando' : 'Error') }}
                        </span>
                        @if($selectedReport->generated_at)
                            <span class="text-[11px] text-gray-500">Generado: {{ \Carbon\Carbon::parse($selectedReport->generated_at)->format('d/m/Y H:i') }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        @if($selectedReport->status === 'completed')
                            <button wire:click="regenerateReport({{ $selectedReport->id }})"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20 border border-cyan-500/20 transition-all duration-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Regenerar
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Report Content --}}
                @if($selectedReport->status === 'completed' && $selectedReport->report_content)
                    @php
                        $report = is_string($selectedReport->report_content) ? json_decode($selectedReport->report_content, true) : $selectedReport->report_content;
                    @endphp

                    @if($report)
                        {{-- 1. Identificación --}}
                        @if(!empty($report['identification']))
                            <div class="bg-gray-800/30 border border-white/5 rounded-xl p-5">
                                <h4 class="text-xs font-bold text-purple-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"></path>
                                    </svg>
                                    Identificación
                                </h4>
                                <div class="grid grid-cols-2 gap-3 text-xs">
                                    @foreach($report['identification'] as $key => $value)
                                        <div>
                                            <span class="text-gray-500">{{ $key }}:</span>
                                            <span class="text-gray-300 ml-1">{{ $value }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- 2. Contextualización --}}
                        @if(!empty($report['contextualizacion']))
                            <div class="bg-gray-800/30 border border-white/5 rounded-xl p-5">
                                <h4 class="text-xs font-bold text-blue-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Contextualización
                                </h4>
                                <div class="prose prose-invert prose-xs max-w-none">
                                    {!! nl2br(e($report['contextualizacion'])) !!}
                                </div>
                            </div>
                        @endif

                        {{-- 3. Resultados Globales --}}
                        @if(!empty($report['resultados_globales']))
                            <div class="bg-gray-800/30 border border-white/5 rounded-xl p-5">
                                <h4 class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Resultados Globales
                                </h4>
                                @if(is_array($report['resultados_globales']))
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                        @foreach($report['resultados_globales'] as $key => $value)
                                            <div class="bg-gray-800/50 border border-white/5 rounded-lg p-3 text-center">
                                                <span class="block text-lg font-bold {{ is_numeric($value) && $value >= 70 ? 'text-emerald-400' : (is_numeric($value) && $value >= 40 ? 'text-amber-400' : 'text-gray-300') }}">{{ $value }}</span>
                                                <span class="text-[10px] text-gray-500">{{ $key }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-xs text-gray-400">{{ $report['resultados_globales'] }}</p>
                                @endif
                            </div>
                        @endif

                        {{-- 4. Resultados por Área --}}
                        @if(!empty($report['resultados_por_area']))
                            <div class="bg-gray-800/30 border border-white/5 rounded-xl p-5">
                                <h4 class="text-xs font-bold text-amber-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Resultados por Área
                                </h4>
                                <div class="space-y-3">
                                    @foreach($report['resultados_por_area'] as $area)
                                        <div class="bg-gray-800/50 border border-white/5 rounded-lg p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs font-bold text-gray-200">{{ $area['name'] ?? $area['area'] ?? 'Área' }}</span>
                                                @if(isset($area['precision']))
                                                    @php $prec = is_numeric($area['precision']) ? $area['precision'] : 0; @endphp
                                                    <span class="text-xs font-medium {{ $prec >= 70 ? 'text-emerald-400' : ($prec >= 40 ? 'text-amber-400' : 'text-red-400') }}">
                                                        {{ $prec }}%
                                                    </span>
                                                @endif
                                            </div>
                                            @if(!empty($area['analisis']))
                                                <p class="text-xs text-gray-400 leading-relaxed">{{ $area['analisis'] }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- 5. Contraste entre Áreas --}}
                        @if(!empty($report['contraste_areas']))
                            <div class="bg-gray-800/30 border border-white/5 rounded-xl p-5">
                                <h4 class="text-xs font-bold text-rose-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                    Contraste entre Áreas
                                </h4>
                                <p class="text-xs text-gray-400 leading-relaxed">{{ $report['contraste_areas'] }}</p>
                            </div>
                        @endif

                        {{-- 6. Perfil del Estudiante --}}
                        @if(!empty($report['perfil_estudiante']))
                            <div class="bg-gray-800/30 border border-white/5 rounded-xl p-5">
                                <h4 class="text-xs font-bold text-cyan-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Perfil del Estudiante
                                </h4>
                                <p class="text-xs text-gray-400 leading-relaxed">{{ $report['perfil_estudiante'] }}</p>
                            </div>
                        @endif

                        {{-- 7. Recomendaciones --}}
                        @if(!empty($report['recomendaciones']))
                            <div class="bg-gray-800/30 border border-white/5 rounded-xl p-5">
                                <h4 class="text-xs font-bold text-purple-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                    Recomendaciones
                                </h4>
                                @if(is_array($report['recomendaciones']))
                                    <ul class="space-y-2">
                                        @foreach($report['recomendaciones'] as $rec)
                                            <li class="flex items-start gap-2 text-xs text-gray-400">
                                                <svg class="w-4 h-4 text-purple-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                                <span>{{ is_string($rec) ? $rec : ($rec['recomendacion'] ?? $rec['text'] ?? '') }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-xs text-gray-400 leading-relaxed">{{ $report['recomendaciones'] }}</p>
                                @endif
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p class="text-sm">No se pudo interpretar el contenido del reporte.</p>
                        </div>
                    @endif
                @elseif($selectedReport->status === 'processing')
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 text-cyan-400 mx-auto mb-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <p class="text-sm text-gray-300 font-medium">Generando reporte con IA...</p>
                        <p class="text-xs text-gray-500 mt-1">Esto puede tomar unos segundos.</p>
                        <button wire:click="checkReportStatus({{ $selectedReport->id }})"
                            class="mt-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20 border border-cyan-500/20 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Verificar estado
                        </button>
                    </div>
                @elseif($selectedReport->status === 'error')
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <p class="text-sm text-gray-300 font-medium">Error al generar el reporte</p>
                        <p class="text-xs text-gray-500 mt-1">Intente generar el reporte nuevamente.</p>
                        <button wire:click="regenerateReport({{ $selectedReport->id }})"
                            class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20 border border-cyan-500/20 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reintentar
                        </button>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <p class="text-sm">No hay reporte disponible para este estudiante.</p>
                        <button wire:click="generateReport({{ $selectedReport->id }})"
                            class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-purple-500/10 text-purple-400 hover:bg-purple-500/20 border border-purple-500/20 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Generar Reporte
                        </button>
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="sticky bottom-0 bg-gray-900/95 backdrop-blur-sm border-t border-white/5 px-6 py-4 flex justify-end">
                <button wire:click="$set('SessionModalReport', false)"
                    class="px-4 py-2 rounded-xl text-xs font-bold bg-gray-800/50 text-gray-400 hover:text-white border border-white/5 hover:border-white/10 transition-all duration-200">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
@endif
