{{-- Session Detail Modal --}}
@if($SessionModalReport && $selectedSession)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" wire:click="$set('showSessionModal', false)"></div>
        <div class="relative bg-gray-900 border border-white/10 rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-2xl">
            {{-- Header --}}
            <div class="sticky top-0 bg-gray-900/95 backdrop-blur-sm border-b border-white/5 px-6 py-2 flex items-center justify-between z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500/20 to-blue-500/20 flex items-center justify-center border border-white/5">
                        <span class="text-sm font-bold text-purple-400">{{ strtoupper(substr($selectedSession->estudiant?->full_name ?? '?', 0, 2)) }}</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">{{ $selectedSession->estudiant?->full_name ?? 'N/A' }}</h3>
                        <p class="text-[11px] text-gray-500">{{ $selectedSession->estudiant?->email ?? '' }}</p>
                    </div>
                </div>
                <button wire:click="$set('showSessionModal', false)" class="min-w-[44px] min-h-[44px] w-7 h-7 rounded-lg bg-gray-800/50 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5 space-y-6">
                {{-- Info Grid --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-gray-800/30 border border-white/5 rounded-lg p-3">
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Grado</span>
                        <span class="text-xs font-medium text-gray-200">{{ $selectedSession->estudiant?->grado?->name ?? '—' }}</span>
                    </div>
                    <div class="bg-gray-800/30 border border-white/5 rounded-lg p-3">
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Diagnóstico</span>
                        <span class="text-xs font-medium text-gray-200">{{ $selectedSession->diagMain?->name ?? '—' }}</span>
                    </div>
                    <div class="bg-gray-800/30 border border-white/5 rounded-lg p-3">
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Inicio</span>
                        <span class="text-xs font-medium text-gray-200">{{ $selectedSession->iniciado_at ? \Carbon\Carbon::parse($selectedSession->iniciado_at)->format('d/m/Y H:i:s') : '—' }}</span>
                    </div>
                    <div class="bg-gray-800/30 border border-white/5 rounded-lg p-3">
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Estado</span>
                        @if($selectedSession->completado_at)
                            <span class="inline-flex items-center gap-1 text-xs text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Completado
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs text-amber-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                En progreso
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Progress Bar --}}
                @if($selectedSession->completado_at)
                    <div class="bg-gray-800/30 border border-white/5 rounded-lg p-4">
                        <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">Progreso de Respuestas</h4>
                        @php
                            $totalQ = $selectedSession->answers->count();
                            $correctQ = $selectedSession->answers->where('is_correct', 1)->count();
                            $incorrectQ = $selectedSession->answers->where('is_correct', 0)->count();
                            $unansweredQ = max(0, ($selectedSession->diagMain?->questions->count() ?? $totalQ) - $totalQ);
                            $pct = $totalQ > 0 ? round(($correctQ / $totalQ) * 100) : 0;
                        @endphp
                        <div class="grid grid-cols-3 gap-3 mb-2">
                            <div class="text-center">
                                <span class="block text-lg font-bold text-emerald-400">{{ $correctQ }}</span>
                                <span class="text-[10px] text-gray-500">Correctas</span>
                            </div>
                            <div class="text-center">
                                <span class="block text-lg font-bold text-red-400">{{ $incorrectQ }}</span>
                                <span class="text-[10px] text-gray-500">Incorrectas</span>
                            </div>
                            <div class="text-center">
                                <span class="block text-lg font-bold text-gray-400">{{ $unansweredQ }}</span>
                                <span class="text-[10px] text-gray-500">Sin responder</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-700/50 rounded-full h-2.5">
                            <div class="bg-emerald-500 h-2.5 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-[10px] text-gray-500 mt-1 block text-right">{{ $pct }}% de precisión</span>
                    </div>

                    {{-- Answers Table --}}
                    <div>
                        <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">Respuestas</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-white/5">
                                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">#</th>
                                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Pregunta</th>
                                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Respuesta</th>
                                        <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Resultado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($selectedSession->answers as $answer)
                                        <tr class="border-b border-white/5">
                                            <td class="py-2 px-3 text-xs text-gray-500">{{ $loop->iteration }}</td>
                                            <td class="py-2 px-3">
                                                <p class="text-xs text-gray-300 max-w-[250px] truncate">{{ $answer->question?->pregunta ?? '—' }}</p>
                                            </td>
                                            <td class="py-2 px-3">
                                                <p class="text-xs text-gray-400 max-w-[200px] truncate">{{ $answer->respuesta_texto ?? '—' }}</p>
                                            </td>
                                            <td class="py-2 px-3 text-center">
                                                @if($answer->is_correct)
                                                    <span class="inline-flex items-center gap-1 text-xs text-emerald-400">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Correcto
                                                    </span>
                                                @elseif($answer->is_correct === 0)
                                                    <span class="inline-flex items-center gap-1 text-xs text-red-400">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                        Incorrecto
                                                    </span>
                                                @else
                                                    <span class="text-xs text-gray-600">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-6 text-center text-xs text-gray-500">Sin respuestas registradas</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-800/30 border border-white/5 rounded-lg p-6 text-center">
                        <svg class="w-10 h-10 text-amber-400/50 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-400">Sesión en progreso</p>
                        <p class="text-xs text-gray-600 mt-1">Los resultados estarán disponibles cuando el estudiante complete la evaluación.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
