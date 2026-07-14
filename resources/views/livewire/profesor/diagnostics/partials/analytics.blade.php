{{-- Analytics Tab --}}
<div class="space-y-6">
    {{-- Performance by Pensum --}}
    <div class="bg-gray-800/30 border border-white/5 rounded-lg p-5">
        <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Rendimiento por Área de Formación</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Área</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Sesiones</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Completadas</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Precisión</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-right">Progreso</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($analytics['by_pensum'] ?? [] as $item)
                        <tr class="border-b border-white/5 hover:bg-white/[0.02] transition-colors">
                            <td class="py-3 px-4">
                                <span class="text-xs text-gray-300">{{ $item['name'] ?? '—' }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="text-xs text-gray-400">{{ $item['total_sessions'] ?? 0 }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="text-xs text-emerald-400">{{ $item['completed_sessions'] ?? 0 }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="text-xs font-medium {{ ($item['accuracy'] ?? 0) >= 70 ? 'text-emerald-400' : (($item['accuracy'] ?? 0) >= 40 ? 'text-amber-400' : 'text-red-400') }}">
                                    {{ number_format($item['accuracy'] ?? 0, 1) }}%
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                @php $pct = $item['total_sessions'] > 0 ? round(($item['completed_sessions'] / $item['total_sessions']) * 100) : 0; @endphp
                                <div class="flex items-center justify-end gap-2">
                                    <div class="w-20 bg-gray-700/50 rounded-full h-1.5">
                                        <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-[10px] text-gray-500 w-8 text-right">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-xs text-gray-500">
                                No hay datos de rendimiento disponibles.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Question Type Distribution --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-5">
            <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Distribución por Tipo de Pregunta</h4>
            @php
                $typeStats = $analytics['by_question_type'] ?? [];
                $totalType = array_sum(array_column($typeStats, 'count'));
            @endphp
            <div class="space-y-3">
                @forelse($typeStats as $type)
                    @php $pct = $totalType > 0 ? round(($type['count'] / $totalType) * 100) : 0; @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-gray-400">
                                {{ $type['type'] === 'multiple' ? 'Múltiple' : ($type['type'] === 'open' ? 'Abierta' : 'Escala') }}
                            </span>
                            <span class="text-gray-300 font-medium">{{ $type['count'] }} ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-gray-700/50 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $type['type'] === 'multiple' ? 'bg-blue-500' : ($type['type'] === 'open' ? 'bg-amber-500' : 'bg-green-500') }}"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-500 italic">Sin datos</p>
                @endforelse
            </div>
        </div>

        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-5">
            <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Distribución por Dificultad</h4>
            @php
                $diffStats = $analytics['by_difficulty'] ?? [];
                $totalDiff = array_sum(array_column($diffStats, 'count'));
            @endphp
            <div class="space-y-3">
                @forelse($diffStats as $diff)
                    @php $pct = $totalDiff > 0 ? round(($diff['count'] / $totalDiff) * 100) : 0; @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-gray-400">
                                {{ $diff['difficulty'] === 'easy' ? 'Fácil' : ($diff['difficulty'] === 'medium' ? 'Media' : 'Difícil') }}
                            </span>
                            <span class="text-gray-300 font-medium">{{ $diff['count'] }} ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-gray-700/50 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $diff['difficulty'] === 'easy' ? 'bg-emerald-500' : ($diff['difficulty'] === 'medium' ? 'bg-amber-500' : 'bg-red-500') }}"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-500 italic">Sin datos</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Insights Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-gradient-to-br from-emerald-500/5 to-emerald-500/0 border border-emerald-500/10 rounded-lg p-5">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Mejor área</span>
            </div>
            <p class="text-sm text-gray-200">{{ $analytics['best_pensum'] ?? '—' }}</p>
            <p class="text-[10px] text-gray-500 mt-1">Mayor precisión en respuestas</p>
        </div>

        <div class="bg-gradient-to-br from-red-500/5 to-red-500/0 border border-red-500/10 rounded-lg p-5">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                </svg>
                <span class="text-xs font-bold text-red-400 uppercase tracking-wider">Área a mejorar</span>
            </div>
            <p class="text-sm text-gray-200">{{ $analytics['worst_pensum'] ?? '—' }}</p>
            <p class="text-[10px] text-gray-500 mt-1">Menor precisión en respuestas</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500/5 to-purple-500/0 border border-purple-500/10 rounded-lg p-5">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span class="text-xs font-bold text-purple-400 uppercase tracking-wider">Total estudiantes evaluados</span>
            </div>
            <p class="text-lg font-bold text-white">{{ $analytics['total_students_evaluated'] ?? 0 }}</p>
            <p class="text-[10px] text-gray-500 mt-1">de {{ $stats['total_students'] ?? 0 }} estudiantes</p>
        </div>
    </div>

    {{-- Detailed Performance by Area --}}
    @if(!empty($analytics['detailed_pensum']))
        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-5">
            <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Desglose por Área</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-white/5">
                            <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Área</th>
                            <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Preguntas</th>
                            <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Correctas</th>
                            <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Incorrectas</th>
                            <th class="py-2 px-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Precisión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($analytics['detailed_pensum'] as $detail)
                            <tr class="border-b border-white/5 text-xs">
                                <td class="py-2 px-3 text-gray-300">{{ $detail['name'] ?? '—' }}</td>
                                <td class="py-2 px-3 text-center text-gray-400">{{ $detail['total_questions'] ?? 0 }}</td>
                                <td class="py-2 px-3 text-center text-emerald-400">{{ $detail['correct_answers'] ?? 0 }}</td>
                                <td class="py-2 px-3 text-center text-red-400">{{ $detail['incorrect_answers'] ?? 0 }}</td>
                                <td class="py-2 px-3 text-center">
                                    @php $acc = $detail['accuracy'] ?? 0; @endphp
                                    <span class="font-medium {{ $acc >= 70 ? 'text-emerald-400' : ($acc >= 40 ? 'text-amber-400' : 'text-red-400') }}">
                                        {{ number_format($acc, 1) }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
