<div class="fade-in">
    {{-- Header --}}
    <div class="mb-6 sm:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Suplencias</h1>
            <p class="text-emerald-600 dark:text-emerald-400 font-medium text-sm">Ausencias de docentes y asignación de suplentes (Lun–Vie)</p>
        </div>
    </div>

    @if ($message)
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-sm font-medium">{{ $message }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Columna 1: registro de ausencia --}}
        <div class="lg:col-span-1 bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-5 h-fit">
            <h2 class="text-sm font-extrabold text-gray-900 dark:text-white mb-4">Registrar ausencia</h2>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Calendario</label>
                    <select wire:model.live="calendarId" class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-2 text-sm">
                        @foreach ($calendars as $cal)
                            <option value="{{ $cal->id }}">{{ $cal->name }} ({{ $cal->status }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Docente ausente</label>
                    <select wire:model="absentProfesorId" class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-2 text-sm">
                        <option value="">Seleccionar docente…</option>
                        @foreach ($profesores as $p)
                            <option value="{{ $p->id }}">{{ $p->lastname }}, {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Desde</label>
                        <input type="date" wire:model="dateStart" class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Hasta</label>
                        <input type="date" wire:model="dateEnd" class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-2 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Motivo</label>
                    <input type="text" wire:model="reason" placeholder="Opcional" class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-2 text-sm">
                </div>

                <button wire:click="registerAbsence"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-500 transition-colors">
                    Registrar ausencia
                </button>
            </div>
        </div>

        {{-- Columna 2: ausencias registradas --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-5">
            <h2 class="text-sm font-extrabold text-gray-900 dark:text-white mb-4">Ausencias registradas</h2>

            @if ($absences->isEmpty())
                <p class="text-sm text-gray-400">No hay ausencias registradas para este calendario.</p>
            @else
                <div class="space-y-2">
                    @foreach ($absences as $absence)
                        <button wire:click="selectAbsence({{ $absence->id }})"
                            class="w-full text-left px-4 py-3 rounded-lg border transition-colors {{ $selectedAbsence?->id === $absence->id ? 'border-emerald-500/50 bg-emerald-500/5' : 'border-gray-200 dark:border-white/5 hover:bg-white/5' }}">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $absence->profesor?->lastname }}, {{ $absence->profesor?->name }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $absence->date_start }} → {{ $absence->date_end }}</span>
                            </div>
                            @if ($absence->reason)
                                <p class="text-xs text-gray-500 mt-1">{{ $absence->reason }}</p>
                            @endif
                        </button>
                    @endforeach
                </div>
            @endif

            @if ($selectedAbsence)
                <div class="mt-6">
                    <h3 class="text-xs font-extrabold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-3">
                        Bloques afectados ({{ $affectedSlots->count() }})
                    </h3>

                    @if ($affectedSlots->isEmpty())
                        <p class="text-sm text-gray-400">No hay clases de este docente dentro del rango de fechas.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($affectedSlots as $slot)
                                <div class="rounded-lg border border-gray-200 dark:border-white/5 p-4">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ $slot->lesson?->pevaluacion?->pensum?->asignatura?->name ?? 'Clase' }}
                                                · {{ $slot->lesson?->pevaluacion?->seccion?->name ?? '' }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $slot->period?->period_label }}</p>
                                        </div>
                                        <span class="text-xs px-2 py-1 rounded bg-red-500/10 text-red-500 font-bold w-fit">
                                            Docente ausente
                                        </span>
                                    </div>

                                    @php $assigned = $assignmentsBySlot->get($slot->id)?->first(); @endphp

                                    @if ($assigned)
                                        <div class="flex items-center gap-2 text-sm">
                                            <span class="text-gray-400">Suplente:</span>
                                            <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                                {{ $assigned->substituteProfesor?->lastname }}, {{ $assigned->substituteProfesor?->name }}
                                            </span>
                                            <span class="text-xs px-2 py-0.5 rounded {{ $assigned->status === 'confirmed' ? 'bg-emerald-500/10 text-emerald-500' : ($assigned->status === 'declined' ? 'bg-red-500/10 text-red-500' : 'bg-amber-500/10 text-amber-500') }} font-bold">
                                                {{ ucfirst($assigned->status) }}
                                            </span>
                                        </div>
                                    @else
                                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Asignar suplente</label>
                                        <select wire:change="assignSubstitute({{ $slot->id }}, $event.target.value)"
                                            class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 px-3 py-2 text-sm">
                                            <option value="">Seleccionar suplente…</option>
                                            @foreach ($candidatesBySlot[$slot->id] ?? collect() as $candidate)
                                                <option value="{{ $candidate['profesor']->id }}">
                                                    {{ $candidate['profesor']->lastname }}, {{ $candidate['profesor']->name }}
                                                    @if ($candidate['conflict']) — {{ $candidate['conflict'] }} @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>