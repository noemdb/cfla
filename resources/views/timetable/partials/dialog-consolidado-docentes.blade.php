{{-- Diálogo compartido (wizard / light): configuración del consolidado de docentes (PDF / HTML / XLS). --}}
{{-- $entangleWizardState: añade los filtros entangled por docentes/área (wizard). --}}
{{-- $withAreaFilter: muestra el bloque del filtro por área + docentes (wizard). --}}
{{-- $withCalendarFilter: añade &calendar=ID al ancla Generar (light). --}}

    <div x-data="{ open: false, format: 'html', orientation: 'portrait', perPage: 2 @if (! empty($entangleWizardState)), profesorIds: $wire.entangle('teachersPdfProfesorIds'), areaId: $wire.entangle('teachersPdfAreaId')@endif }"
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
@if (! empty($withAreaFilter))
                @php
                    $areaOptionsForTeachers = $this->areaFormatOptions();
                    $selectedAreaForTeachers = $teachersPdfAreaId ? $areaOptionsForTeachers->firstWhere('id', (int) $teachersPdfAreaId) : null;
                @endphp
                <div class="relative" x-data="{ open: false, q: '' }"
                     x-on:keydown.escape.window="open = false"
                     @click.outside="open = false">
                    <label class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Área de conocimiento</label>

                    {{-- Trigger --}}
                    <button type="button" disabled
                        class="flex w-full items-center justify-between gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-left outline-none transition-colors dark:border-white/10 dark:bg-white/5 opacity-60 cursor-not-allowed"
                        title="Filtro por área temporalmente deshabilitado">
                        @if ($selectedAreaForTeachers)
                            <span class="min-w-0">
                                <span class="flex items-center gap-1.5">
                                    <span class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $selectedAreaForTeachers->name }}</span>
                                    @if ($selectedAreaForTeachers->code)
                                        <span class="shrink-0 rounded bg-gray-100 px-1.5 py-0.5 text-[9px] font-bold uppercase text-gray-600 dark:bg-white/10 dark:text-gray-300">{{ $selectedAreaForTeachers->code }}</span>
                                    @endif
                                </span>
                                <span class="mt-0.5 block truncate text-[11px] text-gray-500 dark:text-gray-400">
                                    {{ $selectedAreaForTeachers->pestudio?->name ?? 'Sin P.Estudio' }} · {{ $selectedAreaForTeachers->campo_conocimientos_count }} asignatura(s)
                                </span>
                            </span>
                        @else
                            <span class="text-sm text-gray-400">Todas las áreas</span>
                        @endif
                        <svg class="h-4 w-4 shrink-0 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Panel --}}
                    <div x-show="open" x-cloak x-transition.origin.top
                        class="absolute left-0 right-0 z-50 mt-1 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-xl dark:border-white/10 dark:bg-gray-900">
                        <div class="border-b border-gray-100 p-2 dark:border-white/5">
                            <input type="text" x-model="q" placeholder="Buscar área, código o P.Estudio…"
                                class="w-full rounded-md border border-gray-200 bg-white px-2.5 py-1.5 text-xs text-gray-900 placeholder-gray-400 outline-none focus:border-emerald-500 dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @php
                                $haystackAll = mb_strtolower('todas las areas');
                            @endphp
                            <button type="button"
                                wire:click="$set('teachersPdfAreaId', null)"
                                x-on:click="open = false"
                                x-show="q === '' || @js($haystackAll).includes(q.toLowerCase())"
                                class="flex w-full items-center justify-between gap-2 px-3 py-2 text-left transition-colors {{ $selectedAreaForTeachers ? 'hover:bg-gray-50 dark:hover:bg-white/5' : 'bg-emerald-500/10' }}">
                                <span class="min-w-0">
                                    <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100">Todas las áreas</span>
                                    <span class="mt-0.5 block text-[11px] text-gray-500 dark:text-gray-400">Sin filtrar por área — muestra todos los docentes</span>
                                </span>
                                @if (! $selectedAreaForTeachers)
                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600 dark:text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @endif
                            </button>
                            @foreach ($areaOptionsForTeachers as $areaOption)
                                @php
                                    $isSelectedForTeachers = (string) $teachersPdfAreaId === (string) $areaOption->id;
                                    $haystackForTeachers = mb_strtolower(implode(' ', array_filter([
                                        $areaOption->name, $areaOption->code, $areaOption->code_sm,
                                        $areaOption->pestudio?->name, $areaOption->peducativo?->name,
                                    ])));
                                @endphp
                                <button type="button"
                                    wire:key="area-filter-opt-{{ $areaOption->id }}"
                                    wire:click="$set('teachersPdfAreaId', '{{ $areaOption->id }}')"
                                    x-on:click="open = false"
                                    x-show="q === '' || @js($haystackForTeachers).includes(q.toLowerCase())"
                                    class="flex w-full items-start justify-between gap-2 px-3 py-2 text-left transition-colors {{ $isSelectedForTeachers ? 'bg-emerald-500/10' : 'hover:bg-gray-50 dark:hover:bg-white/5' }}">
                                    <span class="min-w-0">
                                        <span class="flex items-center gap-1.5">
                                            <span class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $areaOption->name }}</span>
                                            @if ($areaOption->code)
                                                <span class="shrink-0 rounded bg-gray-100 px-1.5 py-0.5 text-[9px] font-bold uppercase text-gray-600 dark:bg-white/10 dark:text-gray-300">{{ $areaOption->code }}</span>
                                            @endif
                                        </span>
                                        <span class="mt-0.5 block truncate text-[11px] text-gray-500 dark:text-gray-400">
                                            {{ $areaOption->pestudio?->name ?? 'Sin P.Estudio' }}
                                            @if ($areaOption->peducativo?->name) · {{ $areaOption->peducativo->name }} @endif
                                        </span>
                                        <span class="mt-0.5 flex flex-wrap items-center gap-1.5 text-[10px] text-gray-500 dark:text-gray-400">
                                            <span class="inline-flex items-center gap-1 rounded bg-emerald-500/10 px-1.5 py-0.5 font-bold text-emerald-700 dark:text-emerald-300">
                                                {{ $areaOption->campo_conocimientos_count }} asignatura(s)
                                            </span>
                                            @if ($areaOption->leader)
                                                <span class="truncate">Líder: {{ $areaOption->leader->username }}</span>
                                            @endif
                                        </span>
                                    </span>
                                    @if ($isSelectedForTeachers)
                                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600 dark:text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
                @if ($selectedAreaForTeachers)
                    <div class="rounded-lg border border-emerald-500/20 bg-emerald-500/5 px-3 py-2.5 text-[11px] leading-relaxed">
                        <div class="flex flex-wrap items-center gap-1.5 font-bold text-emerald-700 dark:text-emerald-300">
                            <span>{{ $selectedAreaForTeachers->name }}</span>
                            @if ($selectedAreaForTeachers->code)
                                <span class="shrink-0 rounded bg-white px-1.5 py-0.5 text-[9px] font-bold uppercase text-emerald-700 dark:bg-white/10 dark:text-emerald-300">{{ $selectedAreaForTeachers->code }}</span>
                            @endif
                            <span class="font-normal text-emerald-700/70 dark:text-emerald-300/70">· {{ $selectedAreaForTeachers->campo_conocimientos_count }} asignatura(s)</span>
                        </div>
                        <div class="mt-1 text-emerald-700/80 dark:text-emerald-300/80">
                            {{ $selectedAreaForTeachers->pestudio?->name ?? 'Sin P.Estudio' }}
                            @if ($selectedAreaForTeachers->peducativo?->name) · {{ $selectedAreaForTeachers->peducativo->name }} @endif
                            @if ($selectedAreaForTeachers->leader) · Líder: {{ $selectedAreaForTeachers->leader->username }} @endif
                        </div>
                        @if ($selectedAreaForTeachers->description)
                            <div class="mt-1 text-gray-600 dark:text-gray-300">{{ $selectedAreaForTeachers->description }}</div>
                        @endif
                        @if ($selectedAreaForTeachers->observations)
                            <div class="mt-1 text-gray-500 dark:text-gray-400">{{ $selectedAreaForTeachers->observations }}</div>
                        @endif
                    </div>
                @else
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Todas las áreas — se muestran todos los docentes con bloques en los calendarios activos.</p>
                @endif

                <div>
                    <x-select label="Profesor" placeholder="Todos los docentes"
                        wire:model.live="teachersPdfProfesorIds" searchable multiselect>
                        @foreach ($teachersPdfTeachers as $teacher)
                            <x-select.option :label="$teacher['name']" :value="$teacher['id']" :description="$teacher['description']" />
                        @endforeach
                    </x-select>
                    <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                        Vacío = consolida todos los docentes con asignaciones.
                    </p>
                </div>
                @endif

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
                <a :href="'{{ route($moduleRoutePrefix.'.timetable.pdf.all-teachers') }}?format=' + format + '&orientation=' + orientation + '&per_page=' + perPage @if (! empty($entangleWizardState)) + (profesorIds.length ? '&profesor_ids=' + profesorIds.join(',') : '') + (areaId ? '&area_id=' + areaId : '') @endif @if (! empty($withCalendarFilter)) + '&calendar={{ (int) $calendarId }}' @endif"
                    target="_blank" rel="noopener" x-on:click="open = false"
                    class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white transition-colors hover:bg-emerald-700">
                    Generar
                </a>
            </div>
        </div>
    </div>