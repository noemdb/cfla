{{-- Dashboard Tab --}}
<div class="space-y-6">
    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Preguntas</span>
                <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-lg font-bold text-white">{{ number_format($stats['total_questions']) }}</p>
        </div>

        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Sesiones</span>
                <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-lg font-bold text-white">{{ number_format($stats['total_sessions']) }}</p>
        </div>

        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Completadas</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-lg font-bold text-white">{{ number_format($stats['completed_sessions']) }}</p>
            @if($stats['total_sessions'] > 0)
                <div class="mt-1">
                    <div class="w-full bg-gray-700/50 rounded-full h-1.5">
                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ round(($stats['completed_sessions'] / max($stats['total_sessions'], 1)) * 100) }}%"></div>
                    </div>
                    <span class="text-[10px] text-gray-500 mt-0.5 block">{{ round(($stats['completed_sessions'] / max($stats['total_sessions'], 1)) * 100) }}% del total</span>
                </div>
            @endif
        </div>

        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Precisión</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-lg font-bold text-white">{{ $stats['student_accuracy'] }}%</p>
            <span class="text-[10px] text-gray-500">{{ $stats['correct_answers'] }}/{{ $stats['total_answered'] }} respuestas correctas</span>
        </div>
    </div>

    {{-- Progress Overview --}}
    <div class="bg-gray-800/30 border border-white/5 rounded-lg p-5">
        <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Progreso General</h4>
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
            <div class="flex items-center justify-between mb-3">
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
            <div class="flex items-center justify-between mb-3">
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
