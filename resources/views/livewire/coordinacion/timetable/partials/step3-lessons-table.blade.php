@php
    // Permite reutilizar la tabla fuera del Paso 3 completo (asistente light).
    $rows = $rows ?? (($step3ViewMode ?? 'tabs') === 'flat'
        ? ($pevaluaciones ?? collect())
        : ($tabActivePevaluaciones ?? collect()));
@endphp
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/10">
                    @php $lockedSectionSet = $lockedSectionIdSet; @endphp
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-gray-50 dark:bg-gray-800/80">
                            <tr class="text-left text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                <th colspan="2" class="px-3 py-2">
                                    <span class="inline-flex items-center gap-2">
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
                                <th class="px-3 py-2 text-nowrap">Doc. compartido</th>
                                <th class="px-3 py-2">Estado</th>
                                <th class="px-3 py-2 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $pev)
                                @php
                                    $selected = (array_key_exists($pev->id, $selectedPevs) && (bool) $selectedPevs[$pev->id])
                                        || in_array($pev->id, array_map('intval', array_values($selectedPevs)), true);
                                    $derivedT = (int) ceil(((int) ($pev->pensum?->asignatura?->hour_t_week ?? 0)) * 60 / max(1, (int) ($periodsList->count() ? $calendarPeriodMinutes ?? 60 : 60)));
                                    $derivedP = (int) ceil(((int) ($pev->pensum?->asignatura?->hour_p_week ?? 0)) * 60 / max(1, (int) ($periodsList->count() ? $calendarPeriodMinutes ?? 60 : 60)));
                                    $rowLocked = isset($lockedSectionSet[(int) $pev->seccion_id]);
                                @endphp
                                <tr wire:key="lesson-row-{{ $pev->id }}" class="border-t border-gray-100 dark:border-white/5 {{ $selected ? 'bg-emerald-500/5' : '' }}">
                                    <td class="px-3 py-2"></td>
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-200 font-medium">{{ $pev->pensum?->asignatura?->name }}{{ $pev->grupoEstable?->name ? ' · '.$pev->grupoEstable->name : '' }}</td>
                                    <td class="px-3 py-2 text-gray-500 dark:text-gray-400">{{ $pev->profesor?->lastname }}, {{ $pev->profesor?->name }}                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number"
                                            wire:key="lesson-blocks-t-{{ $pev->id }}-{{ $selected ? 'selected' : 'unselected' }}"
                                            @if ($selected && ! $rowLocked) wire:model.live="lessons.{{ $pev->id }}.weekly_blocks_t" @endif
                                            value="{{ $selected ? ($lessons[$pev->id]['weekly_blocks_t'] ?? $derivedT) : $derivedT }}"
                                            min="0" @disabled(! $selected || $rowLocked)
                                            class="w-12 text-center bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded px-1 py-1 text-xs disabled:opacity-60 disabled:cursor-not-allowed">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number"
                                            wire:key="lesson-blocks-p-{{ $pev->id }}-{{ $selected ? 'selected' : 'unselected' }}"
                                            @if ($selected && ! $rowLocked) wire:model.live="lessons.{{ $pev->id }}.weekly_blocks_p" @endif
                                            value="{{ $selected ? ($lessons[$pev->id]['weekly_blocks_p'] ?? $derivedP) : $derivedP }}"
                                            min="0" @disabled(! $selected || $rowLocked)
                                            class="w-12 text-center bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded px-1 py-1 text-xs disabled:opacity-60 disabled:cursor-not-allowed">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number"
                                            wire:key="lesson-prio-{{ $pev->id }}-{{ $selected ? 'selected' : 'unselected' }}"
                                            @if ($selected && ! $rowLocked) wire:model.live="lessons.{{ $pev->id }}.priority" @endif
                                            value="{{ $selected ? ($lessons[$pev->id]['priority'] ?? 0) : 0 }}"
                                            min="0" @disabled(! $selected || $rowLocked)
                                            class="w-12 text-center bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded px-1 py-1 text-xs disabled:opacity-60 disabled:cursor-not-allowed">
                                    </td>
                                    <td class="px-3 py-2">
                                        <select
                                            wire:key="lesson-shift-{{ $pev->id }}-{{ $selected ? 'selected' : 'unselected' }}"
                                            @if ($selected && ! $rowLocked) wire:model.live="lessons.{{ $pev->id }}.shift_id" @endif
                                            @disabled(! $selected || $rowLocked)
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
                                            wire:key="lesson-room-{{ $pev->id }}-{{ $selected ? 'selected' : 'unselected' }}"
                                            @if ($selected && ! $rowLocked) wire:model.live="lessons.{{ $pev->id }}.room_type_required" @endif
                                            @disabled(! $selected || $rowLocked)
                                            class="text-xs bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded px-2 py-1 disabled:opacity-60 disabled:cursor-not-allowed">
                                            <option value="">{{ $selected ? '—' : 'Selecciona' }}</option>
                                            @foreach (['aula', 'laboratorio', 'patio', 'cancha', 'taller', 'salon'] as $type)
                                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <input type="checkbox"
                                            wire:key="lesson-half-{{ $pev->id }}-{{ $selected ? 'selected' : 'unselected' }}"
                                            @if ($selected && ! $rowLocked)
                                                wire:model.live="lessons.{{ $pev->id }}.is_half_group"
                                               
                                            @endif
                                            @disabled(! $selected || $rowLocked)
                                            title="Comparte la celda con otra asignatura de medio grupo"
                                            class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 disabled:opacity-60 disabled:cursor-not-allowed">
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <input type="checkbox"
                                            wire:key="lesson-shared-{{ $pev->id }}-{{ $selected ? 'selected' : 'unselected' }}"
                                            @if ($selected && ! $rowLocked)
                                                wire:model.live="lessons.{{ $pev->id }}.allow_shared_teacher"
                                               
                                            @endif
                                            @disabled(! $selected || $rowLocked)
                                            title="Permite coincidir con otra lesson del mismo docente si ambas lessons tienen esta autorización"
                                            class="rounded border-gray-300 text-violet-600 focus:ring-violet-500 disabled:opacity-60 disabled:cursor-not-allowed">
                                    </td>
                                    <td class="px-3 py-2 text-[10px] flex items-center gap-1.5">
                                        @if (isset($savedPevIds[$pev->id]))
                                            <span class="px-1.5 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-widest">Guardado</span>
                                        @endif
                                        @if (isset($step3Warnings[$pev->id]))
                                            <span class="block mt-0.5 text-amber-500 font-bold" title="{{ implode(' · ', $step3Warnings[$pev->id]) }}">⚠</span>
                                        @endif
                                    </td>
                                    {{-- CRUD: agregar (crear) o eliminar la lección del calendario --}}
                                    <td class="px-3 py-2 text-right">
                                        @if ($selected)
                                            <button type="button"
                                                wire:click="deleteLesson({{ (int) $pev->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="deleteLesson({{ (int) $pev->id }})"
                                                @disabled($rowLocked)
                                                title="Eliminar la lección y sus bloques del calendario"
                                                class="inline-flex items-center gap-1 rounded-lg bg-red-500/10 px-2 py-1 text-[10px] font-bold text-red-600 transition-colors hover:bg-red-500/20 disabled:cursor-not-allowed disabled:opacity-50 dark:text-red-300">
                                                Eliminar
                                            </button>
                                        @else
                                            <button type="button"
                                                wire:click="addLesson({{ (int) $pev->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="addLesson({{ (int) $pev->id }})"
                                                @disabled($rowLocked)
                                                title="Agregar la asignatura al horario"
                                                class="inline-flex items-center gap-1 rounded-lg bg-emerald-500/10 px-2 py-1 text-[10px] font-bold text-emerald-700 transition-colors hover:bg-emerald-500/20 disabled:cursor-not-allowed disabled:opacity-50 dark:text-emerald-300">
                                                Agregar
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-3 py-8 text-center text-sm text-gray-400">No hay lecciones (pevaluaciones) para la selección.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
