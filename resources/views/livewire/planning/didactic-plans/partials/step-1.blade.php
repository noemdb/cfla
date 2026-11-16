{{-- Paso 1: contexto, secciones y periodo del plan --}}
<div class="space-y-4">
    <div class="rounded-lg border border-emerald-200 dark:border-emerald-500/20 bg-emerald-50 dark:bg-emerald-500/10 px-4 py-3 text-xs text-emerald-800 dark:text-emerald-200">
        <p class="font-bold">Universo filtrado, sin búsqueda global</p>
        <p class="mt-1">El plan se construye exactamente con las actividades que cumplen los filtros activos de la página. Si necesitas otro universo, cierra el asistente y ajusta los filtros.</p>
    </div>

    <div>
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Filtros aplicados</p>
        <div class="flex flex-wrap gap-1.5">
            @forelse($filterChips as $chip)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-[11px] font-semibold text-gray-600 dark:text-gray-300">
                    {{ $chip }}
                </span>
            @empty
                <span class="text-xs text-gray-500 dark:text-gray-400">Sin filtros: se usará todo el universo visible de planificación.</span>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <div class="flex items-center justify-between gap-2 mb-1.5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                    Secciones del plan
                </p>
                @if(count($sectionOptions) > 0)
                    <div class="flex items-center gap-1">
                        <button type="button" wire:click="selectAllSections"
                            class="px-2 py-0.5 rounded text-[10px] font-bold text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                            Todas
                        </button>
                        <button type="button" wire:click="clearSections"
                            class="px-2 py-0.5 rounded text-[10px] font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5">
                            Ninguna
                        </button>
                    </div>
                @endif
            </div>

            @if(count($sectionOptions) > 0)
                @php
                    $groupedSections = collect($sectionOptions)->groupBy(
                        fn (array $option): string => $option['pestudio_name'].'|'.$option['grado_name']
                    );
                @endphp

                <div class="rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 max-h-60 overflow-y-auto divide-y divide-gray-100 dark:divide-white/5">
                    @foreach($groupedSections as $groupKey => $options)
                        @php
                            [$planName, $gradoName] = array_pad(explode('|', (string) $groupKey, 2), 2, '');
                        @endphp
                        <div class="px-3 pt-2.5">
                            <p class="text-[9px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                                {{ $gradoName }} · {{ $planName }}
                            </p>
                        </div>
                        <div class="px-3 pb-2 pt-1 flex flex-wrap gap-1.5">
                            @foreach($options as $option)
                                <label class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border cursor-pointer transition-colors
                                    {{ in_array($option['id'], $seccionIds, true) ? 'border-emerald-500/40 bg-emerald-500/10' : 'border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900/40' }}">
                                    <input type="checkbox" value="{{ $option['id'] }}" wire:model.live="seccionIds"
                                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-[11px] font-semibold text-gray-700 dark:text-gray-200">
                                        {{ $option['short_label'] }}
                                    </span>
                                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500">
                                        ({{ $option['activity_count'] }})
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                    {{ count($seccionIds) }} de {{ count($sectionOptions) }} secciones seleccionadas ·
                    se generará un plan independiente por cada una.
                </p>
            @else
                <div class="px-3 py-2.5 rounded-lg border border-red-200 dark:border-red-500/20 bg-red-50 dark:bg-red-500/10 text-xs text-red-700 dark:text-red-300">
                    No hay secciones con actividades para los filtros activos.
                </div>
            @endif
        </div>

        <div>
            <div class="flex items-center justify-between gap-2 mb-1.5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Rango disponible</p>
                @if($this->rangeCustomized())
                    <button type="button" wire:click="resetDerivedPeriod"
                        class="px-2 py-0.5 rounded text-[10px] font-bold text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                        Usar rango derivado
                    </button>
                @endif
            </div>
            @if($rangeStart && $rangeEnd)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <x-input type="date" label="Desde" wire:model.blur="rangeStart" />
                    <x-input type="date" label="Hasta" wire:model.blur="rangeEnd" />
                </div>
                <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                    @if($this->rangeCustomized())
                        Ajustado manualmente.
                        @if($derivedRangeStart && $derivedRangeEnd)
                            Derivado: {{ \Carbon\Carbon::parse($derivedRangeStart)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($derivedRangeEnd)->format('d/m/Y') }}.
                        @endif
                    @else
                        {{ $rangeOrigin === 'lapso' ? 'Tomado del momento/lapso seleccionado.' : 'Tomado de las fechas de las actividades filtradas.' }}
                        Puedes cambiar las fechas para ampliar o reducir el rango.
                    @endif
                </p>
            @else
                <div class="px-3 py-2.5 rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-xs text-gray-500 dark:text-gray-400">
                    Aún no se pudo derivar el periodo.
                </div>
            @endif

            @if($rangeStart && $rangeEnd)
                <div class="mt-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Periodo del plan</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-input type="date" label="Desde" wire:model.live="periodStart"
                            min="{{ $rangeStart }}" max="{{ $rangeEnd }}" />
                        <x-input type="date" label="Hasta" wire:model.live="periodEnd"
                            min="{{ $rangeStart }}" max="{{ $rangeEnd }}" />
                    </div>
                    <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                        Elige fechas dentro del rango disponible. El periodo puede abarcar los días que necesites.
                    </p>
                </div>
            @endif
        </div>
    </div>

    @if(count($weekOptions) === 1)
        <div class="px-3 py-2 rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-xs text-gray-600 dark:text-gray-300">
            <span class="font-bold">Periodo sugerido:</span> {{ $weekOptions[0]['label'] }} ({{ $weekOptions[0]['days'] }} días). Puedes ajustarlo con las fechas.
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-input label="Eje articulador" wire:model.live="axis" placeholder="Alimentación, cultura e identidad" />
        <x-input label="Proyecto / nombre del plan" wire:model.live="projectTitle" placeholder="Ej.: Sabores que nos identifican" />
    </div>

    <x-textarea label="Objetivo general del plan" wire:model.live="objective" rows="3"
        placeholder="Describe el propósito formativo de la secuencia." />

    <div class="flex flex-wrap items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400">
        <x-badge flat negative label="{{ $filteredCount }} actividades filtradas" />
        @if(! empty($filters['pestudio_id']))
            <x-badge flat slate label="Plan de estudio #{{ $filters['pestudio_id'] }}" />
        @endif
        @if(! empty($filters['grado_id']))
            <x-badge flat slate label="Grado #{{ $filters['grado_id'] }}" />
        @endif
        @if($lapsoId)
            <x-badge flat slate label="Momento #{{ $lapsoId }}" />
        @endif
    </div>
</div>
