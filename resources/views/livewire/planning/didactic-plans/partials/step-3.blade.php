{{-- Paso 3: consolidación y edición de sesiones por sección --}}
<div class="space-y-3">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
        <div class="text-xs text-gray-600 dark:text-gray-300">
            <span class="font-bold">{{ count($sessions) }}</span> sesiones en {{ count($sessionGroups) }} secciones ·
            periodo {{ \Carbon\Carbon::parse($periodStart)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($periodEnd)->format('d/m/Y') }}
        </div>
    </div>

    @if(! empty($warnings))
        <div class="rounded-lg border border-amber-200 dark:border-amber-500/20 bg-amber-50 dark:bg-amber-500/10 px-3 py-2 text-[11px] text-amber-800 dark:text-amber-200">
            <p class="font-bold">Revisa antes de aprobar</p>
            <ul class="mt-1 list-disc pl-4 space-y-0.5">
                @foreach($warnings as $warning)
                    <li>{{ $warning }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @forelse($sessionGroups as $group)
        <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50/60 dark:bg-white/5 p-2 space-y-2">
            <div class="flex flex-wrap items-center justify-between gap-2 px-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-600 text-white text-[10px] font-bold">
                        {{ $loop->iteration }}
                    </span>
                    <span class="text-xs font-extrabold text-gray-700 dark:text-gray-200">{{ $group['label'] }}</span>
                    <x-badge flat slate :label="count($group['sessions']).' sesiones'" />
                </div>
                <x-button flat sm label="Agregar sesión" wire:click="addSession({{ $group['seccion_id'] }})" icon="plus" />
            </div>

            @foreach($group['sessions'] as $entry)
                @php
                    $index = $entry['index'];
                    $session = $entry['session'];
                @endphp
                <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900/60"
                    wire:key="session-{{ $session['uid'] ?? $index }}">
                    <div class="flex flex-wrap items-center justify-between gap-2 px-3 py-2 border-b border-gray-100 dark:border-white/5 bg-white dark:bg-white/5 rounded-t-xl">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-700 dark:bg-gray-600 text-white text-[10px] font-bold">{{ $loop->iteration }}</span>
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-200">Sesión de clase</span>
                            @if(! empty($session['source_activity_id']))
                                <x-badge flat slate label="Origen: actividad #{{ $session['source_activity_id'] }}" />
                            @endif
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" wire:click="duplicateSession({{ $index }})"
                                class="px-2 py-1 rounded text-[10px] font-bold text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10">Duplicar</button>
                            <button type="button" wire:click="removeSession({{ $index }})"
                                class="px-2 py-1 rounded text-[10px] font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10">Quitar</button>
                        </div>
                    </div>

                    <div class="p-3 space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <x-input type="date" label="Fecha" wire:model.live="sessions.{{ $index }}.date"
                                min="{{ $periodStart }}" max="{{ $periodEnd }}" />
                            <div class="md:col-span-3">
                                <x-input label="Título / tema de la sesión" wire:model.live="sessions.{{ $index }}.title"
                                    placeholder="Ej.: Los sabores de mi familia" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <x-input label="Temática" wire:model.live="sessions.{{ $index }}.thematic" placeholder="Alimentación y cultura" />
                            <x-input label="ODS (separados por coma)" wire:model.live="sessions.{{ $index }}.ods_text" placeholder="ODS 2, ODS 12" />
                        </div>

                        <x-textarea label="Aprendizaje esperado" wire:model.live="sessions.{{ $index }}.objective" rows="2" />

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <x-textarea label="Inicio" wire:model.live="sessions.{{ $index }}.start" rows="4"
                                placeholder="Activación de saberes, pregunta detonadora…" />
                            <x-textarea label="Desarrollo" wire:model.live="sessions.{{ $index }}.development" rows="4"
                                placeholder="Actividades centrales, trabajo en equipo…" />
                            <x-textarea label="Cierre" wire:model.live="sessions.{{ $index }}.close" rows="4"
                                placeholder="Síntesis, metacognición, tarea…" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <x-input label="Instrumento de evaluación" wire:model.live="sessions.{{ $index }}.evaluation_instrument"
                                placeholder="Lista de cotejo, rúbrica…" />
                            <div class="md:col-span-2">
                                <x-input label="Actividad evaluativa" wire:model.live="sessions.{{ $index }}.evaluation_description"
                                    placeholder="Describe la evidencia o producto." />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <x-textarea label="Referentes y recursos" wire:model.live="sessions.{{ $index }}.resources" rows="2" />
                            <x-textarea label="Observaciones" wire:model.live="sessions.{{ $index }}.observations" rows="2" />
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Indicadores de logro</p>
                                <button type="button" wire:click="addIndicator({{ $index }})"
                                    class="px-2 py-1 rounded text-[10px] font-bold text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10">+ Indicador</button>
                            </div>

                            @forelse(($session['indicators'] ?? []) as $indicatorIndex => $indicator)
                                <div class="mt-1.5 flex items-center gap-2">
                                    <div class="flex-1">
                                        <x-input wire:model.live="sessions.{{ $index }}.indicators.{{ $indicatorIndex }}.name"
                                            placeholder="Indicador de logro" />
                                    </div>
                                    <div class="w-24">
                                        <x-input type="number" step="0.01" min="0" wire:model.live="sessions.{{ $index }}.indicators.{{ $indicatorIndex }}.weighting"
                                            placeholder="Peso" />
                                    </div>
                                    <button type="button" wire:click="removeIndicator({{ $index }}, {{ $indicatorIndex }})"
                                        class="px-2 py-2 rounded text-[11px] font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10">✕</button>
                                </div>
                            @empty
                                <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">Sin indicadores. Agrega uno o justifica la ausencia en las notas del plan (paso 4).</p>
                            @endforelse
                        </div>

                        <label class="inline-flex items-center gap-2 text-[11px] font-semibold text-gray-600 dark:text-gray-300">
                            <input type="checkbox" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                wire:model.live="sessions.{{ $index }}.is_non_teaching">
                            Día no lectivo (jornada especial)
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    @empty
        <div class="rounded-lg border border-red-200 dark:border-red-500/20 bg-red-50 dark:bg-red-500/10 px-3 py-3 text-xs text-red-700 dark:text-red-300">
            No hay sesiones. Vuelve al paso anterior y selecciona actividades, o agrega una sesión manual.
        </div>
    @endforelse
</div>
