<div class="fade-in">
    {{-- Overlay de carga (patrón LessonWizard): paso 4 (disponibilidad) y paso 5 (generar) --}}
    <div wire:loading.flex
         wire:target="setAllAvailable,saveAvailability,runDryRun,confirmAndPublish,analyzeDryRunWithAi"
         class="fixed inset-0 z-[9999] items-center justify-center bg-white/95 dark:bg-gray-900/90 backdrop-blur-md">
        <div class="flex flex-col items-center gap-4">
            <div class="relative w-14 h-14">
                <svg class="absolute inset-0 w-full h-full animate-spin text-emerald-500/40" viewBox="0 0 64 64" fill="none">
                    <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="3" stroke-dasharray="44 132" stroke-linecap="round" class="opacity-80"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-bold text-gray-700 dark:text-gray-200">
                @if ($aiDryRunAnalysisBusy)
                    Analizando horario publicado con IA…
                @else
                    {{ ($currentStep ?? 1) === 5 ? 'Generando horario…' : 'Procesando disponibilidad…' }}
                @endif
            </p>
        </div>
    </div>

    {{-- Header --}}
    <div class="mb-6 sm:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Horario Escolar</h1>
            <p class="text-emerald-600 dark:text-emerald-400 font-medium text-sm">Asistente de creación de horarios (Lun–Vie)</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white/5 text-gray-300 border border-white/5 text-sm font-bold">
                Paso {{ $currentStep }} de 5
            </span>
            <button wire:click="refreshWizard"
                wire:loading.attr="disabled"
                wire:target="refreshWizard"
                type="button"
                title="Reiniciar selección y elegir otro calendario"
                aria-label="Reiniciar selección y elegir otro calendario"
                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/20 text-sm font-bold transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M5.5 9A7 7 0 0117 5.5L20 8M18.5 15A7 7 0 017 18.5L4 16"/>
                </svg>
                <span>Reiniciar</span>
            </button>
        </div>
    </div>

    {{-- Switcher global: alternativas (calendarios) del lapso en edición --}}
    {{-- Sin backdrop-blur-md: backdrop-filter en un ancestro descoloca el
         dropdown nativo del <select> en Chromium (bug conocido). --}}
    @if (count($calendars))
        <div class="bg-white dark:bg-gray-900/40 border border-gray-200 dark:border-white/5 rounded-lg p-4 mb-6">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Horario</span>
                <select wire:key="calendar-switcher-{{ collect($calendars)->map(fn ($calendar) => $calendar['id'].'-'.$calendar['status'].'-'.$calendar['version'])->implode('|') }}" wire:model.live="calendarId" class="flex-1 min-w-[200px] bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                    <option value="">Seleccionar</option>
                    @foreach ($calendars as $c)
                        <option value="{{ $c['id'] }}">{{ $c['name'] }} ({{ $c['status'] }})</option>
                    @endforeach
                </select>
                <button wire:click="$set('showCreateCalendarForm', true)"
                    class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all">+ Nuevo borrador</button>
                {{-- <button wire:click="openEditCalendarForm"
                    {{ filled($calendarId) ? '' : 'disabled' }}
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ filled($calendarId) ? 'bg-white/5 hover:bg-white/10 text-gray-300 border border-gray-200 dark:border-white/10' : 'bg-white/5 text-gray-500 border border-gray-200 dark:border-white/10 cursor-not-allowed opacity-50' }}">
                    Editar calendario.</button> --}}
            </div>
            @if ($calendarId)
                <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                    Editando: <span class="font-bold text-gray-900 dark:text-white">{{ collect($calendars)->firstWhere('id', $calendarId)['name'] ?? '' }}</span>
                    @php
                        $activeCal = collect($calendars)->firstWhere('status', 'active');
                    @endphp
                    @if ($activeCal)
                        · Activo: <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $activeCal['name'] }}</span>
                    @endif
                </div>
            @endif
        </div>
    @endif

    {{-- Tabs · cada paso del wizard es una pestaña (currentStep vía goToStep) --}}
    {{-- Sin backdrop-blur-md: backdrop-filter en ancestro descoloca el dropdown
         nativo del <select> en Chromium (mismo bug documentado en el switcher). --}}
    <div class="bg-white dark:bg-gray-900/40 border border-gray-200 dark:border-white/5 rounded-lg p-1.5 mb-6">
        <div class="flex gap-1 overflow-x-auto" role="tablist" aria-label="Pasos del asistente de horarios">
            @php
                $calendarSelected = filled($calendarId);
            @endphp
            @foreach ([1 => 'Calendario', 2 => 'Aulas', 3 => 'Clases', 4 => 'Disponibilidad', 5 => 'Generar'] as $step => $label)
                @php
                    $stepLocked = $step >= 2 && ! $calendarSelected;
                @endphp
                <button type="button"
                    id="tt-tab-{{ $step }}"
                    role="tab"
                    aria-selected="{{ $currentStep === $step ? 'true' : 'false' }}"
                    aria-controls="tt-step-{{ $step }}"
                    @if ($stepLocked) disabled @endif
                    @if ($stepLocked) aria-disabled="true" tabindex="-1" @endif
                    @if (! $stepLocked) wire:click="goToStep({{ $step }})" @endif
                    class="flex-1 min-w-[110px] px-3 py-2 rounded-lg text-xs font-bold transition-all whitespace-nowrap {{ $stepLocked ? 'opacity-50 cursor-not-allowed bg-gray-100 dark:bg-white/5 text-gray-400 dark:text-gray-500' : ($currentStep === $step ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/5') }}">
                    {{ $step }}. {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Flash --}}
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-sm font-medium">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-sm font-medium">{{ session('error') }}</div>
    @endif
    @if (session()->has('warning'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-600 dark:text-amber-400 text-sm font-medium" role="status">{{ session('warning') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-sm font-medium">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ═══════════ Paso 1 · Calendario ═══════════ --}}
    @if ($currentStep === 1)
        <div class="space-y-6" role="tabpanel" id="tt-step-1" aria-labelledby="tt-tab-1">
            {{-- <div class="bg-white dark:bg-gray-900/40 border border-gray-200 dark:border-white/5 rounded-lg p-5"> --}}
                {{-- <h2 class="text-sm font-extrabold text-gray-900 dark:text-white mb-4">1 · Calendario</h2> --}}
                {{-- <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Pulsa «+ Nuevo borrador» para crear un calendario para un plan de estudio.</p> --}}

            {{-- </div> --}}

            {{-- Detalle del calendario seleccionado (antes: lista de todos los
                 calendarios del lapso; el switcher global ya cubre la elección). --}}
            @if ($selectedCalendarDetail)
                <div x-data="{ scheduleDialogOpen: false, teacherTotalsDialogOpen: false }" class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3">
                            <h2 class="text-sm font-extrabold text-gray-900 dark:text-white">{{ $selectedCalendarDetail['name'] }}</h2>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $selectedCalendarDetail['status'] === 'active' ? 'bg-emerald-500/15 text-emerald-600' : ($selectedCalendarDetail['status'] === 'archived' ? 'bg-gray-500/10 text-gray-500' : 'bg-amber-500/15 text-amber-600') }}">
                                {{ $selectedCalendarDetail['status'] }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="openEditCalendarForm"
                                class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">Editar</button>
                            <button wire:click="duplicateCalendar({{ $selectedCalendarDetail['id'] }})"
                                wire:loading.attr="disabled" wire:target="duplicateCalendar"
                                class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 text-xs font-bold border border-gray-200 dark:border-white/10">
                                <span wire:loading.remove wire:target="duplicateCalendar">Duplicar</span>
                                <span wire:loading wire:target="duplicateCalendar">Duplicando…</span>
                            </button>
                            @if ($selectedCalendarDetail['status'] === 'draft')
                                <button wire:click="activateCalendar({{ $selectedCalendarDetail['id'] }})"
                                    class="px-3 py-1.5 rounded-lg bg-white/5 text-gray-200 text-xs font-bold border border-gray-200 dark:border-white/10">Activar</button>
                                <button wire:click="deleteCalendar({{ $selectedCalendarDetail['id'] }})"
                                    class="px-3 py-1.5 rounded-lg text-red-400 hover:text-red-300 text-xs font-bold">Eliminar</button>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @php
                            $detailItems = [
                                'Lapso' => $selectedCalendarDetail['lapso_name'] ?? '—',
                                'Plan de estudio' => $selectedCalendarDetail['pestudio_name'] ?? '—',
                                'Bloque (min)' => (string) $selectedCalendarDetail['period_minutes'],
                                'Asignaturas por período' => (string) ($selectedCalendarDetail['max_subjects_per_period'] ?? 2),
                                'Estrategia' => ($selectedCalendarDetail['strategy'] ?? 'optimized') === 'legacy' ? 'Legacy' : 'Optimizado',
                                'Versión' => 'v'.$selectedCalendarDetail['version'],
                                'Calidad' => $selectedCalendarDetail['quality_score'] !== null ? $selectedCalendarDetail['quality_score'].'%' : '—',
                                'Turnos' => (string) $selectedCalendarDetail['shifts_count'],
                                'Períodos' => $selectedCalendarDetail['periods_count'].' ('.$selectedCalendarDetail['class_periods_count'].' clase · '.$selectedCalendarDetail['break_periods_count'].' recreo)',
                                'Clases' => (string) $selectedCalendarDetail['lessons_count'],
                                'Slots asignados' => (string) $selectedCalendarDetail['slots_count'],
                                'Conflictos' => (string) $selectedCalendarDetail['conflicts_count'],
                                'Creado' => $selectedCalendarDetail['created_at'] ?? '—',
                                'Actualizado' => $selectedCalendarDetail['updated_at'] ?? '—',
                            ];
                        @endphp
                        @foreach ($detailItems as $label => $value)
                            <div class="rounded-lg bg-white/5 dark:bg-white/5 border border-gray-200 dark:border-white/10 p-3">
                                <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1">{{ $label }}</div>
                                <div class="text-sm font-bold text-gray-900 dark:text-white break-words">{{ $value }}</div>
                            </div>
                        @endforeach
                        @if (count($selectedCalendarDetail['teacher_totals'] ?? []))
                            <div class="rounded-lg border border-sky-500/30 bg-sky-500/5 p-3">
                                <div class="text-[10px] font-bold uppercase tracking-widest text-sky-700 dark:text-sky-300">
                                    Resumen por profesor
                                </div>
                                <button type="button"
                                    x-on:click="teacherTotalsDialogOpen = true"
                                    class="mt-1 inline-flex items-center gap-1.5 text-sm font-bold text-sky-700 hover:text-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 dark:text-sky-300"
                                    aria-label="Ver resumen detallado por profesor">
                                    Ver detalle
                                    <span aria-hidden="true">→</span>
                                </button>
                            </div>
                        @endif
                        @if (count($selectedCalendarDetail['teacher_totals'] ?? []))
                            <div x-cloak x-show="teacherTotalsDialogOpen"
                                x-on:keydown.escape.window="teacherTotalsDialogOpen = false"
                                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 p-4"
                                role="dialog" aria-modal="true" aria-label="Resumen detallado por profesor">
                                <div x-on:click.stop class="max-h-[90vh] w-full max-w-5xl overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-white/10 dark:bg-gray-900">
                                    <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-white/10">
                                        <div>
                                            <h3 class="text-xs font-extrabold uppercase tracking-widest text-gray-700 dark:text-gray-200">
                                                Resumen por profesor
                                            </h3>
                                            <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                                {{ count($selectedCalendarDetail['teacher_totals']) }} profesores · lessons, bloques requeridos y asignaciones
                                            </p>
                                        </div>
                                        <button type="button" x-on:click="teacherTotalsDialogOpen = false"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-full text-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-sky-500/50 dark:hover:bg-white/10 dark:hover:text-white"
                                            aria-label="Cerrar resumen por profesor">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="max-h-[75vh] space-y-2 overflow-y-auto p-4">
                                        @foreach ($selectedCalendarDetail['teacher_totals'] as $teacher)
                                            <div class="rounded-lg border border-sky-500/15 bg-sky-500/5 p-3 dark:bg-white/[0.03]">
                                                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                    <div class="text-xs font-extrabold text-gray-800 dark:text-gray-100">
                                                        {{ $teacher['name'] }}
                                                        <span class="font-normal text-gray-400">#{{ $teacher['id'] }}</span>
                                                    </div>
                                                    <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                                        {{ $teacher['lessons'] }} lessons
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2 sm:grid-cols-5">
                                                    <div>
                                                        <div class="text-[9px] uppercase tracking-widest text-gray-500 dark:text-gray-400">Teóricos</div>
                                                        <div class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $teacher['blocks_t'] }}</div>
                                                    </div>
                                                    <div>
                                                        <div class="text-[9px] uppercase tracking-widest text-gray-500 dark:text-gray-400">Prácticos</div>
                                                        <div class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $teacher['blocks_p'] }}</div>
                                                    </div>
                                                    <div>
                                                        <div class="text-[9px] uppercase tracking-widest text-gray-500 dark:text-gray-400">Requeridos</div>
                                                        <div class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $teacher['required_blocks'] }}</div>
                                                    </div>
                                                    <div>
                                                        <div class="text-[9px] uppercase tracking-widest text-gray-500 dark:text-gray-400">Asignados</div>
                                                        <div class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $teacher['assigned_slots'] }}</div>
                                                    </div>
                                                    <div>
                                                        <div class="text-[9px] uppercase tracking-widest text-gray-500 dark:text-gray-400">Pendientes</div>
                                                        <div class="text-sm font-bold {{ $teacher['required_blocks'] > $teacher['assigned_slots'] ? 'text-amber-600 dark:text-amber-300' : 'text-emerald-600 dark:text-emerald-300' }}">
                                                            {{ max(0, $teacher['required_blocks'] - $teacher['assigned_slots']) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (count($selectedCalendarDetail['schedule_blocks'] ?? []))
                            <div class="rounded-lg border border-emerald-500/30 bg-emerald-500/5 p-3">
                                <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-700 dark:text-emerald-300 mb-1">
                                    Bloques de horario
                                </div>
                                <button type="button"
                                    x-on:click="scheduleDialogOpen = true"
                                    class="mt-1 inline-flex items-center gap-1.5 text-sm font-bold text-emerald-700 hover:text-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 dark:text-emerald-300"
                                    aria-label="Ver bloques de horario del plan de estudio">
                                    Ver detalle
                                    <span aria-hidden="true">→</span>
                                </button>
                            </div>
                        @endif
                    </div>

                    @if (count($selectedCalendarDetail['schedule_blocks'] ?? []))
                        <div x-cloak x-show="scheduleDialogOpen"
                            x-on:keydown.escape.window="scheduleDialogOpen = false"
                            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 p-4"
                            role="dialog" aria-modal="true"
                            aria-label="Bloques de horario del plan de estudio">
                            <div x-on:click.stop class="max-h-[90vh] w-full max-w-7xl overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-white/10 dark:bg-gray-900">
                                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-white/10">
                                    <div>
                                        <h3 class="text-xs font-extrabold uppercase tracking-widest text-gray-700 dark:text-gray-200">
                                            Bloques de horario del plan de estudio
                                        </h3>
                                        <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                            {{ $selectedCalendarDetail['pestudio_name'] ?? 'Plan de estudio seleccionado' }}
                                            · detalle por día, turno y bloque
                                        </p>
                                    </div>
                                    <button type="button" x-on:click="scheduleDialogOpen = false"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-full text-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 dark:hover:bg-white/10 dark:hover:text-white"
                                        aria-label="Cerrar detalle de bloques de horario">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="overflow-auto p-4">
                        <div class="rounded-lg border border-gray-200 dark:border-white/10 overflow-hidden">
                            <div class="px-4 py-3 bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div>
                                        <h3 class="text-xs font-extrabold uppercase tracking-widest text-gray-700 dark:text-gray-200">
                                            Bloques de horario del plan de estudio
                                        </h3>
                                        <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                            {{ $selectedCalendarDetail['pestudio_name'] ?? 'Plan de estudio seleccionado' }}
                                            · detalle por día, turno y bloque
                                        </p>
                                    </div>
                                    <span class="px-2 py-1 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold">
                                        {{ $selectedCalendarDetail['periods_count'] }} bloques registrados
                                    </span>
                                </div>
                            </div>

                            @php
                                $scheduleDays = $selectedCalendarDetail['schedule_blocks'];
                                $scheduleRows = [];
                                $scheduleCells = [];

                                foreach ($scheduleDays as $dayIndex => $day) {
                                    foreach ($day['shift_groups'] ?? [] as $shiftGroup) {
                                        foreach ($shiftGroup['blocks'] as $block) {
                                                    $rowKey = $shiftGroup['code'].'-'.$block['order'];
                                                    $scheduleRows[$rowKey] ??= [
                                                        'code' => $shiftGroup['code'],
                                                        'name' => $shiftGroup['name'],
                                                        'order' => $block['order'],
                                                    ];
                                                    $scheduleCells[$rowKey][$dayIndex] = $block;
                                        }
                                    }
                                }
                            @endphp

                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[860px] table-fixed text-left text-xs">
                                    <caption class="sr-only">Bloques de horario comparados por día</caption>
                                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-white/10 dark:bg-white/5">
                                        <tr class="text-[10px] font-extrabold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                                    <th scope="col" class="w-36 px-4 py-2.5">Turno · bloque</th>
                                                    @foreach ($scheduleDays as $day)
                                                        <th scope="col" class="px-4 py-2.5 text-center">
                                                            {{ $day['day'] }}
                                                            <span class="mt-0.5 block text-[9px] font-normal normal-case tracking-normal opacity-70">
                                                                {{ count($day['blocks']) }} bloques
                                                            </span>
                                                        </th>
                                                    @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                        @foreach ($scheduleRows as $rowKey => $row)
                                                    <tr class="bg-white/5">
                                                        <th scope="row" class="px-4 py-2.5 font-bold text-gray-700 dark:text-gray-200">
                                                            <span class="block">{{ $row['name'] }}</span>
                                                            <span class="mt-0.5 block text-[9px] font-normal uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                                                {{ $row['code'] }} · #{{ $row['order'] }}
                                                            </span>
                                                        </th>
                                                        @foreach ($scheduleDays as $dayIndex => $day)
                                                            @php $block = $scheduleCells[$rowKey][$dayIndex] ?? null; @endphp
                                                            @if ($block)
                                                                @php $isBreak = $block['type'] === 'Recreo'; @endphp
                                                                <td class="px-3 py-2.5 text-center {{ $isBreak ? 'bg-amber-500/5' : '' }}">
                                                                    <span class="block font-mono font-bold text-gray-800 dark:text-gray-100">
                                                                        {{ $block['start'] }}–{{ $block['end'] }}
                                                                    </span>
                                                                    <span class="mt-1 inline-flex rounded px-1.5 py-0.5 text-[9px] font-bold {{ $isBreak ? 'bg-amber-500/15 text-amber-600 dark:text-amber-300' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-300' }}">
                                                                        {{ $block['type'] }}
                                                                    </span>
                                                                </td>
                                                            @else
                                                                <td class="px-3 py-2.5 text-center text-gray-300 dark:text-gray-600">—</td>
                                                            @endif
                                                        @endforeach
                                                    </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mt-5 rounded-lg border border-dashed border-gray-300 dark:border-white/10 px-4 py-5 text-center text-xs text-gray-500 dark:text-gray-400">
                            Este plan de estudio todavía no tiene bloques de horario registrados.
                        </div>
                    @endif

                    @if ($selectedCalendarDetail['conflicts_count'] > 0)
                        <div class="mt-3 px-3 py-2 rounded-lg bg-amber-500/5 border border-amber-500/30 text-amber-600 dark:text-amber-400 text-xs font-medium">
                            ⚠ Este calendario tiene {{ $selectedCalendarDetail['conflicts_count'] }} conflicto(s) registrado(s) — revísalos en el editor manual.
                        </div>
                    @endif
                </div>
            @elseif (count($calendars))
                <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-5 text-sm text-gray-500 dark:text-gray-400">
                    Selecciona un calendario en el switcher superior para ver su detalle.
                </div>
            @endif

            @if ($calendarId)
                <div class="bg-white dark:bg-gray-900/40 border border-gray-200 dark:border-white/5 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-extrabold text-gray-900 dark:text-white">Turno y períodos</h2>
                        <button wire:click="$set('showShiftForm', true)" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">+ Nuevo turno</button>
                    </div>

                    @if ($showShiftForm)
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
                            <input type="text" wire:model="shiftCode" placeholder="Código (M/T)"
                                class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                            <input type="text" wire:model="shiftName" placeholder="Nombre (Mañana)"
                                class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                            <input type="time" wire:model="shiftStart"
                                class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                            <input type="time" wire:model="shiftEnd"
                                class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                            <div class="flex gap-2">
                                <button wire:click="createShift" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-xs font-bold">Guardar</button>
                                <button wire:click="$set('showShiftForm', false)" class="px-4 py-2 rounded-lg bg-white/5 text-gray-400 text-xs font-bold">Cancelar</button>
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach ($shifts as $shift)
                            <span class="px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-xs font-bold text-gray-300">
                                {{ $shift->name }} ({{ $shift->start_time }}–{{ $shift->end_time }})
                            </span>
                        @endforeach
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <select wire:model.live="shiftId" class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="0">Elige un turno</option>
                            @foreach ($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                            @endforeach
                        </select>
                        <button wire:click="generatePeriods"
                            @disabled((int) $shiftId <= 0)
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            wire:target="generatePeriods"
                            class="px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-200 text-sm font-bold border border-white/10 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-white/5">
                            <span wire:loading.remove wire:target="generatePeriods">Vista previa de períodos</span>
                            <span wire:loading wire:target="generatePeriods" class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Generando…
                            </span>
                        </button>
                        <button wire:click="loadEditablePeriods"
                            @disabled((int) $shiftId <= 0)
                            wire:loading.attr="disabled"
                            wire:target="loadEditablePeriods"
                            class="px-4 py-2 rounded-lg bg-sky-500/10 hover:bg-sky-500/20 text-sky-700 dark:text-sky-300 text-sm font-bold border border-sky-500/30 disabled:opacity-50 disabled:cursor-not-allowed">
                            Editar bloques
                        </button>
                        <button wire:click="savePeriods"
                            @disabled((int) $shiftId <= 0)
                            wire:loading.attr="disabled"
                            wire:target="savePeriods"
                            class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="savePeriods">Guardar períodos</span>
                            <span wire:loading wire:target="savePeriods">Guardando…</span>
                        </button>
                        <button wire:click="regeneratePeriods"
                            @disabled((int) $shiftId <= 0)
                            wire:loading.attr="disabled"
                            wire:target="regeneratePeriods"
                            class="px-4 py-2 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 text-sm font-bold border border-amber-500/30 disabled:opacity-50 disabled:cursor-not-allowed">
                            Regenerar
                        </button>
                    </div>

                    @if ($periods)
                        @php
                            $previewClassPeriods = collect($periods)->where('is_break', false);
                            $previewBreakPeriods = collect($periods)->where('is_break', true);
                            $selectedShift = $shifts->firstWhere('id', (int) $shiftId);
                        @endphp
                        <div class="mt-4 rounded-lg border border-emerald-500/20 bg-emerald-500/5 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-xs font-extrabold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Detalle de períodos</h3>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $selectedShift?->name ?? 'Turno seleccionado' }}
                                        @if ($selectedShift)
                                            · {{ $selectedShift->code }} · {{ $selectedShift->start_time }}–{{ $selectedShift->end_time }}
                                        @endif
                                    </p>
                                </div>
                                <div class="flex flex-wrap gap-2 text-[10px] font-bold">
                                    <span class="px-2 py-1 rounded-md bg-white/10 text-gray-500 dark:text-gray-300">{{ count($periods) }} total</span>
                                    <span class="px-2 py-1 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">{{ $previewClassPeriods->count() }} clase</span>
                                    <span class="px-2 py-1 rounded-md bg-amber-500/10 text-amber-600 dark:text-amber-400">{{ $previewBreakPeriods->count() }} recreo</span>
                                </div>
                                <div class="mt-3 flex flex-wrap items-end gap-2 rounded-lg border border-sky-500/20 bg-sky-500/5 p-2.5">
                                    <label class="min-w-[150px] text-[10px] font-bold uppercase tracking-widest text-sky-700 dark:text-sky-300">
                                        Día del bloque
                                        <select wire:model.live="periodDayOfWeek"
                                            class="mt-1 w-full rounded-md border border-sky-500/20 bg-white px-2 py-1.5 text-xs font-semibold text-gray-800 dark:bg-white/5 dark:text-gray-200">
                                            @foreach ([1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'] as $day => $dayName)
                                                <option value="{{ $day }}">{{ $dayName }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label class="min-w-[220px] flex-1 text-[10px] font-bold uppercase tracking-widest text-sky-700 dark:text-sky-300">
                                        Plan de estudio del bloque
                                        <select wire:model.live="periodPestudioId"
                                            class="mt-1 w-full rounded-md border border-sky-500/20 bg-white px-2 py-1.5 text-xs font-semibold text-gray-800 dark:bg-white/5 dark:text-gray-200">
                                            <option value="0">Selecciona un plan</option>
                                            @foreach ($this->calendarPestudios() as $pestudioId => $pestudioName)
                                                <option value="{{ $pestudioId }}">{{ $pestudioName }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <button type="button" wire:click="addPeriodBlock"
                                        class="rounded-md bg-sky-600 px-3 py-2 text-xs font-bold text-white transition-colors hover:bg-sky-700 disabled:opacity-50"
                                        @disabled((int) $periodPestudioId <= 0)>
                                        + Nuevo bloque
                                    </button>
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400">
                                        El bloque nuevo se creará en el día seleccionado.
                                    </span>
                                </div>
                            </div>

                            <div class="mt-4 overflow-x-auto rounded-lg border border-gray-200 dark:border-white/10">
                                <table class="w-full min-w-[760px] text-xs">
                                    <thead class="bg-gray-50 dark:bg-white/5 text-left text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                        <tr>
                                            <th class="px-3 py-2">#</th>
                                            <th class="px-3 py-2">Día</th>
                                            <th class="px-3 py-2">Pestudio</th>
                                            <th class="px-3 py-2">ID</th>
                                            <th class="px-3 py-2">Tipo</th>
                                            <th class="px-3 py-2">Inicio</th>
                                            <th class="px-3 py-2">Fin</th>
                                            <th class="px-3 py-2">Duración</th>
                                            <th class="px-3 py-2">Descripción</th>
                                            <th class="px-3 py-2">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($periods as $p)
                                            @php
                                                $duration = max(0, $p['end'] - $p['start']);
                                                $hours = intdiv($duration, 60);
                                                $minutes = $duration % 60;
                                                $durationLabel = $hours > 0 ? $hours.' h' : '';
                                                $durationLabel .= $minutes > 0 ? ($durationLabel ? ' ' : '').$minutes.' min' : '';
                                            @endphp
                                            <tr wire:key="period-editor-{{ $p['id'] ?? 'new-'.$loop->index }}"
                                                class="border-t border-gray-100 dark:border-white/5 {{ $p['is_break'] ? 'bg-amber-500/5' : '' }}">
                                                <td class="px-3 py-2 font-bold text-gray-700 dark:text-gray-200">{{ $p['order'] }}</td>
                                                <td class="px-3 py-2">
                                                    <select wire:model="periods.{{ $loop->index }}.day_of_week"
                                                        class="rounded border border-gray-200 bg-white/70 px-2 py-1 text-[11px] dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                                                        @foreach ([1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'] as $day => $dayName)
                                                            <option value="{{ $day }}">{{ $dayName }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ $p['pestudio'] }}</td>
                                                <td class="px-3 py-2 font-mono text-gray-500 dark:text-gray-400">{{ $p['pestudio_id'] }}</td>
                                                <td class="px-3 py-2">
                                                    <span class="px-1.5 py-0.5 rounded {{ $p['is_break'] ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' }}">
                                                        {{ $p['is_break'] ? 'Recreo' : 'Clase' }}
                                                    </span>
                                                    <label class="ml-2 inline-flex items-center gap-1 text-[10px] text-gray-500">
                                                        <input type="checkbox" wire:model="periods.{{ $loop->index }}.is_break">
                                                        Recreo
                                                    </label>
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="time" step="60" value="{{ sprintf('%02d:%02d', intdiv($p['start'], 60), $p['start'] % 60) }}"
                                                        wire:change="updatePeriodTime({{ $loop->index }}, 'start', $event.target.value)"
                                                        class="rounded border border-gray-200 bg-white/70 px-2 py-1 font-mono text-[11px] dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="time" step="60" value="{{ sprintf('%02d:%02d', intdiv($p['end'], 60), $p['end'] % 60) }}"
                                                        wire:change="updatePeriodTime({{ $loop->index }}, 'end', $event.target.value)"
                                                        class="rounded border border-gray-200 bg-white/70 px-2 py-1 font-mono text-[11px] dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                                                </td>
                                                <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ $durationLabel }}</td>
                                                <td class="px-3 py-2 text-gray-500 dark:text-gray-400">
                                                    <input type="text" wire:model="periods.{{ $loop->index }}.label"
                                                        placeholder="Clase o recreo"
                                                        class="w-full rounded border border-gray-200 bg-white/70 px-2 py-1 text-[11px] dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                                                </td>
                                                <td class="px-3 py-2">
                                                    <button type="button" wire:click="confirmRemovePeriodBlock({{ $loop->index }})"
                                                        title="Eliminar bloque del editor"
                                                        aria-label="Eliminar bloque {{ $p['order'] }} de {{ $p['pestudio'] }}"
                                                        class="rounded px-2 py-1 text-[10px] font-bold text-red-600 hover:bg-red-500/10 dark:text-red-300">
                                                        Eliminar
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <p class="mt-3 text-[11px] text-gray-500 dark:text-gray-400">
                                Cada fila representa un período independiente de un día concreto. Puedes cambiar el día, la hora, el tipo o eliminar únicamente ese período.
                            </p>
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <button type="button" wire:click="addPeriodBlock"
                                    class="rounded-lg bg-sky-500/10 px-3 py-2 text-xs font-bold text-sky-700 hover:bg-sky-500/20 dark:text-sky-300">
                                    + Agregar bloque
                                </button>
                                <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                    Selecciona el plan y el día para crear un bloque independiente.
                                </span>
                            </div>
                        </div>
                    @endif

                    @if ($periodsList->count())
                        <div class="mt-4 text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ $periodsList->count() }} períodos generados.</div>
                    @endif
                </div>
            @endif
        </div>
    @endif

    {{-- ═══════════ Paso 2 · Aulas ═══════════ --}}
    @if ($currentStep === 2)
        <div class="space-y-6" role="tabpanel" id="tt-step-2" aria-labelledby="tt-tab-2">
            <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-5">
                <div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
                    <h2 class="text-sm font-extrabold text-gray-900 dark:text-white">Aulas registradas</h2>
                    <div class="flex items-center gap-2">
                        <button wire:click="openRoomCreateModal"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8h-16"/></svg>
                            <span>Nueva aula</span>
                        </button>
                        @if (count($pendingRooms))
                            <button wire:click="saveAllRooms"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                <span>Guardar todas las aulas generadas ({{ count($pendingRooms) }})</span>
                            </button>
                        @endif
                        <button wire:click="confirmDeleteAllRooms"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-red-400 hover:text-red-300 text-sm font-bold border border-white/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            <span>Eliminar todas</span>
                        </button>
                    </div>
                </div>

                @forelse ($roomsByPeducativo as $group)
                    <div class="mb-6 last:mb-0">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-widest">
                                {{ $group['name'] }}
                            </span>
                            <span class="text-[11px] text-gray-400 dark:text-gray-500">{{ count($group['rooms']) }} aula(s)</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach ($group['rooms'] as $room)
                                <div class="flex items-center justify-between gap-2 p-4 rounded-lg bg-white/5 border {{ ! empty($room['pending']) ? 'border-amber-500/40' : 'border-white/10' }}">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm font-extrabold text-gray-900 dark:text-white truncate">{{ $room['code'] }}</div>
                                            @if (! empty($room['pending']))
                                                <span class="px-1.5 py-0.5 rounded bg-amber-500/20 border border-amber-500/40 text-amber-600 dark:text-amber-400 text-[9px] font-bold uppercase tracking-widest">Pendiente</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $room['name'] }} · {{ $room['capacity'] }} · {{ $room['type'] }}</div>
                                    </div>
                                    <div class="flex items-center gap-1 shrink-0">
                                        @if (empty($room['pending']))
                                            <button wire:click="editRoom({{ $room['id'] }})" class="px-2 py-1 rounded-md bg-white/5 hover:bg-white/10 border border-white/10 text-emerald-600 dark:text-emerald-400 text-[11px] font-bold">Editar</button>
                                            <button wire:click="deleteRoom({{ $room['id'] }})" class="px-2 py-1 rounded-md bg-white/5 hover:bg-white/10 border border-white/10 text-red-400 hover:text-red-300 text-[11px] font-bold">Eliminar</button>
                                        @else
                                            <button wire:click="removePendingRoom('{{ $room['id'] }}')" class="px-2 py-1 rounded-md bg-white/5 hover:bg-white/10 border border-white/10 text-red-400 hover:text-red-300 text-[11px] font-bold">Quitar</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-gray-500 dark:text-gray-400">Sin aulas registradas.</div>
                @endforelse
            </div>

            {{-- Modal: crear aula --}}
            @if ($showRoomCreateModal)
                <x-modal-card title="Registrar aula" blur="lg" wire:model="showRoomCreateModal" max-width="lg" persistent>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Código *</label>
                                <input type="text" wire:model="roomCode" placeholder="A-101"
                                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                                @error('roomCode') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Nombre *</label>
                                <input type="text" wire:model="roomName" placeholder="Aula 101"
                                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                                @error('roomName') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Capacidad *</label>
                                <input type="number" wire:model="roomCapacity" min="1" placeholder="30"
                                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                                @error('roomCapacity') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Tipo *</label>
                                <select wire:model="roomType" class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                                    @foreach (['aula', 'laboratorio', 'patio', 'cancha', 'taller', 'salon'] as $type)
                                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                                @error('roomType') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        @if ($roomSectionLinkLabel)
                            <div class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg bg-white/5 border border-white/10">
                                <div class="flex items-center gap-2 text-sm text-gray-300">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-14a2 2 0 01-2-2v-6a2 2 0 012-2h2m4 0a2 2 0 110-4 2 2 0 010 4zm8 0a2 2 0 110-4 2 2 0 010 4z"/></svg>
                                    <span class="text-xs"><span class="font-bold text-emerald-400">Sección vinculada:</span> {{ $roomSectionLinkLabel }}</span>
                                </div>
                                <button wire:click="clearRoomSection" class="text-xs text-red-400 hover:text-red-300 font-bold">Quitar</button>
                            </div>
                        @endif

                        <div class="flex flex-wrap items-center gap-2">
                            <button wire:click="saveRoom" class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">Registrar aula</button>
                            <button wire:click="openRoomSectionModal"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-bold border border-white/10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Vincular a Grado/Sección</span>
                            </button>
                            <button wire:click="confirmBulkCreateRooms" class="px-5 py-2.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-200 text-sm font-bold border border-white/10">
                                Registrar aulas por grado/sección
                            </button>
                        </div>
                    </div>

                    <x-slot name="footer">
                        <div class="flex items-center justify-end gap-2">
                            <button wire:click="closeRoomCreateModal" class="px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-bold border border-white/10">Cancelar</button>
                        </div>
                    </x-slot>
                </x-modal-card>
            @endif

            {{-- Dialog: editar aula --}}
            <x-modal-card title="Editar aula" blur="lg" wire:model="roomEditOpen" max-width="md" persistent>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Código</label>
                        <input type="text" wire:model="editRoomCode" placeholder="Código (A-101)"
                            class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                        @error('editRoomCode') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Nombre</label>
                        <input type="text" wire:model="editRoomName" placeholder="Nombre (Aula 101)"
                            class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                        @error('editRoomName') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Capacidad</label>
                            <input type="number" wire:model="editRoomCapacity" min="1"
                                class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                            @error('editRoomCapacity') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Tipo</label>
                            <select wire:model="editRoomType" class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm">
                                @foreach (['aula', 'laboratorio', 'patio', 'cancha', 'taller', 'salon'] as $type)
                                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                            @error('editRoomType') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Sección vinculada (opcional)</label>
                        <select wire:model="editRoomSeccionId" class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                            <option value="">Sin sección vinculada</option>
                            @foreach ($allRoomSecciones as $s)
                                <option value="{{ $s['id'] }}">{{ $s['label'] }}</option>
                            @endforeach
                        </select>
                        @error('editRoomSeccionId') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        @if ($editRoomSectionLinkedRooms && $editRoomSectionLinkedRooms->isNotEmpty())
                            <p class="mt-1.5 flex items-start gap-1.5 text-[11px] leading-snug text-amber-700 dark:text-amber-300">
                                <svg class="w-3.5 h-3.5 shrink-0 mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.19 19h13.62a2 2 0 001.64-3.14L13.64 5.14a2 2 0 00-3.28 0L3.55 15.86A2 2 0 005.19 19z"/></svg>
                                <span>Otra(s) aula(s) ya vinculada(s) a esta sección: <strong>{{ $editRoomSectionLinkedRooms->pluck('code')->implode(', ') }}</strong></span>
                            </p>
                        @endif
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex items-center justify-end gap-2">
                        <button wire:click="closeRoomEdit" class="px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-bold border border-white/10">Cancelar</button>
                        <button wire:click="updateRoom" class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">Guardar cambios</button>
                    </div>
                </x-slot>
            </x-modal-card>

            {{-- Modal: Vincular a Grado/Sección (opcional) --}}
            @if ($showRoomSectionModal)
                <x-modal-card title="Vincular a Grado/Sección" blur="lg" wire:model="showRoomSectionModal" max-width="lg" persistent>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Selecciona el plan de estudio, grado y sección que ocupará este aula.</p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Plan de estudio</label>
                            <select wire:model.live="roomPestudioId" class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                                <option value="">Selecciona un plan…</option>
                                @foreach ($roomPestudios as $p)
                                    <option value="{{ $p->id }}">{{ $p->code }} · {{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Grado</label>
                            <select wire:model.live="roomGradoId" class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                                <option value="">Selecciona un grado…</option>
                                @foreach ($roomGrados as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Sección</label>
                            <select wire:model.live="roomSeccionId" class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                                <option value="">Selecciona una sección…</option>
                                @foreach ($roomSecciones as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($roomSectionLinkedRooms && $roomSectionLinkedRooms->isNotEmpty())
                            <div class="flex items-start gap-2 px-3 py-2 rounded-lg bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 text-amber-700 dark:text-amber-300 text-xs">
                                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.19 19h13.62a2 2 0 001.64-3.14L13.64 5.14a2 2 0 00-3.28 0L3.55 15.86A2 2 0 005.19 19z"/></svg>
                                <span>
                                    Esta sección ya tiene {{ $roomSectionLinkedRooms->count() }} aula(s) vinculada(s):
                                    <strong>{{ $roomSectionLinkedRooms->pluck('code')->implode(', ') }}</strong>.
                                    Puedes vincular varias aulas a la misma sección.
                                </span>
                            </div>
                        @endif
                    </div>
                    <x-slot name="footer">
                        <div class="flex items-center justify-end gap-2">
                            <button wire:click="closeRoomSectionModal" class="px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-bold border border-white/10">Cancelar</button>
                            <button wire:click="closeRoomSectionModal" class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">Listo</button>
                        </div>
                    </x-slot>
                </x-modal-card>
            @endif
        </div>
    @endif

    {{-- ═══════════ Paso 3 · Clases ═══════════ --}}
    @if ($currentStep === 3)
        <div class="space-y-6" role="tabpanel" id="tt-step-3" aria-labelledby="tt-tab-3">
            <div class="bg-white dark:bg-gray-900/40 border border-gray-200 dark:border-white/5 rounded-lg p-5">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-extrabold text-gray-900 dark:text-white">3 · Clases</h2>
                        <span class="px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-widest">{{ $step3SelectedCount }} seleccionadas</span>
                    </div>
                    {{-- <div class="flex items-center gap-1">
                        <button wire:click="setStep3ViewMode('tabs')"
                            class="px-3 py-1.5 rounded-lg text-[11px] font-bold border transition-all duration-200 {{ $step3ViewMode === 'tabs' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white/5 text-gray-400 border-white/10 hover:text-gray-300' }}">Pestañas</button>
                        <button wire:click="setStep3ViewMode('flat')"
                            class="px-3 py-1.5 rounded-lg text-[11px] font-bold border transition-all duration-200 {{ $step3ViewMode === 'flat' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white/5 text-gray-400 border-white/10 hover:text-gray-300' }}">Lista</button>
                    </div> --}}
                </div>

                {{-- Toolbar: búsqueda, orden, asignación masiva --}}
                <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2">
                    <input type="text" wire:model.live.debounce.300ms="step3Search" placeholder="Buscar asignatura o profesor..."
                        class="col-span-1 sm:col-span-2 lg:col-span-1 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                    <select wire:model.live="step3Sort" class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="asignatura">Orden: Asignatura</option>
                        <option value="profesor">Orden: Profesor</option>
                        <option value="blocks">Orden: Bloques</option>
                    </select>
                    <select wire:model.live="step3SortDir" class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="asc">Asc</option>
                        <option value="desc">Desc</option>
                    </select>
                    <select wire:model="bulkShiftId" class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Turno masivo…</option>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}">{{ $shift->code }}</option>
                        @endforeach
                    </select>
                    <select wire:model="bulkRoomType" class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Aula masiva…</option>
                        @foreach (['aula', 'laboratorio', 'patio', 'cancha', 'taller', 'salon'] as $type)
                            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                    <div class="col-span-1 sm:col-span-2 lg:col-span-5 flex items-center gap-2">
                        <button wire:click="bulkAssignShift" wire:loading.attr="disabled" wire:target="bulkAssignShift"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 text-xs font-bold border border-white/10">
                            <span wire:loading.remove wire:target="bulkAssignShift">Aplicar turno</span>
                            <span wire:loading wire:target="bulkAssignShift" class="inline-flex items-center gap-1.5">
                                <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Aplicando…
                            </span>
                        </button>
                        <button wire:click="bulkAssignRoomType" wire:loading.attr="disabled" wire:target="bulkAssignRoomType"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 text-xs font-bold border border-white/10">
                            <span wire:loading.remove wire:target="bulkAssignRoomType">Aplicar aula</span>
                            <span wire:loading wire:target="bulkAssignRoomType" class="inline-flex items-center gap-1.5">
                                <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Aplicando…
                            </span>
                        </button>
                        <button type="button" wire:click="resetLessonCheckboxes"
                            wire:loading.attr="disabled" wire:target="resetLessonCheckboxes"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="px-3 py-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-300 text-xs font-bold border border-amber-500/20">
                            <span wire:loading.remove wire:target="resetLessonCheckboxes">Desmarcar todo</span>
                            <span wire:loading wire:target="resetLessonCheckboxes">Reiniciando…</span>
                        </button>
                        @if (isset($step3Warnings) && count($step3Warnings))
                            <span class="text-[11px] text-amber-500">⚠ {{ count($step3Warnings) }} lección(es) con advertencia</span>
                        @endif
                    </div>
                </div>

                @php $orphanLessons = $this->timetableOrphanLessons(); @endphp
                @if ($orphanLessons !== [])
                    <div class="mb-4 rounded-lg border border-red-500/40 bg-red-500/5 px-4 py-3" role="alert">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <div class="text-xs font-bold uppercase tracking-widest text-red-500">
                                    Lessons con referencia académica huérfana
                                </div>
                                <p class="mt-1 text-[11px] text-gray-600 dark:text-gray-300">
                                    Estas lessons ya están registradas, pero su Pevaluación no existe. No se pueden editar ni publicar hasta restaurar la Pevaluación o retirar la lesson.
                                </p>
                            </div>
                            <span class="rounded-md bg-red-500/10 px-2 py-1 text-[10px] font-bold text-red-500">{{ count($orphanLessons) }}</span>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($orphanLessons as $orphanLesson)
                                <span class="rounded border border-red-500/30 bg-red-500/10 px-2 py-1 text-[10px] font-bold text-red-600 dark:text-red-300">
                                    Lesson #{{ $orphanLesson['id'] }} · Pevaluacion #{{ $orphanLesson['pevaluacion_id'] }} · {{ $orphanLesson['blocks'] }} bloque(s)
                                </span>
                            @endforeach
                        </div>
                        <button type="button" wire:click="confirmRemoveOrphanLessons"
                            wire:loading.attr="disabled" wire:target="confirmRemoveOrphanLessons"
                            class="mt-3 inline-flex items-center rounded-md border border-red-500/40 bg-red-500/10 px-3 py-2 text-[11px] font-bold text-red-600 transition hover:bg-red-500/20 dark:text-red-300">
                            <span wire:loading.remove wire:target="confirmRemoveOrphanLessons">Retirar lessons huérfanas</span>
                            <span wire:loading wire:target="confirmRemoveOrphanLessons">Preparando…</span>
                        </button>
                    </div>
                @endif

                {{-- Pestañas · Pestudio --}}
                @if ($step3ViewMode === 'tabs' && $tabPestudioOptions)
                    <div class="mb-3 border-b border-gray-200 dark:border-white/10">
                        <nav class="flex w-full overflow-x-auto">
                            @foreach ($tabPestudioOptions as $opt)
                                <button type="button" wire:click="selectStep5Pestudio({{ $opt['id'] === 'general' ? "'general'" : $opt['id'] }})"
                                    class="flex-1 text-center px-3 py-2 text-[11px] font-bold uppercase tracking-widest whitespace-nowrap border-b-2 transition-all duration-200
                                    {{ (string) $activePestudioId === (string) $opt['id'] ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500' : 'text-gray-400 dark:text-gray-500 border-transparent hover:text-gray-600 dark:hover:text-gray-300' }}">
                                    {{ $opt['name'] }}
                                </button>
                            @endforeach
                        </nav>
                    </div>
                @endif

                {{-- Pestañas · Grado --}}
                @if ($step3ViewMode === 'tabs' && $tabGradoOptions)
                    <div class="mb-3 border-b border-gray-200 dark:border-white/10">
                        <nav class="flex w-full overflow-x-auto">
                            @foreach ($tabGradoOptions as $opt)
                                <button type="button" wire:click="selectStep5Grade({{ $opt['id'] === 'general' ? "'general'" : $opt['id'] }})"
                                    class="flex-1 text-center px-3 py-2 text-[11px] font-bold uppercase tracking-widest whitespace-nowrap border-b-2 transition-all duration-200
                                    {{ (string) $activeGradoId === (string) $opt['id'] ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500' : 'text-gray-400 dark:text-gray-500 border-transparent hover:text-gray-600 dark:hover:text-gray-300' }}">
                                    {{ $opt['name'] }}
                                </button>
                            @endforeach
                        </nav>
                    </div>
                @endif

                {{-- Pestañas · Sección --}}
                @if ($step3ViewMode === 'tabs' && $tabSeccionOptions)
                    <div class="mb-3 border-b border-gray-200 dark:border-white/10">
                        <nav class="flex w-full overflow-x-auto">
                            @foreach ($tabSeccionOptions as $opt)
                                <button type="button" wire:click="selectStep5Section({{ $opt['id'] === 'general' ? "'general'" : $opt['id'] }})"
                                    class="flex-1 text-center px-3 py-2 text-[11px] font-bold uppercase tracking-widest whitespace-nowrap border-b-2 transition-all duration-200
                                    {{ (string) $activeSeccionId === (string) $opt['id'] ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500' : 'text-gray-400 dark:text-gray-500 border-transparent hover:text-gray-600 dark:hover:text-gray-300' }}">
                                    {{ $opt['label'] ?? 'Sección '.$opt['name'] }}
                                </button>
                            @endforeach
                        </nav>
                    </div>
                @endif

                @php
                    $rows = $step3ViewMode === 'flat' ? $pevaluaciones : $tabActivePevaluaciones;
                    $sumT = 0;
                    $sumP = 0;
                    $selectedT = 0;
                    $selectedP = 0;
                    $selectedHalfBlocks = 0;
                    foreach ($rows as $pev) {
                        $sumT += (int) ceil(((int) ($pev->pensum?->asignatura?->hour_t_week ?? 0)) * 60 / max(1, (int) ($periodsList->count() ? $calendarPeriodMinutes ?? 60 : 60)));
                        $sumP += (int) ceil(((int) ($pev->pensum?->asignatura?->hour_p_week ?? 0)) * 60 / max(1, (int) ($periodsList->count() ? $calendarPeriodMinutes ?? 60 : 60)));
                        if (isset($lessons[$pev->id])) {
                            $selectedT += (int) ($lessons[$pev->id]['weekly_blocks_t'] ?? 0);
                            $selectedP += (int) ($lessons[$pev->id]['weekly_blocks_p'] ?? 0);
                            if (! empty($lessons[$pev->id]['is_half_group'])) {
                                $selectedHalfBlocks += (int) ($lessons[$pev->id]['weekly_blocks_t'] ?? 0)
                                    + (int) ($lessons[$pev->id]['weekly_blocks_p'] ?? 0);
                            }
                        }
                    }
                    $bulkSectionId = is_numeric($activeSeccionId) ? (int) $activeSeccionId : null;
                    $selectedPevIdsForView = collect($selectedPevs)->contains(fn ($value) => is_bool($value))
                        ? array_flip(array_map('intval', array_keys(array_filter($selectedPevs))))
                        : array_flip(array_map('intval', array_filter($selectedPevs, fn ($value) => is_numeric($value) && $value > 0)));
                    $bulkSectionSelected = $bulkSectionId !== null
                        && $rows->isNotEmpty()
                        && $rows->every(fn ($pev) => isset($selectedPevIdsForView[(int) $pev->id]));
                @endphp

                {{-- Resumen de la selección (bloques teóricos/prácticos + capacidad) --}}
                @if ($rows->isNotEmpty())
                    @php
                        // La capacidad pertenece al calendario completo: cada turno
                        // aporta sus propias celdas semanales y no debe descartarse
                        // al elegir solo el máximo entre turnos.
                        $capacidadesPorTurno = collect($shiftCapacityPerDay ?? [])
                            ->filter(fn ($capacity) => (int) $capacity > 0);
                        $capacidadPorDia = $capacidadesPorTurno->sum();
                        $capacidadSemanal = $capacidadPorDia * 5;
                        $maxSubjectsPerPeriod = max(1, (int) ($maxSubjectsPerPeriod ?? 2));
                        $fullGroupBlocks = $selectedT + $selectedP - $selectedHalfBlocks;
                        $totalBlocks = $selectedT + $selectedP;
                        $minimumCells = $fullGroupBlocks + (int) ceil($selectedHalfBlocks / $maxSubjectsPerPeriod);
                        $required = $minimumCells;
                        $overCapacity = $capacidadSemanal > 0 && $required > $capacidadSemanal;
                        $usage = $capacidadSemanal > 0 ? min(100, (int) round($required * 100 / $capacidadSemanal)) : 0;
                        $capacityGap = $capacidadSemanal - $required;
                        $maximumCells = $fullGroupBlocks + $selectedHalfBlocks;
                        $hasDryRunResult = in_array($generationState, ['preview_ready', 'published'], true)
                            && is_array($preview ?? null)
                            && array_key_exists('assignment', $preview);
                        $actualAssignment = collect($preview['assignment'] ?? []);
                        $actualPeriodIds = collect();
                        $actualAssignedBlocks = 0;
                        $actualIncompleteLessons = 0;
                        foreach ($rows as $pev) {
                            if (! isset($selectedPevIdsForView[(int) $pev->id])) {
                                continue;
                            }

                            $lessonId = (int) ($lessons[$pev->id]['id'] ?? 0);
                            $slots = collect($actualAssignment->get((string) $lessonId, $actualAssignment->get($lessonId, [])));
                            $assigned = $slots->pluck('period_id')->filter()->unique();
                            $requiredForLesson = (int) ($lessons[$pev->id]['weekly_blocks_t'] ?? 0)
                                + (int) ($lessons[$pev->id]['weekly_blocks_p'] ?? 0);
                            $actualAssignedBlocks += $assigned->count();
                            $actualPeriodIds = $actualPeriodIds->merge($assigned);
                            if ($requiredForLesson > $assigned->count()) {
                                $actualIncompleteLessons++;
                            }
                        }
                        $actualCells = $actualPeriodIds->unique()->count();
                        $actualCapacityGap = $capacidadSemanal - $actualCells;
                        $actualUsage = $capacidadSemanal > 0
                            ? min(100, (int) round($actualCells * 100 / $capacidadSemanal))
                            : 0;
                        $actualPairedBlocks = max(0, $actualAssignedBlocks - $actualCells);
                        if ($hasDryRunResult) {
                            if ($actualIncompleteLessons > 0) {
                                $feasibilityLabel = 'Preview incompleto';
                                $feasibilityClasses = 'border-amber-500/30 bg-amber-500/10 text-amber-800 dark:text-amber-200';
                            } elseif ($actualCapacityGap < 0) {
                                $feasibilityLabel = 'Sin capacidad';
                                $feasibilityClasses = 'border-red-500/30 bg-red-500/10 text-red-800 dark:text-red-200';
                            } elseif ($actualCells > $minimumCells) {
                                $feasibilityLabel = 'Ajustado';
                                $feasibilityClasses = 'border-sky-500/30 bg-sky-500/10 text-sky-800 dark:text-sky-200';
                            } else {
                                $feasibilityLabel = 'Factible';
                                $feasibilityClasses = 'border-emerald-500/30 bg-emerald-500/10 text-emerald-800 dark:text-emerald-200';
                            }
                        } elseif ($overCapacity) {
                            $feasibilityLabel = 'Sin capacidad teórica';
                            $feasibilityClasses = 'border-red-500/30 bg-red-500/10 text-red-800 dark:text-red-200';
                        } else {
                            $feasibilityLabel = 'Factible en teoría';
                            $feasibilityClasses = 'border-gray-300/80 bg-white/60 text-gray-700 dark:border-white/10 dark:bg-white/[0.04] dark:text-gray-200';
                        }
                        $shiftCapacityStatus = collect($shifts)->map(function ($shift) use (
                            $rows,
                            $selectedPevIdsForView,
                            $lessons,
                            $shiftCapacityPerDay,
                            $maxSubjectsPerPeriod
                        ) {
                            $shiftId = (int) $shift->id;
                            $capacityPerDay = (int) ($shiftCapacityPerDay[$shiftId] ?? 0);
                            $capacity = $capacityPerDay * 5;
                            $blocks = 0;
                            $halfBlocks = 0;

                            foreach ($rows as $pev) {
                                if (! isset($selectedPevIdsForView[(int) $pev->id])) {
                                    continue;
                                }

                                $lesson = $lessons[$pev->id] ?? null;
                                if (! $lesson || (int) ($lesson['shift_id'] ?? 0) !== $shiftId) {
                                    continue;
                                }

                                $lessonBlocks = (int) ($lesson['weekly_blocks_t'] ?? 0)
                                    + (int) ($lesson['weekly_blocks_p'] ?? 0);
                                $blocks += $lessonBlocks;
                                if (! empty($lesson['is_half_group'])) {
                                    $halfBlocks += $lessonBlocks;
                                }
                            }

                            $required = $blocks - $halfBlocks
                                + (int) ceil($halfBlocks / max(1, $maxSubjectsPerPeriod));
                            $remaining = $capacity - $required;

                            if ($capacity <= 0) {
                                $status = 'empty';
                                $label = 'Sin slots';
                            } elseif ($remaining < 0) {
                                $status = 'overflow';
                                $label = 'Desbordado';
                            } elseif ($remaining === 0) {
                                $status = 'full';
                                $label = 'Completo';
                            } else {
                                $status = 'available';
                                $label = 'Disponible';
                            }

                            return [
                                'id' => $shiftId,
                                'name' => $shift->name,
                                'code' => $shift->code,
                                'capacity' => $capacity,
                                'required' => $required,
                                'remaining' => $remaining,
                                'status' => $status,
                                'label' => $label,
                            ];
                        })->values();
                    @endphp
                    @if ($shiftCapacityStatus->isNotEmpty())
                        <div class="mb-4 rounded-lg border border-gray-200/80 bg-gray-50/70 px-3 py-2.5 dark:border-white/10 dark:bg-white/[0.03]">
                            <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                    Capacidad por turno
                                </span>
                                <span class="text-[10px] text-gray-400 dark:text-gray-500">
                                    La carga se compara con los slots del turno seleccionado
                                </span>
                            </div>
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                @foreach ($shiftCapacityStatus as $shiftStatus)
                                    @php
                                        $statusStyles = match ($shiftStatus['status']) {
                                            'available' => [
                                                'container' => 'border-emerald-500/20 bg-emerald-500/[0.04]',
                                                'dot' => 'bg-emerald-500',
                                                'label' => 'text-emerald-700 dark:text-emerald-300',
                                                'detail' => 'text-emerald-700/80 dark:text-emerald-300/80',
                                            ],
                                            'full' => [
                                                'container' => 'border-sky-500/20 bg-sky-500/[0.04]',
                                                'dot' => 'bg-sky-500',
                                                'label' => 'text-sky-700 dark:text-sky-300',
                                                'detail' => 'text-sky-700/80 dark:text-sky-300/80',
                                            ],
                                            'overflow' => [
                                                'container' => 'border-amber-500/30 bg-amber-500/[0.06]',
                                                'dot' => 'bg-amber-500',
                                                'label' => 'text-amber-700 dark:text-amber-300',
                                                'detail' => 'text-amber-700/80 dark:text-amber-300/80',
                                            ],
                                            default => [
                                                'container' => 'border-gray-300/80 bg-gray-500/[0.04]',
                                                'dot' => 'bg-gray-400',
                                                'label' => 'text-gray-600 dark:text-gray-300',
                                                'detail' => 'text-gray-500 dark:text-gray-400',
                                            ],
                                        };
                                    @endphp
                                    <div class="w-full rounded-md border px-2.5 py-2 {{ $statusStyles['container'] }}">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex min-w-0 items-center gap-1.5">
                                                <span class="h-1.5 w-1.5 shrink-0 rounded-full {{ $statusStyles['dot'] }}" aria-hidden="true"></span>
                                                <span class="truncate text-[11px] font-bold text-gray-700 dark:text-gray-200">
                                                    {{ $shiftStatus['name'] }}
                                                    <span class="font-normal text-gray-400 dark:text-gray-500">({{ $shiftStatus['code'] }})</span>
                                                </span>
                                            </div>
                                            <span class="shrink-0 text-[10px] font-extrabold {{ $statusStyles['label'] }}">
                                                {{ $shiftStatus['label'] }}
                                            </span>
                                        </div>
                                        <div class="mt-1 text-[10px] {{ $statusStyles['detail'] }}">
                                            {{ $shiftStatus['required'] }} / {{ $shiftStatus['capacity'] }} celdas mínimas estimadas
                                            @if ($shiftStatus['status'] === 'available')
                                                · {{ $shiftStatus['remaining'] }} libres
                                            @elseif ($shiftStatus['status'] === 'overflow')
                                                · {{ abs($shiftStatus['remaining']) }} de más
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif

                @if ($rows->isNotEmpty())
                    <div class="mb-3 rounded-lg {{ $overCapacity ? 'bg-amber-500/5 border border-amber-500/30 text-amber-700 dark:text-amber-300' : 'bg-emerald-500/5 border border-emerald-500/20 text-emerald-700 dark:text-emerald-300' }} px-3 py-2.5 text-xs">
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                            <span class="font-bold">{{ $rows->count() }} asignaturas seleccionadas</span>
                            <span>{{ $totalBlocks }} bloques requeridos ({{ $selectedT }} T · {{ $selectedP }} P)</span>
                            @if ($selectedHalfBlocks > 0)
                                <span>{{ $selectedHalfBlocks }} bloques de medio grupo</span>
                            @endif
                            <span class="font-bold">Estimación: {{ $minimumCells }}–{{ $maximumCells }} celdas</span>
                            <span class="rounded-full border px-2 py-0.5 text-[10px] font-extrabold {{ $feasibilityClasses }}">
                                {{ $feasibilityLabel }}
                            </span>
                        </div>
                        @if ($capacidadSemanal > 0)
                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px]">
                                <span>Capacidad: {{ $capacidadSemanal }} celdas ({{ $capacidadPorDia }}/día × 5)</span>
                                <span>Máximo: {{ $maxSubjectsPerPeriod }} asignaturas por período</span>
                                <span>Uso teórico: {{ $usage }}%</span>
                                <span class="font-bold">
                                    @if ($overCapacity)
                                        ⚠ el mejor caso excede por {{ abs($capacityGap) }} celdas
                                    @else
                                        ✓ margen teórico: {{ $capacityGap }} celdas
                                    @endif
                                </span>
                            </div>
                            @if ($hasDryRunResult)
                                <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 border-t border-current/10 pt-2 text-[11px]">
                                    <span class="font-bold">Dry-run: {{ $actualCells }} celdas usadas · {{ $actualUsage }}% de uso</span>
                                    <span>{{ $actualAssignedBlocks }}/{{ $totalBlocks }} bloques asignados</span>
                                    <span>{{ $actualPairedBlocks }} bloques compartidos</span>
                                    @if ($actualIncompleteLessons > 0)
                                        <span class="font-bold text-amber-700 dark:text-amber-300">
                                            ⚠ {{ $actualIncompleteLessons }} lesson(s) incompleta(s)
                                        </span>
                                    @elseif ($actualCapacityGap > 0)
                                        <span class="font-bold">✓ margen real: {{ $actualCapacityGap }} celdas</span>
                                    @else
                                        <span class="font-bold">⚠ sin margen real</span>
                                    @endif
                                </div>
                            @endif
                            @if ($selectedHalfBlocks > 0)
                                <p class="mt-1 text-[10px] opacity-80">
                                    La estimación va de ningún emparejamiento al máximo teórico de medios grupos.
                                    El dry-run incorpora profesor, aula, disponibilidad, turno y sección.
                                </p>
                            @endif
                        @endif
                    </div>
                @endif

                <div class="rounded-lg border border-gray-200 dark:border-white/10">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-gray-50 dark:bg-gray-800/80">
                            <tr class="text-left text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                <th colspan="2" class="px-3 py-2">
                                    <span class="inline-flex items-center gap-2">
                                        @if ($step3ViewMode === 'tabs' && $bulkSectionId !== null)
                                            @php
                                                $bulkSectionLabel = collect($tabSeccionOptions)
                                                    ->firstWhere('id', $bulkSectionId)['label'] ?? 'la sección activa';
                                            @endphp
                                            <input type="checkbox"
                                                wire:key="bulk-select-section-{{ $bulkSectionId }}-{{ $selectionResetToken }}"
                                                wire:click="toggleSelectAllForSection({{ $bulkSectionId }})"
                                                title="Seleccionar todas las asignaturas de {{ $bulkSectionLabel }}"
                                                @checked($bulkSectionSelected)
                                                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        @endif
                                        <span>Asignatura · Sección</span>
                                    </span>
                                </th>
                                <th class="px-3 py-2">Profesor</th>
                                <th class="px-3 py-2">T</th>
                                <th class="px-3 py-2">
                                    <span class="inline-flex items-center gap-1.5">
                                        <span>P</span>
                                        <button type="button"
                                            wire:click="resetPracticalBlocks"
                                            wire:loading.attr="disabled"
                                            wire:loading.class="opacity-50 cursor-not-allowed"
                                            wire:target="resetPracticalBlocks"
                                            title="Poner en cero todos los bloques prácticos"
                                            aria-label="Poner en cero todos los bloques prácticos"
                                            class="inline-flex items-center rounded-full border border-amber-500/25 bg-amber-500/10 px-1.5 py-0.5 text-[9px] font-extrabold text-amber-700 transition-colors hover:bg-amber-500/20 dark:text-amber-300">
                                            <span wire:loading.remove wire:target="resetPracticalBlocks">0</span>
                                            <span wire:loading wire:target="resetPracticalBlocks">…</span>
                                        </button>
                                    </span>
                                </th>
                                <th class="px-3 py-2">Prio</th>
                                <th class="px-3 py-2">Turno</th>
                                <th class="px-3 py-2">Aula req.</th>
                                <th class="px-3 py-2 text-nowrap">Medio grupo</th>
                                <th class="px-3 py-2">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $pev)
                                @php
                                    $selected = (array_key_exists($pev->id, $selectedPevs) && (bool) $selectedPevs[$pev->id])
                                        || in_array($pev->id, array_map('intval', array_values($selectedPevs)), true);
                                    $derivedT = (int) ceil(((int) ($pev->pensum?->asignatura?->hour_t_week ?? 0)) * 60 / max(1, (int) ($periodsList->count() ? $calendarPeriodMinutes ?? 60 : 60)));
                                    $derivedP = (int) ceil(((int) ($pev->pensum?->asignatura?->hour_p_week ?? 0)) * 60 / max(1, (int) ($periodsList->count() ? $calendarPeriodMinutes ?? 60 : 60)));
                                @endphp
                                <tr class="border-t border-gray-100 dark:border-white/5 {{ $selected ? 'bg-emerald-500/5' : '' }}">
                                    <td class="px-3 py-2">
                                        <input type="checkbox"
                                            wire:key="select-pev-{{ $pev->id }}-{{ $selectionResetToken }}"
                                            wire:model.live="selectedPevs.{{ $pev->id }}"
                                            class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    </td>
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-200 font-medium">{{ $pev->pensum?->asignatura?->name }}{{ $pev->grupoEstable?->name ? ' · '.$pev->grupoEstable->name : '' }}</td>
                                    <td class="px-3 py-2 text-gray-500 dark:text-gray-400">{{ $pev->profesor?->lastname }}, {{ $pev->profesor?->name }}                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number"
                                            wire:key="lesson-blocks-t-{{ $pev->id }}-{{ $selected ? 'selected' : 'unselected' }}"
                                            @if ($selected) wire:model="lessons.{{ $pev->id }}.weekly_blocks_t" wire:change="autosaveLessons" @endif
                                            value="{{ $selected ? ($lessons[$pev->id]['weekly_blocks_t'] ?? $derivedT) : $derivedT }}"
                                            min="0" @disabled(! $selected)
                                            class="w-12 text-center bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded px-1 py-1 text-xs disabled:opacity-60 disabled:cursor-not-allowed">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number"
                                            wire:key="lesson-blocks-p-{{ $pev->id }}-{{ $selected ? 'selected' : 'unselected' }}"
                                            @if ($selected) wire:model="lessons.{{ $pev->id }}.weekly_blocks_p" wire:change="autosaveLessons" @endif
                                            value="{{ $selected ? ($lessons[$pev->id]['weekly_blocks_p'] ?? $derivedP) : $derivedP }}"
                                            min="0" @disabled(! $selected)
                                            class="w-12 text-center bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded px-1 py-1 text-xs disabled:opacity-60 disabled:cursor-not-allowed">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number"
                                            @if ($selected) wire:model="lessons.{{ $pev->id }}.priority" wire:change="autosaveLessons" @endif
                                            value="{{ $selected ? ($lessons[$pev->id]['priority'] ?? 0) : 0 }}"
                                            min="0" @disabled(! $selected)
                                            class="w-12 text-center bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded px-1 py-1 text-xs disabled:opacity-60 disabled:cursor-not-allowed">
                                    </td>
                                    <td class="px-3 py-2">
                                        <select
                                            @if ($selected) wire:model="lessons.{{ $pev->id }}.shift_id" wire:change="autosaveLessons" @endif
                                            @disabled(! $selected)
                                            class="text-xs bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded px-2 py-1 disabled:opacity-60 disabled:cursor-not-allowed">
                                            @if (! $selected)
                                                <option value="">Selecciona</option>
                                            @endif
                                            @foreach ($shifts as $shift)
                                                <option value="{{ $shift->id }}">{{ $shift->code }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-2">
                                        <select
                                            @if ($selected) wire:model="lessons.{{ $pev->id }}.room_type_required" wire:change="autosaveLessons" @endif
                                            @disabled(! $selected)
                                            class="text-xs bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded px-2 py-1 disabled:opacity-60 disabled:cursor-not-allowed">
                                            <option value="">{{ $selected ? '—' : 'Selecciona' }}</option>
                                            @foreach (['aula', 'laboratorio', 'patio', 'cancha', 'taller', 'salon'] as $type)
                                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <input type="checkbox"
                                            @if ($selected)
                                                wire:model.live="lessons.{{ $pev->id }}.is_half_group"
                                                wire:change="autosaveLessons"
                                            @endif
                                            @disabled(! $selected)
                                            title="Comparte la celda con otra asignatura de medio grupo"
                                            class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 disabled:opacity-60 disabled:cursor-not-allowed">
                                    </td>
                                    <td class="px-3 py-2 text-[10px] flex items-center gap-1.5">
                                        @if (isset($savedPevIds[$pev->id]))
                                            <span class="px-1.5 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-widest">Guardado</span>
                                        @endif
                                        @if (isset($step3Warnings[$pev->id]))
                                            <span class="block mt-0.5 text-amber-500 font-bold" title="{{ implode(' · ', $step3Warnings[$pev->id]) }}">⚠</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-3 py-8 text-center text-sm text-gray-400">No hay lecciones (pevaluaciones) para la selección.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mb-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>{{ $rows->count() }} asignatura(s) · {{ $step3SelectedCount }} seleccionada(s)</span>
                    <span class="flex items-center gap-2">
                        @if ($lessonsDirty)
                            <span class="text-amber-600 dark:text-amber-400 font-bold">● Cambios sin guardar</span>
                        @elseif ($lessonsSavedAt)
                            <span class="text-emerald-600 dark:text-emerald-400">Guardado {{ $lessonsSavedAt }}</span>
                        @endif
                        <span>Los bloques se derivan de <code>hour_t_week/hour_p_week</code></span>
                        <button wire:click="syncAcademicLoad"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            wire:target="syncAcademicLoad"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white/5 hover:bg-white/10 border border-white/10 text-gray-500 dark:text-gray-300 font-bold">
                            <span wire:loading.remove wire:target="syncAcademicLoad">Sincronizar carga académica</span>
                            <span wire:loading wire:target="syncAcademicLoad" class="inline-flex items-center gap-1.5">
                                <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Sincronizando…
                            </span>
                        </button>
                        @if ($step3ViewMode === 'tabs' && is_numeric($activeSeccionId))
                            <div class="inline-flex items-center gap-1.5">
                                <select wire:model="replicateToSeccionId"
                                    title="Sección destino para replicar las lecciones"
                                    class="max-w-[16rem] rounded-md border border-white/10 bg-white/5 px-2.5 py-1 text-[11px] font-bold text-gray-500 dark:text-gray-300">
                                    <option value="">Replicar a sección…</option>
                                    @foreach ($tabSeccionOptions as $sectionOption)
                                        @if ((int) $sectionOption['id'] !== (int) $activeSeccionId)
                                            <option value="{{ $sectionOption['id'] }}">{{ $sectionOption['label'] }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <button wire:click="replicateLessonsToSection"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                    wire:target="replicateLessonsToSection"
                                    title="Copiar las lecciones configuradas a la sección seleccionada"
                                    class="inline-flex items-center gap-1.5 rounded-md bg-indigo-500/10 px-2.5 py-1 text-[11px] font-bold text-indigo-700 transition-colors hover:bg-indigo-500/20 dark:text-indigo-300">
                                    <span wire:loading.remove wire:target="replicateLessonsToSection">Replicar</span>
                                    <span wire:loading wire:target="replicateLessonsToSection">Replicando…</span>
                                </button>
                            </div>
                        @endif
                        @if (is_numeric($activeSeccionId))
                            <button type="button"
                                wire:click="downloadLessonsBackup({{ (int) $activeSeccionId }})"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                wire:target="downloadLessonsBackup"
                                title="Descargar respaldo JSON de las lessons de la sección activa"
                                class="inline-flex items-center gap-1.5 rounded-md bg-sky-500/10 px-2.5 py-1 text-[11px] font-bold text-sky-700 transition-colors hover:bg-sky-500/20 dark:text-sky-300">
                                <span wire:loading.remove wire:target="downloadLessonsBackup">Backup JSON</span>
                                <span wire:loading wire:target="downloadLessonsBackup">Preparando…</span>
                            </button>
                        @endif
                        <label title="Seleccionar respaldo JSON de lessons por grado y sección"
                            class="inline-flex cursor-pointer items-center gap-1.5 rounded-md bg-white/5 px-2.5 py-1 text-[11px] font-bold text-gray-600 transition-colors hover:bg-white/10 dark:text-gray-300">
                            <span>Elegir restore</span>
                            <input type="file" wire:model="lessonsBackupFile" accept="application/json,.json" class="sr-only">
                        </label>
                        <button type="button"
                            wire:click="restoreLessonsBackup"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            wire:target="restoreLessonsBackup,lessonsBackupFile"
                            title="Restaurar la configuración del grado y sección desde el JSON seleccionado"
                            class="inline-flex items-center gap-1.5 rounded-md bg-violet-500/10 px-2.5 py-1 text-[11px] font-bold text-violet-700 transition-colors hover:bg-violet-500/20 dark:text-violet-300">
                            <span wire:loading.remove wire:target="restoreLessonsBackup">Restore</span>
                            <span wire:loading wire:target="restoreLessonsBackup">Restaurando…</span>
                        </button>
                    </span>
                </div>

                {{-- <div class="mt-5 rounded-lg border border-dashed border-gray-300 dark:border-white/10 p-4">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">Importación masiva (CSV / Excel)</div>
                    <div class="flex flex-wrap items-center gap-3">
                        <input type="file" wire:model="importFile"
                            accept=".csv,.xlsx,.xls,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                            class="text-xs text-gray-500 dark:text-gray-400 file:mr-3 file:px-3 file:py-2 file:rounded-lg file:border-0 file:bg-white/5 file:text-gray-300 file:text-xs file:font-bold hover:file:bg-white/10">
                        <button wire:click="importLessons" wire:loading.attr="disabled" wire:target="importFile"
                            class="px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-200 text-sm font-bold border border-white/10">
                            Importar
                        </button>
                        <button wire:click="downloadTemplate"
                            class="px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-bold border border-white/10">
                            Descargar plantilla
                        </button>
                    </div>
                    <p class="mt-2 text-[11px] text-gray-400 dark:text-gray-500">Columnas: <code>pevaluacion_id</code> <em>o</em> (<code>seccion_id</code> + <code>asignatura</code>), <code>turno</code> (M/T), <code>bloques_t</code>, <code>bloques_p</code>, <code>aula</code>, <code>prioridad</code>.</p>
                    @if ($importMessage)
                        <div class="mt-2 text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ $importMessage }}</div>
                    @endif
                    @if ($importErrors)
                        <ul class="mt-2 space-y-1 max-h-32 overflow-y-auto">
                            @foreach ($importErrors as $err)
                                <li class="text-xs text-red-500 dark:text-red-400">• {{ $err }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div> --}}

                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Los bloques se derivan de <code>hour_t_week/hour_p_week</code> y la duración del bloque.</span>
                    <div class="flex items-center gap-2">
                        <button wire:click="autosaveLessons"
                            @disabled($step3SelectedCount === 0)
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            wire:target="autosaveLessons"
                            class="px-4 py-2.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-bold border border-white/10">
                            <span wire:loading.remove wire:target="autosaveLessons">Guardar borrador</span>
                            <span wire:loading wire:target="autosaveLessons" class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Guardando…
                            </span>
                        </button>
                        <button wire:click="recalculateLessonBlocks"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            wire:target="recalculateLessonBlocks"
                            title="Recalcular bloques desde las horas actuales de las asignaturas"
                            class="px-4 py-2.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-bold border border-white/10">
                            <span wire:loading.remove wire:target="recalculateLessonBlocks">Recalcular bloques</span>
                            <span wire:loading wire:target="recalculateLessonBlocks" class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Recalculando…
                            </span>
                        </button>
                        <button wire:click="saveLessons"
                            @disabled($step3SelectedCount === 0)
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            wire:target="saveLessons"
                            class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">
                            <span wire:loading.remove wire:target="saveLessons">Guardar clases y continuar</span>
                            <span wire:loading wire:target="saveLessons" class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Guardando…
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════ Paso 4 · Disponibilidad ═══════════ --}}
    @if ($currentStep === 4)
        <div class="space-y-6" role="tabpanel" id="tt-step-4" aria-labelledby="tt-tab-4">
            <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-5">
                <div class="flex items-center justify-between mb-4 gap-3 flex-wrap">
                    <h2 class="text-sm font-extrabold text-gray-900 dark:text-white">4 · Disponibilidad docente</h2>
                    <button wire:click="setAllAvailable" @disabled($step3SelectedCount === 0) wire:loading.attr="disabled" wire:target="setAllAvailable"
                        class="px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-200 text-sm font-bold border border-white/10">
                        Marcar 100% disponible (profesores de asignaturas seleccionadas)
                    </button>
                </div>

                <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">Elige un profesor para ajustar su disponibilidad puntualmente (día × período).</div>

                {{-- Búsqueda + selector de profesor --}}
                <div class="grid grid-cols-1 md:grid-cols-[1fr_2fr_auto] gap-3 mb-5">
                    <div>
                        <input type="text" wire:model.live="searchProfesor" placeholder="Buscar profesor…"
                            class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-gray-900 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <select wire:model.live="selectedProfesorId"
                            class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-gray-900 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Seleccionar profesor…</option>
                            @foreach ($profesoresFiltrados as $p)
                                <option value="{{ $p->id }}">{{ $p->lastname }}, {{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 self-center">
                        {{ $profesoresFiltrados->count() }} profesor(es)
                    </div>
                </div>

                @if ($selectedProfesor)
                    @php
                        $profAvail = $this->availability[$selectedProfesor->id] ?? [];
                        $totalPeriodos = 0;
                        $disponibles = 0;
                        foreach ($availabilityGrid as $shiftId => $g) {
                            foreach (range(1, 5) as $day) {
                                foreach ($g['blocks'] as $order => $block) {
                                    $totalPeriodos++;
                                    if (! empty($profAvail[$shiftId][$day][$order])) {
                                        $disponibles++;
                                    }
                                }
                            }
                        }
                    @endphp
                    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-4">
                        <div class="flex items-center justify-between mb-3 gap-3 flex-wrap">
                            <div class="text-sm font-extrabold text-gray-900 dark:text-white">
                                {{ $selectedProfesor->lastname }}, {{ $selectedProfesor->name }}
                                <span class="ml-2 text-[10px] font-bold uppercase tracking-widest text-gray-400">Disponibilidad</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                                    {{ $disponibles }}/{{ $totalPeriodos }} bloques disponibles
                                </span>
                                <button wire:click="markAllAvailable"
                                    class="px-3 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-xs font-bold">
                                    Marcar todo disponible
                                </button>
                                <button wire:click="fillAvailabilityFromLessons"
                                    class="px-3 py-1.5 rounded-lg bg-sky-500/10 hover:bg-sky-500/20 border border-sky-500/30 text-sky-600 dark:text-sky-400 text-xs font-bold">
                                    Prellenar desde sus clases
                                </button>
                                <button wire:click="uncheckAllAvailability"
                                    class="px-3 py-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 text-red-600 dark:text-red-400 text-xs font-bold">
                                    Desmarcar todo
                                </button>
                                <select wire:change="copyAvailabilityFrom($event.target.value)"
                                    class="px-2 py-1.5 rounded-lg bg-white/5 border border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400 text-xs font-bold">
                                    <option value="">Copiar de…</option>
                                    @foreach ($profesoresFiltrados->where('id', '!=', $selectedProfesor->id) as $p)
                                        <option value="{{ $p->id }}">{{ $p->lastname }}, {{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @foreach ($availabilityGrid as $shiftId => $g)
                            <div class="mb-4 last:mb-0">
                                <div class="text-[10px] font-bold text-gray-400 mb-1">
                                    {{ $g['name'] ?? ('Turno '.$shiftId) }}
                                    · {{ substr((string) $g['start'], 0, 5) }}–{{ substr((string) $g['end'], 0, 5) }}
                                </div>
                                <div class="grid grid-cols-6 gap-1" x-data>
                                    <div class="text-[10px] font-bold text-gray-400">Hora</div>
                                    @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dayLabel)
                                        <div class="text-[10px] font-bold text-gray-400 text-center">{{ $dayLabel }}</div>
                                    @endforeach

                                    @foreach ($g['blocks'] as $order => $block)
                                        <div class="contents">
                                            <div class="text-[10px] font-bold text-gray-400 flex items-center">{{ $block['start_time'] }}–{{ $block['end_time'] }}</div>
                                            @for ($day = 1; $day <= 5; $day++)
                                                <div class="flex justify-center">
                                                    <input type="checkbox"
                                                        wire:model.live="availability.{{ $selectedProfesor->id }}.{{ $shiftId }}.{{ $day }}.{{ $order }}"
                                                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                                </div>
                                            @endfor
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-lg border border-dashed border-gray-300 dark:border-white/10 p-6 text-center text-sm text-gray-400">
                        Selecciona un profesor para ver y ajustar su disponibilidad.
                    </div>
                @endif

                <div class="mt-5 flex items-center gap-3">
                    <button wire:click="saveAvailability" @disabled($step3SelectedCount === 0) class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">Guardar disponibilidad</button>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Solo se guarda la disponibilidad vinculada a las asignaturas seleccionadas en el paso 3.</span>
                </div>
            </div>
        </div>
    @endif

    @if ($showTeacherScheduleDialog)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-950/70 p-4"
            role="dialog" aria-modal="true" aria-labelledby="teacher-schedule-title"
            x-data x-on:keydown.escape.window="$wire.closeTeacherScheduleDialog()">
            <div x-on:click.stop class="flex max-h-[92vh] w-full max-w-7xl flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-white/10 dark:bg-gray-900">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-4 dark:border-white/10">
                    <div>
                        <h2 id="teacher-schedule-title" class="text-sm font-extrabold text-gray-900 dark:text-white">Horario por profesor</h2>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lessons asignadas en la sección activa, organizadas por día y bloque.</p>
                    </div>
                    <button type="button" wire:click="closeTeacherScheduleDialog"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-full text-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50 dark:hover:bg-white/10 dark:hover:text-white"
                        aria-label="Cerrar horario por profesor">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="border-b border-gray-200 bg-gray-50 px-5 py-3 dark:border-white/10 dark:bg-white/5">
                    <label for="teacher-schedule-profesor" class="mb-1 block text-[10px] font-extrabold uppercase tracking-widest text-gray-500 dark:text-gray-400">Profesor</label>
                    <select id="teacher-schedule-profesor" wire:model.live="teacherScheduleProfesorId"
                        class="w-full max-w-xl rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none focus:border-fuchsia-500 focus:ring-2 focus:ring-fuchsia-500/40 dark:border-white/10 dark:bg-gray-900 dark:text-white">
                        @forelse ($teacherScheduleOptions as $teacher)
                            <option value="{{ $teacher['id'] }}">{{ $teacher['name'] }}</option>
                        @empty
                            <option value="">No hay profesores asociados</option>
                        @endforelse
                    </select>
                </div>
                <div class="overflow-auto p-5">
                    @if ($teacherScheduleGrid !== [])
                        <table class="w-full min-w-[900px] table-fixed border-collapse text-left text-xs">
                            <caption class="sr-only">Lessons del profesor seleccionado por día y bloque</caption>
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50 text-[10px] font-extrabold uppercase tracking-widest text-gray-500 dark:border-white/10 dark:bg-white/5 dark:text-gray-400">
                                    <th class="w-36 px-3 py-2.5">Turno · bloque</th>
                                    @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dayLabel)
                                        <th class="px-3 py-2.5 text-center">{{ $dayLabel }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                @foreach ($teacherScheduleGrid as $row)
                                    <tr>
                                        <th scope="row" class="px-3 py-2.5 font-bold text-gray-700 dark:text-gray-200">
                                            <span class="block">{{ $row['shift'] }}</span>
                                            <span class="mt-0.5 block text-[9px] font-normal uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ $row['code'] }} · #{{ $row['order'] }}</span>
                                        </th>
                                        @for ($day = 1; $day <= 5; $day++)
                                            <td class="px-2 py-2 align-top">
                                                @forelse ($row['cells'][$day] ?? [] as $lesson)
                                                    <div class="mb-1 rounded-md border border-fuchsia-500/20 bg-fuchsia-500/5 p-2 last:mb-0">
                                                        <div class="font-bold text-gray-900 dark:text-white">{{ $lesson['subject'] }}</div>
                                                        <div class="mt-0.5 font-mono text-[10px] text-gray-500 dark:text-gray-400">{{ $lesson['start'] }}–{{ $lesson['end'] }}</div>
                                                        <div class="mt-0.5 text-[10px] text-gray-500 dark:text-gray-400">Lesson #{{ $lesson['lesson_id'] }} · Sección {{ $lesson['section'] }}</div>
                                                    </div>
                                                @empty
                                                    <span class="text-gray-300 dark:text-gray-700">—</span>
                                                @endforelse
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="rounded-lg border border-dashed border-gray-300 px-4 py-10 text-center text-sm text-gray-500 dark:border-white/10 dark:text-gray-400">
                            El profesor seleccionado no tiene lessons asignadas en la sección activa.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════ Paso 5 · Generar ═══════════ --}}
    @if ($currentStep === 5)
        <div class="space-y-6" role="tabpanel" id="tt-step-5" aria-labelledby="tt-tab-5">
            <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-5">
                <h2 class="text-sm font-extrabold text-gray-900 dark:text-white mb-4">5 · Generar horario</h2>

                <div class="flex flex-wrap items-center gap-3">
                    <button wire:click="runDryRun" @disabled($step3SelectedCount === 0) wire:loading.attr="disabled" wire:target="runDryRun"
                        class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">
                        <span wire:loading.remove wire:target="runDryRun">Previsualizar (dry-run)</span>
                        <span wire:loading wire:target="runDryRun">Generando…</span>
                    </button>
                    @if (($selectedCalendarDetail['status'] ?? null) === 'active')
                    <button type="button"
                        wire:click="openAiDryRunDialog"
                        wire:loading.attr="disabled"
                        wire:target="openAiDryRunDialog,analyzeDryRunWithAi"
                        title="Analizar el horario publicado con IA"
                        class="inline-flex items-center gap-2 rounded-lg border border-violet-500/30 bg-violet-500/10 px-4 py-2.5 text-sm font-bold text-violet-700 transition-colors hover:bg-violet-500/20 dark:text-violet-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.75h4.5l.75 3.25 2.75 1.25 2.5-1.5 2.25 3.9-2.5 1.75v3.1l2.5 1.75-2.25 3.9-2.5-1.5-2.75 1.25-.75 3.25h-4.5L9 19.9l-2.75-1.25-2.5 1.5-2.25-3.9L4 14.5v-3.1L1.5 9.65l2.25-3.9 2.5 1.5L9 6.0l.75-2.25Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10.25v3.5m0 2.5h.01"/>
                        </svg>
                        <span wire:loading.remove wire:target="openAiDryRunDialog,analyzeDryRunWithAi">Analizar con IA</span>
                        <span wire:loading wire:target="openAiDryRunDialog,analyzeDryRunWithAi">Analizando…</span>
                    </button>
                    <a href="{{ route('app.planning.timetable.pdf.teachers', ['calendar' => $calendarId]) }}"
                        target="_blank"
                        rel="noopener"
                        title="Generar un PDF con los horarios de todos los docentes del calendario"
                        class="inline-flex items-center gap-2 rounded-lg border border-sky-500/30 bg-sky-500/10 px-4 py-2.5 text-sm font-bold text-sky-700 transition-colors hover:bg-sky-500/20 dark:text-sky-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 2h9l3 3v17H6z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6M9 17h6M15 2v4h4"/>
                        </svg>
                        PDF docentes
                    </a>
                    @endif

                    @if ($generationState === 'preview_ready' && $preview)
                        {{-- <button wire:click="updateDraftPreview" wire:loading.attr="disabled" wire:target="updateDraftPreview"
                            class="px-5 py-2.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-bold border border-white/10">
                            <span wire:loading.remove wire:target="updateDraftPreview">Actualizar borrador</span>
                            <span wire:loading wire:target="updateDraftPreview">Actualizando…</span>
                        </button> --}}
                        <button wire:click="confirmAndPublish" wire:loading.attr="disabled" wire:target="confirmAndPublish"
                            class="px-5 py-2.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold">
                            Confirmar y publicar secciones válidas
                        </button>
                        <button wire:click="undoLastPreviewChange" wire:loading.attr="disabled"
                            class="px-4 py-2.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-bold border border-white/10">
                            Deshacer último cambio
                        </button>
                        <button wire:click="restoreGeneratedPreview" wire:loading.attr="disabled"
                            class="px-4 py-2.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-bold border border-white/10">
                            Restaurar dry-run
                        </button>
                    @endif

                    @if ($generationState === 'published')
                        <span class="px-4 py-2 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-sm font-bold">Horario publicado.</span>
                    @endif
                </div>
                @if ($generationState === 'preview_ready' && $preview)
                    @php $publicationReadiness = $this->publicationReadiness(); @endphp
                    @php $displayHardConflicts = $publicationReadiness['display_hard_conflicts'] ?? []; @endphp
                    <div class="mt-4 grid grid-cols-2 gap-2 md:grid-cols-5" aria-label="Resumen de publicación">
                        <div class="rounded-md border border-white/10 bg-white/5 px-3 py-2">
                            <div class="text-[10px] uppercase tracking-wide text-gray-500">Cobertura</div>
                            <div class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ $publicationReadiness['quality']['coverage'] ?? 0 }}%</div>
                        </div>
                        <div class="rounded-md border border-white/10 bg-white/5 px-3 py-2">
                            <div class="text-[10px] uppercase tracking-wide text-gray-500">Asignadas</div>
                            <div class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ $publicationReadiness['assigned'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-md border border-white/10 bg-white/5 px-3 py-2">
                            <div class="text-[10px] uppercase tracking-wide text-gray-500">Sin asignar</div>
                            <div class="mt-1 text-sm font-bold text-amber-600">{{ $publicationReadiness['unassigned'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-md border border-white/10 bg-white/5 px-3 py-2">
                            <div class="text-[10px] uppercase tracking-wide text-gray-500">Bloqueantes</div>
                            <div class="mt-1 text-sm font-bold {{ !empty($publicationReadiness['hard_conflicts']) ? 'text-red-500' : 'text-emerald-600' }}">{{ count($publicationReadiness['hard_conflicts'] ?? []) }}</div>
                        </div>
                        <div class="rounded-md border border-white/10 bg-white/5 px-3 py-2">
                            <div class="text-[10px] uppercase tracking-wide text-gray-500">Calidad</div>
                            <div class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ $publicationReadiness['quality']['score'] ?? 0 }}/100</div>
                        </div>
                    </div>
                    @if (!empty($displayHardConflicts))
                        <details class="mt-4 rounded-lg border border-red-500/30 bg-red-500/5">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-red-500">
                                <span>Detalle de conflictos bloqueantes</span>
                                <span class="rounded-md bg-red-500/10 px-2 py-1">{{ count($displayHardConflicts) }}</span>
                            </summary>
                            <div class="space-y-2 border-t border-red-500/20 px-4 pb-4 pt-3">
                                @foreach ($displayHardConflicts as $conflict)
                                    <div class="rounded-md border border-red-500/20 bg-white/5 p-3">
                                        <div class="flex flex-wrap items-start justify-between gap-2">
                                            <div>
                                                <div class="text-xs font-bold text-gray-800 dark:text-gray-100">{{ $conflict['title'] ?? 'Conflicto bloqueante' }}</div>
                                                <div class="mt-1 text-[11px] text-gray-600 dark:text-gray-300">
                                                    {{ $conflict['subject'] ?? 'Asignatura sin nombre' }}
                                                    · Grado {{ $conflict['grade'] ?? '—' }}
                                                    · Sección {{ $conflict['section'] ?? '—' }}
                                                    · {{ $conflict['teacher'] ?? 'Sin docente' }}
                                                </div>
                                            </div>
                                            <span class="rounded bg-red-500/10 px-2 py-1 text-[10px] font-bold text-red-500">
                                                Lesson #{{ $conflict['lesson_id'] ?? '—' }}
                                            </span>
                                        </div>
                                        <div class="mt-2 text-[11px] text-red-600 dark:text-red-300">
                                            <strong>Período:</strong> {{ $conflict['period'] ?? 'Sin período válido' }}
                                        </div>
                                        @if (($conflict['type'] ?? '') === 'missing_pevaluacion')
                                            <div class="mt-2 rounded-md bg-red-500/10 px-2.5 py-2 text-[11px] text-red-600 dark:text-red-300">
                                                La lesson referencia la Pevaluación #{{ $conflict['pevaluacion_id'] ?? '—' }},
                                                pero ese registro académico no existe.
                                                {{ $conflict['resolution'] ?? 'Restaura la Pevaluacion o retira la lesson del calendario.' }}
                                            </div>
                                        @endif
                                        @if (($conflict['type'] ?? '') === 'incomplete_assignment')
                                            <div class="mt-1 text-[11px] text-gray-600 dark:text-gray-300">
                                                Asignados: <strong>{{ $conflict['assigned_blocks'] }}</strong>
                                                · Requeridos: <strong>{{ $conflict['required_blocks'] }}</strong>
                                                · Faltan: <strong class="text-red-500">{{ $conflict['missing_blocks'] }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    @endif
                @endif

                @if ($generationState === 'generating')
                    <div class="mt-4 flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Ejecutando el motor de asignación…
                    </div>
                @endif

                @if (in_array($generationState, ['preview_ready', 'published'], true) && $preview)
                    @php $generationReadiness = $this->publicationReadiness(); @endphp
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-4 rounded-lg bg-white/5 border border-white/10">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Asignadas</div>
                            <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $generationReadiness['assigned'] ?? 0 }}</div>
                        </div>
                        <div class="p-4 rounded-lg bg-white/5 border border-white/10">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Sin asignar</div>
                            <div class="text-2xl font-extrabold text-red-500">{{ $generationReadiness['unassigned'] ?? 0 }}</div>
                        </div>
                        <div class="p-4 rounded-lg bg-white/5 border border-white/10">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Tiempo</div>
                            <div class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $preview['elapsed_seconds'] ?? 0 }}s</div>
                        </div>
                    </div>

                    @php
                        $activeConflictGroup = is_numeric($activeSeccionId)
                            ? ($generationConflictGroups[(int) $activeSeccionId] ?? null)
                            : null;
                    @endphp
                    {{-- @if ($activeConflictGroup)
                            <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                                <div>
                                    <div class="text-[10px] font-bold uppercase tracking-widest text-red-500">
                                        Conflictos accionables · {{ $activeConflictGroup['grade'] }} · Sección {{ $activeConflictGroup['section'] }} · #{{ $activeConflictGroup['section_id'] }}
                                    </div>
                                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                                        Revisa la causa probable y aplica la recomendación antes de volver a previsualizar.
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="#timetable-preview-grid"
                                        class="px-2 py-1 rounded-md bg-emerald-500/10 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                        Ver horario
                                    </a>
                                    <span class="px-2 py-1 rounded-md bg-red-500/10 text-[10px] font-bold text-red-500">
                                        {{ $activeConflictGroup['count'] }} pendiente(s)
                                    </span>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach (collect($activeConflictGroup['items'])->groupBy('teacher') as $teacher => $items)
                                    <details class="rounded-lg border border-red-500/20 bg-white/5 px-3 py-2">
                                        <summary class="cursor-pointer text-xs font-bold text-gray-700 dark:text-gray-200">
                                            {{ $teacher }} · {{ $items->count() }} sin asignar
                                        </summary>
                                        <div class="mt-3 space-y-3">
                                            @foreach ($items as $item)
                                                <div class="rounded-lg border border-white/10 bg-black/5 dark:bg-white/[0.03] p-3">
                                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                                        <div>
                                                            <div class="text-xs font-bold text-gray-800 dark:text-gray-100">{{ $item['subject'] }}</div>
                                                            <div class="mt-0.5 text-[11px] text-gray-500 dark:text-gray-400">
                                                                Sección {{ $item['section'] }} · Turno {{ $item['shift'] }}
                                                            </div>
                                                        </div>
                                                        <div class="flex flex-wrap gap-1">
                                                            <span class="px-1.5 py-0.5 rounded bg-white/10 text-[10px] text-gray-500 dark:text-gray-300">T {{ $item['blocks_t'] }}</span>
                                                            <span class="px-1.5 py-0.5 rounded bg-white/10 text-[10px] text-gray-500 dark:text-gray-300">P {{ $item['blocks_p'] }}</span>
                                                            <span class="px-1.5 py-0.5 rounded bg-white/10 text-[10px] text-gray-500 dark:text-gray-300">Aula {{ $item['room_type'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2 rounded-md bg-red-500/10 px-2.5 py-2 text-[11px] text-red-600 dark:text-red-300">
                                                        <strong>Causa probable:</strong> {{ $item['reason'] }}
                                                    </div>
                                                    <div class="mt-2 text-[11px] text-gray-500 dark:text-gray-400">
                                                        Candidatos: <strong>{{ $item['available_periods'] }}</strong>
                                                        · Libres: <strong class="{{ $item['free_periods'] < $item['required_periods'] ? 'text-red-500' : '' }}">{{ $item['free_periods'] }}</strong>
                                                        · Necesarios: <strong>{{ $item['required_periods'] }}</strong>
                                                    </div>
                                                    @if (($item['section_blocked_periods'] ?? 0) || ($item['teacher_occupied_periods'] ?? 0) || ($item['room_unavailable_periods'] ?? 0))
                                                        <div class="mt-1 text-[10px] text-gray-500 dark:text-gray-400">
                                                            @if (($item['section_blocked_periods'] ?? 0) > 0)
                                                                Sección ocupada: {{ $item['section_blocked_periods'] }}
                                                            @endif
                                                            @if (($item['teacher_occupied_periods'] ?? 0) > 0)
                                                                @if (($item['section_blocked_periods'] ?? 0) > 0) · @endif
                                                                Docente ocupado: {{ $item['teacher_occupied_periods'] }}
                                                            @endif
                                                            @if (($item['room_unavailable_periods'] ?? 0) > 0)
                                                                @if (($item['section_blocked_periods'] ?? 0) || ($item['teacher_occupied_periods'] ?? 0)) · @endif
                                                                Aula no disponible: {{ $item['room_unavailable_periods'] }}
                                                            @endif
                                                        @endif
                                                    @endif
                                                    <ul class="mt-2 space-y-1 text-[11px] text-gray-600 dark:text-gray-300">
                                                        @foreach ($item['actions'] as $action)
                                                            <li class="flex gap-1.5"><span class="text-emerald-500">→</span><span>{{ $action }}</span></li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>
                                @endforeach
                            </div>
                        </div>
                    @endif --}}

                    {{-- Checklist pre-publicación: revisar antes de «Confirmar y publicar» --}}
                    @php $checklist = $this->publishChecklist(); @endphp
                    @if ($checklist !== [] && ($checklist['sin_asignar'] > 0 || $checklist['asignadas'] === 0))
                        @php $actionableConflictGroups = $generationConflictGroups ?? []; @endphp
                        <details class="mt-4 rounded-lg {{ $checklist['sin_asignar'] > 0 ? 'bg-amber-500/5 border border-amber-500/30' : 'bg-red-500/5 border border-red-500/30' }}">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 p-4 text-[10px] font-bold uppercase tracking-widest {{ $checklist['sin_asignar'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-red-500' }}">
                                <span>⚠ Revisa antes de publicar</span>
                                <span class="text-base leading-none transition-transform details-open:rotate-180" aria-hidden="true">⌄</span>
                            </summary>
                            <div class="border-t {{ $checklist['sin_asignar'] > 0 ? 'border-amber-500/20' : 'border-red-500/20' }} px-4 pb-4 pt-3">
                            <ul class="space-y-1 text-xs text-gray-600 dark:text-gray-300">
                                @if ($checklist['sin_asignar'] > 0)
                                    <li>• <strong>{{ $checklist['sin_asignar'] }}</strong> lección(es) sin asignar quedarán como conflictos <code>unassigned</code>.</li>
                                    @if ($checklist['docentes_afectados'] > 0)
                                        <li>• Docentes afectados ({{ $checklist['docentes_afectados'] }}): {{ $checklist['docentes'] }}{{ $checklist['docentes_afectados'] > 5 ? '…' : '' }}</li>
                                    @endif
                                @endif
                                @if ($checklist['asignadas'] === 0)
                                    <li>• <strong>Ninguna</strong> lección quedó asignada: revisa períodos, turnos y disponibilidad antes de publicar.</li>
                                @endif
                                <li>• Calidad del horario: <strong>{{ $checklist['calidad'] }}%</strong> asignado · {{ $checklist['secciones_con_horario'] }} sección(es) con horario.</li>
                            </ul>

                            @if ($actionableConflictGroups !== [])
                                <div class="mt-4 space-y-3 border-t border-amber-500/20 pt-3">
                                    <div class="text-[10px] font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400">
                                        Incidencias que requieren revisión
                                    </div>
                                    <div class="space-y-1.5">
                                        @foreach ($actionableConflictGroups as $conflictGroup)
                                            @foreach ($conflictGroup['items'] as $conflictItem)
                                                <div class="rounded-md border border-amber-500/20 bg-amber-500/5 px-3 py-2 text-[11px] text-gray-700 dark:text-gray-200">
                                                    <span class="font-bold">{{ $conflictGroup['grade'] }}</span>
                                                    <span class="text-gray-400 dark:text-gray-500">·</span>
                                                    <span>Sección {{ $conflictGroup['section'] }}</span>
                                                    <span class="text-gray-400 dark:text-gray-500">·</span>
                                                    <span class="font-semibold">{{ $conflictItem['subject'] }}</span>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                    <div class="pt-1 text-[10px] font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400">
                                        Detalle para resolver
                                    </div>
                                    @foreach ($actionableConflictGroups as $conflictGroup)
                                        <details class="rounded-lg border border-amber-500/20 bg-white/5 px-3 py-2" @if ($loop->first) open @endif>
                                            <summary class="cursor-pointer text-xs font-bold text-gray-700 dark:text-gray-200">
                                                {{ $conflictGroup['grade'] }} · Sección {{ $conflictGroup['section'] }} · #{{ $conflictGroup['section_id'] }}
                                                <span class="ml-1 font-normal text-red-500">({{ $conflictGroup['count'] }} sin asignar)</span>
                                            </summary>
                                            <div class="mt-3 space-y-3">
                                                @foreach ($conflictGroup['items'] as $conflictItem)
                                                    <div class="rounded-lg border border-white/10 bg-black/5 dark:bg-white/[0.03] p-3 text-[11px]">
                                                        <div class="flex flex-wrap items-start justify-between gap-2">
                                                            <div>
                                                                <div class="font-bold text-gray-800 dark:text-gray-100">{{ $conflictItem['subject'] }}</div>
                                                                <div class="mt-0.5 text-gray-500 dark:text-gray-400">
                                                                    Docente: {{ $conflictItem['teacher'] ?: 'No asignado' }}
                                                                    · Turno: {{ $conflictItem['shift'] }}
                                                                </div>
                                                            </div>
                                                            <div class="flex gap-1">
                                                                <span class="rounded bg-white/10 px-1.5 py-0.5 text-gray-500 dark:text-gray-300">T {{ $conflictItem['blocks_t'] }}</span>
                                                                <span class="rounded bg-white/10 px-1.5 py-0.5 text-gray-500 dark:text-gray-300">P {{ $conflictItem['blocks_p'] }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="mt-2 rounded-md bg-red-500/10 px-2.5 py-2 text-red-600 dark:text-red-300">
                                                            <strong>Causa:</strong> {{ $conflictItem['reason'] }}
                                                        </div>
                                                        <div class="mt-2 text-gray-500 dark:text-gray-400">
                                                            Candidatos: <strong>{{ $conflictItem['available_periods'] }}</strong>
                                                            · Libres: <strong class="{{ $conflictItem['free_periods'] < $conflictItem['required_periods'] ? 'text-red-500' : '' }}">{{ $conflictItem['free_periods'] }}</strong>
                                                            · Necesarios: <strong>{{ $conflictItem['required_periods'] }}</strong>
                                                        </div>
                                                        @if (! empty($conflictItem['assigned_blocks']))
                                                            <div class="mt-2 rounded-md bg-white/5 px-2.5 py-2 text-gray-600 dark:text-gray-300">
                                                                <strong>Bloque asociado:</strong>
                                                                {{ implode(' · ', $conflictItem['assigned_blocks']) }}
                                                            </div>
                                                        @endif
                                                        @if (($conflictItem['section_blocked_periods'] ?? 0) || ($conflictItem['teacher_occupied_periods'] ?? 0) || ($conflictItem['room_unavailable_periods'] ?? 0))
                                                            <div class="mt-1 text-[10px] text-gray-500 dark:text-gray-400">
                                                                @if (($conflictItem['section_blocked_periods'] ?? 0) > 0) Sección ocupada: {{ $conflictItem['section_blocked_periods'] }} @endif
                                                                @if (($conflictItem['teacher_occupied_periods'] ?? 0) > 0) · Docente ocupado: {{ $conflictItem['teacher_occupied_periods'] }} @endif
                                                                @if (($conflictItem['room_unavailable_periods'] ?? 0) > 0) · Aula no disponible: {{ $conflictItem['room_unavailable_periods'] }} @endif
                                                            </div>
                                                        @endif
                                                        <ul class="mt-2 space-y-1 text-gray-600 dark:text-gray-300">
                                                            @foreach ($conflictItem['actions'] as $action)
                                                                <li class="flex gap-1.5"><span class="text-emerald-500">→</span><span>{{ $action }}</span></li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </details>
                                    @endforeach
                                </div>
                            @endif
                            </div>
                        </details>
                    @elseif ($checklist !== [])
                        <div class="mt-4 px-4 py-2 rounded-lg bg-emerald-500/5 border border-emerald-500/30 text-xs text-emerald-600 dark:text-emerald-400">
                            ✓ Checklist OK: {{ $checklist['asignadas'] }} clases asignadas · {{ $checklist['secciones_con_horario'] }} sección(es) · calidad {{ $checklist['calidad'] }}%
                        </div>
                    @endif

                    {{-- Vista previa por sección: pestañas pestudio → grado → sección --}}
                    <div class="mt-6 space-y-4">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Horario previsualizado</h3>
                            {{-- <span class="text-xs text-gray-500 dark:text-gray-400">Elige pestudio · grado · sección</span> --}}
                        </div>

                        {{-- Pestañas · Pestudio --}}
                        @if ($tabPestudioOptions)
                            <div class="border-b border-gray-200 dark:border-white/10">
                                <nav class="flex w-full overflow-x-auto">
                                    @foreach ($tabPestudioOptions as $opt)
                                        <button type="button" wire:click="selectStep5Pestudio({{ $opt['id'] === 'general' ? "'general'" : $opt['id'] }})"
                                            class="flex-1 text-center px-3 py-2 text-[11px] font-bold uppercase tracking-widest whitespace-nowrap border-b-2 transition-all duration-200
                                            {{ (string) $activePestudioId === (string) $opt['id'] ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500' : 'text-gray-400 dark:text-gray-500 border-transparent hover:text-gray-600 dark:hover:text-gray-300' }}">
                                            {{ $opt['name'] }}
                                        </button>
                                    @endforeach
                                </nav>
                            </div>
                        @endif

                        {{-- Pestañas · Grado --}}
                        @if ($tabGradoOptions)
                            <div class="border-b border-gray-200 dark:border-white/10">
                                <nav class="flex w-full overflow-x-auto">
                                    @foreach ($tabGradoOptions as $opt)
                                        <button type="button" wire:click="selectStep5Grade({{ $opt['id'] === 'general' ? "'general'" : $opt['id'] }})"
                                            class="flex-1 text-center px-3 py-2 text-[11px] font-bold uppercase tracking-widest whitespace-nowrap border-b-2 transition-all duration-200
                                            {{ $step5GradeTab !== 'pestudio' && (string) $activeGradoId === (string) $opt['id'] ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500' : 'text-gray-400 dark:text-gray-500 border-transparent hover:text-gray-600 dark:hover:text-gray-300' }}">
                                            {{ $opt['name'] }}
                                        </button>
                                    @endforeach
                                    <button type="button" wire:click="showPestudioFormats"
                                        title="Formatos PDF"
                                        aria-label="Formatos PDF"
                                        class="flex-none w-12 text-center px-2 py-2 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap border-b-2 transition-all duration-200
                                        {{ $step5GradeTab === 'pestudio' && $step5PestudioTab === 'formats' ? 'text-violet-600 dark:text-violet-400 border-violet-500' : 'text-gray-400 dark:text-gray-500 border-transparent hover:text-gray-600 dark:hover:text-gray-300' }}">
                                        PDF
                                    </button>
                                </nav>
                            </div>
                        @endif

                        {{-- Pestañas · Sección --}}
                        @if ($tabSeccionOptions)
                            <div class="border-b border-gray-200 dark:border-white/10">
                                <nav class="flex w-full overflow-x-auto">
                                    @foreach ($tabSeccionOptions as $opt)
                                        <button type="button" wire:click="selectStep5Section({{ $opt['id'] === 'general' ? "'general'" : $opt['id'] }})"
                                            class="flex-1 text-center px-3 py-2 text-[11px] font-bold uppercase tracking-widest whitespace-nowrap border-b-2 transition-all duration-200
                                            {{ $step5SectionTab !== 'formats' && (string) $activeSeccionId === (string) $opt['id'] ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500' : 'text-gray-400 dark:text-gray-500 border-transparent hover:text-gray-600 dark:hover:text-gray-300' }}">
                                            {{ $opt['label'] ?? 'Sección '.$opt['name'] }}
                                        </button>
                                    @endforeach
                                    <button type="button" wire:click="showSectionFormats"
                                        title="Formatos PDF"
                                        aria-label="Formatos PDF"
                                        class="flex-none w-12 text-center px-2 py-2 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap border-b-2 transition-all duration-200
                                        {{ $step5SectionTab === 'formats' ? 'text-violet-600 dark:text-violet-400 border-violet-500' : 'text-gray-400 dark:text-gray-500 border-transparent hover:text-gray-600 dark:hover:text-gray-300' }}">
                                        PDF
                                    </button>
                                </nav>
                            </div>
                        @endif

                        @if ($step5GradeTab === 'pestudio' && $step5PestudioTab === 'formats')
                            <div class="rounded-lg border border-violet-500/25 bg-violet-500/5 p-4">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <div class="text-[10px] font-bold uppercase tracking-widest text-violet-700 dark:text-violet-300">Formatos del pestudio</div>
                                        <h3 class="mt-1 text-base font-extrabold text-gray-900 dark:text-white">
                                            {{ $tabPestudioOptions ? (collect($tabPestudioOptions)->firstWhere('id', $activePestudioId)['name'] ?? 'Pestudio activo') : 'Pestudio activo' }}
                                        </h3>
                                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                                            Genera el PDF consolidado de cada grado asociado a este pestudio.
                                        </p>
                                    </div>
                                    <span class="rounded-md bg-violet-500/10 px-2 py-1 text-[10px] font-bold text-violet-700 dark:text-violet-300">
                                        {{ count($tabGradoOptions) }} grado(s)
                                    </span>
                                </div>
                                @if (is_numeric($activePestudioId))
                                    <a href="{{ route($moduleRoutePrefix.'.timetable.pdf.pestudio-preview', ['calendar' => $calendarId, 'pestudio' => (int) $activePestudioId]) }}"
                                        target="_blank"
                                        class="mt-4 inline-flex items-center gap-2 rounded-md bg-violet-600 px-3 py-2 text-xs font-bold text-white transition-colors hover:bg-violet-700">
                                        Generar PDF del pestudio completo
                                    </a>
                                @endif
                                <div class="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($tabGradoOptions as $gradeOption)
                                        <div class="flex items-center justify-between gap-3 rounded-md border border-white/10 bg-white/5 px-3 py-2">
                                            <div class="min-w-0">
                                                <div class="truncate text-xs font-bold text-gray-800 dark:text-gray-100">
                                                    {{ $gradeOption['name'] }}
                                                </div>
                                                <div class="text-[10px] text-gray-500 dark:text-gray-400">Horario consolidado del grado</div>
                                            </div>
                                            @if (is_numeric($gradeOption['id']))
                                                <a href="{{ route($moduleRoutePrefix.'.timetable.pdf.grade-preview', ['calendar' => $calendarId, 'grado' => (int) $gradeOption['id']]) }}"
                                                    target="_blank"
                                                    class="shrink-0 rounded-md bg-violet-500/10 px-2.5 py-1.5 text-[10px] font-bold text-violet-700 transition-colors hover:bg-violet-500/20 dark:text-violet-300">
                                                    PDF
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @elseif ($step5SectionTab === 'formats')
                            <div class="rounded-lg border border-violet-500/25 bg-violet-500/5 p-4">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <div class="text-[10px] font-bold uppercase tracking-widest text-violet-700 dark:text-violet-300">Formatos del grado</div>
                                        <h3 class="mt-1 text-base font-extrabold text-gray-900 dark:text-white">
                                            {{ $tabGradoOptions ? (collect($tabGradoOptions)->firstWhere('id', $activeGradoId)['name'] ?? 'Grado activo') : 'Grado activo' }}
                                        </h3>
                                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                                            Genera el PDF del horario para cada sección asociada al grado seleccionado.
                                        </p>
                                    </div>
                                    <span class="rounded-md bg-violet-500/10 px-2 py-1 text-[10px] font-bold text-violet-700 dark:text-violet-300">
                                        {{ count($tabSeccionOptions) }} sección(es)
                                    </span>
                                </div>
                                @if (is_numeric($activeGradoId))
                                    <a href="{{ route($moduleRoutePrefix.'.timetable.pdf.grade-preview', ['calendar' => $calendarId, 'grado' => (int) $activeGradoId]) }}"
                                        target="_blank"
                                        class="mt-4 inline-flex items-center gap-2 rounded-md bg-violet-600 px-3 py-2 text-xs font-bold text-white transition-colors hover:bg-violet-700">
                                        Generar PDF del grado
                                    </a>
                                @endif
                                <div class="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($tabSeccionOptions as $sectionOption)
                                        <div class="flex items-center justify-between gap-3 rounded-md border border-white/10 bg-white/5 px-3 py-2">
                                            <div class="min-w-0">
                                                <div class="truncate text-xs font-bold text-gray-800 dark:text-gray-100">
                                                    Sección {{ $sectionOption['name'] }}
                                                </div>
                                                <div class="text-[10px] text-gray-500 dark:text-gray-400">{{ $sectionOption['label'] }}</div>
                                            </div>
                                            <a href="{{ route($moduleRoutePrefix.'.timetable.pdf.preview', ['calendar' => $calendarId, 'seccion' => (int) $sectionOption['id']]) }}"
                                                target="_blank"
                                                class="shrink-0 rounded-md bg-violet-500/10 px-2.5 py-1.5 text-[10px] font-bold text-violet-700 transition-colors hover:bg-violet-500/20 dark:text-violet-300">
                                                PDF
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @php $secGrid = $sectionPreviewGrid ?? []; @endphp
                        @if ($step5GradeTab !== 'pestudio' && $step5SectionTab !== 'formats' && $sectionSlotParity && count($sectionSlotParity['sections']) > 1)
                            <details class="mb-4 rounded-lg border {{ $sectionSlotParity['balanced'] ? 'border-emerald-500/20 bg-emerald-500/[0.03]' : 'border-amber-500/25 bg-amber-500/[0.04]' }}">
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 text-[10px] font-bold uppercase tracking-widest {{ $sectionSlotParity['balanced'] ? 'text-emerald-700 dark:text-emerald-300' : 'text-amber-700 dark:text-amber-300' }}">
                                    <span>Equidad de slots · {{ $sectionSlotParity['grade'] }}</span>
                                    <span class="flex items-center gap-2 normal-case tracking-normal">
                                        <span>{{ $sectionSlotParity['balanced'] ? '✓ Todas iguales' : '⚠ Diferencia de '.$sectionSlotParity['delta'].' slot(s)' }}</span>
                                        <span class="text-base leading-none transition-transform details-open:rotate-180" aria-hidden="true">⌄</span>
                                    </span>
                                </summary>
                                <div x-data="{ parityTab: 'sections' }" class="border-t {{ $sectionSlotParity['balanced'] ? 'border-emerald-500/15' : 'border-amber-500/20' }} px-4 pb-3 pt-3">
                                    <p class="mb-3 text-xs text-gray-600 dark:text-gray-300">
                                        Por regla general, las secciones de un mismo grado deben mantener la misma cantidad de slots llenos.
                                        @if ($sectionSlotParity['balanced'])
                                            La distribución actual está equilibrada.
                                        @else
                                            La sección con más carga tiene {{ $sectionSlotParity['delta'] }} slot(s) más que la sección con menos carga.
                                        @endif
                                    </p>
                                    <div class="mb-3 flex gap-1 border-b border-gray-200 dark:border-white/10">
                                        <button type="button" x-on:click="parityTab = 'sections'"
                                            class="border-b-2 px-3 py-2 text-[10px] font-extrabold uppercase tracking-widest transition-colors"
                                            :class="parityTab === 'sections' ? 'border-emerald-500 text-emerald-700 dark:text-emerald-300' : 'border-transparent text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'">
                                            Por sección
                                        </button>
                                        <button type="button" x-on:click="parityTab = 'subjects'"
                                            class="border-b-2 px-3 py-2 text-[10px] font-extrabold uppercase tracking-widest transition-colors"
                                            :class="parityTab === 'subjects' ? 'border-emerald-500 text-emerald-700 dark:text-emerald-300' : 'border-transparent text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'">
                                            Por asignatura
                                        </button>
                                    </div>

                                    <div x-show="parityTab === 'sections'" x-cloak>
                                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                            @foreach ($sectionSlotParity['sections'] as $paritySection)
                                                <div class="rounded-md border border-white/10 bg-white/5 px-3 py-2">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span class="truncate text-xs font-bold text-gray-800 dark:text-gray-100">
                                                            Sección {{ $paritySection['name'] }}
                                                        </span>
                                                        <span class="shrink-0 text-sm font-extrabold {{ $paritySection['filled_slots'] === $sectionSlotParity['max'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-700 dark:text-gray-200' }}">
                                                            {{ $paritySection['filled_slots'] }}
                                                        </span>
                                                    </div>
                                                    <div class="mt-0.5 text-[10px] text-gray-500 dark:text-gray-400">
                                                        slot(s) llenos · {{ $paritySection['assigned_lessons'] }} asignatura(s) con horario
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div x-show="parityTab === 'subjects'" x-cloak>
                                        <p class="mb-3 text-[11px] text-gray-500 dark:text-gray-400">
                                            Comparativa de bloques/slots asignados por asignatura entre las secciones asociadas a {{ $sectionSlotParity['grade'] }}.
                                        </p>
                                        <div class="overflow-x-auto rounded-md border border-white/10">
                                            <table class="min-w-full text-left">
                                                <thead class="bg-black/5 dark:bg-white/[0.03]">
                                                    <tr class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                        <th class="min-w-[15rem] px-3 py-2">Asignatura</th>
                                                        @foreach ($sectionSlotParity['sections'] as $paritySection)
                                                            <th class="min-w-[7rem] px-3 py-2 text-center">Sección {{ $paritySection['name'] }}</th>
                                                        @endforeach
                                                        <th class="min-w-[7rem] px-3 py-2 text-center">Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200/80 dark:divide-white/10">
                                                    @forelse ($sectionSlotParity['subjects'] as $paritySubject)
                                                        <tr class="text-[11px] text-gray-700 dark:text-gray-200">
                                                            <td class="px-3 py-2.5">
                                                                <div class="font-bold">{{ $paritySubject['name'] }}</div>
                                                                <div class="mt-0.5 text-[10px] text-gray-400 dark:text-gray-500">
                                                                    Total del grupo: {{ array_sum($paritySubject['sections']) }} slot(s)
                                                                </div>
                                                            </td>
                                                            @foreach ($sectionSlotParity['sections'] as $paritySection)
                                                                @php $subjectSlots = (int) ($paritySubject['sections'][$paritySection['id']] ?? 0); @endphp
                                                                <td class="px-3 py-2.5 text-center">
                                                                    <span class="inline-flex min-w-[2rem] items-center justify-center rounded-md px-2 py-1 font-extrabold {{ $subjectSlots === $paritySubject['max'] ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300' : ($subjectSlots < $paritySubject['min'] ? 'bg-red-500/10 text-red-600 dark:text-red-300' : 'bg-amber-500/10 text-amber-700 dark:text-amber-300') }}">
                                                                        {{ $subjectSlots }}
                                                                    </span>
                                                                </td>
                                                            @endforeach
                                                            <td class="px-3 py-2.5 text-center">
                                                                <span class="text-[10px] font-bold {{ $paritySubject['balanced'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-300' }}">
                                                                    {{ $paritySubject['balanced'] ? 'Equilibrada' : 'Diferencia '.$paritySubject['delta'] }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="{{ count($sectionSlotParity['sections']) + 2 }}" class="px-3 py-4 text-center text-xs text-gray-500 dark:text-gray-400">
                                                                No hay asignaturas configuradas para comparar.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </details>
                        @endif
                        @if ($step5GradeTab !== 'pestudio' && $step5SectionTab !== 'formats' && $activeConflictGroup)
                            @include('livewire.coordinacion.timetable.partials.preview-conflicts', ['activeConflictGroup' => $activeConflictGroup])
                        @endif
                        @if ($step5GradeTab !== 'pestudio' && $step5SectionTab !== 'formats' && $preview && $periodsList->isNotEmpty())
                            @php $sectionGapSummary = $this->previewSectionGapSummary($activeSeccionId); @endphp
                            @if (($sectionGapSummary['empty_cells'] ?? 0) > 0 || !empty($sectionGapSummary['incomplete_lessons']))
                                <div class="mb-4 rounded-lg border border-amber-500/30 bg-amber-500/5 p-4" role="status">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <div class="text-[10px] font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400">
                                                Cobertura de la sección
                                            </div>
                                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                                                La generación por sección puede dejar períodos disponibles sin una lesson asignada.
                                            </p>
                                        </div>
                                        <span class="rounded-md bg-amber-500/10 px-2 py-1 text-[10px] font-bold text-amber-700 dark:text-amber-300">
                                            {{ $sectionGapSummary['empty_cells'] ?? 0 }} celda(s) vacía(s)
                                        </span>
                                    </div>
                                    @if (!empty($sectionGapSummary['empty_periods']))
                                        <div class="mt-3 flex flex-wrap gap-1.5">
                                            @foreach ($sectionGapSummary['empty_periods'] as $emptyPeriod)
                                                <span class="rounded-md border border-amber-500/20 bg-white/5 px-2 py-1 text-[10px] text-gray-600 dark:text-gray-300">
                                                    {{ $emptyPeriod['day'] }} · {{ $emptyPeriod['time'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if (!empty($sectionGapSummary['incomplete_lessons']))
                                        <div class="mt-3 border-t border-amber-500/20 pt-3">
                                            <div class="text-[10px] font-bold uppercase tracking-widest text-red-500">Lessons incompletas</div>
                                            <div class="mt-1 space-y-1 text-[11px] text-gray-600 dark:text-gray-300">
                                                @foreach ($sectionGapSummary['incomplete_lessons'] as $incompleteLesson)
                                                    <div>
                                                        <strong>{{ $incompleteLesson['subject'] }}</strong>:
                                                        {{ $incompleteLesson['assigned'] }}/{{ $incompleteLesson['required'] }} bloques,
                                                        faltan {{ $incompleteLesson['missing'] }}.
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            <div id="timetable-preview-grid">
                            <div class="flex items-center justify-end pb-2">
                                <div class="inline-flex overflow-hidden rounded-lg border border-gray-200 shadow-sm dark:border-white/10" role="group" aria-label="Exportar y auditar dry-run">
                                <a href="{{ route($moduleRoutePrefix.'.timetable.pdf.preview', ['calendar' => $calendarId, 'seccion' => (int) $activeSeccionId]) }}"
                                    target="_blank" rel="noopener"
                                    class="inline-flex items-center gap-1.5 border-r border-gray-200 bg-white/5 px-3 py-1.5 text-xs font-bold text-gray-300 transition-colors hover:bg-white/10 dark:border-white/10">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Exportar PDF
                                </a>
                                <button type="button"
                                    wire:click="downloadDryRunResult({{ (int) $activeSeccionId }})"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                    wire:target="downloadDryRunResult"
                                    title="Descargar informe JSON auditable del dry-run"
                                    class="inline-flex items-center gap-1.5 bg-sky-500/10 px-3 py-1.5 text-xs font-bold text-sky-700 transition-colors hover:bg-sky-500/20 dark:text-sky-300">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l-4-4m4 4l4-4M5 21h14"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="downloadDryRunResult">Auditoría JSON</span>
                                    <span wire:loading wire:target="downloadDryRunResult">Preparando…</span>
                                </button>
                                <button type="button"
                                    wire:click="persistCurrentSectionSlots"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                    wire:target="persistCurrentSectionSlots"
                                    title="Guardar en la base de datos los slots de la sección activa"
                                    class="inline-flex items-center gap-1.5 bg-emerald-500/10 px-3 py-1.5 text-xs font-bold text-emerald-700 transition-colors hover:bg-emerald-500/20 dark:text-emerald-300">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12l4 4L19 6"/>
                                    </svg>
                                    <span wire:loading.remove wire:target="persistCurrentSectionSlots">Guardar sección</span>
                                    <span wire:loading wire:target="persistCurrentSectionSlots">Guardando…</span>
                                </button>
                                <button type="button"
                                    wire:click="downloadCurrentSectionSlotsBackup({{ (int) $activeSeccionId }})"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                    wire:target="downloadCurrentSectionSlotsBackup"
                                    title="Descargar respaldo JSON de los slots de la sección activa"
                                    class="inline-flex items-center gap-1.5 bg-sky-500/10 px-3 py-1.5 text-xs font-bold text-sky-700 transition-colors hover:bg-sky-500/20 dark:text-sky-300">
                                    <span wire:loading.remove wire:target="downloadCurrentSectionSlotsBackup">Backup slots</span>
                                    <span wire:loading wire:target="downloadCurrentSectionSlotsBackup">Preparando…</span>
                                </button>
                                <label title="Seleccionar respaldo JSON de slots de la sección activa"
                                    class="inline-flex cursor-pointer items-center gap-1.5 bg-white/5 px-3 py-1.5 text-xs font-bold text-gray-600 transition-colors hover:bg-white/10 dark:text-gray-300">
                                    <span>Elegir restore</span>
                                    <input type="file" wire:model="slotsBackupFile" accept="application/json,.json" class="sr-only">
                                </label>
                                <button type="button"
                                    wire:click="restoreCurrentSectionSlotsBackup"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                    wire:target="restoreCurrentSectionSlotsBackup,slotsBackupFile"
                                    title="Restaurar los slots de la sección activa desde un respaldo JSON"
                                    class="inline-flex items-center gap-1.5 bg-violet-500/10 px-3 py-1.5 text-xs font-bold text-violet-700 transition-colors hover:bg-violet-500/20 dark:text-violet-300">
                                    <span wire:loading.remove wire:target="restoreCurrentSectionSlotsBackup">Restore slots</span>
                                    <span wire:loading wire:target="restoreCurrentSectionSlotsBackup">Restaurando…</span>
                                </button>
                                <button type="button"
                                    wire:click="openTeacherScheduleDialog"
                                    title="Consultar las lessons asignadas por profesor"
                                    aria-label="Consultar horario por profesor"
                                    class="inline-flex items-center gap-1.5 bg-fuchsia-500/10 px-3 py-1.5 text-xs font-bold text-fuchsia-700 transition-colors hover:bg-fuchsia-500/20 dark:text-fuchsia-300">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    Horario docente
                                </button>
                                </div>
                            </div>
                            @foreach ($periodsList->groupBy('shift_id') as $shiftId => $shiftPeriods)
                                @php $shift = $shifts->firstWhere('id', $shiftId); @endphp
                                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-1.5">
                                    <div class="-mx-1.5 -mt-1.5 mb-1.5 flex items-center justify-between gap-1.5 rounded-t-lg border-b border-emerald-500/15 bg-emerald-500/5 px-1.5 py-1 dark:border-emerald-400/10 dark:bg-emerald-400/5">
                                        <div class="flex min-w-0 items-center gap-2">
                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-300" aria-hidden="true">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                                                    <circle cx="12" cy="12" r="9"/>
                                                </svg>
                                            </span>
                                            <span class="truncate text-[11px] font-extrabold uppercase tracking-wide text-emerald-800 dark:text-emerald-200">
                                                {{ $shift?->name ?? ('Turno '.$shiftId) }}
                                            </span>
                                        </div>
                                        @if ($shift?->start_time)
                                            <span class="shrink-0 rounded-md bg-white/70 px-2 py-1 font-mono text-[10px] font-bold text-emerald-700 dark:bg-black/10 dark:text-emerald-300">
                                                {{ substr((string) $shift->start_time, 0, 5) }}–{{ substr((string) $shift->end_time, 0, 5) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="grid grid-cols-6 gap-px">
                                        <div class="text-[10px] font-bold text-gray-400">Hora</div>
                                        @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dayLabel)
                                            <div class="text-[10px] font-bold text-gray-400 text-center">{{ $dayLabel }}</div>
                                        @endforeach

                                        @foreach ($shiftPeriods->groupBy('order_in_day') as $order => $group)
                                            @php $rowPeriod = $group->first(); @endphp
                                            <div class="contents">
                                                @php $isBreakRow = (bool) $rowPeriod?->is_break; @endphp
                                                <div class="flex items-center rounded px-1 text-[10px] font-bold {{ $isBreakRow ? 'bg-amber-500/5 text-amber-700/80 dark:bg-amber-400/5 dark:text-amber-300/80' : 'text-gray-400' }}">
                                                    {{ substr((string) $rowPeriod?->start_time, 0, 5) }}–{{ substr((string) $rowPeriod?->end_time, 0, 5) }}
                                                </div>
                                                @for ($day = 1; $day <= 5; $day++)
                                                    @php
                                                        $cellAssignments = $secGrid[$shiftId][$order][$day] ?? [];
                                                        $targetPeriod = $group->firstWhere('day_of_week', $day);
                                                    @endphp
                                                    @php $isBreak = (bool) ($group->firstWhere('day_of_week', $day)?->is_break ?? $rowPeriod?->is_break); @endphp
                                                    <div
                                                        @if (! $isBreak && $targetPeriod)
                                                            x-on:dragover.prevent
                                                            x-on:drop.prevent="$wire.movePreviewLesson(parseInt(event.dataTransfer.getData('lesson-id')), parseInt(event.dataTransfer.getData('period-id')), {{ (int) $targetPeriod->id }})"
                                                        @endif
                                                        class="relative flex min-h-[28px] flex-col items-stretch justify-center gap-px rounded p-px {{ $isBreak ? 'bg-amber-500/5 text-amber-700/70 dark:bg-amber-400/5 dark:text-amber-300/70' : ($cellAssignments ? 'bg-emerald-500/5 border border-emerald-500/10' : 'border border-transparent') }} {{ ! $isBreak ? 'hover:bg-emerald-500/10 transition-colors' : '' }}">
                                                        @if ($cellAssignments)
                                                            @foreach ($cellAssignments as $cell)
                                                                <div
                                                                    draggable="true"
                                                                    x-on:dragstart="event.dataTransfer.effectAllowed = 'move'; event.dataTransfer.setData('lesson-id', '{{ (int) $cell['lesson_id'] }}'); event.dataTransfer.setData('period-id', '{{ (int) $cell['period_id'] }}')"
                                                                    title="{{ !empty($cell['is_half_group']) ? 'Asignatura de medio grupo' : 'Asignatura de grupo completo' }}"
                                                                    class="flex cursor-grab flex-col gap-px rounded p-0.5 text-center leading-tight active:cursor-grabbing {{ count($cellAssignments) > 1 ? 'bg-white/5' : '' }} {{ !empty($cell['is_half_group']) ? 'border border-solid border-violet-500/[0.02] bg-violet-500/[0.02]' : '' }}">
                                                                    <div class="flex min-h-3.5 items-center justify-between gap-px">
                                                                        <span class="sr-only">Acciones de {{ $cell['asignatura'] }}</span>
                                                                        <span class="min-w-0 flex-1"></span>
                                                                        <button type="button"
                                                                            wire:click.stop="confirmRemovePreviewLesson({{ (int) $cell['lesson_id'] }})"
                                                                            title="Retirar esta lección del preview"
                                                                            aria-label="Retirar {{ $cell['asignatura'] }} del preview"
                                                                            class="inline-flex h-3.5 w-3.5 shrink-0 items-center justify-center rounded-full border border-gray-300/70 bg-white/80 text-[9px] font-bold leading-none text-gray-500 transition-colors hover:border-red-400/60 hover:bg-red-500/10 hover:text-red-500 focus:outline-none focus:ring-2 focus:ring-red-400 dark:border-white/15 dark:bg-gray-900/50 dark:text-gray-300">
                                                                            <span aria-hidden="true">×</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="flex min-h-4 items-center justify-center gap-px px-px text-[9px] font-bold text-gray-900 dark:text-white">
                                                                        <span>{{ $cell['asignatura'] }}</span>
                                                                    </div>
                                                                    @if ($cell['profesor'])
                                                                        <div class="px-px text-[8px] text-gray-500 dark:text-gray-400">{{ $cell['profesor'] }}</div>
                                                                    @endif
                                                                    @if ($cell['grupo'])
                                                                        <div class="px-px text-[8px] text-emerald-600 dark:text-emerald-400">{{ $cell['grupo'] }}</div>
                                                                    @endif
                                                                    @if (!empty($cell['is_half_group']))
                                                                        <div class="flex justify-end">
                                                                            <button type="button"
                                                                                wire:click.stop="openAddPreviewLessonModal({{ (int) $targetPeriod?->id }})"
                                                                                title="Agregar una lección a este período"
                                                                                aria-label="Agregar una lección al período {{ $targetPeriod?->period_label }}"
                                                                                class="inline-flex h-3.5 w-3.5 items-center justify-center rounded-full bg-emerald-600 text-[10px] font-bold leading-none text-white shadow-sm transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                                                                                <span aria-hidden="true">+</span>
                                                                                <span class="sr-only">Agregar lección</span>
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        @elseif ($isBreak)
                                                            <div class="w-full text-center text-[9px] font-semibold uppercase tracking-wide opacity-75">Receso</div>
                                                        @else
                                                            <button type="button"
                                                                wire:click="openAddPreviewLessonModal({{ (int) $targetPeriod?->id }})"
                                                                title="Agregar una lección a este período"
                                                                aria-label="Agregar una lección al período {{ $targetPeriod?->period_label }}"
                                                                class="absolute right-1 top-1 z-10 inline-flex h-5 w-5 items-center justify-center rounded bg-emerald-600 text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                                                                <span aria-hidden="true" class="text-sm leading-none">+</span>
                                                                <span class="sr-only">Agregar lección</span>
                                                            </button>
                                                            <span class="flex min-h-[28px] items-center justify-center text-[9px] text-gray-400 dark:text-gray-500">Vacío</span>
                                                        @endif
                                                    </div>
                                                @endfor
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        @else
                            <div class="rounded-lg border border-dashed border-gray-300 dark:border-white/10 p-6 text-center text-sm text-gray-400">
                                Selecciona una sección con clases asignadas en la vista previa (o ejecuta el dry-run).
                            </div>
                        @endif

                    </div>
                @endif
            </div>
        </div>
    @endif

    <x-modal-card
        title="Diagnóstico del horario publicado"
        blur="lg"
        wire:model="showAiAnalysisModal"
        max-width="7xl"
        persistent
    >
        @if ($aiDryRunAnalysis)
            <div class="max-h-[70vh] overflow-y-auto rounded-lg border border-violet-500/20 bg-violet-500/[0.03] px-4 py-4 dark:bg-violet-500/[0.06]">
                <div class="prose prose-sm max-w-none text-gray-700 dark:prose-invert dark:text-gray-200
                    prose-headings:mb-3 prose-headings:mt-5 prose-headings:font-extrabold prose-headings:text-gray-900
                    prose-p:my-2 prose-li:my-1 prose-table:my-4 prose-th:bg-violet-500/10 prose-th:px-3 prose-th:py-2
                    prose-td:border-gray-200 prose-td:px-3 prose-td:py-2 dark:prose-headings:text-white dark:prose-td:border-white/10">
                    {!! \Illuminate\Support\Str::markdown($aiDryRunAnalysis) !!}
                </div>
            </div>
            @if ($aiDryRunAnalysisModel)
                <p class="mt-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                    Modelo: {{ $aiDryRunAnalysisModel }}
                </p>
            @endif
        @else
            <div class="rounded-lg border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500 dark:border-white/10 dark:text-gray-400">
                No hay un diagnóstico disponible.
            </div>
        @endif
        <x-slot name="footer">
            <button
                type="button"
                wire:click="$set('showAiAnalysisModal', false)"
                class="w-full rounded-lg bg-violet-600 px-4 py-2.5 text-sm font-bold text-white transition-colors hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500/50"
            >
                Cerrar diagnóstico
            </button>
        </x-slot>
    </x-modal-card>

    {{-- Modal · Nuevo borrador (vive a nivel raíz: el botón "+ Nuevo borrador"
         del switcher global es visible en TODOS los pasos, no solo en el 1;
         dentro de @if($currentStep===1) el modal no existía en el DOM cuando
         se abría desde pasos 2-5). --}}
    <x-modal-card title="Nuevo borrador de calendario" blur="lg" wire:model="showCreateCalendarForm" max-width="md" persistent>
        <div class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Lapso</label>
                <select wire:model.live="lapsoId" class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Selecciona un lapso</option>
                    @foreach ($lapsos as $lapso)
                        <option value="{{ $lapso->id }}">{{ $lapso->name }}</option>
                    @endforeach
                </select>
                @error('lapsoId') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Plan de estudio (pestudio)</label>
                <select wire:model="pestudioId" class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Selecciona el plan</option>
                    @foreach ($pestudios as $pes)
                        <option value="{{ $pes->id }}">{{ $pes->name }}</option>
                    @endforeach
                </select>
                @error('pestudioId') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Nombre del calendario</label>
                <input type="text" wire:model.live="calendarName" placeholder="Horario 2025-2026 · Lapso I"
                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                @error('calendarName') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Duración del bloque (min)</label>
                <input type="number" wire:model="periodMinutes" min="30" max="120"
                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Asignaturas por período</label>
                <input type="number" wire:model="maxSubjectsPerPeriod" min="1" max="10"
                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                @error('maxSubjectsPerPeriod') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">Por defecto: 2. Aplica a las asignaturas marcadas como medio grupo.</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Estrategia de generación</label>
                <select wire:model="strategy" class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="optimized">Optimizado</option>
                    <option value="legacy">Legacy</option>
                </select>
                @error('strategy') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">Optimizado permite reacomodar medio grupo; Legacy conserva las asignaciones legacy bloqueadas.</p>
            </div>
        </div>
        <x-slot name="footer">
            <div class="flex items-center gap-3 justify-end">
                <button wire:click="$set('showCreateCalendarForm', false)"
                    class="px-4 py-2 rounded-lg bg-white/5 text-gray-400 text-sm font-bold">Cancelar</button>
                <button wire:click="createCalendar"
                    class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">Crear borrador</button>
            </div>
        </x-slot>
    </x-modal-card>

    {{-- Modal · Editar calendario seleccionado (nombre + duración del bloque;
         lapso/pestudio son identidad y no se editan, SPEC §10.1). --}}
    <x-modal-card title="Editar calendario" blur="lg" wire:model="showEditCalendarForm" max-width="md" persistent>
        <div class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Nombre del calendario</label>
                <input type="text" wire:model.live="calendarName" placeholder="Horario 2025-2026 · Lapso I"
                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                @error('calendarName') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Duración del bloque (min)</label>
                <input type="number" wire:model="periodMinutes" min="30" max="120"
                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                @error('periodMinutes') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Asignaturas por período</label>
                <input type="number" wire:model="maxSubjectsPerPeriod" min="1" max="10"
                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                @error('maxSubjectsPerPeriod') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">Por defecto: 2. Aplica a las asignaturas marcadas como medio grupo.</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Estrategia de generación</label>
                <select wire:model="strategy" class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="optimized">Optimizado</option>
                    <option value="legacy">Legacy</option>
                </select>
                @error('strategy') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">Optimizado permite reacomodar medio grupo; Legacy conserva las asignaciones legacy bloqueadas.</p>
            </div>
        </div>
        <x-slot name="footer">
            <div class="flex items-center gap-3 justify-end">
                <button wire:click="$set('showEditCalendarForm', false)"
                    class="px-4 py-2 rounded-lg bg-white/5 text-gray-400 text-sm font-bold">Cancelar</button>
                <button wire:click="updateCalendar"
                    class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">Guardar cambios</button>
            </div>
        </x-slot>
    </x-modal-card>

@if ($showAddPreviewLessonModal)
    @php
        $targetPeriod = $this->addPreviewPeriod();
        $lessonsToAdd = $this->availablePreviewLessons();
    @endphp
    <x-modal-card title="Agregar lección al período" blur="lg" wire:model="showAddPreviewLessonModal" align="center" max-width="md" persistent>
        <div class="space-y-3">
            {{-- Contexto del período destino --}}
            <div class="rounded-lg border border-emerald-500/20 bg-emerald-500/5 px-3 py-2.5">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-800 dark:text-gray-200">
                            @if ($targetPeriod)
                                {{ $targetPeriod->shift?->name ?? 'Turno' }} · {{ $targetPeriod->day_label ?? ('Día '.$targetPeriod->day_of_week) }}
                                · {{ substr((string) $targetPeriod->start_time, 0, 5) }}–{{ substr((string) $targetPeriod->end_time, 0, 5) }}
                            @else
                                Selecciona una lección para este período
                            @endif
                        </p>
                        <p class="mt-0.5 text-[11px] text-gray-500 dark:text-gray-400">
                            Se muestran {{ $lessonsToAdd->count() }} opción(es)
                            @if ($addPreviewLessonSource === 'missing') faltantes @elseif ($addPreviewLessonSource === 'grade') de la sección actual @else del listado actual @endif
                            · primero las del mismo turno.
                        </p>
                    </div>
                    @if ($targetPeriod)
                        <span class="shrink-0 rounded-md bg-emerald-500/15 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-300">
                            {{ $targetPeriod->shift?->code ?? '—' }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Búsqueda --}}
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-[minmax(0,1fr)_auto]">
                <select wire:model.live="addPreviewLessonSource"
                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-emerald-500 focus:ring-emerald-500/50 outline-none dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                    <option value="missing">Asignaturas faltantes</option>
                    <option value="current">Listado actual</option>
                    <option value="grade">Todas las asignaturas de la sección actual</option>
                </select>
                @if (!is_numeric($activeSeccionId))
                    <span class="self-center text-[10px] text-amber-600 dark:text-amber-300">Selecciona una sección para consultar el grado.</span>
                @endif
            </div>
            <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>
                </svg>
                <input type="text" wire:model.live.debounce.250ms="addPreviewLessonSearch"
                    placeholder="Buscar asignatura, docente o sección…"
                    class="w-full rounded-lg border border-gray-300 bg-white pl-9 pr-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-emerald-500/50 outline-none dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
            </div>

            <div class="max-h-[min(52vh,26rem)] space-y-2 overflow-y-auto pr-1">
            @forelse ($lessonsToAdd as $availableLesson)
                @php
                    $shiftMismatch = $targetPeriod && (int) $availableLesson->shift_id !== (int) $targetPeriod->shift_id;
                    $teacherName = trim(($availableLesson->pevaluacion?->profesor?->lastname ?? '').' '.($availableLesson->pevaluacion?->profesor?->name ?? ''));
                    $isGradeCandidate = !empty($availableLesson->is_grade_candidate);
                @endphp
                <button type="button"
                    wire:click="{{ $isGradeCandidate ? 'addPreviewPevaluacion('.(int) $availableLesson->pevaluacion_id.')' : 'addPreviewLesson('.(int) $availableLesson->id.')' }}"
                    wire:loading.attr="disabled" wire:target="addPreviewLesson,addPreviewPevaluacion"
                    class="group flex w-full items-center gap-3 rounded-lg border px-3 py-2.5 text-left transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500/50 disabled:opacity-50
                        {{ $shiftMismatch
                            ? 'border-amber-500/30 bg-amber-500/5 hover:border-amber-500/60 hover:bg-amber-500/10'
                            : 'border-gray-200 bg-white hover:border-emerald-500/60 hover:bg-emerald-50 dark:border-white/10 dark:bg-white/5 dark:hover:bg-emerald-500/10' }}">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md text-sm font-bold
                        {{ $shiftMismatch ? 'bg-amber-500/15 text-amber-700 dark:text-amber-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' }}">
                        +
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="flex items-center gap-1.5">
                            <span class="block truncate text-sm font-bold text-gray-900 dark:text-gray-200">
                                {{ $availableLesson->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura sin nombre' }}
                            </span>
                            @if ($shiftMismatch)
                                <span class="shrink-0 rounded bg-amber-500/15 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-amber-700 dark:text-amber-300" title="La lección es de otro turno">
                                    otro turno
                                </span>
                            @endif
                        </span>
                        <span class="mt-0.5 block truncate text-[11px] text-gray-500 dark:text-gray-400">
                            {{ $availableLesson->pevaluacion?->seccion?->grado?->name ? $availableLesson->pevaluacion->seccion->grado->name.' · ' : '' }}Sección {{ $availableLesson->pevaluacion?->seccion?->name ?? '—' }}
                            @if ($teacherName) · {{ $teacherName }} @endif
                            @if ($isGradeCandidate) · Nueva en este calendario @endif
                        </span>
                    </span>
                    <span class="shrink-0 text-right text-[10px] font-semibold text-gray-500 dark:text-gray-400">
                        <span class="block">T {{ $availableLesson->weekly_blocks_t }}</span>
                        <span class="block">P {{ $availableLesson->weekly_blocks_p }}</span>
                    </span>
                </button>
            @empty
                <div class="rounded-lg border border-dashed border-gray-300 px-3 py-6 text-center text-xs text-gray-400 dark:border-white/10">
                    @if ($addPreviewLessonSearch !== '')
                        Sin resultados para «{{ $addPreviewLessonSearch }}».
                    @else
                        No hay lecciones sin asignar disponibles para agregar.
                    @endif
                </div>
            @endforelse
            </div>
        </div>
        <x-slot name="footer">
            <div class="flex items-center justify-between gap-3">
                <span class="text-[10px] text-gray-400 dark:text-gray-500">Las de «otro turno» se agregan con advertencia.</span>
                <button type="button" wire:click="closeAddPreviewLessonModal"
                    class="px-4 py-2 rounded-lg bg-white/5 text-gray-400 text-sm font-bold">
                    Cancelar
                </button>
            </div>
        </x-slot>
    </x-modal-card>
@endif

    @include('coordinacion.help-timetable-wizard')
</div>