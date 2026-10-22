@php
    $lightCalendar = $calendarId
        ? \App\Models\app\Timetable\TimetableCalendar::query()->with(['lapso:id,name', 'pestudio:id,name'])->find($calendarId)
        : null;
@endphp

<div class="mx-auto w-full max-w-[1600px] px-4 py-6">
    @if ($lightStep === 2 && $lightCalendar)
        <div class="mb-3 flex justify-end">
            {{-- Selector WireUI de calendario activo, justo encima del header
                 y alineado a la derecha. Solo permite calendarios «active». --}}
            <x-dropdown position="bottom-end" width="7xl" height="auto">
                <x-slot name="trigger">
                    <button type="button"
                        wire:loading.attr="disabled"
                        wire:target="switchCalendar"
                        title="Cambiar calendario activo"
                        aria-label="Cambiar calendario activo"
                        aria-haspopup="menu"
                        class="inline-flex max-w-[22rem] items-center gap-2 rounded-xl border border-emerald-500/30 bg-emerald-500/[0.07] px-3 py-2 text-left text-emerald-800 transition-colors hover:border-emerald-500/50 hover:bg-emerald-500/10 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 disabled:cursor-wait disabled:opacity-70 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-200">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                        </svg>
                        <span class="min-w-0">
                            <span class="block truncate text-xs font-extrabold leading-tight">{{ $lightCalendar->name }}</span>
                            @if ($lightCalendar->pestudio?->name)
                                <span class="block truncate text-[10px] font-semibold leading-tight text-emerald-600/70 dark:text-emerald-300/70">{{ $lightCalendar->pestudio->name }}</span>
                            @endif
                        </span>
                        <svg class="ml-1 h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                        </svg>
                    </button>
                </x-slot>

                @if ($lightCalendar->status !== 'active')
                    <x-dropdown.header label="Calendario abierto ({{ $lightCalendar->status }})" />
                    <x-dropdown.item icon="exclamation-triangle">
                        <span class="min-w-0 flex-1 truncate text-xs font-semibold">{{ $lightCalendar->name }}</span>
                    </x-dropdown.item>
                    <div class="my-1 border-t border-secondary-200 dark:border-secondary-600"></div>
                @endif

                <x-dropdown.header label="Calendarios activos" />
                @forelse ($calendarSwitcher as $calendarOption)
                    <x-dropdown.item
                        wire:key="light-calendar-option-{{ $calendarOption['id'] }}"
                        wire:click="switchCalendar({{ $calendarOption['id'] }})"
                        :icon="$calendarOption['id'] === (int) $lightCalendar->id ? 'check' : 'calendar-days'">
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-xs font-semibold">{{ $calendarOption['name'] }}</span>
                            <span class="block truncate text-[10px] text-secondary-400 dark:text-secondary-500">
                                {{ $calendarOption['pestudio'] }}@if ($calendarOption['lapso'] !== '') · {{ $calendarOption['lapso'] }}@endif
                            </span>
                        </span>
                    </x-dropdown.item>
                @empty
                    <div class="px-4 py-3 text-xs text-secondary-500 dark:text-secondary-400">
                        No hay calendarios activos.
                    </div>
                @endforelse
            </x-dropdown>
        </div>
    @endif

    <header class="mb-5 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold tracking-tight text-gray-900 dark:text-white">Horario · modo rápido</h1>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                @if ($lightStep === 2 && $lightTab === 'classes')
                    Define las clases del calendario (asignaturas, bloques, turno y aula). Los cambios se aplican al guardar.
                @else
                    Elige un calendario y organiza los bloques con arrastrar y soltar. Los cambios se guardan al instante.
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            {{-- Sincronización con la carga académica asignada. Visible desde el
                 paso 1 para que la acción sea descubrible; se habilita al abrir
                 un calendario. --}}
            <button type="button" wire:click="syncAcademicLoad"
                wire:loading.attr="disabled"
                wire:target="syncAcademicLoad"
                @disabled(! $lightCalendar)
                title="{{ $lightCalendar ? 'Crear las clases faltantes desde las Pevaluaciones vigentes del lapso y P.Estudio' : 'Selecciona un calendario para sincronizar la carga académica' }}"
                aria-label="Sincronizar carga académica"
                aria-disabled="{{ $lightCalendar ? 'false' : 'true' }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-sky-500/40 bg-sky-500/10 px-3 py-1.5 text-xs font-bold text-sky-700 transition-colors hover:bg-sky-500/20 disabled:cursor-not-allowed disabled:opacity-50 dark:text-sky-300">
                    <span wire:loading.remove wire:target="syncAcademicLoad" class="inline-flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Sincronizar carga
                    </span>
                    <span wire:loading wire:target="syncAcademicLoad" class="inline-flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Sincronizando…
                    </span>
                </button>

                @if ($lightStep === 2 && $lightCalendar)
                {{-- Indicador guardado / guardando --}}
                <span class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider dark:border-white/10">
                    <span wire:loading.remove wire:target="movePreviewLesson,addPreviewLesson,addPreviewPevaluacion,removePreviewLesson,togglePreviewSlotLock,toggleSectionPreviewSlotsLock,saveLessons,syncAcademicLoad,confirmRemoveOrphanLessons,removeOrphanLessons"
                        class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Guardado{{ $lastSavedAt ? ' · '.$lastSavedAt : '' }}
                    </span>
                    <span wire:loading wire:target="movePreviewLesson,addPreviewLesson,addPreviewPevaluacion,removePreviewLesson,togglePreviewSlotLock,toggleSectionPreviewSlotsLock,saveLessons,syncAcademicLoad,confirmRemoveOrphanLessons,removeOrphanLessons"
                        class="inline-flex items-center gap-1.5 text-amber-600 dark:text-amber-300">
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-500"></span>
                        Guardando…
                    </span>
                </span>

                <button type="button" wire:click="openTeacherScheduleDialog"
                    title="Consultar las lessons asignadas por profesor"
                    aria-label="Consultar horario por profesor"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-fuchsia-500/10 px-3 py-1.5 text-xs font-bold text-fuchsia-700 transition-colors hover:bg-fuchsia-500/20 dark:text-fuchsia-300">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Horario docente
                </button>

                {{-- Dropdown: formatos y reportes PDF --}}
                <x-dropdown position="bottom-end" width="3xl" height="auto">
                    <x-slot name="trigger">
                        <x-button
                            label="Formatos"
                            icon="document-text"
                            right-icon="chevron-down"
                            color="base"
                            variant="outline"
                            class="font-bold" />
                    </x-slot>

                    <x-dropdown.item
                        href="#"
                        wire:click="openPestudiosExport"
                        wire:loading.attr="disabled"
                        wire:target="openPestudiosExport"
                        icon="arrow-down-tray"
                        label="Exportar P.Estudios (PDF / HTML / XLS)"
                        class="w-full" />

                    <x-dropdown.item
                        href="#"
                        x-on:click.prevent="$dispatch('open-teachers-pdf')"
                        icon="document-arrow-down"
                        label="Consolidado de docentes"
                        separator />

                    <x-dropdown.item
                        href="#"
                        x-on:click.prevent="$dispatch('open-teacher-totals')"
                        icon="table-cells"
                        label="Totalización por docente" />
                </x-dropdown>
                @endif
            </div>
    </header>

    @if ($lightStep === 2 && $lightCalendar && ! $academicSync['in_sync'])
        @php
            $pendingSync = max(0, (int) $academicSync['missing'] - (int) $academicSync['missing_locked']);
        @endphp
        <div class="mb-5 flex flex-wrap items-start gap-3 rounded-xl border border-amber-500/30 bg-amber-500/[0.06] px-4 py-3 text-amber-800 dark:text-amber-200"
            role="status" aria-live="polite">
            <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-extrabold uppercase tracking-wide">No hay sincronía con la carga académica</p>
                <p class="mt-1 text-[11px] leading-relaxed text-amber-700/90 dark:text-amber-200/80">
                    @if ((int) $academicSync['missing'] > 0)
                        {{ (int) $academicSync['missing'] }} asignatura(s) con carga vigente aún no tienen clase en este calendario.
                    @endif
                    @if ((int) $academicSync['missing_locked'] > 0)
                        {{ (int) $academicSync['missing_locked'] }} de ellas están en secciones con el horario bloqueado.
                    @endif
                    @if ((int) $academicSync['orphaned'] > 0)
                        {{ (int) $academicSync['orphaned'] }} clase(s) ya no corresponden a la carga vigente del lapso/P.Estudio. Puedes retirarlas con «Limpiar huérfanas».
                    @endif
                </p>
            </div>
            @if ($pendingSync > 0)
                <button type="button" wire:click="syncAcademicLoad"
                    wire:loading.attr="disabled"
                    wire:target="syncAcademicLoad"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-bold text-white transition-colors hover:bg-amber-700 disabled:cursor-not-allowed disabled:opacity-50">
                    <span wire:loading.remove wire:target="syncAcademicLoad">Sincronizar ahora</span>
                    <span wire:loading wire:target="syncAcademicLoad">Sincronizando…</span>
                </button>
            @endif
            @if ((int) $academicSync['orphaned'] > 0)
                <button type="button" wire:click="confirmRemoveOrphanLessons"
                    wire:loading.attr="disabled"
                    wire:target="confirmRemoveOrphanLessons,removeOrphanLessons"
                    title="Eliminar las clases que ya no pertenecen a la carga académica vigente"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-500/40 bg-red-500/10 px-3 py-1.5 text-xs font-bold text-red-700 transition-colors hover:bg-red-500/20 disabled:cursor-not-allowed disabled:opacity-50 dark:text-red-300">
                    <span wire:loading.remove wire:target="removeOrphanLessons">Limpiar huérfanas ({{ (int) $academicSync['orphaned'] }})</span>
                    <span wire:loading wire:target="removeOrphanLessons">Limpiando…</span>
                </button>
            @endif
        </div>
    @endif

    @if ($lightStep === 2 && $lightCalendar && $academicSync['in_sync'])
        <div class="mb-5 flex flex-wrap items-start gap-3 rounded-xl border border-emerald-500/30 bg-emerald-500/[0.06] px-4 py-3 text-emerald-800 dark:text-emerald-200"
            role="status" aria-live="polite">
            <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-extrabold uppercase tracking-wide">Sincronizado con la carga académica</p>
                <p class="mt-1 text-[11px] leading-relaxed text-emerald-700/90 dark:text-emerald-200/80">
                    @if ((int) $academicSync['active'] > 0)
                        La carga vigente del lapso/P.Estudio tiene clase en este calendario y no hay clases huérfanas.
                    @else
                        No hay cargas académicas vigentes para el lapso/P.Estudio de este calendario.
                    @endif
                </p>
            </div>
            @if ((int) $academicSync['active'] > 0)
                <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wide">
                    {{ (int) $academicSync['active'] }} {{ (int) $academicSync['active'] === 1 ? 'carga vigente' : 'cargas vigentes' }}
                </span>
            @endif
        </div>
    @endif

    {{-- Diálogo: configuración del consolidado de docentes (PDF / HTML / XLS). --}}
    <div x-data="{ open: false, format: 'html', orientation: 'portrait', perPage: 2 }"
        x-on:open-teachers-pdf.window="open = true"
        x-on:keydown.escape.window="open = false"
        x-cloak x-show="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 p-4"
        role="dialog" aria-modal="true" aria-label="Consolidado de docentes">
        <div x-on:click.stop
            class="w-full max-w-md overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-white/10 dark:bg-gray-900">
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-white/10">
                <div>
                    <h3 class="text-xs font-extrabold uppercase tracking-widest text-gray-700 dark:text-gray-200">
                        Consolidado de docentes
                    </h3>
                    <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                        Elige el formato de salida · PDF, HTML o XLS
                    </p>
                </div>
                <button type="button" x-on:click="open = false"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-full text-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 dark:hover:bg-white/10 dark:hover:text-white"
                    aria-label="Cerrar">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="space-y-4 p-4">
                <div>
                    <label for="teachers-pdf-format"
                        class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                        Formato
                    </label>
                    <select id="teachers-pdf-format" x-model="format"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 dark:border-white/10 dark:bg-gray-800 dark:text-gray-100">
                        <option value="pdf">PDF</option>
                        <option value="html">HTML</option>
                        <option value="xls">XLS</option>
                    </select>
                </div>

                <div x-cloak x-show="format !== 'xls'">
                    <label for="teachers-pdf-orientation"
                        class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                        Orientación
                    </label>
                    <select id="teachers-pdf-orientation" x-model="orientation"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 dark:border-white/10 dark:bg-gray-800 dark:text-gray-100">
                        <option value="portrait">Vertical</option>
                        <option value="landscape">Horizontal</option>
                    </select>
                </div>

                <div x-cloak x-show="format !== 'xls'">
                    <label for="teachers-pdf-per-page"
                        class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                        Horarios por página
                    </label>
                    <select id="teachers-pdf-per-page" x-model.number="perPage"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 dark:border-white/10 dark:bg-gray-800 dark:text-gray-100">
                        @foreach (range(1, 6) as $n)
                            <option value="{{ $n }}">{{ $n }}</option>
                        @endforeach
                    </select>
                </div>

                <p x-cloak x-show="format === 'xls'"
                    class="rounded-lg bg-amber-500/10 px-3 py-2 text-[11px] font-medium text-amber-700 dark:text-amber-300">
                    El XLS conserva el mismo patrón visual del PDF, con una hoja por cada docente.
                </p>
                <p x-cloak x-show="format === 'pdf'"
                    class="rounded-lg bg-sky-500/10 px-3 py-2 text-[11px] font-medium text-sky-700 dark:text-sky-300">
                    El PDF abre la vista imprimible y lanza el diálogo de impresión del navegador (guardar como PDF).
                </p>
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-200 px-4 py-3 dark:border-white/10">
                <button type="button" x-on:click="open = false"
                    class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-bold text-gray-600 transition-colors hover:bg-gray-100 dark:border-white/10 dark:text-gray-300 dark:hover:bg-white/5">
                    Cancelar
                </button>
                <a :href="'{{ route($moduleRoutePrefix.'.timetable.pdf.all-teachers') }}?format=' + format + '&orientation=' + orientation + '&per_page=' + perPage"
                    target="_blank" rel="noopener" x-on:click="open = false"
                    class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white transition-colors hover:bg-emerald-700">
                    Generar
                </a>
            </div>
        </div>
    </div>

    {{-- Diálogo: totalización de bloques por docente (PDF / HTML / XLS). --}}
    <div x-data="{ open: false, format: 'pdf' }"
        x-on:open-teacher-totals.window="open = true"
        x-on:keydown.escape.window="open = false"
        x-cloak x-show="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 p-4"
        role="dialog" aria-modal="true" aria-label="Totalización por docente">
        <div x-on:click.stop
            class="w-full max-w-md overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-white/10 dark:bg-gray-900">
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-white/10">
                <div>
                    <h3 class="text-xs font-extrabold uppercase tracking-widest text-gray-700 dark:text-gray-200">
                        Totalización por docente
                    </h3>
                    <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                        Elige el formato de salida · PDF, HTML o XLS
                    </p>
                </div>
                <button type="button" x-on:click="open = false"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-full text-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 dark:hover:bg-white/10 dark:hover:text-white"
                    aria-label="Cerrar">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="space-y-4 p-4">
                <div>
                    <label for="teacher-totals-format-light"
                        class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                        Formato
                    </label>
                    <select id="teacher-totals-format-light" x-model="format"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 dark:border-white/10 dark:bg-gray-800 dark:text-gray-100">
                        <option value="pdf">PDF</option>
                        <option value="html">HTML</option>
                        <option value="xls">XLS</option>
                    </select>
                </div>

                <p x-cloak x-show="format === 'xls'"
                    class="rounded-lg bg-amber-500/10 px-3 py-2 text-[11px] font-medium text-amber-700 dark:text-amber-300">
                    El XLS agrega las columnas «Horas Administrativas» y «Horas Formación» vacías para llenado manual.
                </p>
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-200 px-4 py-3 dark:border-white/10">
                <button type="button" x-on:click="open = false"
                    class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-bold text-gray-600 transition-colors hover:bg-gray-100 dark:border-white/10 dark:text-gray-300 dark:hover:bg-white/5">
                    Cancelar
                </button>
                <a :href="'{{ route($moduleRoutePrefix.'.timetable.pdf.teacher-block-totals') }}?format=' + format"
                    target="_blank" rel="noopener" x-on:click="open = false"
                    class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white transition-colors hover:bg-emerald-700">
                    Generar
                </a>
            </div>
        </div>
    </div>

    {{-- Diálogo WireUI: exportación de los horarios de todos los P.Estudios. --}}
    <x-dialog id="pestudios-export" title="Exportar P.Estudios" width="lg" blur="lg" z-index="z-[2000]">
        <div x-data="{ format: 'pdf' }" class="space-y-4 text-left">
            <div>
                <label for="pestudios-export-format"
                    class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                    Formato
                </label>
                <select id="pestudios-export-format" x-model="format"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 dark:border-white/10 dark:bg-gray-800 dark:text-gray-100">
                    <option value="pdf">PDF</option>
                    <option value="html">HTML</option>
                    <option value="xls">XLS (Excel)</option>
                </select>
            </div>

            <p x-cloak x-show="format === 'xls'"
                class="rounded-lg bg-amber-500/10 px-3 py-2 text-[11px] font-medium text-amber-700 dark:text-amber-300">
                El XLS conserva la misma estructura y paleta de colores del PDF (teal, azul y ámbar), con una hoja por
                cada P.Estudio del lapso.
            </p>
            <p x-cloak x-show="format === 'html'"
                class="rounded-lg bg-sky-500/10 px-3 py-2 text-[11px] font-medium text-sky-700 dark:text-sky-300">
                El HTML abre la misma vista del PDF directamente en el navegador, apta para imprimir.
            </p>

            <div class="flex items-center justify-end gap-2 border-t border-gray-200 pt-3 dark:border-white/10">
                <button type="button" x-on:click="close()"
                    class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-bold text-gray-600 transition-colors hover:bg-gray-100 dark:border-white/10 dark:text-gray-300 dark:hover:bg-white/5">
                    Cancelar
                </button>
                <a :href="'{{ route($moduleRoutePrefix.'.timetable.pdf.all-pestudios') }}?format=' + format"
                    target="_blank" rel="noopener" x-on:click="close()"
                    class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white transition-colors hover:bg-emerald-700">
                    Generar
                </a>
            </div>
        </div>
    </x-dialog>

    @if ($lightStep === 1)
        {{-- ── Paso 1: seleccionar calendario ───────────────────────────── --}}
        @if ($lightCalendars === [])
            <div class="rounded-lg border border-dashed border-gray-300 px-6 py-12 text-center text-sm text-gray-400 dark:border-white/10">
                No hay calendarios de horario disponibles.
            </div>
        @else
            @php
                $calendarGroups = [
                    [
                        'label' => 'Activos',
                        'description' => 'Calendarios en uso o editables.',
                        'calendars' => array_values(array_filter(
                            $lightCalendars,
                            fn (array $calendar): bool => $calendar['status'] !== 'archived',
                        )),
                    ],
                    [
                        'label' => 'Archivados',
                        'description' => 'Calendarios retirados (solo consulta).',
                        'calendars' => array_values(array_filter(
                            $lightCalendars,
                            fn (array $calendar): bool => $calendar['status'] === 'archived',
                        )),
                    ],
                ];
                $calendarGroupRendered = false;
            @endphp

            @foreach ($calendarGroups as $group)
                @if ($group['calendars'] !== [])
                    <section class="{{ $calendarGroupRendered ? 'mt-8 border-t-2 border-gray-300 pt-6 dark:border-white/20' : '' }}">
                        <div class="mb-2 flex flex-wrap items-center gap-2">
                            <h2 class="text-[11px] font-extrabold uppercase tracking-widest {{ $group['label'] === 'Archivados' ? 'text-gray-400 dark:text-gray-500' : 'text-emerald-700 dark:text-emerald-300' }}">
                                {{ $group['label'] }}
                            </h2>
                            <span class="rounded-full bg-gray-500/10 px-2 py-0.5 text-[10px] font-bold text-gray-500 dark:text-gray-400">{{ count($group['calendars']) }}</span>
                            <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $group['description'] }}</span>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($group['calendars'] as $calendar)
                                @php
                                    $statusLabel = match ($calendar['status']) {
                                        'active' => 'Activo',
                                        'draft' => 'Borrador',
                                        'generating' => 'Generando',
                                        'archived' => 'Archivado',
                                        default => ucfirst($calendar['status']),
                                    };
                                    $statusClass = match ($calendar['status']) {
                                        'active' => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300',
                                        'draft' => 'bg-sky-500/10 text-sky-700 dark:text-sky-300',
                                        'generating' => 'bg-amber-500/10 text-amber-700 dark:text-amber-300',
                                        default => 'bg-gray-500/10 text-gray-600 dark:text-gray-300',
                                    };
                                @endphp
                                <button type="button" wire:click="chooseCalendar({{ $calendar['id'] }})"
                                    wire:loading.attr="disabled" wire:target="chooseCalendar"
                                    class="group flex flex-col gap-2 rounded-xl border border-gray-200 bg-white p-4 text-left transition-all hover:border-emerald-500/50 hover:bg-emerald-50/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 disabled:cursor-not-allowed disabled:opacity-60 dark:border-white/10 dark:bg-white/[0.03] dark:hover:bg-emerald-500/5">
                                    <div class="flex items-start justify-between gap-2">
                                        <span class="min-w-0 break-words text-sm font-extrabold leading-snug text-gray-900 dark:text-white">{{ $calendar['name'] }}</span>
                                        <span class="shrink-0 rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                        {{ $calendar['pestudio'] ?: 'Sin P.Estudio' }}
                                        @if ($calendar['lapso']) · {{ $calendar['lapso'] }} @endif
                                    </div>
                                    <div class="mt-1 flex flex-wrap items-center gap-2 text-[10px] font-bold uppercase tracking-wider">
                                        @if ($calendar['sync']['in_sync'] && (int) $calendar['sync']['active'] === 0)
                                            <span class="inline-flex items-center gap-1 rounded-md bg-gray-500/10 px-2 py-0.5 text-gray-500 dark:text-gray-400"
                                                title="Este calendario no tiene carga académica vigente ni clases huérfanas.">
                                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                                Sin carga vigente
                                            </span>
                                        @elseif ($calendar['sync']['in_sync'])
                                            <span class="inline-flex items-center gap-1 rounded-md bg-emerald-500/10 px-2 py-0.5 text-emerald-700 dark:text-emerald-300"
                                                title="Todas las Pevaluaciones vigentes tienen clase y no hay clases huérfanas.">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                En sincronía
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-md bg-amber-500/10 px-2 py-0.5 text-amber-700 dark:text-amber-300"
                                                title="{{ (int) $calendar['sync']['missing'] }} asignatura(s) pendiente(s) y {{ (int) $calendar['sync']['orphaned'] }} clase(s) huérfana(s).">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                Sin sincronizar
                                                @if ((int) $calendar['sync']['missing'] > 0)
                                                    <span>· {{ (int) $calendar['sync']['missing'] }} pend.</span>
                                                @endif
                                                @if ((int) $calendar['sync']['orphaned'] > 0)
                                                    <span>· {{ (int) $calendar['sync']['orphaned'] }} huérf.</span>
                                                @endif
                                            </span>
                                        @endif
                                        <span class="text-gray-400 dark:text-gray-500">{{ $calendar['lessons'] }} lesson(s)</span>
                                        <span class="text-gray-400 dark:text-gray-500">{{ $calendar['slots'] }} slot(s)</span>
                                    </div>
                                    <span class="mt-1 inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 opacity-0 transition-opacity group-hover:opacity-100 dark:text-emerald-300">
                                        Abrir grilla
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </section>
                    @php $calendarGroupRendered = true; @endphp
                @endif
            @endforeach
        @endif
    @else
        @php
            $sectionLockedGrid = is_numeric($activeSeccionId) && (int) $activeSeccionId > 0 ? $activeSectionLocked : false;
        @endphp

        {{-- Encabezado común: selección Grado · Sección --}}
        <div class="mb-4 grid grid-cols-1 items-end gap-2 sm:grid-cols-2">
            @if (! empty($tabGradoOptions))
                <label class="flex flex-col gap-1">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Grado</span>
                    <select wire:change="selectStep5Grade($event.target.value)"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none focus:border-emerald-500 focus:ring-emerald-500/50 dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                        @foreach ($tabGradoOptions as $opt)
                            <option value="{{ $opt['id'] }}" @selected((string) $activeGradoId === (string) $opt['id'])>{{ $opt['name'] }}</option>
                        @endforeach
                    </select>
                </label>
            @endif

            @if (! empty($tabSeccionOptions))
                <div class="flex flex-col gap-1">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Sección</span>
                    <div class="flex items-center gap-2">
                        <select wire:change="selectStep5Section($event.target.value)"
                            class="min-w-0 flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none focus:border-emerald-500 focus:ring-emerald-500/50 dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                            @foreach ($tabSeccionOptions as $opt)
                                <option value="{{ $opt['id'] }}" @selected((string) $activeSeccionId === (string) $opt['id'])>
                                    Sección {{ $opt['name'] }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Bloquear / desbloquear la sección (junto al selector) --}}
                        @if (is_numeric($activeSeccionId) && (int) $activeSeccionId > 0)
                            <button type="button"
                                wire:key="light-section-lock-{{ (int) $activeSeccionId }}-{{ $sectionLockedGrid ? 'l' : 'u' }}"
                                wire:click="toggleSectionTimetableLock({{ (int) $activeSeccionId }})"
                                wire:loading.attr="disabled"
                                wire:target="toggleSectionTimetableLock"
                                title="{{ $sectionLockedGrid ? 'Desbloquear el horario de esta sección' : 'Bloquear el horario de esta sección' }}"
                                aria-label="{{ $sectionLockedGrid ? 'Desbloquear' : 'Bloquear' }} el horario de la sección"
                                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg transition-colors disabled:cursor-not-allowed disabled:opacity-50 {{ $sectionLockedGrid ? 'bg-red-500/10 text-red-600 hover:bg-red-500/20 dark:text-red-300' : 'bg-amber-500/10 text-amber-600 hover:bg-amber-500/20 dark:text-amber-300' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    @if ($sectionLockedGrid)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                                    @endif
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Pestañas del modo rápido: grilla (Paso 5) ↔ clases (Paso 3). --}}
        <nav class="mb-5 flex w-full overflow-x-auto" role="tablist" aria-label="Vistas del modo rápido">
            <button type="button"
                id="light-tab-grid"
                role="tab"
                aria-selected="{{ $lightTab !== 'classes' ? 'true' : 'false' }}"
                aria-controls="light-panel-grid"
                wire:click="setLightTab('grid')"
                wire:loading.attr="disabled"
                wire:target="setLightTab"
                class="flex-1 text-center px-3 py-2 text-[11px] font-bold uppercase tracking-widest whitespace-nowrap border-b-2 transition-all duration-200 {{ $lightTab !== 'classes' ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500' : 'text-gray-400 dark:text-gray-500 border-transparent hover:text-gray-600 dark:hover:text-gray-300' }}">
                Grilla
            </button>
            <button type="button"
                id="light-tab-classes"
                role="tab"
                aria-selected="{{ $lightTab === 'classes' ? 'true' : 'false' }}"
                aria-controls="light-panel-classes"
                wire:click="setLightTab('classes')"
                wire:loading.attr="disabled"
                wire:target="setLightTab"
                class="flex-1 text-center px-3 py-2 text-[11px] font-bold uppercase tracking-widest whitespace-nowrap border-b-2 transition-all duration-200 {{ $lightTab === 'classes' ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500' : 'text-gray-400 dark:text-gray-500 border-transparent hover:text-gray-600 dark:hover:text-gray-300' }}">
                Clases
            </button>
        </nav>

        @if ($lightTab === 'classes')
            <div id="light-panel-classes" role="tabpanel" aria-labelledby="light-tab-classes" class="space-y-4">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-extrabold text-gray-900 dark:text-white">Gestión de clases</h2>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Configura bloques T/P, prioridad, turno, aula y agrupación de la selección actual.
                            Al guardar, volverás a la grilla.
                        </p>
                    </div>
                    <span class="rounded-lg border border-emerald-500/25 bg-emerald-500/5 px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest text-emerald-700 dark:text-emerald-300">
                        {{ $step3SelectedCount }} seleccionadas
                    </span>
                </div>

                @include('livewire.coordinacion.timetable.partials.step3-lessons-summary', ['summaryMode' => 'persisted'])

                @include('livewire.coordinacion.timetable.partials.step3-lessons-table')

                <div class="flex items-center justify-end">
                    <button wire:click="saveLessons"
                        @disabled($step3SelectedCount === 0 && ! $lessonsDirty)
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        wire:target="saveLessons"
                        class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">
                        <span wire:loading.remove wire:target="saveLessons">Guardar clases</span>
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
        @else
            <div id="light-panel-grid" role="tabpanel" aria-labelledby="light-tab-grid">
        {{-- ── Paso 2: grilla (agregar / intercambiar + colisiones) ─────── --}}
        @php
            $secGrid = $sectionPreviewGrid ?? [];
            $highlightProfesorId = $this->highlightProfesorId;
            $dayLabels = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes'];

            $lightCollisions = [];
            $lightCollisionSources = [];
            foreach ($secGrid as $orders) {
                foreach ($orders as $order => $days) {
                    foreach ($days as $day => $cellItems) {
                        foreach ($cellItems as $item) {
                            if (empty($item['collision'])) {
                                continue;
                            }

                            $lightCollisions[] = [
                                'lesson_id' => (int) $item['lesson_id'],
                                'period_id' => (int) $item['period_id'],
                                'asignatura' => (string) $item['asignatura'],
                                'profesor' => (string) ($item['profesor'] ?? ''),
                                'profesor_id' => (int) ($item['profesor_id'] ?? 0),
                                'day' => (int) $day,
                                'order' => (int) $order,
                                'sources' => $item['collision_pestudios'] ?? [],
                            ];

                            foreach ($item['collision_pestudios'] as $source) {
                                $lightCollisionSources[$source] = true;
                            }
                        }
                    }
                }
            }
        @endphp

        {{-- PDF de la sección (usa los slots persistidos) --}}
        @if (is_numeric($activeSeccionId) && (int) $activeSeccionId > 0)
            <div class="mb-4 flex justify-end">
                <a href="{{ route($moduleRoutePrefix.'.timetable.pdf.preview', ['calendar' => $calendarId, 'seccion' => (int) $activeSeccionId, 'source' => 'persisted']) }}"
                    target="_blank" rel="noopener"
                    title="Generar el PDF del horario de esta sección"
                    aria-label="Generar PDF de la sección"
                    class="inline-flex items-center gap-2 rounded-lg bg-violet-500/10 px-3 py-1.5 text-xs font-bold text-violet-700 transition-colors hover:bg-violet-500/20 dark:text-violet-300">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Generar PDF
                </a>
            </div>
        @endif

        {{-- Alerta de colisiones (no bloqueante) --}}
        @if ($lightCollisions !== [])
            <div class="mb-4 flex flex-wrap items-center gap-2 rounded-lg border border-amber-500/30 bg-amber-500/[0.06] px-4 py-2.5 text-xs text-amber-800 dark:text-amber-200">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
                <span class="font-bold">{{ count($lightCollisions) }} colisión(es) de docente (no bloqueante).</span>
                <span>Usa el panel «Colisiones» para saltar a cada celda o resaltar al docente.</span>
            </div>
        @endif

        {{-- Grilla + panel de colisiones --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start">
            <div class="min-w-0 flex-1">
        @if ($periodsList->isNotEmpty())
            <div class="space-y-3">
                @foreach ($periodsList->groupBy('shift_id') as $shiftId => $shiftPeriods)
                    @php $shift = $shifts->firstWhere('id', $shiftId); @endphp
                    <div class="rounded-lg border border-gray-200 p-1.5 dark:border-white/10 {{ $sectionLockedGrid ? 'pointer-events-none opacity-50 select-none' : '' }}">
                        <div class="-mx-1.5 -mt-1.5 mb-1.5 flex items-center justify-between gap-2 rounded-t-lg border-b border-emerald-500/15 bg-emerald-500/5 px-2 py-1.5 dark:border-emerald-400/10 dark:bg-emerald-400/5">
                            <span class="text-[11px] font-extrabold uppercase tracking-wide text-emerald-800 dark:text-emerald-200">
                                {{ $shift?->name ?? ('Turno '.$shiftId) }}
                            </span>
                            @if ($shift?->start_time)
                                <span class="font-mono text-[10px] font-bold text-emerald-700 dark:text-emerald-300">
                                    {{ substr((string) $shift->start_time, 0, 5) }}–{{ substr((string) $shift->end_time, 0, 5) }}
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-6 gap-px">
                            <div class="text-[10px] font-bold text-gray-400">Hora</div>
                            @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dayLabel)
                                <div class="text-center text-[10px] font-bold text-gray-400">{{ $dayLabel }}</div>
                            @endforeach

                            @foreach ($shiftPeriods->groupBy('order_in_day') as $order => $group)
                                @php $rowPeriod = $group->first(); @endphp
                                <div class="contents">
                                    @php $isBreakRow = (bool) $rowPeriod?->is_break; @endphp
                                    <div class="flex items-center rounded px-1 text-[10px] font-bold {{ $isBreakRow ? 'bg-amber-500/5 text-amber-700/80 dark:text-amber-300/80' : 'text-gray-400' }}">
                                        {{ substr((string) $rowPeriod?->start_time, 0, 5) }}–{{ substr((string) $rowPeriod?->end_time, 0, 5) }}
                                    </div>

                                    @for ($day = 1; $day <= 5; $day++)
                                        @php
                                            $cellAssignments = $secGrid[$shiftId][$order][$day] ?? [];
                                            $targetPeriod = $group->firstWhere('day_of_week', $day);
                                            $isBreak = (bool) ($targetPeriod?->is_break ?? $rowPeriod?->is_break);
                                        @endphp
                                        <div
                                            wire:key="light-cell-{{ $shiftId }}-{{ $order }}-{{ $day }}"
                                            @if (! $isBreak && $targetPeriod)
                                                x-data="{ dropOver: false }"
                                                x-on:dragover.prevent="dropOver = true"
                                                x-on:dragleave="dropOver = false"
                                                x-on:drop.prevent="dropOver = false; $wire.movePreviewLesson(parseInt($event.dataTransfer.getData('lesson-id')), parseInt($event.dataTransfer.getData('period-id')), {{ (int) $targetPeriod->id }})"
                                                :class="dropOver ? 'ring-2 ring-emerald-500/70 bg-emerald-500/15' : ''"
                                            @endif
                                            class="relative flex min-h-[32px] flex-col items-stretch justify-center gap-px rounded p-px {{ $isBreak ? 'bg-amber-500/5 text-amber-700/70 dark:text-amber-300/70' : ($cellAssignments ? 'border border-emerald-500/10 bg-emerald-500/5' : 'border border-transparent') }} {{ ! $isBreak ? 'transition-colors hover:bg-emerald-500/10' : '' }}">

                                            @if ($cellAssignments)
                                                @php
                                                    $fullAssignments = collect($cellAssignments)->filter(fn ($c) => ($c['half'] ?? null) === null)->values();
                                                    $supAssignments = collect($cellAssignments)->filter(fn ($c) => (int) ($c['half'] ?? -1) === 0)->values();
                                                    $infAssignments = collect($cellAssignments)->filter(fn ($c) => (int) ($c['half'] ?? -1) === 1)->values();
                                                    $hasHalves = $supAssignments->isNotEmpty() || $infAssignments->isNotEmpty();
                                                @endphp

                                                @foreach ($fullAssignments as $slotItem)
                                                    @include('livewire.planning.timetable.partials.light-slot', ['cell' => $slotItem, 'targetPeriod' => $targetPeriod, 'highlightProfesorId' => $highlightProfesorId])
                                                @endforeach
                                                @if ($hasHalves)
                                                    <div class="flex flex-col gap-px" x-data="{ dropSup: false, dropInf: false }">
                                                        <div
                                                            x-on:dragover.prevent="dropSup = true"
                                                            x-on:dragleave="dropSup = false"
                                                            x-on:drop.prevent.stop="dropSup = false; $wire.movePreviewLesson(parseInt($event.dataTransfer.getData('lesson-id')), parseInt($event.dataTransfer.getData('period-id')), {{ (int) $targetPeriod?->id }}, 0)"
                                                            :class="dropSup ? 'ring-1 ring-sky-500/80 bg-sky-500/15' : ''"
                                                            class="rounded border-b border-dashed border-sky-500/40 pt-px">
                                                            @forelse ($supAssignments as $slotItem)
                                                                @include('livewire.planning.timetable.partials.light-slot', ['cell' => $slotItem, 'targetPeriod' => $targetPeriod, 'highlightProfesorId' => $highlightProfesorId])
                                                            @empty
                                                                <span class="flex items-center justify-center text-[7px] font-bold uppercase tracking-wide text-sky-600/70 dark:text-sky-300/70">½ sup.</span>
                                                            @endforelse
                                                        </div>
                                                        <div
                                                            x-on:dragover.prevent="dropInf = true"
                                                            x-on:dragleave="dropInf = false"
                                                            x-on:drop.prevent.stop="dropInf = false; $wire.movePreviewLesson(parseInt($event.dataTransfer.getData('lesson-id')), parseInt($event.dataTransfer.getData('period-id')), {{ (int) $targetPeriod?->id }}, 1)"
                                                            :class="dropInf ? 'ring-1 ring-sky-500/80 bg-sky-500/15' : ''"
                                                            class="rounded">
                                                            @forelse ($infAssignments as $slotItem)
                                                                @include('livewire.planning.timetable.partials.light-slot', ['cell' => $slotItem, 'targetPeriod' => $targetPeriod, 'highlightProfesorId' => $highlightProfesorId])
                                                            @empty
                                                                <span class="flex items-center justify-center text-[7px] font-bold uppercase tracking-wide text-sky-600/70 dark:text-sky-300/70">½ inf.</span>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                @endif
                                            @elseif ($isBreak)
                                                <div class="w-full text-center text-[9px] font-semibold uppercase tracking-wide opacity-75">Receso</div>
                                            @else
                                                <button type="button"
                                                    wire:click="openAddPreviewLessonModal({{ (int) $targetPeriod?->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="openAddPreviewLessonModal"
                                                    @disabled(! $targetPeriod)
                                                    title="Agregar una lección a este período"
                                                    aria-label="Agregar una lección al período {{ $targetPeriod?->period_label }}"
                                                    class="absolute inset-0 z-10 flex w-full items-center justify-center gap-1 rounded text-[9px] font-semibold text-gray-400 transition-colors hover:bg-emerald-500/10 hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400 disabled:cursor-not-allowed disabled:opacity-40 dark:text-gray-500 dark:hover:text-emerald-300">
                                                    <span aria-hidden="true" class="text-sm font-bold leading-none">+</span>
                                                    <span>Agregar</span>
                                                </button>
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
            <div class="rounded-lg border border-dashed border-gray-300 px-6 py-12 text-center text-sm text-gray-400 dark:border-white/10">
                Este calendario no tiene períodos configurados.
            </div>
        @endif
            </div>

            {{-- Panel de colisiones accionable --}}
            <aside class="w-full lg:sticky lg:top-4 lg:w-80 lg:shrink-0">
                <div class="rounded-xl border border-gray-200 bg-white p-3 dark:border-white/10 dark:bg-white/[0.03]">
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <h2 class="text-[11px] font-extrabold uppercase tracking-widest {{ $lightCollisions !== [] ? 'text-amber-700 dark:text-amber-300' : 'text-emerald-700 dark:text-emerald-300' }}">
                            Colisiones ({{ count($lightCollisions) }})
                        </h2>
                        @if ($highlightProfesorId)
                            <button type="button" wire:click="clearHighlightProfesor"
                                class="text-[10px] font-bold text-fuchsia-700 hover:underline dark:text-fuchsia-300">
                                Quitar resaltado
                            </button>
                        @endif
                    </div>

                    @if ($highlightProfesorId)
                        <div class="mb-2 rounded-md bg-fuchsia-500/10 px-2 py-1.5 text-[10px] font-bold text-fuchsia-700 dark:text-fuchsia-300">
                            Resaltando al docente #{{ $highlightProfesorId }} en la grilla.
                        </div>
                    @endif

                    @if ($lightCollisions === [])
                        <p class="rounded-md border border-dashed border-gray-300 px-3 py-4 text-center text-[11px] text-gray-400 dark:border-white/10">
                            Sin colisiones de docente.
                        </p>
                    @else
                        <ul class="max-h-[70vh] space-y-2 overflow-y-auto pr-1">
                            @foreach ($lightCollisions as $collision)
                                @php $collisionHighlighted = $highlightProfesorId && (int) $collision['profesor_id'] === (int) $highlightProfesorId; @endphp
                                <li wire:key="light-collision-{{ $collision['lesson_id'] }}-{{ $collision['period_id'] }}"
                                    class="rounded-md border px-2.5 py-2 {{ $collisionHighlighted ? 'border-fuchsia-500/40 bg-fuchsia-500/[0.06]' : 'border-amber-500/25 bg-amber-500/[0.04]' }}">
                                    <div class="text-[11px] font-bold text-gray-800 dark:text-gray-100">{{ $collision['asignatura'] }}</div>
                                    <div class="mt-0.5 text-[10px] text-gray-500 dark:text-gray-400">
                                        {{ $collision['profesor'] !== '' ? $collision['profesor'] : ('Docente #'.$collision['profesor_id']) }}
                                        · {{ $dayLabels[$collision['day']] ?? ('Día '.$collision['day']) }}
                                        · bloque {{ $collision['order'] }}
                                    </div>
                                    <div class="mt-1 text-[10px] text-amber-700 dark:text-amber-300">
                                        {{ implode(' · ', $collision['sources']) }}
                                    </div>
                                    <div class="mt-1.5 flex flex-wrap items-center gap-2">
                                        <button type="button"
                                            x-on:click="const el = document.getElementById('light-slot-{{ $collision['lesson_id'] }}-{{ $collision['period_id'] }}'); if (el) { el.scrollIntoView({ behavior: 'smooth', block: 'center' }); el.classList.add('ring-2','ring-amber-500'); setTimeout(() => el.classList.remove('ring-2','ring-amber-500'), 1600); }"
                                            class="rounded-md bg-amber-500/15 px-2 py-1 text-[10px] font-bold text-amber-700 transition-colors hover:bg-amber-500/25 dark:text-amber-300">
                                            Ir a la celda
                                        </button>
                                        @if ($collision['profesor_id'] > 0)
                                            <button type="button" wire:click="toggleHighlightProfesor({{ $collision['profesor_id'] }})"
                                                class="rounded-md bg-fuchsia-500/10 px-2 py-1 text-[10px] font-bold text-fuchsia-700 transition-colors hover:bg-fuchsia-500/20 dark:text-fuchsia-300">
                                                {{ $collisionHighlighted ? 'Quitar resaltado' : 'Resaltar docente' }}
                                            </button>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </aside>
        </div>

        {{-- Modal agregar lección --}}
        @if ($showAddPreviewLessonModal)
            @php
                $lightTargetPeriod = $this->addPreviewPeriod();
                $lightLessonsToAdd = $this->availablePreviewLessons();
            @endphp
            <x-modal-card title="Agregar lección al período" blur="lg" wire:model="showAddPreviewLessonModal" align="center" max-width="md" persistent>
                <div class="space-y-3">
                    <div class="rounded-lg border border-emerald-500/20 bg-emerald-500/5 px-3 py-2.5">
                        <p class="text-xs font-bold text-gray-800 dark:text-gray-200">
                            @if ($lightTargetPeriod)
                                {{ $lightTargetPeriod->shift?->name ?? 'Turno' }} · {{ $lightTargetPeriod->day_label }}
                                · {{ substr((string) $lightTargetPeriod->start_time, 0, 5) }}–{{ substr((string) $lightTargetPeriod->end_time, 0, 5) }}
                            @else
                                Selecciona una lección para este período
                            @endif
                        </p>
                        <p class="mt-0.5 text-[11px] text-gray-500 dark:text-gray-400">
                            {{ $lightLessonsToAdd->count() }} opción(es) · primero las del mismo turno.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        <select wire:model.live="addPreviewLessonSource"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none focus:border-emerald-500 focus:ring-emerald-500/50 dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                            <option value="missing">Asignaturas faltantes</option>
                            <option value="current">Listado actual</option>
                            <option value="grade">Todas las de la sección</option>
                        </select>
                        <select wire:model.live="addPreviewLessonType"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none focus:border-emerald-500 focus:ring-emerald-500/50 dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                            <option value="theory">Hora teórica</option>
                            <option value="practice">Hora práctica</option>
                        </select>
                    </div>

                    <input type="text" wire:model.live.debounce.250ms="addPreviewLessonSearch"
                        placeholder="Buscar asignatura, docente o sección…"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 outline-none focus:border-emerald-500 focus:ring-emerald-500/50 dark:border-white/10 dark:bg-white/5 dark:text-gray-200">

                    <div class="max-h-[min(52vh,26rem)] space-y-2 overflow-y-auto pr-1">
                        @forelse ($lightLessonsToAdd as $availableLesson)
                            @php
                                $shiftMismatch = $lightTargetPeriod && (int) $availableLesson->shift_id !== (int) $lightTargetPeriod->shift_id;
                                $teacherName = trim(($availableLesson->pevaluacion?->profesor?->lastname ?? '').' '.($availableLesson->pevaluacion?->profesor?->name ?? ''));
                                $isGradeCandidate = ! empty($availableLesson->is_grade_candidate);
                            @endphp
                            <button type="button"
                                wire:click="{{ $isGradeCandidate ? 'addPreviewPevaluacion('.(int) $availableLesson->pevaluacion_id.')' : 'addPreviewLesson('.(int) $availableLesson->id.')' }}"
                                wire:loading.attr="disabled" wire:target="addPreviewLesson,addPreviewPevaluacion"
                                class="flex w-full items-center gap-3 rounded-lg border px-3 py-2.5 text-left transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500/50 disabled:opacity-50
                                    {{ $shiftMismatch
                                        ? 'border-amber-500/30 bg-amber-500/5 hover:border-amber-500/60 hover:bg-amber-500/10'
                                        : 'border-gray-200 bg-white hover:border-emerald-500/60 hover:bg-emerald-50 dark:border-white/10 dark:bg-white/5 dark:hover:bg-emerald-500/10' }}">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md text-sm font-bold {{ $shiftMismatch ? 'bg-amber-500/15 text-amber-700 dark:text-amber-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' }}">+</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-bold text-gray-900 dark:text-gray-200">
                                        {{ $availableLesson->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura sin nombre' }}
                                    </span>
                                    <span class="mt-0.5 block truncate text-[11px] text-gray-500 dark:text-gray-400">
                                        Sección {{ $availableLesson->pevaluacion?->seccion?->name ?? '—' }}
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
                                No hay lecciones disponibles para agregar.
                            </div>
                        @endforelse
                    </div>
                </div>

                <x-slot name="footer">
                    <button type="button" wire:click="closeAddPreviewLessonModal"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm font-bold text-gray-600 transition-colors hover:bg-gray-100 dark:border-white/10 dark:text-gray-300 dark:hover:bg-white/5">
                        Cerrar
                    </button>
                </x-slot>
            </x-modal-card>
        @endif

        {{-- Horario por profesor (toda la carga del docente entre P.Estudios) --}}
        @if ($showTeacherScheduleDialog)
            <div class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-950/70 p-4"
                role="dialog" aria-modal="true" aria-labelledby="light-teacher-schedule-title"
                x-data x-on:keydown.escape.window="$wire.closeTeacherScheduleDialog()">
                <div x-on:click.stop class="flex max-h-[92vh] w-full max-w-7xl flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-white/10 dark:bg-gray-900">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-4 dark:border-white/10">
                        <div>
                            <h2 id="light-teacher-schedule-title" class="text-sm font-extrabold text-gray-900 dark:text-white">Horario por profesor</h2>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Toda la carga horaria asignada al profesor en las secciones de todos los calendarios/P.Estudios del lapso.</p>
                        </div>
                        <button type="button" wire:click="closeTeacherScheduleDialog"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-full text-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50 dark:hover:bg-white/10 dark:hover:text-white"
                            aria-label="Cerrar horario por profesor">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <div class="border-b border-gray-200 bg-gray-50 px-5 py-3 dark:border-white/10 dark:bg-white/5">
                        <label for="light-teacher-schedule-profesor" class="mb-1 block text-[10px] font-extrabold uppercase tracking-widest text-gray-500 dark:text-gray-400">Profesor</label>
                        <select id="light-teacher-schedule-profesor" wire:model.live="teacherScheduleProfesorId"
                            class="w-full max-w-xl rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none focus:border-fuchsia-500 focus:ring-2 focus:ring-fuchsia-500/40 dark:border-white/10 dark:bg-gray-900 dark:text-white">
                            @forelse ($teacherScheduleOptions as $teacher)
                                <option value="{{ $teacher['id'] }}">{{ $teacher['name'] }}</option>
                            @empty
                                <option value="">No hay profesores asociados</option>
                            @endforelse
                        </select>
                    </div>

                    @if ($teacherScheduleCalendars !== [])
                        <div class="flex border-b border-gray-200 bg-gray-50 dark:border-white/10 dark:bg-white/5"
                            role="tablist" aria-label="P.Estudios del horario docente">
                            @foreach ($teacherScheduleCalendars as $cal)
                                @php $tabActive = (int) ($teacherScheduleActiveCalendarId ?? 0) === (int) $cal['calendar_id']; @endphp
                                <button type="button" role="tab" aria-selected="{{ $tabActive ? 'true' : 'false' }}"
                                    wire:click="setTeacherScheduleTab({{ $cal['calendar_id'] }})"
                                    title="{{ $cal['calendar'] }}"
                                    class="flex-1 basis-0 border-b-2 px-3 py-2 text-center text-xs font-bold transition-colors {{ $tabActive ? 'border-fuchsia-500 text-fuchsia-700 dark:text-fuchsia-300' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                                    {{ $cal['pestudio'] }}
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <div class="overflow-auto p-5">
                        @if ($teacherScheduleHasAssignments)
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
                                                            <div class="flex items-start justify-between gap-1">
                                                                <div class="font-bold text-gray-900 dark:text-white">{{ $lesson['subject'] }}</div>
                                                                @if (! empty($lesson['pestudio_code']))
                                                                    <span class="shrink-0 rounded bg-fuchsia-500/10 px-1 py-0.5 text-[9px] font-bold uppercase tracking-wide text-fuchsia-700 dark:text-fuchsia-300">{{ $lesson['pestudio_code'] }}</span>
                                                                @endif
                                                            </div>
                                                            <div class="mt-0.5 font-mono text-[10px] text-gray-500 dark:text-gray-400">{{ $lesson['start'] }}–{{ $lesson['end'] }}</div>
                                                            <div class="mt-0.5 text-[10px] text-gray-500 dark:text-gray-400">
                                                                @if (! empty($lesson['grado'])){{ $lesson['grado'] }} · @endif Sección {{ $lesson['section'] }}
                                                            </div>
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
                                El profesor seleccionado no tiene lessons asignadas en este P.Estudio.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
            </div>
        @endif
    @endif

    {{-- State loading global (mismo patrón visual que /app/planning/diagnostico). --}}
    <div id="timetable-light-global-loading" class="hidden fixed bottom-6 right-6 z-[60]">
        <div class="flex items-center gap-3 rounded-full border border-emerald-500/20 bg-gray-900/90 py-3 pl-3 pr-5 text-xs font-bold uppercase tracking-widest text-white shadow-2xl shadow-emerald-500/10 ring-1 ring-white/5 backdrop-blur-xl">
            <span class="relative flex h-7 w-7 items-center justify-center rounded-full border border-emerald-500/20 bg-emerald-500/15">
                <svg class="h-4 w-4 animate-spin text-emerald-400" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="absolute -right-0.5 -top-0.5 h-2 w-2 animate-ping rounded-full bg-emerald-500"></span>
                <span class="absolute -right-0.5 -top-0.5 h-2 w-2 rounded-full bg-emerald-500"></span>
            </span>
            <span>Cargando</span>
            <span class="h-3 w-px bg-white/10"></span>
            <span class="text-[10px] font-normal normal-case tracking-normal text-white/60">Sincronizando…</span>
        </div>
    </div>
    <div id="timetable-light-global-bar" class="pointer-events-none fixed inset-x-0 top-0 z-[61] hidden h-0.5 animate-pulse bg-gradient-to-r from-emerald-500 via-cyan-500 to-emerald-500"></div>
</div>

@script
<script>
    (() => {
        // El hook es global: se registra una sola vez aunque se navegue con
        // wire:navigate y se vuelva a montar el componente.
        if (window.__timetableLightLoadingHooks) return;
        window.__timetableLightLoadingHooks = true;

        let pending = 0;
        let safety = null;

        const elements = () => ({
            pill: document.getElementById('timetable-light-global-loading'),
            bar: document.getElementById('timetable-light-global-bar'),
        });

        const show = () => {
            const { pill, bar } = elements();
            if (pill) pill.classList.remove('hidden');
            if (bar) bar.classList.remove('hidden');
        };

        const hide = () => {
            const { pill, bar } = elements();
            if (pill) pill.classList.add('hidden');
            if (bar) bar.classList.add('hidden');
        };

        const armSafety = () => {
            clearTimeout(safety);
            safety = setTimeout(() => {
                pending = 0;
                hide();
            }, 15000);
        };

        const release = () => {
            pending = Math.max(0, pending - 1);
            if (pending === 0) {
                clearTimeout(safety);
                hide();
            }
        };

        Livewire.hook('request', ({ fail }) => {
            pending++;
            show();
            armSafety();
            fail(release);
        });

        Livewire.hook('commit', ({ succeed }) => {
            succeed(release);
        });
    })();
</script>
@endscript
