<div>
    <x-modal-card title="Importar actividades" blur="lg" wire:model="showModal" align="center" max-width="xl" persistent>
        <div class="space-y-4">
            {{-- Área destino --}}
            @if ($target)
                <div class="rounded-lg border border-emerald-500/20 bg-emerald-500/5 px-3 py-2.5">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                        Importar hacia
                    </div>
                    <div class="mt-0.5 text-sm font-bold text-gray-900 dark:text-white">
                        {{ $target['asignatura'] }}
                        <span class="font-normal text-gray-500 dark:text-gray-400">
                            · {{ $target['grado'] }} · Sección {{ $target['seccion'] }} · {{ $target['lapso'] }}
                        </span>
                    </div>
                </div>
            @endif

            {{-- Indicador de pasos --}}
            <ol class="flex items-center gap-2">
                @foreach ([1 => 'Sección', 2 => 'Actividades', 3 => 'Vista previa'] as $number => $label)
                    <li class="flex flex-1 items-center gap-2">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[11px] font-bold
                            {{ $step >= $number ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500 dark:bg-white/10 dark:text-gray-400' }}">
                            {{ $number }}
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-widest
                            {{ $step >= $number ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-gray-500' }}">
                            {{ $label }}
                        </span>
                        @if (! $loop->last)
                            <span class="hidden h-px flex-1 bg-gray-200 sm:block dark:bg-white/10"></span>
                        @endif
                    </li>
                @endforeach
            </ol>

            {{-- Paso 1: secciones --}}
            @if ($step === 1)
                <div class="space-y-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Secciones del mismo grado con la misma asignatura. Elige la sección de la que quieres importar.
                    </p>

                    @forelse ($sections as $section)
                        <button type="button"
                            wire:click="selectSection({{ $section['id'] }})"
                            wire:loading.attr="disabled" wire:target="selectSection"
                            @disabled($section['activities_count'] === 0)
                            class="flex w-full items-center justify-between gap-3 rounded-lg border px-3 py-2.5 text-left transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500/50 disabled:cursor-not-allowed disabled:opacity-50
                                {{ $section['activities_count'] > 0
                                    ? 'border-gray-200 bg-white hover:border-emerald-500/60 hover:bg-emerald-50 dark:border-white/10 dark:bg-white/5 dark:hover:bg-emerald-500/10'
                                    : 'border-gray-200 bg-white/50 dark:border-white/10 dark:bg-white/5' }}">
                            <span class="flex items-center gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md text-sm font-bold
                                    {{ $section['activities_count'] > 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-gray-100 text-gray-400 dark:bg-white/5 dark:text-gray-500' }}">
                                    {{ strtoupper(substr($section['name'], 0, 2)) }}
                                </span>
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-bold text-gray-900 dark:text-gray-200">
                                        Sección {{ $section['name'] }}
                                    </span>
                                    <span class="block text-[11px] text-gray-500 dark:text-gray-400">
                                        {{ $section['activities_count'] }} actividad(es)
                                    </span>
                                </span>
                            </span>
                            @if ($section['activities_count'] > 0)
                                <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            @else
                                <span class="shrink-0 text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Sin actividades</span>
                            @endif
                        </button>
                    @empty
                        <div class="rounded-lg border border-amber-500/20 bg-amber-500/5 px-3 py-2.5 text-xs text-amber-700 dark:text-amber-300">
                            No hay otras secciones en el mismo grado.
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- Paso 2: selección de actividades --}}
            @if ($step === 2)
                <div class="space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ count($selectedActivityIds) }} de {{ count($sourceActivities) }} seleccionada(s).
                        </p>
                        <button type="button" wire:click="toggleAll"
                            class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 hover:text-emerald-500 dark:text-emerald-400">
                            {{ count($selectedActivityIds) === count($sourceActivities) ? 'Quitar todas' : 'Seleccionar todas' }}
                        </button>
                    </div>

                    <div class="max-h-[min(52vh,26rem)] space-y-2 overflow-y-auto pr-1">
                        @foreach ($sourceActivities as $activity)
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-white px-3 py-2.5 transition-colors hover:border-emerald-500/50 dark:border-white/10 dark:bg-white/5">
                                <input type="checkbox"
                                    wire:model.live="selectedActivityIds"
                                    value="{{ $activity['id'] }}"
                                    class="mt-0.5 h-4 w-4 shrink-0 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="min-w-0 flex-1">
                                    <span class="flex items-center gap-2">
                                        <span class="block truncate text-sm font-bold text-gray-900 dark:text-gray-200">
                                            {{ $activity['topic'] }}
                                        </span>
                                        @if ($activity['status'])
                                            <span class="shrink-0 rounded bg-emerald-500/10 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-emerald-600 dark:text-emerald-400">Aprobada</span>
                                        @else
                                            <span class="shrink-0 rounded bg-amber-500/10 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-amber-600 dark:text-amber-400">En revisión</span>
                                        @endif
                                    </span>
                                    @if ($activity['thematic'])
                                        <span class="mt-0.5 block truncate text-[11px] text-gray-500 dark:text-gray-400">
                                            {{ $activity['thematic'] }}
                                        </span>
                                    @endif
                                    <span class="mt-0.5 block text-[10px] text-gray-400 dark:text-gray-500">
                                        {{ $activity['finicial'] ? \Carbon\Carbon::parse($activity['finicial'])->format('d/m/Y') : '—' }}
                                        @if ($activity['ffinal'])
                                            – {{ \Carbon\Carbon::parse($activity['ffinal'])->format('d/m/Y') }}
                                        @endif
                                        · {{ $activity['achievements_count'] }} indicador(es)
                                    </span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Paso 3: vista previa --}}
            @if ($step === 3)
                <div class="space-y-3">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="rounded-md border border-white/10 bg-white/5 px-3 py-2">
                            <div class="text-[10px] uppercase tracking-wide text-gray-500">Actividades</div>
                            <div class="mt-1 text-lg font-extrabold text-gray-900 dark:text-white">{{ $preview['activities_count'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-md border border-white/10 bg-white/5 px-3 py-2">
                            <div class="text-[10px] uppercase tracking-wide text-gray-500">Indicadores</div>
                            <div class="mt-1 text-lg font-extrabold text-gray-900 dark:text-white">{{ $preview['achievements_count'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-md border border-white/10 bg-white/5 px-3 py-2">
                            <div class="text-[10px] uppercase tracking-wide text-gray-500">Desde</div>
                            <div class="mt-1 text-lg font-extrabold text-gray-900 dark:text-white">Sección {{ $preview['source_section'] ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="max-h-[min(46vh,22rem)] space-y-1.5 overflow-y-auto pr-1">
                        @foreach ($preview['activities'] ?? [] as $activity)
                            <div class="rounded-lg border border-gray-200 bg-white px-3 py-2 dark:border-white/10 dark:bg-white/5">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="truncate text-xs font-bold text-gray-800 dark:text-gray-100">{{ $activity['topic'] }}</span>
                                    <span class="shrink-0 text-[10px] text-gray-500 dark:text-gray-400">{{ $activity['achievements_count'] }} ind.</span>
                                </div>
                                @if ($activity['thematic'])
                                    <div class="mt-0.5 truncate text-[10px] text-gray-500 dark:text-gray-400">{{ $activity['thematic'] }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <p class="text-[11px] text-gray-500 dark:text-gray-400">
                        Se agregarán a las actividades actuales del área destino (no se reemplazan).
                    </p>
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="flex items-center justify-between gap-3">
                <button type="button" wire:click="close"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-600 transition-colors hover:bg-gray-50 dark:border-white/10 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10">
                    Cancelar
                </button>
                <div class="flex items-center gap-2">
                    @if ($step > 1)
                        <button type="button" wire:click="back"
                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-600 transition-colors hover:bg-gray-50 dark:border-white/10 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10">
                            Atrás
                        </button>
                    @endif

                    @if ($step === 2)
                        <button type="button" wire:click="goToPreview"
                            wire:loading.attr="disabled" wire:target="goToPreview"
                            class="rounded-lg bg-emerald-600 px-4 py-1.5 text-xs font-bold text-white transition-colors hover:bg-emerald-700 disabled:opacity-50">
                            Vista previa
                        </button>
                    @endif

                    @if ($step === 3)
                        <button type="button" wire:click="save"
                            wire:loading.attr="disabled" wire:target="save"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-1.5 text-xs font-bold text-white transition-colors hover:bg-emerald-700 disabled:opacity-50">
                            <span wire:loading.remove wire:target="save">Importar actividades</span>
                            <span wire:loading wire:target="save">Importando…</span>
                        </button>
                    @endif
                </div>
            </div>
        </x-slot>
    </x-modal-card>
</div>
