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
    {{-- Sin backdrop-blur-md: backdrop-filter en un ancestro descoloca el
         dropdown nativo del <select> en Chromium (bug conocido). --}}
    @if (count($calendars))
        <div class="bg-white dark:bg-gray-900/40 border border-gray-200 dark:border-white/5 rounded-lg p-4 mb-6">
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

    {{-- ═══════════ Paso 1 · Calendario ═══════════ --}}
    @if ($currentStep === 1)
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-900/40 border border-gray-200 dark:border-white/5 rounded-lg p-5">
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
            <div class="bg-white dark:bg-gray-900/40 border border-gray-200 dark:border-white/5 rounded-lg p-5">
                <h2 class="text-sm font-extrabold text-gray-900 dark:text-white mb-4">2 · Aulas</h2>
                {{-- Datos principales (requeridos) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
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
                {{-- Vínculo a sección (opcional) --}}
                @if ($roomSectionLinkLabel)
                    <div class="mb-4 flex items-center justify-between gap-3 px-3 py-2 rounded-lg bg-white/5 border border-white/10">
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
                </div>
                <div class="mt-4 pt-4 border-t border-white/10 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Registra automáticamente un aula/salón/ambiente por cada <strong>grado/sección</strong> de los pestudios activos (nombre asociado al grado y la sección).</p>
                    <button wire:click="confirmBulkCreateRooms"
                        class="px-5 py-2.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-200 text-sm font-bold border border-white/10">
                        Registrar aulas por grado/sección
                    </button>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-5">
                <div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
                    <h2 class="text-sm font-extrabold text-gray-900 dark:text-white">Aulas registradas</h2>
                    <div class="flex items-center gap-2">
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

    {{-- ═══════════ Paso 3 · Lecciones ═══════════ --}}
    @if ($currentStep === 3)
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-900/40 border border-gray-200 dark:border-white/5 rounded-lg p-5">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-extrabold text-gray-900 dark:text-white">3 · Lecciones</h2>
                        <span class="px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-widest">{{ $step3SelectedCount }} seleccionadas</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button wire:click="setStep3ViewMode('tabs')"
                            class="px-3 py-1.5 rounded-lg text-[11px] font-bold border transition-all duration-200 {{ $step3ViewMode === 'tabs' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white/5 text-gray-400 border-white/10 hover:text-gray-300' }}">Pestañas</button>
                        <button wire:click="setStep3ViewMode('flat')"
                            class="px-3 py-1.5 rounded-lg text-[11px] font-bold border transition-all duration-200 {{ $step3ViewMode === 'flat' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white/5 text-gray-400 border-white/10 hover:text-gray-300' }}">Lista</button>
                    </div>
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
                        <button wire:click="bulkAssignShift" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 text-xs font-bold border border-white/10">Aplicar turno</button>
                        <button wire:click="bulkAssignRoomType" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 text-xs font-bold border border-white/10">Aplicar aula</button>
                        @if (isset($step3Warnings) && count($step3Warnings))
                            <span class="text-[11px] text-amber-500">⚠ {{ count($step3Warnings) }} lección(es) con advertencia</span>
                        @endif
                    </div>
                </div>

                {{-- Pestañas · Pestudio --}}
                @if ($step3ViewMode === 'tabs' && $tabPestudioOptions)
                    <div class="mb-3 border-b border-gray-200 dark:border-white/10">
                        <nav class="flex w-full overflow-x-auto">
                            @foreach ($tabPestudioOptions as $opt)
                                <button wire:click="$set('activePestudioId', {{ $opt['id'] === 'general' ? "'general'" : $opt['id'] }})"
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
                                <button wire:click="$set('activeGradoId', {{ $opt['id'] === 'general' ? "'general'" : $opt['id'] }})"
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
                                <button wire:click="$set('activeSeccionId', {{ $opt['id'] === 'general' ? "'general'" : $opt['id'] }})"
                                    class="flex-1 text-center px-3 py-2 text-[11px] font-bold uppercase tracking-widest whitespace-nowrap border-b-2 transition-all duration-200
                                    {{ (string) $activeSeccionId === (string) $opt['id'] ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500' : 'text-gray-400 dark:text-gray-500 border-transparent hover:text-gray-600 dark:hover:text-gray-300' }}">
                                    Sección {{ $opt['name'] }}
                                </button>
                            @endforeach
                        </nav>
                    </div>
                @endif

                @php
                    $rows = $step3ViewMode === 'flat' ? $pevaluaciones : $tabActivePevaluaciones;
                    $sumT = 0;
                    $sumP = 0;
                    foreach ($rows as $pev) {
                        $sumT += (int) ceil(((int) ($pev->pensum?->asignatura?->hour_t_week ?? 0)) * 60 / max(1, (int) ($periodsList->count() ? $calendarPeriodMinutes ?? 60 : 60)));
                        $sumP += (int) ceil(((int) ($pev->pensum?->asignatura?->hour_p_week ?? 0)) * 60 / max(1, (int) ($periodsList->count() ? $calendarPeriodMinutes ?? 60 : 60)));
                    }
                @endphp

                {{-- Resumen de la selección (bloques teóricos/prácticos) --}}
                @if ($rows->isNotEmpty())
                    <div class="mb-3 px-3 py-2 rounded-lg bg-emerald-500/5 border border-emerald-500/20 text-xs text-emerald-600 dark:text-emerald-400">
                        {{ $rows->count() }} área(s) de formación · {{ $sumT }} bloques teóricos · {{ $sumP }} bloques prácticos · {{ $sumT + $sumP }} bloques totales
                    </div>
                @endif

                <div class="mb-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>{{ $rows->count() }} asignatura(s) · {{ $step3SelectedCount }} seleccionada(s)</span>
                    <span>Los bloques se derivan de <code>hour_t_week/hour_p_week</code> · <code>Guardar</code> persiste el borrador</span>
                </div>

                <div class="rounded-lg border border-gray-200 dark:border-white/10">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-gray-50 dark:bg-gray-800/80">
                            <tr class="text-left text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                <th class="px-3 py-2">
                                    <input type="checkbox" wire:click="toggleSelectAll" title="Seleccionar todo"
                                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                </th>
                                <th class="px-3 py-2">Asignatura · Sección</th>
                                <th class="px-3 py-2">Profesor</th>
                                <th class="px-3 py-2">T</th>
                                <th class="px-3 py-2">P</th>
                                <th class="px-3 py-2">Prio</th>
                                <th class="px-3 py-2">Turno</th>
                                <th class="px-3 py-2">Aula req.</th>
                                <th class="px-3 py-2">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $pev)
                                @php
                                    $selected = array_key_exists($pev->id, $lessons);
                                    $derivedT = (int) ceil(((int) ($pev->pensum?->asignatura?->hour_t_week ?? 0)) * 60 / max(1, (int) ($periodsList->count() ? $calendarPeriodMinutes ?? 60 : 60)));
                                    $derivedP = (int) ceil(((int) ($pev->pensum?->asignatura?->hour_p_week ?? 0)) * 60 / max(1, (int) ($periodsList->count() ? $calendarPeriodMinutes ?? 60 : 60)));
                                @endphp
                                <tr class="border-t border-gray-100 dark:border-white/5 {{ $selected ? 'bg-emerald-500/5' : '' }}">
                                    <td class="px-3 py-2">
                                        <input type="checkbox" wire:model.live="selectedPevs.{{ $pev->id }}"
                                            class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    </td>
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-200 font-medium">{{ $pev->pensum?->asignatura?->name }}{{ $pev->grupoEstable?->name ? ' · '.$pev->grupoEstable->name : '' }}</td>
                                    <td class="px-3 py-2 text-gray-500 dark:text-gray-400">{{ $pev->profesor?->lastname }}, {{ $pev->profesor?->name }}</td>
                                    <td class="px-3 py-2">
                                        @if ($selected)
                                            <input type="number" wire:model="lessons.{{ $pev->id }}.weekly_blocks_t" wire:change="autosaveLessons" min="0"
                                                class="w-12 text-center bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded px-1 py-1 text-xs">
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">{{ $derivedT }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        @if ($selected)
                                            <input type="number" wire:model="lessons.{{ $pev->id }}.weekly_blocks_p" wire:change="autosaveLessons" min="0"
                                                class="w-12 text-center bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded px-1 py-1 text-xs">
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">{{ $derivedP }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        @if ($selected)
                                            <input type="number" wire:model="lessons.{{ $pev->id }}.priority" wire:change="autosaveLessons" min="0"
                                                class="w-12 text-center bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded px-1 py-1 text-xs">
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        @if ($selected)
                                            <select wire:model="lessons.{{ $pev->id }}.shift_id" wire:change="autosaveLessons" class="text-xs bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded px-2 py-1">
                                                @foreach ($shifts as $shift)
                                                    <option value="{{ $shift->id }}">{{ $shift->code }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        @if ($selected)
                                            <select wire:model="lessons.{{ $pev->id }}.room_type_required" wire:change="autosaveLessons" class="text-xs bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded px-2 py-1">
                                                <option value="">—</option>
                                                @foreach (['aula', 'laboratorio', 'patio', 'cancha', 'taller', 'salon'] as $type)
                                                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-[10px]">
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
                                    <td colspan="9" class="px-3 py-8 text-center text-sm text-gray-400">No hay lecciones (pevaluaciones) para la selección.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 rounded-lg border border-dashed border-gray-300 dark:border-white/10 p-4">
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
                    <p class="mt-2 text-[11px] text-gray-400 dark:text-gray-500">Columnas: <code>pevaluacion_id</code> (obligatorio), <code>turno</code> (M/T), <code>bloques_t</code>, <code>bloques_p</code>, <code>aula</code>, <code>prioridad</code>.</p>
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
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Los bloques se derivan de <code>hour_t_week/hour_p_week</code> y la duración del bloque.</span>
                    <div class="flex items-center gap-2">
                        <button wire:click="autosaveLessons" class="px-4 py-2.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-bold border border-white/10">Guardar borrador</button>
                        <button wire:click="saveLessons" class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">Guardar lecciones y continuar</button>
                    </div>
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
                        @foreach ($periodsList->groupBy('shift_id') as $shiftId => $shiftPeriods)
                            @php $shift = $shifts->firstWhere('id', $shiftId); @endphp
                            <div class="mb-4 last:mb-0">
                                <div class="text-[10px] font-bold text-gray-400 mb-1">
                                    {{ $shift?->name ?? ('Turno '.$shiftId) }}
                                    {{ $shift?->start_time ? '· '.substr((string) $shift->start_time, 0, 5).'–'.substr((string) $shift->end_time, 0, 5) : '' }}
                                </div>
                                <div class="grid grid-cols-6 gap-1">
                                    <div class="text-[10px] font-bold text-gray-400">Período</div>
                                    @foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie'] as $dayLabel)
                                        <div class="text-[10px] font-bold text-gray-400 text-center">{{ $dayLabel }}</div>
                                    @endforeach

                                    @foreach ($shiftPeriods->groupBy('order_in_day') as $order => $group)
                                        <div class="contents">
                                            <div class="text-[10px] font-bold text-gray-400 flex items-center">{{ $order }}º</div>
                                            @for ($day = 1; $day <= 5; $day++)
                                                @php $period = $group->firstWhere('day_of_week', $day); @endphp
                                                <div class="flex justify-center">
                                                    @if ($period)
                                                        <input type="checkbox"
                                                            wire:model.live="availability.{{ $profesor->id }}.{{ $period->id }}"
                                                            class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                                    @else
                                                        <span class="text-[10px] text-gray-300">–</span>
                                                    @endif
                                                </div>
                                            @endfor
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
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

    @include('coordinacion.help-timetable-wizard')
</div>