<div class="fade-in">
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
        </div>
    </div>

    {{-- Stepper --}}
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-4 mb-6">
        <div class="flex flex-wrap items-center gap-2">
            @foreach ([1 => 'Calendario', 2 => 'Aulas', 3 => 'Lecciones', 4 => 'Disponibilidad', 5 => 'Generar'] as $step => $label)
                <button wire:click="goToStep({{ $step }})"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $currentStep === $step ? 'bg-emerald-600 text-white' : 'bg-white/5 text-gray-400 hover:text-gray-200' }}">
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

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 text-sm font-medium">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Switcher global: alternativas (calendarios) del lapso en edición --}}
    @if (count($calendars))
        <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-4 mb-6">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Alternativas del lapso</span>
                <select wire:model.live="calendarId" class="flex-1 min-w-[200px] bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                    @foreach ($calendars as $c)
                        <option value="{{ $c['id'] }}">{{ $c['name'] }} ({{ $c['status'] }})</option>
                    @endforeach
                </select>
                <a href="{{ request()->url() }}"
                    class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all">+ Nuevo borrador</a>
            </div>
            @if ($calendarId)
                <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                    Editando: <span class="font-bold text-gray-900 dark:text-white">{{ collect($calendars)->firstWhere('id', $calendarId)['name'] ?? '' }}</span>
                    @php($activeCal = collect($calendars)->firstWhere('status', 'active'))
                    @if ($activeCal)
                        · Activo: <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $activeCal['name'] }}</span>
                    @endif
                </div>
            @endif
        </div>
    @endif

    {{-- ═══════════ Paso 1 · Calendario ═══════════ --}}
    @if ($currentStep === 1)
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-5">
                <h2 class="text-sm font-extrabold text-gray-900 dark:text-white mb-4">1 · Calendario</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Lapso</label>
                        <select wire:model.live="lapsoId" class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Selecciona un lapso</option>
                            @foreach ($lapsos as $lapso)
                                <option value="{{ $lapso->id }}">{{ $lapso->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Nombre del calendario</label>
                        <input type="text" wire:model.live="calendarName" placeholder="Horario 2025-2026 · Lapso I"
                            class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Duración del bloque (min)</label>
                        <input type="number" wire:model="periodMinutes" min="30" max="120"
                            class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    </div>
                </div>
                <button wire:click="createCalendar"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition-all">
                    Crear borrador
                </button>
            </div>

            {{-- PLAN-TIMETABLE-002 §4.5: alternativas (calendarios) del lapso --}}
            @if (count($calendars))
                <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-extrabold text-gray-900 dark:text-white">Calendarios del lapso</h2>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ count($calendars) }} alternativa(s) · máximo 1 activo</span>
                    </div>
                    <div class="space-y-2">
                        @foreach ($calendars as $c)
                            <div class="flex flex-wrap items-center justify-between gap-3 p-3 rounded-lg border {{ $calendarId === $c['id'] ? 'border-emerald-500/40 bg-emerald-500/5' : 'border-gray-200 dark:border-white/10 bg-white/5' }}">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <div class="text-sm font-extrabold text-gray-900 dark:text-white">{{ $c['name'] }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">v{{ $c['version'] }} · calidad {{ $c['quality_score'] ?? '—' }}</div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $c['status'] === 'active' ? 'bg-emerald-500/15 text-emerald-600' : ($c['status'] === 'archived' ? 'bg-gray-500/10 text-gray-500' : 'bg-amber-500/15 text-amber-600') }}">
                                        {{ $c['status'] }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button wire:click="selectCalendar({{ $c['id'] }})" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-bold">Continuar</button>
                                    @if ($c['status'] === 'draft')
                                        <button wire:click="activateCalendar({{ $c['id'] }})" class="px-3 py-1.5 rounded-lg bg-white/5 text-gray-200 text-xs font-bold border border-gray-200 dark:border-white/10">Activar</button>
                                        <button wire:click="deleteCalendar({{ $c['id'] }})" class="px-3 py-1.5 rounded-lg text-red-400 hover:text-red-300 text-xs font-bold">Eliminar</button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($calendarId)
                <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-5">
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

                    <div class="flex items-center gap-3">
                        <select wire:model="shiftId" class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="0">Elige un turno</option>
                            @foreach ($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                            @endforeach
                        </select>
                        <button wire:click="generatePeriods" class="px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-200 text-sm font-bold border border-white/10">Vista previa de períodos</button>
                        <button wire:click="savePeriods" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">Guardar períodos (Lun–Vie)</button>
                    </div>

                    @if ($periods)
                        <div class="mt-4 grid grid-cols-5 gap-2">
                            @foreach ($periods as $p)
                                <div class="text-center px-2 py-2 rounded-lg bg-white/5 border border-white/10 text-xs font-bold text-gray-300">{{ $p['label'] }}</div>
                            @endforeach
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
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-5">
                <h2 class="text-sm font-extrabold text-gray-900 dark:text-white mb-4">2 · Aulas</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
                    <input type="text" wire:model="roomCode" placeholder="Código (A-101)"
                        class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                    <input type="text" wire:model="roomName" placeholder="Nombre (Aula 101)"
                        class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                    <input type="number" wire:model="roomCapacity" min="1" placeholder="Capacidad"
                        class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 outline-none">
                    <select wire:model="roomType" class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm">
                        @foreach (['aula', 'laboratorio', 'patio', 'cancha', 'taller', 'salon'] as $type)
                            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <button wire:click="saveRoom" class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">Registrar aula</button>
            </div>

            <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-5">
                <h2 class="text-sm font-extrabold text-gray-900 dark:text-white mb-4">Aulas registradas</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse ($rooms as $room)
                        <div class="flex items-center justify-between p-4 rounded-lg bg-white/5 border border-white/10">
                            <div>
                                <div class="text-sm font-extrabold text-gray-900 dark:text-white">{{ $room['code'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $room['name'] }} · {{ $room['capacity'] }} · {{ $room['type'] }}</div>
                            </div>
                            <button wire:click="deleteRoom({{ $room['id'] }})" class="text-red-400 hover:text-red-300 text-xs font-bold">Eliminar</button>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500 dark:text-gray-400 col-span-full">Sin aulas registradas.</div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════ Paso 3 · Lecciones ═══════════ --}}
    @if ($currentStep === 3)
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-extrabold text-gray-900 dark:text-white">3 · Lecciones</h2>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ count($selectedPevs) }} seleccionadas de {{ $pevaluaciones->count() }}</span>
                </div>

                <div class="max-h-72 overflow-y-auto rounded-lg border border-gray-200 dark:border-white/10">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-gray-50 dark:bg-gray-800/80">
                            <tr class="text-left text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                <th class="px-3 py-2">Sel</th>
                                <th class="px-3 py-2">Asignatura · Sección</th>
                                <th class="px-3 py-2">Profesor</th>
                                <th class="px-3 py-2">T</th>
                                <th class="px-3 py-2">P</th>
                                <th class="px-3 py-2">Turno</th>
                                <th class="px-3 py-2">Aula req.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pevaluaciones as $pev)
                                <tr class="border-t border-gray-100 dark:border-white/5">
                                    <td class="px-3 py-2">
                                        <input type="checkbox" wire:model.live="selectedPevs.{{ $pev->id }}"
                                            class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    </td>
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-200 font-medium">{{ $pev->pensum?->asignatura?->name }} · {{ $pev->seccion?->name }}</td>
                                    <td class="px-3 py-2 text-gray-500 dark:text-gray-400">{{ $pev->profesor?->lastname }}, {{ $pev->profesor?->name }}</td>
                                    <td class="px-3 py-2 text-gray-500 dark:text-gray-400">{{ (int) ceil(((int) ($pev->pensum?->asignatura?->hour_t_week ?? 0)) * 60 / max(1, (int) ($periodsList->count() ? $calendarPeriodMinutes ?? 60 : 60))) }}</td>
                                    <td class="px-3 py-2 text-gray-500 dark:text-gray-400">{{ (int) ceil(((int) ($pev->pensum?->asignatura?->hour_p_week ?? 0)) * 60 / max(1, (int) ($periodsList->count() ? $calendarPeriodMinutes ?? 60 : 60))) }}</td>
                                    <td class="px-3 py-2">
                                        <select wire:model="lessons.{{ $loop->index }}.shift_id" class="text-xs bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded px-2 py-1">
                                            @foreach ($shifts as $shift)
                                                <option value="{{ $shift->id }}">{{ $shift->code }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-2">
                                        <select wire:model="lessons.{{ $loop->index }}.room_type_required" class="text-xs bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded px-2 py-1">
                                            <option value="">—</option>
                                            @foreach (['aula', 'laboratorio', 'patio', 'cancha', 'taller', 'salon'] as $type)
                                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Los bloques se derivan de <code>hour_t_week/hour_p_week</code> y la duración del bloque.</span>
                    <button wire:click="saveLessons" class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">Guardar lecciones y continuar</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════ Paso 4 · Disponibilidad ═══════════ --}}
    @if ($currentStep === 4)
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-extrabold text-gray-900 dark:text-white">4 · Disponibilidad docente</h2>
                    <button wire:click="setAllAvailable" class="px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-200 text-sm font-bold border border-white/10">Marcar todo disponible</button>
                </div>

                <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">Grilla día × período por docente. Ajusta la disponibilidad si es necesario.</div>

                @foreach ($profesores as $profesor)
                    <div class="mb-6 rounded-lg border border-gray-200 dark:border-white/10 p-4">
                        <div class="text-sm font-extrabold text-gray-900 dark:text-white mb-3">{{ $profesor->lastname }}, {{ $profesor->name }}</div>
                        <div class="grid grid-cols-6 gap-1">
                            <div class="text-[10px] font-bold text-gray-400">Período</div>
                            @foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie'] as $dayLabel)
                                <div class="text-[10px] font-bold text-gray-400 text-center">{{ $dayLabel }}</div>
                            @endforeach

                            @foreach ($periodsList->groupBy('order_in_day') as $order => $group)
                                <div class="contents">
                                    <div class="text-[10px] font-bold text-gray-400 flex items-center">{{ $order }}º</div>
                                    @foreach ($group as $period)
                                        <div class="flex justify-center">
                                            <input type="checkbox"
                                                wire:model.live="availability.{{ $profesor->id }}.{{ $period->id }}"
                                                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <button wire:click="saveAvailability" class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">Guardar disponibilidad</button>
            </div>
        </div>
    @endif

    {{-- ═══════════ Paso 5 · Generar ═══════════ --}}
    @if ($currentStep === 5)
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-5">
                <h2 class="text-sm font-extrabold text-gray-900 dark:text-white mb-4">5 · Generar horario</h2>

                <div class="flex flex-wrap items-center gap-3">
                    <button wire:click="runDryRun" wire:loading.attr="disabled" wire:target="runDryRun"
                        class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">
                        <span wire:loading.remove wire:target="runDryRun">Previsualizar (dry-run)</span>
                        <span wire:loading wire:target="runDryRun">Generando…</span>
                    </button>

                    @if ($generationState === 'preview_ready' && $preview)
                        <button wire:click="confirmAndPublish" wire:loading.attr="disabled" wire:target="confirmAndPublish"
                            class="px-5 py-2.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold">
                            Confirmar y publicar
                        </button>
                    @endif

                    @if ($generationState === 'published')
                        <span class="px-4 py-2 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-sm font-bold">Horario publicado.</span>
                    @endif
                </div>

                @if ($generationState === 'generating')
                    <div class="mt-4 flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Ejecutando el motor de asignación…
                    </div>
                @endif

                @if ($generationState === 'preview_ready' && $preview)
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-4 rounded-lg bg-white/5 border border-white/10">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Asignadas</div>
                            <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ count($preview['assignment'] ?? []) }}</div>
                        </div>
                        <div class="p-4 rounded-lg bg-white/5 border border-white/10">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Sin asignar</div>
                            <div class="text-2xl font-extrabold text-red-500">{{ count($preview['unassigned'] ?? []) }}</div>
                        </div>
                        <div class="p-4 rounded-lg bg-white/5 border border-white/10">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Tiempo</div>
                            <div class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $preview['elapsed_seconds'] ?? 0 }}s</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>