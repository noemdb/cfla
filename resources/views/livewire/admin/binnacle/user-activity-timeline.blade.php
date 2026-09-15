<div class="fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-1">Línea de Actividad</h1>
            <p class="text-emerald-400 font-medium text-sm">Actividad cronológica agrupada por día. Elige un usuario para acotar la línea a su actividad.</p>
        </div>
        <div class="text-sm text-gray-400">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                Mínimo 500 registros por consulta
            </span>
        </div>
    </div>

    <!-- Selector de usuario + rango -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-4 mb-6">
        @if($selfMode)
            <p class="text-sm text-gray-400 mb-3">
                Mostrando <span class="text-emerald-400 font-medium">tu propia actividad</span>. Este acceso solo permite ver tus registros.
            </p>
        @endif

        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            @if(!$selfMode)
                <div class="relative flex-1 min-w-[12rem]">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Usuario</label>
                    <input type="search"
                           wire:model.live.debounce.300ms="userSearch"
                           placeholder="Buscar por username o correo…"
                           class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:border-emerald-500 focus:outline-none">
                    @if($userSearch && $candidates->isNotEmpty())
                        <ul class="absolute z-20 mt-1 w-full bg-gray-800 border border-white/10 rounded-lg shadow-xl overflow-hidden">
                            @foreach($candidates as $u)
                                <li>
                                    <button type="button" wire:click="selectUser({{ $u->id }})"
                                            class="w-full text-left px-3 py-2 hover:bg-white/5 transition-colors">
                                        <span class="text-sm text-gray-200 font-medium">{{ $u->username }}</span>
                                        <span class="block text-xs text-gray-500">{{ $u->email }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Rango de fechas</label>
                @php
                    $rangeShort = [1 => '24h', 7 => '7d', 15 => '15d', 30 => '30d', 90 => '3m', 0 => 'Todo'];
                @endphp
                {{-- Segmented control: botones unidos como un solo control; exactamente uno activo. --}}
                <div role="radiogroup" aria-label="Rango de fechas"
                     class="inline-flex w-full max-w-full overflow-x-auto rounded-lg border border-white/10 bg-gray-800/60 p-0.5 shadow-inner sm:w-auto">
                    @foreach(\App\Livewire\Admin\Binnacle\UserActivityTimeline::DATE_RANGES as $days => $label)
                        @php $isActive = (int) $rangeDays === (int) $days; @endphp
                        <button type="button"
                                role="radio"
                                aria-checked="{{ $isActive ? 'true' : 'false' }}"
                                title="{{ $label }}"
                                wire:click="$set('rangeDays', {{ $days }})"
                                class="min-w-[3.25rem] whitespace-nowrap rounded-[7px] px-3 py-1.5 text-xs font-bold transition-all duration-150
                                {{ $isActive
                                    ? 'bg-white text-gray-900 shadow-sm dark:bg-white/90 dark:text-gray-900'
                                    : 'text-gray-400 hover:text-gray-200' }}">
                            {{ $rangeShort[$days] ?? $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-white/5">
            <div class="flex flex-wrap items-end gap-3">
                <div class="w-full sm:w-56">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Tipo de evento</label>
                    <select wire:model.live="eventType"
                            class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:border-emerald-500 focus:outline-none">
                        <option value="">Todos</option>
                        @foreach(\App\Livewire\Admin\Binnacle\UserActivityTimeline::EVENT_TYPES as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-44">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Categoría</label>
                    <select wire:model.live="category"
                            class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:border-emerald-500 focus:outline-none">
                        <option value="">Todas</option>
                        @foreach(\App\Livewire\Admin\Binnacle\UserActivityTimeline::CATEGORIES as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-40">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Severidad</label>
                    <select wire:model.live="severity"
                            class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:border-emerald-500 focus:outline-none">
                        <option value="">Todas</option>
                        @foreach(\App\Livewire\Admin\Binnacle\UserActivityTimeline::SEVERITIES as $value)
                            <option value="{{ $value }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[12rem]">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Buscar en título/descripción</label>
                    <input type="search" wire:model.live.debounce.300ms="search"
                           placeholder="Ej: Perfil, matrícula, error…"
                           class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:border-emerald-500 focus:outline-none">
                </div>
                <div>
                    @if($hasFilters)
                        <button type="button" wire:click="clearFilters"
                                class="px-3 py-2 rounded-lg text-sm font-medium bg-white/5 border border-white/10 text-gray-300 hover:bg-white/10 transition-colors">
                            Limpiar filtros
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($selected)
        <div class="mb-6 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-300 font-bold">
                {{ strtoupper(substr($selected->username, 0, 1)) }}
            </div>
            <div>
                <p class="text-white font-bold">{{ $selected->username }}</p>
                <p class="text-xs text-gray-500">{{ $selected->email }} — {{ $entries->count() }} eventos</p>
            </div>
        </div>
    @else
        <div class="mb-6 flex items-center gap-2 text-sm text-gray-400">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-500/10 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-sky-300">
                Feed global
            </span>
            <span>{{ $entries->count() }} evento(s) en el rango seleccionado</span>
        </div>
    @endif

    @forelse($grouped as $date => $dayEntries)
        <section class="mb-10">
            {{-- Separador de día --}}
            <div class="mb-6 flex items-center gap-3">
                <span class="h-px flex-1 bg-white/5"></span>
                <span class="rounded-full border border-white/10 bg-gray-900/60 px-3 py-1 text-xs font-bold text-white">
                    {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                </span>
                <span class="text-xs text-gray-500">{{ $dayEntries->count() }} eventos</span>
                <span class="h-px flex-1 bg-white/5"></span>
            </div>

            {{-- Timeline alternado: izquierda / derecha --}}
            <ol class="relative space-y-6">
                <span class="pointer-events-none absolute bottom-0 left-4 top-0 w-px bg-white/10 md:left-1/2 md:-translate-x-1/2" aria-hidden="true"></span>

                @foreach($dayEntries as $entry)
                    @php
                        [$dot, $ring] = match ($entry->event_severity) {
                            'critical' => ['bg-red-500 border-red-400', 'ring-red-500/20'],
                            'alert' => ['bg-orange-500 border-orange-400', 'ring-orange-500/20'],
                            'warning' => ['bg-yellow-500 border-yellow-400', 'ring-yellow-500/20'],
                            'debug' => ['bg-gray-500 border-gray-400', 'ring-gray-500/20'],
                            default => ['bg-emerald-500 border-emerald-400', 'ring-emerald-500/20'],
                        };
                        $isLeft = $loop->iteration % 2 === 1;
                    @endphp

                    <li class="relative md:grid md:grid-cols-2 md:gap-x-10">
                        <span class="absolute left-4 top-5 z-10 h-3 w-3 -translate-x-1/2 rounded-full border-2 ring-4 {{ $dot }} {{ $ring }} md:left-1/2" aria-hidden="true"></span>

                        @if($isLeft)
                            <div class="pl-10 md:pl-0">
                                <x-binnacle.event-card :entry="$entry" />
                            </div>
                            <div class="hidden md:block" aria-hidden="true"></div>
                        @else
                            <div class="hidden md:block" aria-hidden="true"></div>
                            <div class="pl-10 md:pl-0">
                                <x-binnacle.event-card :entry="$entry" />
                            </div>
                        @endif
                    </li>
                @endforeach
            </ol>
        </section>
    @empty
        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg py-12 text-center">
            <p class="text-gray-400 text-sm">Sin actividad registrada en el rango seleccionado.</p>
        </div>
    @endforelse
</div>
