<div class="fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-1">Línea de Actividad</h1>
            <p class="text-emerald-400 font-medium text-sm">Actividad cronológica de un usuario, agrupada por día.</p>
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

    <!-- Selector de usuario -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-4 mb-6">
        @if($selfMode)
            <p class="text-sm text-gray-400 mb-3">
                Mostrando <span class="text-emerald-400 font-medium">tu propia actividad</span>. Este acceso solo permite ver tus registros.
            </p>
        @endif

        <div class="flex flex-wrap items-start gap-3">
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

            <div class="w-full sm:w-52">
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Rango de fechas</label>
                <select wire:model.live="rangeDays"
                        class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:border-emerald-500 focus:outline-none">
                    @foreach(\App\Livewire\Admin\Binnacle\UserActivityTimeline::DATE_RANGES as $days => $label)
                        <option value="{{ $days }}">{{ $label }}</option>
                    @endforeach
                </select>
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

        @forelse($grouped as $date => $dayEntries)
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-sm font-bold text-white">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</span>
                    <span class="text-xs text-gray-500">{{ $dayEntries->count() }} eventos</span>
                    <span class="flex-1 h-px bg-white/5"></span>
                </div>

                <ol class="relative border-l border-white/10 ml-2 space-y-6">
                    @foreach($dayEntries as $entry)
                        @php
                            $dot = match ($entry->event_severity) {
                                'critical' => 'bg-red-500 border-red-400',
                                'alert' => 'bg-orange-500 border-orange-400',
                                'warning' => 'bg-yellow-500 border-yellow-400',
                                'debug' => 'bg-gray-500 border-gray-400',
                                default => 'bg-emerald-500 border-emerald-400',
                            };
                            $ring = match ($entry->event_severity) {
                                'critical' => 'ring-red-500/20',
                                'alert' => 'ring-orange-500/20',
                                'warning' => 'ring-yellow-500/20',
                                default => 'ring-emerald-500/20',
                            };
                        @endphp
                        <li class="relative pl-6">
                            <span class="absolute -left-[5px] top-1 w-2.5 h-2.5 rounded-full border-2 ring-4 {{ $dot }} {{ $ring }}"></span>
                            <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-4">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-gray-100">{{ $entry->title }}</span>
                                    <x-binnacle.badge :value="$entry->event_severity" kind="severity" />
                                    <x-binnacle.badge :value="$entry->event_category" kind="category" />
                                    <span class="text-xs text-gray-500 ml-auto">{{ $entry->created_at?->format('H:i:s') }}</span>
                                </div>
                                @if($entry->description)
                                    <p class="text-sm text-gray-400 mb-2">{{ $entry->description }}</p>
                                @endif
                                @if($entry->event_type === 'model_updated' && $entry->changed_fields)
                                    <div class="flex flex-wrap gap-1.5 mt-1">
                                        @foreach($entry->changed_fields as $field)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/5 border border-white/10 text-[11px] text-gray-400 font-mono">{{ $field }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="flex flex-wrap gap-3 mt-2 text-[11px] text-gray-600 font-mono">
                                    @if($entry->ip_address)<span>{{ $entry->ip_address }}</span>@endif
                                    @if($entry->request_method)<span>{{ $entry->request_method }} {{ \Illuminate\Support\Str::limit((string) $entry->request_url, 60) }}</span>@endif
                                    @if($entry->request_id)<span>req: {{ $entry->request_id }}</span>@endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        @empty
            <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg py-12 text-center">
                <p class="text-gray-400 text-sm">Sin actividad registrada para este usuario en el rango seleccionado.</p>
            </div>
        @endforelse
    @else
        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg py-12 text-center">
            <p class="text-gray-400 text-sm">Selecciona un usuario para ver su línea de actividad.</p>
        </div>
    @endif
</div>