<div>
    {{-- ─── MODAL ─── --}}
    @if($showModal)
        <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="competencias-modal">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="close"></div>

            <div class="relative min-h-screen flex items-start justify-center p-4 pt-8 pb-24">
                <div class="relative w-[95vw] max-w-6xl bg-gray-900 border border-white/10 rounded-lg shadow-2xl overflow-hidden"
                     @click.away="$wire.close()">

                    {{-- ─── HEADER ─── --}}
                    <div class="px-8 py-5 border-b border-white/5 flex items-center justify-between bg-amber-500/5 shrink-0">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 bg-amber-500/10 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-white uppercase tracking-wider truncate">
                                    Competencias e Indicadores
                                </h3>
                                <p class="text-[11px] text-gray-400 mt-0.5 truncate">
                                    {{ $asignaturaName }}
                                    @if($pestudioName)
                                        <span class="text-gray-600"> · {{ $pestudioName }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <button wire:click="close"
                            class="inline-flex items-center justify-center min-w-[44px] min-h-[44px] w-8 h-8 rounded-lg bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10 transition-all duration-200 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- ─── CONTENT ─── --}}
                    <div class="p-4 sm:p-6 lg:p-8 max-h-[80vh] overflow-y-auto">

                        @php
                            $uniqueReferents = collect($competencias)->pluck('referent')->filter()->unique('id')->values();

                            $filtered = collect($competencias)->filter(function($c) use ($search, $filterReferentId, $onlyWithIndicators) {
                                $matchSearch = empty($search)
                                    || str_contains(strtolower($c['name'] ?? ''), strtolower($search))
                                    || str_contains(strtolower($c['description'] ?? ''), strtolower($search));

                                $matchReferent = empty($filterReferentId)
                                    || ($c['referent']['id'] ?? '') == $filterReferentId;

                                $matchIndicators = !$onlyWithIndicators || count($c['indicators'] ?? []) > 0;

                                return $matchSearch && $matchReferent && $matchIndicators;
                            })->values();
                        @endphp

                        {{-- ─── FILTERS ─── --}}
                        <div class="mb-5 space-y-3">
                            {{-- Search + Toggles --}}
                            <div class="flex items-center gap-3">
                                {{-- Search --}}
                                <div class="relative flex-1 max-w-xs">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input type="text" wire:model.live="search" placeholder="Buscar competencia..."
                                        class="w-full bg-gray-800/50 border border-white/10 rounded-lg pl-9 pr-3 py-1.5 text-[11px] text-gray-300 placeholder-gray-600 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
                                    @if($search)
                                        <button wire:click="$set('search', '')" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>

                                {{-- Only with indicators toggle --}}
                                <button wire:click="$toggle('onlyWithIndicators')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-[10px] font-bold transition-all duration-200
                                        {{ $onlyWithIndicators ? 'bg-blue-500/10 text-blue-400 border-blue-500/30' : 'bg-gray-800/50 text-gray-500 border-white/5 hover:text-gray-300' }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Con indicadores
                                </button>
                            </div>

                            {{-- Referent pills --}}
                            @if($uniqueReferents->count() > 1)
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <button wire:click="$set('filterReferentId', '')"
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all duration-200
                                            {{ empty($filterReferentId) ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-gray-800/50 text-gray-500 border-white/5 hover:text-gray-300' }}">
                                        Todos
                                    </button>
                                    @foreach($uniqueReferents as $ref)
                                        <button wire:click="$set('filterReferentId', '{{ $ref['id'] }}')"
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all duration-200
                                                {{ (int) $filterReferentId === (int) $ref['id'] ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30' : 'bg-gray-800/50 text-gray-500 border-white/5 hover:text-gray-300' }}">
                                            {{ $ref['code'] ?? $ref['name'] }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- ─── COMPETENCIAS LIST ─── --}}
                        @php $filteredCount = $filtered->count(); @endphp

                        @forelse($filtered as $competencia)
                            @php
                                $referent = $competencia['referent'] ?? null;
                                $indicators = $competencia['indicators'] ?? [];
                            @endphp

                            {{-- Referent Group --}}
                            @if($referent && !$filterReferentId)
                                <div class="mb-2">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 text-[10px] font-bold">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        @if($referent['code'])
                                            <span>{{ $referent['code'] }}</span>
                                            <span class="text-indigo-500">·</span>
                                        @endif
                                        <span>{{ $referent['name'] ?? 'Referente' }}</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Competency Card --}}
                            <div class="bg-gray-800/30 border border-white/5 rounded-lg mb-2 last:mb-0 overflow-hidden">
                                <div class="px-4 py-2 border-b border-white/5">
                                    <div class="flex items-start gap-3">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-500/10 text-emerald-400 text-[10px] font-bold shrink-0 mt-0.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-white">{{ $competencia['name'] }}</p>
                                            @if(!empty($competencia['description']))
                                                <p class="text-[11px] text-gray-400 mt-1">{{ $competencia['description'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Indicators --}}
                                @if(count($indicators) > 0)
                                    <div class="divide-y divide-white/5">
                                        @foreach($indicators as $indicator)
                                            <div class="px-4 py-2.5 flex items-start gap-3 hover:bg-gray-700/10 transition-colors">
                                                <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-blue-500/10 text-blue-400 text-[8px] font-bold shrink-0 mt-0.5">
                                                    {{ $indicator['code'] ?? $loop->iteration }}
                                                </span>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-[12px] text-gray-300 leading-snug">{{ $indicator['description'] ?? '—' }}</p>
                                                    @if(!empty($indicator['expected_level']))
                                                        <p class="text-[10px] text-gray-500 mt-0.5">
                                                            <span class="text-gray-600">Nivel esperado:</span> {{ $indicator['expected_level'] }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="px-4 py-2 text-center">
                                        <p class="text-[11px] text-gray-500">Sin indicadores registrados</p>
                                    </div>
                                @endif
                            </div>

                        @empty
                            {{-- Empty State --}}
                            <div class="py-16 text-center">
                                <svg class="w-14 h-14 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <p class="text-sm font-medium text-gray-400">
                                    {{ $competencias ? 'Sin resultados' : 'Sin competencias registradas' }}
                                </p>
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ $competencias ? 'Ninguna competencia coincide con los filtros aplicados.' : 'Esta área de formación no tiene competencias ni indicadores asignados en su pensum.' }}
                                </p>
                            </div>
                        @endforelse

                        {{-- Summary --}}
                        @if($competencias)
                            <div class="mt-4 pt-3 border-t border-white/5 flex items-center gap-3 text-[10px] text-gray-500">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    {{ $filteredCount }} de {{ count($competencias) }} competencia{{ count($competencias) !== 1 ? 's' : '' }}
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    @php
                                        $totalIndicators = collect($competencias)->sum(fn($c) => count($c['indicators'] ?? []));
                                    @endphp
                                    {{ $totalIndicators }} indicador{{ $totalIndicators !== 1 ? 'es' : '' }}
                                </span>
                                @if($search || $filterReferentId || $onlyWithIndicators)
                                    <button wire:click="$set('search', ''); $set('filterReferentId', ''); $set('onlyWithIndicators', false)"
                                        class="ml-auto inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold text-gray-500 hover:text-white bg-gray-800/50 hover:bg-gray-700/50 transition-all duration-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Limpiar filtros
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
