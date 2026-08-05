<div class="max-w-4xl mx-auto py-8 px-4 space-y-8">

    {{-- Header --}}
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-white">Panel de Progreso</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Resumen de tu avance académico
        </p>
    </div>

    {{-- 1. Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        {{-- Total --}}
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-2">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-sky-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Totales</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
            <p class="text-[11px] text-gray-500 dark:text-gray-400">Actividades publicadas</p>
        </div>

        {{-- Completed --}}
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-2">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Completadas</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['completed'] }}</p>
            <p class="text-[11px] text-gray-500 dark:text-gray-400">
                @if($stats['total'] > 0)
                    {{ $stats['progress_pct'] }}% del total
                @else
                    Sin actividades
                @endif
            </p>
        </div>

        {{-- Comments --}}
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-2">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Comentarios</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['comments'] }}</p>
            <p class="text-[11px] text-gray-500 dark:text-gray-400">En actividades</p>
        </div>

        {{-- Downloads --}}
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-2">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Descargas</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['downloads'] }}</p>
            <p class="text-[11px] text-gray-500 dark:text-gray-400">Recursos descargados</p>
        </div>
    </div>

    {{-- 2. Continue Learning --}}
    @if($recentLogs->isNotEmpty())
    <section>
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Continuar Aprendiendo</h2>
        </div>

        <div class="space-y-2">
            @foreach($recentLogs as $log)
                @php $act = $log->activity; @endphp
                @if(!$act) @continue @endif
                <a href="{{ route('student.lms.activity', $act) }}"
                   class="group block bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:border-emerald-500/30 transition-all duration-200">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <div @class([
                                'w-9 h-9 rounded-lg flex items-center justify-center shrink-0 mt-0.5',
                                'bg-emerald-500/10' => $log->event === 'COMPLETE',
                                'bg-sky-500/10' => $log->event !== 'COMPLETE',
                            ])>
                                @if($log->event === 'COMPLETE')
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors truncate">
                                        {{ $act->topic ?? 'Actividad sin título' }}
                                    </p>
                                    @if($act->lmsPublication?->isPreviewToStudents())
                                        <span class="shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-500/10 border border-amber-300 dark:border-amber-500/30">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Vista previa
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                    {{ $act->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                                    &middot;
                                    {{ $act->pevaluacion?->profesor?->lastname }} {{ $act->pevaluacion?->profesor?->name }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-[11px] text-gray-400 dark:text-gray-500 whitespace-nowrap">
                                @if($log->event === 'COMPLETE')
                                    Completado
                                @else
                                    {{ $log->created_at->diffForHumans() }}
                                @endif
                            </span>
                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @elseif($suggestedActivities->isNotEmpty())
    <section>
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Publicaciones Recientes</h2>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Actividades publicadas más recientes</p>
        <div class="space-y-2">
            @foreach($suggestedActivities as $activity)
            <a href="{{ route('student.lms.activity', $activity) }}"
               class="group block bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:border-emerald-500/30 transition-all duration-200">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 mt-0.5 bg-sky-500/10">
                            <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors truncate">
                                {{ $activity->topic ?? 'Actividad sin título' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                {{ $activity->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                                &middot;
                                {{ $activity->pevaluacion?->profesor?->lastname }} {{ $activity->pevaluacion?->profesor?->name }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="text-[11px] text-gray-400 dark:text-gray-500 whitespace-nowrap">
                            {{ $activity->lmsPublication?->publish_at?->diffForHumans() }}
                        </span>
                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- 3. Próximas publicaciones (publish_at = fecha más relevante para el estudiante) --}}
    @if($upcoming->isNotEmpty())
    <section>
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Próximas Publicaciones</h2>
        </div>

        <div class="space-y-2">
            @foreach($upcoming as $activity)
                @php
                    $publishAt = $activity->lmsPublication?->publish_at;
                    $daysLeft = $publishAt
                        ? now()->startOfDay()->diffInDays($publishAt->copy()->startOfDay(), false)
                        : null;
                @endphp
                <a href="{{ route('student.lms.activity', $activity) }}"
                   class="group block bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:border-sky-500/30 transition-all duration-200">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 mt-0.5 bg-sky-500/10">
                                <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors truncate">
                                        {{ $activity->topic ?? 'Actividad sin título' }}
                                    </p>
                                    <span class="shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-semibold uppercase tracking-wider text-sky-700 dark:text-sky-300 bg-sky-100 dark:bg-sky-500/10 border border-sky-300 dark:border-sky-500/30">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Vista previa
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                    {{ $activity->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                                    &middot;
                                    {{ $activity->pevaluacion?->lapso?->name ?? '' }}
                                </p>
                            </div>
                        </div>
                        <div class="shrink-0 text-[11px] font-medium whitespace-nowrap px-2.5 py-1 rounded-full bg-sky-500/10 text-sky-400">
                            @if(!$publishAt)
                                Próximamente
                            @elseif($publishAt->isToday())
                                Se publica hoy a las {{ $publishAt->format('H:i') }}
                            @elseif($daysLeft === 1)
                                Se publica mañana
                            @elseif($daysLeft <= 7)
                                Se publica en {{ $daysLeft }} días
                            @else
                                Se publica el {{ $publishAt->translatedFormat('j M') }}
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- 4. Subject Distribution --}}
    @if($subjectDistribution->isNotEmpty())
    <section>
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Distribución por Asignatura</h2>
        </div>

        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-5 space-y-5">
            @foreach($subjectDistribution as $subject)
                @php $pct = $subject['total'] > 0 ? round(($subject['completed'] / $subject['total']) * 100) : 0; @endphp
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $subject['name'] }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $subject['completed'] }}/{{ $subject['total'] }}
                            <span class="text-gray-400 dark:text-gray-500 ml-0.5">· {{ $pct }}%</span>
                        </span>
                    </div>
                    <div class="w-full h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500"
                             style="width: {{ $pct }}%; background: linear-gradient(90deg, #10b981, #34d399);">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Empty state --}}
    @if($stats['total'] === 0 && $recentLogs->isEmpty() && $upcoming->isEmpty())
    <div class="text-center py-16">
        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="text-gray-500 dark:text-gray-400 font-medium">No hay actividades publicadas</p>
        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">
            Cuando tus profesores publiquen contenido, aparecerá aquí.
        </p>
    </div>
    @endif
</div>
