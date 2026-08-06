<div class="max-w-4xl mx-auto py-8 px-4 space-y-8" wire:poll.10s>

    {{-- 0. Hero: saludo + progreso + siguiente lección --}}
    <section class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 sm:p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center gap-6">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">{{ $greeting }}</p>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $firstName }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Tu avance en un vistazo. Sigue aprendiendo sin perder el ritmo.
                </p>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    @if($streak > 0)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-orange-700 dark:text-orange-300 bg-orange-100 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30">
                        <svg class="w-3.5 h-3.5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05c-1.354 1.553-2.4 3.307-3.018 5.194-.512 1.565-.17 3.24.89 4.303C4.087 16.616 5.857 17.34 8.004 17.34c2.148 0 3.918-.724 5.08-1.793 1.06-1.062 1.402-2.738.89-4.303-.618-1.887-1.663-3.64-3.017-5.194a1 1 0 00-1.562.112z" clip-rule="evenodd"/>
                        </svg>
                        {{ $streak }} {{ $streak === 1 ? 'día' : 'días' }} de racha
                    </span>
                    @endif
                </div>

                @if($nextLesson)
                <a href="{{ route('student.lms.activity', $nextLesson) }}"
                   class="group mt-4 inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold shadow-sm transition-colors">
                    <span class="max-w-[16rem] sm:max-w-xs truncate">{{ $nextLesson->topic }}</span>
                    <svg class="w-4 h-4 shrink-0 group-hover:translate-x-0.5 transition-transform" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <p class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $nextLesson->pevaluacion?->pensum?->asignatura?->name ?? 'Lección' }}
                    </span>
                    @if($nextLesson->lmsPublication?->publish_at?->isFuture())
                        <span class="inline-flex items-center gap-1 font-semibold text-amber-600 dark:text-amber-400"
                              x-data="{ target: '{{ $nextLesson->lmsPublication->publish_at->toIso8601String() }}', label: '', timer: null, tick() { const left = new Date(this.target) - new Date(); if (left <= 0) { this.label = 'Publicada ahora'; clearInterval(this.timer); return; } const h = Math.floor(left / 3.6e6); const m = Math.floor((left % 3.6e6) / 6e4); const s = Math.floor((left % 6e4) / 1e3); this.label = 'Comienza en ' + h + 'h ' + m + 'm ' + s + 's'; }, init() { this.tick(); this.timer = setInterval(() => this.tick(), 1000); } }"
                              x-text="label">Comienza en…</span>
                    @else
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $nextLesson->lmsPublication?->publish_at?->diffForHumans() }}
                        </span>
                    @endif
                </p>
                @else
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    Aún no hay lecciones disponibles. Tus profesores publicarán contenido pronto.
                </p>
                @endif
            </div>

            <div class="shrink-0 mx-auto sm:mx-0">
                <div class="relative w-36 h-36" x-data="{ pct: 0, target: {{ $stats['progress_pct'] }} }"
                     x-init="() => { const start = performance.now(); const dur = 1000; const step = (now) => { const k = Math.min((now - start) / dur, 1); pct = Math.round(target * (1 - Math.pow(1 - k, 3))); if (k < 1) requestAnimationFrame(step); }; requestAnimationFrame(step); }">
                    <svg class="w-36 h-36 -rotate-90" viewBox="0 0 120 120" aria-hidden="true">
                        <circle cx="60" cy="60" r="52" fill="none" stroke-width="10" class="stroke-gray-100 dark:stroke-gray-700/60"></circle>
                        <circle cx="60" cy="60" r="52" fill="none" stroke-width="10" stroke-linecap="round"
                                class="stroke-emerald-500"
                                stroke-dasharray="326.7"
                                :style="'stroke-dashoffset: ' + (326.7 - (326.7 * pct / 100))"
                                style="stroke-dashoffset: 326.7; transition: stroke-dashoffset 1s cubic-bezier(0.22, 1, 0.36, 1);"></circle>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums" x-text="pct + '%'">0%</span>
                        <span class="text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">completado</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 1. Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        {{-- Lecciones --}}
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-2 shadow-sm">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-sky-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Lecciones</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Disponibles para ti</p>
        </div>

        {{-- Completadas --}}
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-2 shadow-sm">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Completadas</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['completed'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                @if($stats['total'] > 0)
                    {{ $stats['progress_pct'] }}% del total
                @else
                    Sin actividades
                @endif
            </p>
        </div>

        {{-- Comentarios --}}
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-2 shadow-sm">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Comentarios</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['comments'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Que has dejado</p>
        </div>

        {{-- Descargas --}}
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-2 shadow-sm">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Descargas</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['downloads'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Recursos descargados</p>
        </div>
    </div>

    {{-- 2. Continue Learning --}}
    @if($recentLogs->isNotEmpty())
    <section>
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Continuar Aprendiendo</h2>
        </div>

        <div class="space-y-2">
            @foreach($recentLogs as $log)
                @php $act = $log->activity; @endphp
                @if(!$act) @continue @endif
                <a href="{{ route('student.lms.activity', $act) }}"
                   class="group block bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:border-emerald-500/40">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <div @class([
                                'w-9 h-9 rounded-lg flex items-center justify-center shrink-0 mt-0.5',
                                'bg-emerald-500/10' => $log->event === 'COMPLETE',
                                'bg-sky-500/10' => $log->event !== 'COMPLETE',
                            ])>
                                @if($log->event === 'COMPLETE')
                                    <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-sky-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                        <span class="shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-500/10 border border-amber-300 dark:border-amber-500/30">
                                            <svg class="w-2.5 h-2.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                                @if($log->event === 'COMPLETE')
                                    Completado
                                @else
                                    {{ $log->created_at->diffForHumans() }}
                                @endif
                            </span>
                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-emerald-500 transition-colors" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Publicaciones Recientes</h2>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Actividades publicadas más recientes</p>
        <div class="space-y-2">
            @foreach($suggestedActivities as $activity)
            <a href="{{ route('student.lms.activity', $activity) }}"
               class="group block bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:border-emerald-500/40">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 mt-0.5 bg-emerald-500/10">
                            <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                            {{ $activity->lmsPublication?->publish_at?->diffForHumans() }}
                        </span>
                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-emerald-500 transition-colors" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Próximas Publicaciones</h2>
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
                   class="group block bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:border-emerald-500/40">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 mt-0.5 bg-emerald-500/10">
                                <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors truncate">
                                        {{ $activity->topic ?? 'Actividad sin título' }}
                                    </p>
                                    <span class="shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-300 dark:border-emerald-500/30">
                                        <svg class="w-2.5 h-2.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <div class="shrink-0 text-xs font-medium whitespace-nowrap px-2.5 py-1 rounded-full border bg-emerald-100 dark:bg-emerald-500/10 border-emerald-300 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-300">
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

    {{-- 4. Todas las lecciones (búsqueda + filtro + paginación) --}}
    @if($allLessons->total() > 0 || $this->search !== '' || $this->subjectFilter !== '')
    <section>
        <div class="flex items-center gap-2 mb-1">
            <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Todas las Lecciones</h2>
            <span class="text-xs text-gray-400 dark:text-gray-500">({{ $allLessons->total() }})</span>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
            Tu catálogo completo, de la más reciente a la más antigua
        </p>

        {{-- Búsqueda + filtro por asignatura --}}
        <div class="flex flex-col sm:flex-row gap-2 mb-3">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="search"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Buscar lección…"
                       class="w-full pl-9 pr-8 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500">
                @if($this->search !== '')
                <button type="button"
                        wire:click="$set('search', '')"
                        aria-label="Limpiar búsqueda"
                        class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                @endif
            </div>

            <select wire:model.live="subjectFilter"
                    class="sm:w-52 py-2 px-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500">
                <option value="">Todas las asignaturas</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject }}">{{ $subject }}</option>
                @endforeach
            </select>
        </div>

        {{-- Leyenda de estado --}}
        <div class="flex items-center gap-3 mb-2 text-xs text-gray-400 dark:text-gray-500">
            <span class="inline-flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Publicada
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Vista previa
            </span>
        </div>

        @if($allLessons->isNotEmpty())
        <ul class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($allLessons as $activity)
                @php $isPreview = $activity->lmsPublication?->isPreviewToStudents(); @endphp
            <li>
                <a href="{{ route('student.lms.activity', $activity) }}"
                   class="group flex items-center justify-between gap-3 py-2">
                    <span class="flex items-center gap-2.5 min-w-0">
                        <span @class([
                            'w-1.5 h-1.5 rounded-full shrink-0',
                            'bg-emerald-500' => !$isPreview,
                            'bg-amber-400' => $isPreview,
                        ])></span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 truncate transition-colors">
                            {{ $activity->topic ?? 'Actividad sin título' }}
                        </span>
                        @if($isPreview)
                            <span class="shrink-0 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-500/10 border border-amber-300 dark:border-amber-500/30">
                                Vista previa
                            </span>
                        @endif
                        <span class="hidden md:inline text-xs text-gray-400 dark:text-gray-500 truncate">
                            {{ $activity->pevaluacion?->pensum?->asignatura?->name ?? '' }}
                        </span>
                    </span>
                    <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                        {{ $activity->lmsPublication?->publish_at?->translatedFormat('j M Y') }}
                    </span>
                </a>
            </li>
            @endforeach
        </ul>

        <div class="mt-4">
            {{ $allLessons->links() }}
        </div>
        @else
        <div class="text-center py-10 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                @if($this->search !== '' && $this->subjectFilter !== '')
                    No se encontraron lecciones para “<span class="font-semibold text-gray-700 dark:text-gray-300">{{ $this->search }}</span>” en {{ $this->subjectFilter }}.
                @elseif($this->search !== '')
                    No se encontraron lecciones para “<span class="font-semibold text-gray-700 dark:text-gray-300">{{ $this->search }}</span>”.
                @else
                    No se encontraron lecciones en {{ $this->subjectFilter }}.
                @endif
            </p>
            <button type="button"
                    wire:click="resetFilters"
                    class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 transition-colors">
                <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar filtros
            </button>
        </div>
        @endif
    </section>
    @endif

    {{-- 5. Subject Distribution --}}
    @if($subjectDistribution->isNotEmpty())
    <section>
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Distribución por Asignatura</h2>
        </div>

        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-5 shadow-sm">
            @foreach($subjectDistribution as $subject)
                @php $pct = $subject['total'] > 0 ? round(($subject['completed'] / $subject['total']) * 100) : 0; @endphp
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $subject['name'] }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $subject['completed'] }} de {{ $subject['total'] }}
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

    {{-- 6. Tu actividad reciente --}}
    @if($recentComments->isNotEmpty() || $recentDownloads->isNotEmpty())
    <section>
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Tu actividad reciente</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @if($recentComments->isNotEmpty())
            <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-3">Comentarios recientes</p>
                <ul class="space-y-3">
                    @foreach($recentComments as $comment)
                    @php $cAct = $comment->activity; @endphp
                    @if(!$cAct) @continue @endif
                    <li>
                        <a href="{{ route('student.lms.activity', $cAct) }}" class="block group">
                            <p class="text-sm text-gray-800 dark:text-gray-200 line-clamp-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                {{ $comment->body }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 truncate">
                                {{ $cAct->topic }} &middot; {{ $cAct->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                            </p>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if($recentDownloads->isNotEmpty())
            <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-3">Descargas recientes</p>
                <ul class="space-y-3">
                    @foreach($recentDownloads as $log)
                    @php $dAct = $log->activity; @endphp
                    @if(!$dAct) @continue @endif
                    <li>
                        <a href="{{ route('student.lms.activity', $dAct) }}" class="block group">
                            <p class="text-sm text-gray-800 dark:text-gray-200 truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                {{ $downloadResources[$log->context_id] ?? $dAct->topic }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 truncate">
                                {{ $dAct->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                                &middot; {{ $log->created_at->diffForHumans() }}
                            </p>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </section>
    @endif
</div>
