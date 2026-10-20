{{-- Paso 3 · resumen de capacidad y bloques (wizard completo y tab Clases).
     `summaryMode` = 'preview' (wizard: métricas del dry-run/preview) o
     'persisted' (modo rápido `/light`: edita `timetable_slots` directamente,
     sin dry-run → se muestran métricas de la grilla guardada). --}}
                @php
                    $rows = $rows ?? ($step3ViewMode === 'flat' ? $pevaluaciones : $tabActivePevaluaciones);
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
                        $persistedMode = ($summaryMode ?? 'preview') === 'persisted';
                        $hasDryRunResult = ! $persistedMode
                            && in_array($generationState, ['preview_ready', 'published'], true)
                            && is_array($preview ?? null)
                            && array_key_exists('assignment', $preview);
                        $hasPersistedGrid = $persistedMode
                            && is_array($preview ?? null)
                            && array_key_exists('assignment', $preview);
                        $actualAssignment = collect($preview['assignment'] ?? []);
                        $actualPeriodIds = collect();
                        $actualAssignedBlocks = 0;
                        $actualIncompleteLessons = 0;
                        $actualAssignedLessons = 0;
                        $actualCountedLessons = 0;
                        foreach ($rows as $pev) {
                            // El wizard evalúa solo la selección; el modo rápido
                            // refleja la grilla completa de la sección activa.
                            if ($hasDryRunResult && ! isset($selectedPevIdsForView[(int) $pev->id])) {
                                continue;
                            }

                            $lessonId = (int) ($lessons[$pev->id]['id'] ?? 0);
                            if ($lessonId <= 0) {
                                continue;
                            }

                            $actualCountedLessons++;
                            $slots = collect($actualAssignment->get((string) $lessonId, $actualAssignment->get($lessonId, [])));
                            $assigned = $slots->pluck('period_id')->filter()->unique();
                            $requiredForLesson = (int) ($lessons[$pev->id]['weekly_blocks_t'] ?? 0)
                                + (int) ($lessons[$pev->id]['weekly_blocks_p'] ?? 0);
                            $actualAssignedBlocks += $assigned->count();
                            if ($assigned->isNotEmpty()) {
                                $actualAssignedLessons++;
                            }
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
                        } elseif ($hasPersistedGrid) {
                            if ($actualCountedLessons === 0) {
                                $feasibilityLabel = 'Sin clases';
                                $feasibilityClasses = 'border-gray-300/80 bg-white/60 text-gray-700 dark:border-white/10 dark:bg-white/[0.04] dark:text-gray-200';
                            } elseif ($actualIncompleteLessons > 0) {
                                $feasibilityLabel = 'Grilla incompleta';
                                $feasibilityClasses = 'border-amber-500/30 bg-amber-500/10 text-amber-800 dark:text-amber-200';
                            } else {
                                $feasibilityLabel = 'Grilla guardada';
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
                            @elseif ($hasPersistedGrid && $actualCountedLessons > 0)
                                <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 border-t border-current/10 pt-2 text-[11px]">
                                    <span class="font-bold">Grilla: {{ $actualCells }} celdas ocupadas</span>
                                    <span>{{ $actualAssignedLessons }}/{{ $actualCountedLessons }} clases ubicadas</span>
                                    @if ($actualIncompleteLessons > 0)
                                        <span class="font-bold text-amber-700 dark:text-amber-300">
                                            ⚠ {{ $actualIncompleteLessons }} clase(s) sin ubicar
                                        </span>
                                    @else
                                        <span class="font-bold text-emerald-700 dark:text-emerald-300">✓ todas las clases ubicadas</span>
                                    @endif
                                    @if (! empty($lastSavedAt ?? null))
                                        <span class="opacity-80">Actualizado {{ $lastSavedAt }}</span>
                                    @endif
                                </div>
                            @elseif ($hasPersistedGrid)
                                <p class="mt-2 border-t border-current/10 pt-2 text-[11px] opacity-80">
                                    Esta sección aún no tiene clases guardadas. Selecciona asignaturas y pulsa
                                    <span class="font-bold">Guardar clases</span> para reflejarlas en la grilla.
                                </p>
                            @endif
                            @if ($selectedHalfBlocks > 0 && ! ($persistedMode ?? false))
                                <p class="mt-1 text-[10px] opacity-80">
                                    La estimación va de ningún emparejamiento al máximo teórico de medios grupos.
                                    El dry-run incorpora profesor, aula, disponibilidad, turno y sección.
                                </p>
                            @endif
                        @endif
                    </div>
                @endif
