{{-- Paso 2: selección de actividades del universo filtrado --}}
<div class="space-y-3">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
        <div class="text-xs text-gray-600 dark:text-gray-300">
            <span class="font-bold">{{ $periodActivityCount }}</span> actividades dentro del periodo en {{ count($sectionTabs) }} secciones ·
            <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ count($selectedIds) }}</span> seleccionadas
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if($activitySectionId)
                <x-button flat sm label="Toda la sección" wire:click="toggleSection({{ $activitySectionId }})" />
            @endif
            <x-button flat sm label="Afines al eje" wire:click="selectAffinity" spinner="selectAffinity" />
            <x-button flat sm label="Completas del periodo" wire:click="selectComplete" spinner="selectComplete" />
            <x-button flat sm label="Limpiar" wire:click="clearSelection" />
        </div>
    </div>

    @if(count($sectionTabs) > 1)
        <div class="flex flex-wrap gap-1.5">
            @foreach($sectionTabs as $tab)
                <button type="button" wire:click="focusSection({{ $tab['id'] }})"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border text-[11px] font-semibold transition-colors
                        {{ $activitySectionId === $tab['id'] ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-700 dark:text-emerald-300' : 'border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900/40 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5' }}">
                    {{ $tab['short_label'] }}
                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold
                        {{ $tab['selected'] > 0 ? 'bg-emerald-600 text-white' : 'bg-gray-100 dark:bg-white/10 text-gray-500 dark:text-gray-400' }}">
                        {{ $tab['selected'] }}/{{ $tab['total'] }}
                    </span>
                </button>
            @endforeach
        </div>
    @endif

    @if($activitySectionId)
        @php
            $activeTab = collect($sectionTabs)->firstWhere('id', $activitySectionId);
        @endphp
        @if($activeTab)
            <div class="flex flex-wrap items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400">
                <span class="font-bold text-gray-700 dark:text-gray-200">{{ $activeTab['label'] }}</span>
                <x-badge flat slate :label="$activeTab['total'].' actividades'" />
            </div>
        @endif
    @endif

    <div class="rounded-lg border border-gray-200 dark:border-white/10 overflow-hidden">
        <div class="max-h-[46vh] overflow-y-auto">
            <table class="w-full text-left text-[11px]">
                <thead class="sticky top-0 z-10 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[9px]">
                    <tr>
                        <th class="px-2 py-2 w-8"></th>
                        <th class="px-2 py-2">Fecha</th>
                        <th class="px-2 py-2">Tema / actividad</th>
                        <th class="px-2 py-2">Asignatura</th>
                        <th class="px-2 py-2">Docente</th>
                        <th class="px-2 py-2 text-center">Aptitud</th>
                        <th class="px-2 py-2 text-center">Señales</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @forelse($visibleActivities as $activity)
                        @php
                            $selected = in_array($activity['id'], $selectedIds, true);
                            $levelColor = match ($activity['level']) {
                                'alta' => 'positive',
                                'media' => 'warning',
                                default => 'negative',
                            };
                        @endphp
                        <tr class="align-top transition-colors {{ $selected ? 'bg-emerald-50/70 dark:bg-emerald-500/10' : 'hover:bg-gray-50 dark:hover:bg-white/5' }}">
                            <td class="px-2 py-2">
                                <input type="checkbox" class="mt-0.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                    wire:click="toggleActivity({{ $activity['id'] }})" @checked($selected)>
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap font-semibold text-gray-700 dark:text-gray-200">
                                {{ $activity['date'] ? \Carbon\Carbon::parse($activity['date'])->format('d/m') : '—' }}
                                @if($activity['end'] && $activity['end'] !== $activity['date'])
                                    <span class="text-gray-400">→ {{ \Carbon\Carbon::parse($activity['end'])->format('d/m') }}</span>
                                @endif
                            </td>
                            <td class="px-2 py-2">
                                <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $activity['title'] }}</p>
                                @if(! empty($activity['reasons']))
                                    <p class="mt-0.5 text-[10px] text-gray-500 dark:text-gray-400">{{ implode(' · ', $activity['reasons']) }}</p>
                                @endif
                            </td>
                            <td class="px-2 py-2 text-gray-600 dark:text-gray-300">{{ $activity['subject'] }}</td>
                            <td class="px-2 py-2 text-gray-600 dark:text-gray-300">{{ $activity['professor'] ?: '—' }}</td>
                            <td class="px-2 py-2 text-center">
                                <x-badge flat :color="$levelColor" :label="$activity['score'].'/100'" />
                            </td>
                            <td class="px-2 py-2">
                                <div class="flex flex-wrap justify-center gap-1">
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $activity['complete'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-gray-100 text-gray-400 dark:bg-white/5 dark:text-gray-500' }}" title="Inicio, desarrollo y cierre">3 momentos</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $activity['has_evaluation'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-gray-100 text-gray-400 dark:bg-white/5 dark:text-gray-500' }}" title="Actividad evaluativa">Eval.</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $activity['has_indicators'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-gray-100 text-gray-400 dark:bg-white/5 dark:text-gray-500' }}" title="Indicadores de logro">{{ $activity['indicator_count'] }} ind.</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $activity['has_ods'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-gray-100 text-gray-400 dark:bg-white/5 dark:text-gray-500' }}" title="Mención de ODS">ODS</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-xs text-gray-500 dark:text-gray-400">
                                No hay actividades de esta sección dentro del periodo seleccionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(! empty($warnings))
        <div class="rounded-lg border border-amber-200 dark:border-amber-500/20 bg-amber-50 dark:bg-amber-500/10 px-3 py-2 text-[11px] text-amber-800 dark:text-amber-200">
            <p class="font-bold">Advertencias del universo</p>
            <ul class="mt-1 list-disc pl-4 space-y-0.5">
                @foreach($warnings as $warning)
                    <li>{{ $warning }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <p class="text-[11px] text-gray-500 dark:text-gray-400">
        La puntuación ordena y explica la aptitud; puedes seleccionar actividades del universo filtrado que estén dentro del periodo seleccionado.
    </p>
</div>
