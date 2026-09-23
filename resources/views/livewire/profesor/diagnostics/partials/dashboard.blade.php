{{-- Dashboard Tab --}}
<div class="space-y-6">
    {{-- Header 3 cols: Lapso / Plan / Referente — réplica planning, scope profesor->pevaluacion->pensum --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Lapso</p>
            <p class="text-sm text-white font-medium">{{ ($stats['display_lapso'] ?? null)?->name ?? '—' }}</p>
        </div>
        <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Plan de Estudio</p>
            <p class="text-sm text-white font-medium">
                @if($stats['display_pestudio'] ?? null)
                    {{ $stats['display_pestudio']->code }} — {{ $stats['display_pestudio']->name }}
                @else
                    <span class="text-gray-400">Múltiples planes</span>
                @endif
            </p>
        </div>
        <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Referente</p>
            <p class="text-sm text-white font-medium">{{ ($stats['display_referent'] ?? null) ? $stats['display_referent']->code.' — '.$stats['display_referent']->name : '—' }}</p>
        </div>
    </div>

    {{-- KPI 8 cols — réplica planning, solo cifras asociadas al profesor --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
            <p class="text-lg font-extrabold text-cyan-400">{{ number_format($stats['total_questions']) }}</p>
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Preguntas</p>
        </div>
        <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
            <p class="text-lg font-extrabold text-sky-400">{{ number_format($stats['pensums_with_answers'] ?? 0) }}</p>
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">A.Formación c/ resp.</p>
        </div>
        <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
            <p class="text-lg font-extrabold text-indigo-400">{{ number_format($stats['questions_with_answers'] ?? 0) }}</p>
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Preg. c/ resp.</p>
        </div>
        <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
            <p class="text-lg font-extrabold text-amber-400">{{ number_format($stats['total_answers'] ?? 0) }}</p>
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Respuestas</p>
        </div>
        <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
            <p class="text-lg font-extrabold text-purple-400">{{ number_format($stats['students_with_sessions'] ?? 0) }}</p>
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Estudiantes</p>
        </div>
        <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
            @if($stats['completion_rate'] !== null)
                <p class="text-lg font-extrabold {{ $stats['completion_rate'] >= 80 ? 'text-emerald-400' : ($stats['completion_rate'] >= 50 ? 'text-amber-400' : 'text-red-400') }}">{{ $stats['completion_rate'] }}%</p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">% Completitud</p>
                <p class="text-[10px] text-gray-500">{{ $stats['completed_sessions'] }} / {{ $stats['total_sessions'] }}</p>
            @else
                <p class="text-lg font-extrabold text-gray-500">—</p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">% Completitud</p>
                <p class="text-[10px] text-gray-600">Sin datos</p>
            @endif
        </div>
        <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
            @if($stats['abandon_rate'] !== null)
                <p class="text-lg font-extrabold {{ $stats['abandon_rate'] <= 20 ? 'text-emerald-400' : ($stats['abandon_rate'] <= 40 ? 'text-amber-400' : 'text-red-400') }}">{{ $stats['abandon_rate'] }}%</p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Tasa Abandono</p>
                <p class="text-[10px] text-gray-500">{{ ($stats['total_sessions'] - $stats['completed_sessions']) }} / {{ $stats['total_sessions'] }}</p>
            @else
                <p class="text-lg font-extrabold text-gray-500">—</p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Tasa Abandono</p>
                <p class="text-[10px] text-gray-600">Sin datos</p>
            @endif
        </div>
        <div class="bg-gray-800/40 rounded-lg p-3 border border-white/5 text-center">
            @if($stats['total_answered'] > 0)
                <p class="text-lg font-extrabold {{ $stats['student_accuracy'] >= 80 ? 'text-emerald-400' : ($stats['student_accuracy'] >= 60 ? 'text-amber-400' : 'text-red-400') }}">{{ $stats['student_accuracy'] }}%</p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Precisión</p>
                <p class="text-[10px] text-gray-500">{{ $stats['correct_answers'] }} / {{ $stats['total_answered'] }}</p>
            @else
                <p class="text-lg font-extrabold text-gray-500">—</p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">Precisión</p>
                <p class="text-[10px] text-gray-600">Sin datos</p>
            @endif
        </div>
    </div>

    {{-- Progress Overview --}}
    <div class="bg-gray-800/30 border border-white/5 rounded-lg p-5">
        <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-2">Progreso General</h4>
        <div class="space-y-3">
            <div>
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="text-gray-400">Sesiones Completadas</span>
                    <span class="text-gray-300 font-medium">{{ $stats['completed_sessions'] }}/{{ max($stats['total_sessions'], 1) }}</span>
                </div>
                <div class="w-full bg-gray-700/50 rounded-full h-2">
                    <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $stats['total_sessions'] > 0 ? round(($stats['completed_sessions'] / $stats['total_sessions']) * 100) : 0 }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="text-gray-400">Sesiones Activas</span>
                    <span class="text-gray-300 font-medium">{{ $stats['active_sessions'] }}</span>
                </div>
                <div class="w-full bg-gray-700/50 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $stats['total_sessions'] > 0 ? round(($stats['active_sessions'] / $stats['total_sessions']) * 100) : 0 }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="text-gray-400">Precisión de Respuestas</span>
                    <span class="text-gray-300 font-medium">{{ $stats['student_accuracy'] }}%</span>
                </div>
                <div class="w-full bg-gray-700/50 rounded-full h-2">
                    <div class="bg-amber-500 h-2 rounded-full" style="width: {{ min($stats['student_accuracy'], 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        {{-- Recent Questions --}}
        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-5">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-xs font-bold text-white uppercase tracking-wider">Preguntas Recientes</h4>
                <button wire:click="setActiveTab('questions')" class="text-[10px] text-purple-400 hover:text-purple-300 font-medium">Ver todas →</button>
            </div>
            <div class="space-y-2">
                @forelse($allQuestions->sortByDesc('created_at')->take(5) as $question)
                    <div class="flex items-center gap-2 p-2 rounded-lg hover:bg-white/[0.02]">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase {{ $question->tipo_pregunta === 'multiple' ? 'bg-blue-500/10 text-blue-400' : ($question->tipo_pregunta === 'open' ? 'bg-amber-500/10 text-amber-400' : 'bg-green-500/10 text-green-400') }}">
                            {{ $question->tipo_pregunta === 'multiple' ? 'Múltiple' : ($question->tipo_pregunta === 'open' ? 'Abierta' : 'Escala') }}
                        </span>
                        <span class="text-xs text-gray-400 truncate flex-1">{{ Str::limit($question->pregunta, 60) }}</span>
                    </div>
                @empty
                    <p class="text-xs text-gray-600 italic py-2">No hay preguntas recientes.</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Sessions --}}
        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-5">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-xs font-bold text-white uppercase tracking-wider">Sesiones Recientes</h4>
                <button wire:click="setActiveTab('sessions')" class="text-[10px] text-purple-400 hover:text-purple-300 font-medium">Ver todas →</button>
            </div>
            <div class="space-y-2">
                @forelse($allSessions->sortByDesc('iniciado_at')->take(5) as $session)
                    <div class="flex items-center justify-between p-2 rounded-lg hover:bg-white/[0.02]">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg {{ $session->completado_at ? 'bg-emerald-500/10' : 'bg-amber-500/10' }} flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 {{ $session->completado_at ? 'text-emerald-400' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $session->completado_at ? 'M5 13l4 4L19 7' : 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' }}"></path>
                                </svg>
                            </span>
                            <span class="text-xs text-gray-400">{{ Str::limit($session->estudiant?->full_name ?? 'N/A', 30) }}</span>
                        </div>
                        <span class="text-[10px] text-gray-600">{{ $session->iniciado_at ? \Carbon\Carbon::parse($session->iniciado_at)->format('d/m') : '—' }}</span>
                    </div>
                @empty
                    <p class="text-xs text-gray-600 italic py-2">No hay sesiones recientes.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
