<div class="max-w-4xl mx-auto py-8 px-4 space-y-6">
    @php
    // D2 · Color por materia. Misma paleta/clave que home/activity (Tailwind JIT
    // necesita las clases literales aquí). Misma clave → mismo color por asignatura.
    $__sc = [
        'sky' => ['dot' => 'bg-sky-400', 'chip' => 'bg-sky-500/10 text-sky-400', 'text' => 'text-sky-600 dark:text-sky-300'],
        'emerald' => ['dot' => 'bg-emerald-400', 'chip' => 'bg-emerald-500/10 text-emerald-400', 'text' => 'text-emerald-600 dark:text-emerald-300'],
        'amber' => ['dot' => 'bg-amber-400', 'chip' => 'bg-amber-500/10 text-amber-400', 'text' => 'text-amber-600 dark:text-amber-300'],
        'indigo' => ['dot' => 'bg-indigo-400', 'chip' => 'bg-indigo-500/10 text-indigo-400', 'text' => 'text-indigo-600 dark:text-indigo-300'],
        'purple' => ['dot' => 'bg-purple-400', 'chip' => 'bg-purple-500/10 text-purple-400', 'text' => 'text-purple-600 dark:text-purple-300'],
        'orange' => ['dot' => 'bg-orange-400', 'chip' => 'bg-orange-500/10 text-orange-400', 'text' => 'text-orange-600 dark:text-orange-300'],
        'rose' => ['dot' => 'bg-rose-400', 'chip' => 'bg-rose-500/10 text-rose-400', 'text' => 'text-rose-600 dark:text-rose-300'],
        'teal' => ['dot' => 'bg-teal-400', 'chip' => 'bg-teal-500/10 text-teal-400', 'text' => 'text-teal-600 dark:text-teal-300'],
        'slate' => ['dot' => 'bg-slate-400', 'chip' => 'bg-slate-500/10 text-slate-400', 'text' => 'text-slate-600 dark:text-slate-300'],
    ];
    $__scResolve = static fn (?string $name): array => $__sc[\App\Models\app\Academy\Asignatura::colorKey($name)] ?? $__sc['slate'];
    @endphp

    <div class="flex items-center gap-4">
        @if($showMascot)
            {{-- C4: la mascota saluda como cabecera (5–12 años). --}}
            <x-lms.mascot :variant="'greet'" :size="'sm'" :emphasis="$mascotEmphasis" />
        @else
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        @endif
        <div>
            <h1 class="text-lg font-display font-bold text-gray-900 dark:text-white">Información Académica</h1>
            <p class="text-xs text-gray-600 dark:text-gray-400">Tu plan de estudio y planificación</p>
        </div>
    </div>

    @if($inscripcionData && $inscripcionData['estudiant'])
        {{-- Resumen de inscripción --}}
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Grado</label>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inscripcionData['grado']?->name ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Sección</label>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inscripcionData['seccion']?->name ?? '—' }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Plan de Estudio</label>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inscripcionData['pestudio']?->name ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Pensum (Asignaturas) --}}
        @if($pensums && $pensums->isNotEmpty())
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-4">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Áreas de Formación</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($pensums as $pensum)
                    @php $sc = $__scResolve($pensum->asignatura?->name); @endphp
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 transition-all duration-200 ease-out hover:-translate-y-0.5 hover:shadow-md hover:border-gray-200 dark:hover:border-gray-600 motion-reduce:transform-none motion-reduce:transition-none">
                        <div class="w-8 h-8 rounded-full {{ $sc['chip'] }} flex items-center justify-center shrink-0">
                            <span class="text-xs font-bold">{{ strtoupper(substr($pensum->asignatura?->name ?? '?', 0, 2)) }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $pensum->asignatura?->name ?? 'Sin asignatura' }}</p>
                            <p class="text-[10px] text-gray-500">{{ $pensum->asignatura?->code ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Planificación (Pevaluacions) --}}
        @if($pevaluacions && $pevaluacions->isNotEmpty())
        <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-4">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white shrink-0">Planificación Académica</h2>
                <div class="flex items-center gap-0.5">
                    <a href="{{ route('student.lms.home') }}"
                       class="px-2.5 py-1.5 min-h-[36px] text-xs font-medium rounded-lg transition-colors text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-300 hover:bg-emerald-500/10 focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                        Actividades
                    </a>
                    <a href="{{ route('student.lms.lessons') }}"
                       class="px-2.5 py-1.5 min-h-[36px] text-xs font-medium rounded-lg transition-colors text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-300 hover:bg-emerald-500/10 focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                        Lecciones
                    </a>
                    <a href="{{ route('student.lms.resources') }}"
                       class="px-2.5 py-1.5 min-h-[36px] text-xs font-medium rounded-lg transition-colors text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-300 hover:bg-emerald-500/10 focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                        Recursos
                    </a>
                </div>
            </div>
            <div class="space-y-2">
                @foreach($pevaluacions as $pev)
                    @php
                        $pensumId = $pev->pensum?->id;
                        $stat = $pensumId ? ($areaStats[$pensumId] ?? ['activities' => 0, 'lessons' => 0, 'comments' => 0]) : null;
                        $sc = $__scResolve($pev->pensum?->asignatura?->name);
                    @endphp
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 transition-colors hover:border-emerald-500/30">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate flex items-center gap-2">
                                <span class="w-2 h-2 {{ $sc['dot'] }} rounded-full shrink-0" aria-hidden="true"></span>
                                {{ $pev->pensum?->asignatura?->name ?? '—' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $pev->lapso?->name ?? '' }}
                                @if($pev->profesor)
                                    · {{ $pev->profesor?->user?->profile?->firstname ?? '' }} {{ $pev->profesor?->user?->profile?->lastname ?? '' }}
                                @endif
                            </p>
                            @if($stat)
                            <div class="flex items-center gap-3 mt-2">
                                <span class="inline-flex items-center gap-1 text-[11px] text-gray-600 dark:text-gray-400 tabular-nums">
                                    <svg class="w-3 h-3 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                    </svg>
                                    {{ $stat['activities'] }} activ.
                                </span>
                                <span class="inline-flex items-center gap-1 text-[11px] text-gray-600 dark:text-gray-400 tabular-nums">
                                    <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    {{ $stat['lessons'] }} lecc.
                                </span>
                                <span class="inline-flex items-center gap-1 text-[11px] text-gray-600 dark:text-gray-400 tabular-nums">
                                    <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    {{ $stat['comments'] }} coment.
                                </span>
                            </div>
                            @endif
                        </div>
                        <span class="text-xs text-gray-500 ml-3 shrink-0 max-w-[120px] truncate">{{ $pev->objetivo ?? '' }}</span>
                        <button type="button" wire:click="showDetail({{ $pev->id }})"
                                class="shrink-0 inline-flex items-center gap-1 px-3 py-2 min-h-[44px] text-xs font-medium rounded-lg transition-all duration-200 ease-out text-gray-600 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-300 hover:bg-emerald-500/10 ml-2 focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                            <svg class="w-3.5 h-3.5 inline -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Detalle
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="text-center py-8">
            <p class="text-sm text-gray-500">No hay planificación disponible para este lapso.</p>
        </div>
        @endif
    @else
        <div class="text-center py-12">
            @if($showMascot)
                {{-- C4: la mascota idle "anima en el vacío" sin datos. --}}
                <x-lms.mascot :variant="'idle'" :size="'sm'" :emphasis="$mascotEmphasis" />
            @else
                <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            @endif
            <p class="text-gray-600 font-medium">No se encontraron datos académicos.</p>
            <p class="text-xs text-gray-500 mt-1">Contacta al departamento de control de estudio.</p>
        </div>
    @endif

    {{-- ═══ DETALLE DE PLANIFICACIÓN (MODAL) ═══ --}}
    @if($selectedPevId && $selectedPev)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data x-cloak
         role="dialog" aria-modal="true" aria-labelledby="pev-dialog-title"
         @keydown.escape.window="$wire.closeDetail()"
         x-init="
            $nextTick(() => {
                const dialog = $el.querySelector('.lms-dialog');
                dialog?.focus();
                dialog?.addEventListener('keydown', (e) => {
                    if (e.key !== 'Tab') return;
                    const f = dialog.querySelectorAll('a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])');
                    const first = f[0], last = f[f.length - 1];
                    if (!first || !last) return;
                    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
                    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
                });
                $el.querySelector('.backdrop')?.addEventListener('click', () => $wire.closeDetail());
            });
        ">
        <div class="backdrop fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div class="lms-dialog relative bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden z-10 outline-none" tabindex="-1">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
                <div class="min-w-0 flex-1">
                    <h3 id="pev-dialog-title" class="text-base font-display font-bold text-gray-900 dark:text-white truncate pr-4 flex items-center gap-2">
                        <span class="w-2 h-2 {{ $__scResolve($selectedPev->pensum?->asignatura?->name)['dot'] }} rounded-full shrink-0" aria-hidden="true"></span>
                        {{ $selectedPev->pensum?->asignatura?->name ?? 'Planificación' }}
                    </h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                        {{ $selectedPev->lapso?->name ?? '' }}
                        @if($selectedPev->profesor)
                            · {{ $selectedPev->profesor?->user?->profile?->firstname ?? '' }} {{ $selectedPev->profesor?->user?->profile?->lastname ?? '' }}
                        @endif
                    </p>
                </div>
                <button type="button" wire:click="closeDetail" aria-label="Cerrar"
                        class="shrink-0 p-2 min-h-[44px] rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body — Bento grid --}}
            <div class="flex-1 overflow-auto p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 auto-rows-min">

                    {{-- Objetivo / Descripción — full width --}}
                    @if($selectedPev->objetivo || $selectedPev->description)
                    <div class="md:col-span-3 bg-gray-50 dark:bg-gray-900/30 rounded-xl border border-gray-100 dark:border-gray-700/50 p-4">
                        @if($selectedPev->objetivo)
                        <h4 class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Objetivo</h4>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $selectedPev->objetivo }}</p>
                        @endif
                        @if($selectedPev->description)
                        <h4 class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1 mt-3">Descripción</h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $selectedPev->description }}</p>
                        @endif
                    </div>
                    @endif

                    {{-- Actividades — col-span-2 --}}
                    @if($selectedActivities && $selectedActivities->isNotEmpty())
                    <div class="md:col-span-2 bg-white dark:bg-gray-800/70 rounded-xl border border-sky-200/60 dark:border-sky-800/40 p-4 shadow-sm">
                        <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            Actividades
                            <span class="ml-auto text-[10px] font-normal text-gray-400">{{ $selectedActivities->count() }}</span>
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($selectedActivities as $activity)
                            <a href="{{ route('student.lms.activity', $activity) }}"
                               class="group flex items-start gap-3 p-3 min-h-[44px] rounded-lg bg-white dark:bg-gray-900/40 border border-sky-100 dark:border-sky-900/50 hover:border-sky-300 dark:hover:border-sky-600 hover:shadow-md transition-all duration-200 ease-out focus-visible:ring-2 ring-sky-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                                <div class="w-8 h-8 rounded-lg bg-sky-500/10 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors">
                                        {{ $activity->name ?? $activity->topic ?? 'Actividad' }}
                                    </p>
                                    @if($activity->topic && $activity->name)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ $activity->topic }}</p>
                                    @endif
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Lecciones — col-span-1 --}}
                    @if($pevLessons && $pevLessons->isNotEmpty())
                    <div class="bg-white dark:bg-gray-800/70 rounded-xl border border-emerald-200/60 dark:border-emerald-800/40 p-4 shadow-sm">
                        <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Lecciones
                            <span class="ml-auto text-[10px] font-normal text-gray-400">{{ $pevLessons->count() }}</span>
                        </h4>
                        <div class="space-y-2">
                            @foreach($pevLessons as $lesson)
                            <a href="{{ route('student.lms.activity', $lesson) }}"
                               class="group flex items-start gap-3 p-3 min-h-[44px] rounded-lg bg-white dark:bg-gray-900/40 border border-emerald-100 dark:border-emerald-900/50 hover:border-emerald-300 dark:hover:border-emerald-600 hover:shadow-md transition-all duration-200 ease-out focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                        {{ $lesson->name ?? $lesson->topic ?? 'Lección' }}
                                    </p>
                                    @if($lesson->lmsPublication)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ $lesson->lmsPublication->title ?? '' }}</p>
                                    @endif
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Recursos — full width, 3-column inner grid --}}
                    @if($pevResources && $pevResources->isNotEmpty())
                    <div class="md:col-span-3 bg-white dark:bg-gray-800/70 rounded-xl border border-amber-200/60 dark:border-amber-800/40 p-4 shadow-sm">
                        <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Recursos
                            <span class="ml-auto text-[10px] font-normal text-gray-400">{{ $pevResources->count() }}</span>
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach($pevResources as $resource)
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-white dark:bg-gray-900/40 border border-amber-100 dark:border-amber-900/50">
                                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $resource->display_name ?? 'Recurso' }}</p>
                                    @if($resource->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ $resource->description }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Empty state --}}
                    @if((!$selectedActivities || $selectedActivities->isEmpty()) && (!$pevLessons || $pevLessons->isEmpty()) && (!$pevResources || $pevResources->isEmpty()))
                    <div class="md:col-span-3 text-center py-10">
                        <p class="text-sm text-gray-400">No hay contenido registrado en esta planificación.</p>
                    </div>
                    @endif

                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 shrink-0">
                <button type="button" wire:click="closeDetail"
                        class="px-4 py-2 min-h-[44px] text-xs font-medium rounded-lg transition-colors text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
