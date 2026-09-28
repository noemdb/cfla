{{-- Paso 2: Seleccionar (compartido: manager de campoConocimiento y wizard de creación)
     Parámetros opcionales del @include:
       $primaryAction (default 'assignSelectedSubjects')
       $primaryLabel  (default 'Adscribir Seleccionadas')
       $showDataBack  (default false: botón extra "← Datos" hacia prevCreateStep) --}}
@php
    $selectPrimaryAction = $primaryAction ?? 'assignSelectedSubjects';
    $selectPrimaryLabel = $primaryLabel ?? 'Adscribir Seleccionadas';
    $selectShowDataBack = $showDataBack ?? false;
    $associatedPensumIds = $wizardOnlyUnassigned
        ? \App\Models\app\Academy\CampoConocimiento::whereNotNull('pensum_id')->pluck('pensum_id')->map(fn ($id) => (int) $id)->all()
        : [];
    // Mapa pensum_id => adscripciones (con su área) para mostrar a qué
    // AreaConocimiento pertenece cada pensum. Una sola query por render.
    $visiblePensumIds = $this->availableSubjects->take($this->visibleCount)->flatMap(fn ($a) => $a->pensums->pluck('id'))->unique()->values();
    $pensumAreasMap = \App\Models\app\Academy\CampoConocimiento::whereIn('pensum_id', $visiblePensumIds)
        ->with('area_conocimiento:id,name,code,code_sm')
        ->get()
        ->groupBy('pensum_id');
@endphp
<div class="bg-white/5 border border-white/10 rounded-lg p-4">
    <div class="flex items-center justify-between mb-3">
        <h4 class="text-xs font-bold text-gray-300 flex items-center gap-2">
            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Seleccionar Asignaturas
            @if($wizardOnlyUnassigned)
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold leading-none bg-amber-500/10 text-amber-300 border border-amber-500/20">solo sin área</span>
            @endif
            @if($creatingArea)
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold leading-none bg-blue-500/10 text-blue-300 border border-blue-500/20" title="Área en creación">
                    {{ \Illuminate\Support\Str::limit($name ?: 'Nueva área', 24) }}{{ $code ? ' · '.$code : '' }}{{ ($pestudio_id && isset($pestudios[$pestudio_id])) ? ' · '.$pestudios[$pestudio_id] : '' }}
                </span>
            @endif
        </h4>
        <div class="flex items-center gap-1.5">
            <button type="button" wire:click="toggleOnlyUnassigned"
                class="px-2 py-1 text-[10px] font-bold rounded-lg border transition-all duration-200 {{ $wizardOnlyUnassigned ? 'bg-amber-500/15 text-amber-300 border-amber-500/30' : 'bg-white/5 text-gray-400 hover:text-white border-white/5' }}"
                title="Mostrar solo pensums no asociados a ninguna área de conocimiento">
                Sin área
            </button>
            <button type="button" wire:click="selectAllAvailable" title="Selecciona solo las visibles en pantalla (usa Cargar más para ver el resto)"
                class="px-2 py-1 text-[10px] font-bold bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20 rounded-lg transition-all duration-200">
                Seleccionar Visibles
            </button>
            <button type="button" wire:click="deselectAll"
                class="px-2 py-1 text-[10px] font-bold bg-gray-500/10 text-gray-400 hover:bg-gray-500/20 border border-white/5 rounded-lg transition-all duration-200">
                Deseleccionar
            </button>
        </div>
    </div>

    {{-- Búsqueda en el listado --}}
    <div class="mb-3">
        <div class="relative">
            <svg class="w-4 h-4 text-gray-500 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text"
                wire:model.live.debounce.300ms="wizardSearch"
                placeholder="Buscar por nombre o código..."
                class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
            @if($wizardSearch)
                <button type="button" wire:click="wizardSearch = ''"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors duration-200"
                    title="Limpiar búsqueda">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- Grid de asignaturas disponibles (sin agrupar) --}}
    <div class="max-h-[360px] overflow-y-auto pr-1">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
            @forelse($this->availableSubjects->take($this->visibleCount) as $asignatura)
                        @php
                            $isSelected = array_key_exists($asignatura->id, $selectedSubjects);
                            $selectedPensumId = $selectedSubjects[$asignatura->id] ?? null;
                            $colorKey = \App\Models\app\Academy\Asignatura::colorKey($asignatura->name);
                            // Pensums activos con grado activo, priorizando el plan seleccionado.
                            $pensums = $asignatura->pensums
                                ->where('status_active', true)
                                ->filter(fn ($p) => $p->grado?->status_active === 'true');
                            if ($wizardFilterPestudio) {
                                $filtered = $pensums->where('pestudio_id', (int) $wizardFilterPestudio);
                                if ($filtered->isNotEmpty()) { $pensums = $filtered; }
                            }
                            $pensums = $pensums->values();
                            // Opción "solo pensums sin área": descarta los ya adscritos.
                            if ($wizardOnlyUnassigned) {
                                $pensums = $pensums->whereNotIn('id', $associatedPensumIds)->values();
                            }

                            // Resumen agregado: grados y secciones únicos a partir de los pensums.
                            $gradosUnicos = $pensums->pluck('grado')->filter()->unique('id');
                            $seccionesUnicas = $gradosUnicos->flatMap(fn ($g) => $g->seccions ?? collect())->unique('id');
                            $pensumCount = $pensums->count();
                            $pensumSummary = $gradosUnicos->pluck('code')->filter()->join(', ') ?: $gradosUnicos->pluck('name')->filter()->join(', ');
                            $seccionSummary = $seccionesUnicas->pluck('name')->join(', ') ?: '—';

                            $hiddenCount = max(0, $pensums->count() - 2);
                            $tipLines = $pensums->map(function ($pensum) use ($pensumAreasMap) {
                                $grado = $pensum->grado;
                                $pestudio = $pensum->pestudio ?? $grado?->pestudio;
                                $psName = $pestudio?->name ?? '?';
                                $psShort = match (true) {
                                    str_contains($psName, 'CIENCIA') && str_contains($psName, 'TECNOLOG') => 'MG-CT',
                                    str_contains($psName, 'MEDIA GENERAL') => 'MG',
                                    str_contains($psName, 'PRIMARIA') => 'PRI',
                                    str_contains($psName, 'INICIAL') => 'INI',
                                    default => \Illuminate\Support\Str::limit($psName, 10),
                                };
                                $secciones = $grado?->seccions?->pluck('name')->join(', ') ?? '—';
                                $lineAreas = $pensumAreasMap->get($pensum->id, collect())
                                    ->map(fn ($c) => $c->area_conocimiento?->code_sm)->filter()->unique()->join(', ');
                                return "{$psShort} · ".($grado?->code ?? $grado?->name ?? '—')." · Secc {$secciones}".($lineAreas !== '' ? " → {$lineAreas}" : ' · Sin área');
                            });

                            // Metadatos académicos.
                            $horasT = $asignatura->hour_t_week;
                            $horasP = $asignatura->hour_p_week;
                            $creditos = $asignatura->unid_credit;
                            $escala = $asignatura->tescala;

                            // Distinción sutil por Grado (color Tailwind-compatible)
                            $primaryGrado = $pensums->first()?->grado;
                            $gradoClasses = $primaryGrado?->tailwind_classes ?? [
                                'bar' => 'bg-slate-500',
                                'badge' => 'bg-slate-500/10 text-slate-300 border border-slate-500/20',
                                'dot' => 'bg-slate-500',
                                'border' => 'border-slate-500/30',
                                'ring' => 'ring-slate-500/20',
                                'subtleBg' => 'bg-slate-500/[0.04]',
                            ];
                        @endphp
                        <div x-data="{ showTip: false, top: 0, left: 0 }"
                             @mouseenter="showTip = true; const r = $el.getBoundingClientRect(); top = r.top; left = r.left + r.width/2"
                             @mouseleave="showTip = false"
                             class="relative">
                            <button type="button"
                                wire:click="toggleSubject({{ $asignatura->id }})"
                                class="relative h-56 w-full flex flex-col items-start text-left rounded-xl border p-3 overflow-hidden transition-all duration-200 group
                                    {{ $isSelected
                                        ? 'bg-emerald-500/15 border-emerald-500/50 ring-1 ring-emerald-500/40'
                                        : 'bg-white/[0.03] border-white/10 hover:bg-white/[0.06] hover:border-white/25' }}">
                                @if($primaryGrado)
                                    <span class="absolute top-0 inset-x-0 h-0.5 {{ $gradoClasses['bar'] }} opacity-60 pointer-events-none"></span>
                                    <span class="absolute inset-0 rounded-xl {{ $gradoClasses['subtleBg'] }} pointer-events-none"></span>
                                @endif
                                <span class="absolute top-2 right-2 w-5 h-5 rounded-full flex items-center justify-center transition-all duration-200
                                    {{ $isSelected ? 'bg-emerald-500 text-white' : 'bg-white/5 text-transparent border border-white/10' }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </span>

                                <span class="inline-flex items-center gap-1.5 pr-6 flex-wrap">
                                    <span class="w-2 h-2 rounded-full {{ $this->materiaColorClass($colorKey) }}"></span>
                                    <span class="text-[10px] font-mono text-gray-400">{{ $asignatura->code }}</span>
                                    @if($primaryGrado)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold leading-none {{ $gradoClasses['badge'] }}" title="{{ $primaryGrado->full_name }}">{{ $primaryGrado->code_sm }}</span>
                                    @endif
                                    @if($escala)
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-white/5 text-gray-500 border border-white/5">Esc {{ $escala }}</span>
                                    @endif
                                </span>

                                <span class="text-[13px] font-semibold leading-snug line-clamp-2 mt-0.5 {{ $isSelected ? 'text-emerald-200' : 'text-gray-200' }}">
                                    {{ $asignatura->name }}
                                </span>

                                @if($isSelected && $pensums->count() > 1)
                                    <label class="mt-2 block w-full">
                                        <span class="block text-[8px] font-bold uppercase tracking-wider text-gray-600 mb-0.5">Pensum / Grado</span>
                                        <select
                                            wire:change="selectPensum({{ $asignatura->id }}, $event.target.value)"
                                            class="w-full bg-white/5 border border-emerald-500/30 text-gray-200 rounded-md px-1.5 py-1 text-[10px] focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/40 outline-none transition-all">
                                            @foreach($pensums as $pensum)
                                                @php
                                                    $grado = $pensum->grado;
                                                    $pestudio = $pensum->pestudio ?? $grado?->pestudio;
                                                    $psName = $pestudio?->name ?? '?';
                                                    $psShort = match (true) {
                                                        str_contains($psName, 'CIENCIA') && str_contains($psName, 'TECNOLOG') => 'MG-CT',
                                                        str_contains($psName, 'MEDIA GENERAL') => 'MG',
                                                        str_contains($psName, 'PRIMARIA') => 'PRI',
                                                        str_contains($psName, 'INICIAL') => 'INI',
                                                        default => \Illuminate\Support\Str::limit($psName, 10),
                                                    };
                                                    $optAreas = $pensumAreasMap->get($pensum->id, collect())
                                                        ->map(fn ($c) => $c->area_conocimiento?->code_sm)->filter()->unique()->join(', ');
                                                @endphp
                                                <option value="{{ $pensum->id }}" {{ (int) $selectedPensumId === (int) $pensum->id ? 'selected' : '' }}>
                                                    {{ $psShort }} · {{ $grado?->code ?? $grado?->name ?? '—' }}{{ $optAreas !== '' ? ' → '.$optAreas : ' (sin área)' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>
                                @endif

                                {{-- Metadatos académicos: créditos · horas T/P --}}
                                <span class="mt-2 grid grid-cols-3 gap-1 text-center">
                                    @if($creditos !== null)
                                        <span class="flex flex-col rounded-md bg-white/[0.03] border border-white/5 px-1 py-1">
                                            <span class="text-[8px] uppercase tracking-wider text-gray-600">Créditos</span>
                                            <span class="text-[10px] font-bold text-gray-300">{{ $creditos }}</span>
                                        </span>
                                    @endif
                                    @if($horasT !== null)
                                        <span class="flex flex-col rounded-md bg-white/[0.03] border border-white/5 px-1 py-1">
                                            <span class="text-[8px] uppercase tracking-wider text-gray-600">H Teóricas</span>
                                            <span class="text-[10px] font-bold text-gray-300">{{ $horasT }}</span>
                                        </span>
                                    @endif
                                    @if($horasP !== null)
                                        <span class="flex flex-col rounded-md bg-white/[0.03] border border-white/5 px-1 py-1">
                                            <span class="text-[8px] uppercase tracking-wider text-gray-600">H Prácticas</span>
                                            <span class="text-[10px] font-bold text-gray-300">{{ $horasP }}</span>
                                        </span>
                                    @endif
                                </span>

                                {{-- Asociación plan de estudio · grado · sección (según pensum activo) --}}
                                <span class="mt-auto pt-1.5 border-t border-white/5 w-full space-y-0.5">
                                    @forelse($pensums->take(2) as $pensum)
                                        @php
                                            $grado = $pensum->grado;
                                            $pestudio = $pensum->pestudio ?? $grado?->pestudio;
                                            $psName = $pestudio?->name ?? '?';
                                            $psShort = match (true) {
                                                str_contains($psName, 'CIENCIA') && str_contains($psName, 'TECNOLOG') => 'MG-CT',
                                                str_contains($psName, 'MEDIA GENERAL') => 'MG',
                                                str_contains($psName, 'PRIMARIA') => 'PRI',
                                                str_contains($psName, 'INICIAL') => 'INI',
                                                default => \Illuminate\Support\Str::limit($psName, 10),
                                            };
                                            $secciones = $grado?->seccions?->pluck('name')->join(', ') ?? '—';
                                            $areasForPensum = $pensumAreasMap->get($pensum->id, collect());
                                            $areasCodes = $areasForPensum->map(fn ($c) => $c->area_conocimiento?->code_sm)->filter()->unique()->join(', ');
                                            $areasNames = $areasForPensum->map(fn ($c) => $c->area_conocimiento?->name)->filter()->unique()->join(', ');
                                        @endphp
                                        <span class="block text-[9px] leading-tight text-gray-500 truncate">
                                            <span class="font-bold text-gray-400">{{ $psShort }}</span>
                                            <span class="text-gray-600"> · </span>
                                            <span>{{ $grado?->code ?? $grado?->name ?? '—' }}</span>
                                            <span class="text-gray-600"> · </span>
                                            <span>Secc {{ $secciones }}</span>
                                            @if($areasCodes !== '')
                                                <span class="text-gray-600"> · </span>
                                                <span class="font-bold text-amber-400/90" title="Adscrito en: {{ $areasNames }}">→ {{ $areasCodes }}</span>
                                            @else
                                                <span class="text-gray-600"> · Sin área</span>
                                            @endif
                                        </span>
                                    @empty
                                        <span class="block text-[9px] text-gray-600">Sin pensum activo</span>
                                    @endforelse
                                    @if($hiddenCount > 0)
                                        <span class="block text-[9px] text-gray-600">+{{ $hiddenCount }} más</span>
                                    @endif
                                    @if($pensumCount > 0 && $pensumSummary)
                                        <span class="block text-[9px] text-gray-600 truncate">
                                            Grados: <span class="text-gray-400">{{ $pensumSummary }}</span> · Secc: <span class="text-gray-400">{{ $seccionSummary }}</span>
                                        </span>
                                    @endif
                                </span>
                            </button>

                            {{-- Tooltip: detalle completo de la asociación (#8) --}}
                            <div x-show="showTip" x-cloak
                                 class="fixed z-[80] w-72 bg-gray-800 border border-white/15 rounded-xl shadow-2xl p-3 pointer-events-none"
                                 :style="`top:${top}px; left:${left}px; transform: translate(-50%, calc(-100% - 8px));`">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">{{ $asignatura->name }}</p>
                                @forelse($tipLines as $line)
                                    <p class="text-[11px] text-gray-300 leading-relaxed truncate">{{ $line }}</p>
                                @empty
                                    <p class="text-[11px] text-gray-500">Sin pensum activo</p>
                                @endforelse
                            </div>
                        </div>
            @empty
            <div class="py-10 text-center">
                <svg class="w-10 h-10 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="text-gray-500 text-xs">
                    {{ $wizardSearch
                        ? 'No hay asignaturas que coincidan con la búsqueda "' . $wizardSearch . '".'
                        : 'No hay asignaturas disponibles para adscribir.' }}
                </p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Cargar más (#4) --}}
    @if($this->remainingSubjectsCount > 0)
        <div class="flex justify-center mt-3">
            <button type="button" wire:click="loadMore"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-[11px] font-bold bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg border border-white/10 transition-all duration-200">
                Cargar más ({{ $this->remainingSubjectsCount }} restantes)
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>
    @endif

    {{-- Acciones paso 2 --}}
    <div class="flex items-center justify-between mt-3">
        <div class="flex items-center gap-2">
            @if($selectShowDataBack)
                <button type="button" wire:click="prevCreateStep"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg border border-white/5 transition-all duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Datos
                </button>
            @endif
            <button type="button" wire:click="prevStepWizard"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg border border-white/5 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Atrás
            </button>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[11px] text-gray-500">
                <strong class="text-emerald-400">{{ count($selectedSubjects) }}</strong> seleccionada(s) de <strong class="text-gray-300">{{ $this->availableSubjects->count() }}</strong> disponible(s)
            </span>
            <button type="button" wire:click="{{ $selectPrimaryAction }}" wire:loading.attr="disabled"
                class="inline-flex items-center gap-1.5 px-4 py-1.5 text-[11px] font-bold bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-200 {{ empty($selectedSubjects) ? 'opacity-40 cursor-not-allowed' : '' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ $selectPrimaryLabel }} ({{ count($selectedSubjects) }})
            </button>
        </div>
    </div>
</div>
