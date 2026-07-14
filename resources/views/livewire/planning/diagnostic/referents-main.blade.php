<div class="fade-in">
    {{-- Loading overlay --}}
    <div wire:loading.flex wire:target="openImportModal,loadImportPreview,importData,createReferent,createCompetency,createIndicator,saveReferent,saveCompetency,saveIndicator,confirmDeleteReferent,confirmDeleteCompetency,confirmDeleteIndicator,deleteReferent,deleteCompetency,deleteIndicator,toggleReferentActive"
         class="fixed inset-0 z-[9999] bg-black/70 backdrop-blur-sm items-center justify-center">
        <div class="flex items-center gap-3 px-6 py-3 rounded-lg bg-gray-900 border border-white/10">
            <svg class="w-5 h-5 animate-spin text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span class="text-sm text-gray-300 font-medium">Procesando...</span>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-1">Instrumentos referentes normativo para la planificación y el diagnóstico.</h1>
            <p class="text-emerald-400 font-medium">Gestión de referentes curriculares, competencias e indicadores de logro</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="{{ $viewMode === 'referents' ? 'createReferent' : ($viewMode === 'competencies' ? 'createCompetency' : 'createIndicator') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo {{ $viewMode === 'referents' ? 'Referente' : ($viewMode === 'competencies' ? 'Competencia' : 'Indicador') }}
            </button>
        </div>
    </div>

    {{-- Breadcrumb navigation --}}
    <div class="flex items-center gap-2 mb-6">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider cursor-pointer transition-colors
            {{ $viewMode === 'referents' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-gray-800/50 text-gray-500 border border-white/5 hover:text-emerald-300 hover:bg-white/5' }}"
            wire:click="backToReferents">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
            </svg>
            Referentes
        </span>

        @if(in_array($viewMode, ['competencies', 'indicators']))
            <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider cursor-pointer transition-colors
                {{ $viewMode === 'competencies' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-gray-800/50 text-gray-500 border border-white/5 hover:text-emerald-300 hover:bg-white/5' }}"
                wire:click="backToCompetencies">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                {{ $currentReferent?->name ?? 'Competencias' }}
            </span>
        @endif

        @if($viewMode === 'indicators')
            <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                {{ $currentCompetency?->name ?? 'Indicadores' }}
            </span>
        @endif
    </div>

    {{-- Filters: Referents mode --}}
    @if($viewMode === 'referents')
        <div class="flex flex-wrap items-center gap-3 mb-6 pb-4 border-b border-white/5">
            <div class="relative flex-1 min-w-[200px] max-w-xs">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar referentes..."
                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg pl-9 pr-3 py-1.5 text-xs text-gray-300 placeholder-gray-600 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
            </div>

            <select wire:model.live="filterPestudioId"
                class="bg-gray-800/50 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-gray-300 focus:border-emerald-500/50 transition-all duration-200 appearance-none cursor-pointer min-w-[180px]">
                <option value="">Todos los planes</option>
                @foreach($referentPestudios as $pestudio)
                    <option value="{{ $pestudio->id }}">{{ $pestudio->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterGradoId"
                class="bg-gray-800/50 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-gray-300 focus:border-emerald-500/50 transition-all duration-200 appearance-none cursor-pointer min-w-[150px]">
                <option value="">Todos los grados</option>
                @if($filterPestudioId && $filterPestudioGrados)
                    @foreach($filterPestudioGrados as $grado)
                        <option value="{{ $grado->id }}">{{ $grado->name }}</option>
                    @endforeach
                @else
                    @foreach($grados as $grado)
                        <option value="{{ $grado->id }}">{{ $grado->name }}</option>
                    @endforeach
                @endif
            </select>

            <select wire:model.live="filterActive"
                class="bg-gray-800/50 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-gray-300 focus:border-emerald-500/50 transition-all duration-200">
                <option value="">Todos los estados</option>
                <option value="1">Activos</option>
                <option value="0">Inactivos</option>
            </select>

            <button wire:click="resetFilters"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Limpiar
            </button>
        </div>

        {{-- Referents Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Nombre</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Código</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Versión</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Plan de Estudio</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Comp.</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Ind.</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $referent)
                        <tr class="border-b border-white/5 hover:bg-white/[0.02] transition-colors">
                            <td class="py-3 px-4">
                                <span class="text-xs text-gray-300 font-medium">{{ $referent->name }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <code class="text-[11px] bg-gray-800/50 text-gray-400 px-1.5 py-0.5 rounded">{{ $referent->code ?? '—' }}</code>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-xs text-gray-400">{{ $referent->version ?? '—' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-500/10 text-purple-400 border border-purple-500/20">
                                    {{ $referent->pestudio?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="text-xs text-gray-400">{{ $referent->competencies_count }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="text-xs text-gray-400">{{ $referent->total_indicators_count ?? 0 }}</span>
                            </td>
                            <td class="py-3 px-4">
                                @if($referent->active)
                                    <span class="inline-flex items-center gap-1 text-xs text-emerald-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <button wire:click="openDetailModal({{ $referent->id }})"
                                        title="Ver detalles"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="showReferentDetail({{ $referent->id }})"
                                        title="Ver competencias"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="openImportModal({{ $referent->id }})"
                                        title="Importar competencias desde JSON"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="editReferent({{ $referent->id }})"
                                        title="Editar referente"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="toggleReferentActive({{ $referent->id }})"
                                        title="{{ $referent->active ? 'Desactivar' : 'Activar' }}"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20 border border-cyan-500/20 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDeleteReferent({{ $referent->id }})"
                                        title="Eliminar referente"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <svg class="w-12 h-12 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                </svg>
                                <p class="text-sm font-medium text-gray-400">
                                    @if($search || $filterActive)
                                        No se encontraron referentes
                                    @else
                                        No hay referentes registrados
                                    @endif
                                </p>
                                @if($search || $filterActive)
                                    <button wire:click="resetFilters" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                                        Limpiar filtros
                                    </button>
                                @else
                                    <button wire:click="createReferent" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200">
                                        + Crear primer referente
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="mt-4 pt-4 border-t border-white/5">
                {{ $items->links('vendor.livewire.custom-tailwind') }}
            </div>
        @endif

    {{-- Filters: Competencies mode --}}
    @elseif($viewMode === 'competencies')
        <div class="flex flex-wrap items-center gap-3 mb-6 pb-4 border-b border-white/5">
            <div class="relative">
                <select wire:model.live="compFilterPestudioId"
                    class="bg-gray-800/50 border border-white/10 rounded-lg pl-3 pr-8 py-1.5 text-xs text-gray-300 focus:border-emerald-500/50 transition-all duration-200 appearance-none cursor-pointer min-w-[180px]">
                    <option value="">Todos los planes</option>
                    @foreach($referentPestudios as $pestudio)
                        <option value="{{ $pestudio->id }}">{{ $pestudio->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="relative">
                <select wire:model.live="compFilterGradoId"
                    class="bg-gray-800/50 border border-white/10 rounded-lg pl-3 pr-8 py-1.5 text-xs text-gray-300 focus:border-emerald-500/50 transition-all duration-200 appearance-none cursor-pointer min-w-[150px]">
                    <option value="">Todos los grados</option>
                    @if($compFilterPestudioId && $compFilterPestudioGrados->isNotEmpty())
                        @foreach($compFilterPestudioGrados as $grado)
                            <option value="{{ $grado->id }}">{{ $grado->name }}</option>
                        @endforeach
                    @else
                        @foreach($compGrados as $grado)
                            <option value="{{ $grado->id }}">{{ $grado->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="relative">
                <select wire:model.live="compFilterPensumId"
                    class="bg-gray-800/50 border border-white/10 rounded-lg pl-3 pr-8 py-1.5 text-xs text-gray-300 focus:border-emerald-500/50 transition-all duration-200 appearance-none cursor-pointer min-w-[200px]">
                    <option value="">Todas las áreas</option>
                    @foreach($compPensums as $pensum)
                        <option value="{{ $pensum->id }}">{{ $pensum->asignatura_name ?? $pensum->fullname }}</option>
                    @endforeach
                </select>
            </div>
            <button wire:click="resetFilters"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Limpiar
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Competencia</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Área de Formación</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Grado</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Indicadores</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $competency)
                        <tr class="border-b border-white/5 hover:bg-white/[0.02] transition-colors">
                            <td class="py-3 px-4">
                                <span class="text-xs text-gray-300 font-medium">{{ $competency->name }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-xs text-gray-400">{{ $competency->pensum?->asignatura_name ?? $competency->pensum?->fullname ?? '—' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-xs text-gray-400">{{ $competency->pensum?->grado?->name ?? '—' }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="text-xs text-gray-400">{{ $competency->indicators_count }}</span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <button wire:click="openDetailModal({{ $competency->id }})"
                                        title="Ver detalles"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="showCompetencyDetail({{ $competency->id }})"
                                        title="Ver indicadores"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="editCompetency({{ $competency->id }})"
                                        title="Editar competencia"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDeleteCompetency({{ $competency->id }})"
                                        title="Eliminar competencia"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <svg class="w-12 h-12 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-sm font-medium text-gray-400">No hay competencias para este referente</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="mt-4 pt-4 border-t border-white/5">
                {{ $items->links('vendor.livewire.custom-tailwind') }}
            </div>
        @endif

    {{-- View: Indicators --}}
    @elseif($viewMode === 'indicators')
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Código</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Descripción</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Nivel Esperado</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $indicator)
                        <tr class="border-b border-white/5 hover:bg-white/[0.02] transition-colors">
                            <td class="py-3 px-4">
                                <code class="text-[11px] bg-gray-800/50 text-gray-400 px-1.5 py-0.5 rounded">{{ $indicator->code ?? '—' }}</code>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-xs text-gray-300">{{ $indicator->description }}</span>
                            </td>
                            <td class="py-3 px-4">
                                @php $level = $expectedLevels[$indicator->expected_level] ?? ['label' => 'No definido', 'color' => 'default']; @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold
                                    {{ $level['color'] === 'success' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                    {{ $level['color'] === 'info' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : '' }}
                                    {{ $level['color'] === 'warning' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                    {{ $level['color'] === 'default' ? 'bg-gray-800/50 text-gray-500 border border-white/5' : '' }}">
                                    {{ $level['label'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <button wire:click="openDetailModal({{ $indicator->id }})"
                                        title="Ver detalles"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="editIndicator({{ $indicator->id }})"
                                        title="Editar indicador"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDeleteIndicator({{ $indicator->id }})"
                                        title="Eliminar indicador"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center">
                                <svg class="w-12 h-12 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <p class="text-sm font-medium text-gray-400">No hay indicadores para esta competencia</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="mt-4 pt-4 border-t border-white/5">
                {{ $items->links('vendor.livewire.custom-tailwind') }}
            </div>
        @endif
    @endif

    {{-- Create/Edit Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
            <div class="relative bg-gray-900 border border-white/10 rounded-lg w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="sticky top-0 bg-gray-900/95 backdrop-blur-sm border-b border-white/5 px-6 py-3 flex items-center justify-between z-10">
                    <h3 class="text-sm font-bold text-white">
                        @if($viewMode === 'referents')
                            {{ $editingId ? 'Editar Referente' : 'Nuevo Referente' }}
                        @elseif($viewMode === 'competencies')
                            {{ $editingId ? 'Editar Competencia' : 'Nueva Competencia' }}
                        @else
                            {{ $editingId ? 'Editar Indicador' : 'Nuevo Indicador' }}
                        @endif
                    </h3>
                    <button wire:click="$set('showModal', false)"
                        class="w-7 h-7 rounded-lg bg-gray-800/50 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4">
                    @if($viewMode === 'referents')
                        {{-- Referent form --}}
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Plan de Estudio</label>
                            <select wire:model="form.pestudio_id"
                                class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
                                <option value="">Seleccione plan de estudio</option>
                                @foreach($referentPestudios as $pestudio)
                                    <option value="{{ $pestudio->id }}">{{ $pestudio->name }}</option>
                                @endforeach
                            </select>
                            @error('form.pestudio_id') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Nombre</label>
                            <input type="text" wire:model="form.name" placeholder="Ej: Reforma Educativa 2017"
                                class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 placeholder-gray-600 focus:border-emerald-500/50 transition-all duration-200">
                            @error('form.name') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Código</label>
                                <input type="text" wire:model="form.code" placeholder="Ej: RES-2017-01"
                                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 placeholder-gray-600 focus:border-emerald-500/50 transition-all duration-200">
                                @error('form.code') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Versión</label>
                                <input type="text" wire:model="form.version" placeholder="Ej: 1.0"
                                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 placeholder-gray-600 focus:border-emerald-500/50 transition-all duration-200">
                                @error('form.version') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Descripción</label>
                            <textarea wire:model="form.description" rows="2" placeholder="Descripción del referente..."
                                class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 placeholder-gray-600 focus:border-emerald-500/50 transition-all duration-200 resize-none"></textarea>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" wire:model="form.active" id="form_active"
                                class="w-4 h-4 rounded border-white/10 bg-gray-800/50 text-emerald-500 focus:ring-emerald-500/20">
                            <label for="form_active" class="text-xs text-gray-400">Activo</label>
                        </div>

                    @elseif($viewMode === 'competencies')
                        {{-- Competency form --}}
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Área de Formación</label>
                            <select wire:model="form.pensum_id"
                                class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 transition-all duration-200">
                                <option value="">Seleccione área (opcional)</option>
                                @foreach($pensums as $pensum)
                                    <option value="{{ $pensum->id }}">{{ $pensum->asignatura_name ?? $pensum->fullname }}</option>
                                @endforeach
                            </select>
                            @error('form.pensum_id') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Nombre</label>
                            <input type="text" wire:model="form.name" placeholder="Nombre de la competencia"
                                class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 placeholder-gray-600 focus:border-emerald-500/50 transition-all duration-200">
                            @error('form.name') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Descripción</label>
                            <textarea wire:model="form.description" rows="2" placeholder="Descripción de la competencia..."
                                class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 placeholder-gray-600 focus:border-emerald-500/50 transition-all duration-200 resize-none"></textarea>
                        </div>

                    @else
                        {{-- Indicator form --}}
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Código</label>
                            <input type="text" wire:model="form.code" placeholder="Ej: IND-001"
                                class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 placeholder-gray-600 focus:border-emerald-500/50 transition-all duration-200">
                            @error('form.code') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Descripción</label>
                            <textarea wire:model="form.description" rows="2" placeholder="Descripción del indicador de logro..."
                                class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 placeholder-gray-600 focus:border-emerald-500/50 transition-all duration-200 resize-none"></textarea>
                            @error('form.description') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Nivel Esperado</label>
                            <select wire:model="form.expected_level"
                                class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-xs text-gray-300 focus:border-emerald-500/50 transition-all duration-200">
                                <option value="">Seleccione nivel</option>
                                @foreach($expectedLevels as $val => $level)
                                    <option value="{{ $val }}">{{ $level['label'] }}</option>
                                @endforeach
                            </select>
                            @error('form.expected_level') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>

                <div class="sticky bottom-0 bg-gray-900/95 backdrop-blur-sm border-t border-white/5 px-6 py-3 flex items-center justify-between">
                    <button wire:click="$set('showModal', false)"
                        class="px-4 py-2 rounded-lg text-xs font-bold bg-gray-800/50 text-gray-400 hover:text-white border border-white/5 transition-all duration-200">
                        Cancelar
                    </button>
                    @php
                        $saveMethod = $viewMode === 'indicators' ? 'Indicator' : ($viewMode === 'competencies' ? 'Competency' : 'Referent');
                    @endphp
                    <button wire:click="save{{ $saveMethod }}" wire:loading.attr="disabled"
                        class="px-6 py-2 rounded-lg text-xs font-bold bg-emerald-500 text-white hover:bg-emerald-600 transition-all duration-200 disabled:opacity-50">
                        <span wire:loading.remove wire:target="save{{ $saveMethod }}">Guardar</span>
                        <span wire:loading wire:target="save{{ $saveMethod }}">Guardando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Detail Modal --}}
    @if($detailModal && $detailItem)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeDetailModal"></div>
            <div class="relative bg-gray-900 border border-white/10 rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-2xl">
                {{-- Header --}}
                <div class="sticky top-0 bg-gray-900/95 backdrop-blur-sm border-b border-white/5 px-6 py-3 flex items-center justify-between z-10">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        @if($viewMode === 'competencies')
                            Detalle de Competencia
                        @elseif($viewMode === 'indicators')
                            Detalle de Indicador
                        @else
                            Detalle de Referente Curricular
                        @endif
                    </h3>
                    <button wire:click="closeDetailModal"
                        class="w-7 h-7 rounded-lg bg-gray-800/50 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5 space-y-6">
                    @if($viewMode === 'referents')
                        {{-- Referent detail --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="md:col-span-2">
                                <div class="flex items-start gap-3 p-4 rounded-lg bg-gradient-to-br from-emerald-500/5 to-emerald-500/[0.02] border border-emerald-500/10">
                                    <div class="w-12 h-12 rounded-lg bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-white">{{ $detailItem->name }}</h2>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $detailItem->code ?? 'Sin código' }}{{ $detailItem->version ? ' · v' . $detailItem->version : '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 rounded-lg bg-gray-800/30 border border-white/5">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Plan de Estudio</label>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-purple-500/10 text-purple-400 border border-purple-500/20">
                                    {{ $detailItem->pestudio?->name ?? '—' }}
                                </span>
                            </div>

                            <div class="p-4 rounded-lg bg-gray-800/30 border border-white/5">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Competencias Asociadas</label>
                                <span class="inline-flex items-center gap-1.5 text-sm font-bold text-white">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    {{ $detailItem->competencies_count }}
                                </span>
                            </div>

                            <div class="p-4 rounded-lg bg-gray-800/30 border border-white/5">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Indicadores de Logro</label>
                                <span class="inline-flex items-center gap-1.5 text-sm font-bold text-white">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    {{ $detailItem->total_indicators_count ?? 0 }}
                                </span>
                            </div>

                            <div class="p-4 rounded-lg bg-gray-800/30 border border-white/5">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Estado</label>
                                @if($detailItem->active)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-400">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500">
                                        <span class="w-2 h-2 rounded-full bg-gray-500"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </div>

                            <div class="p-4 rounded-lg bg-gray-800/30 border border-white/5">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">ID Registro</label>
                                <span class="text-xs text-gray-300 font-mono">#{{ $detailItem->id }}</span>
                            </div>

                            @if($detailItem->description)
                                <div class="md:col-span-2 p-4 rounded-lg bg-gray-800/30 border border-white/5">
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Descripción</label>
                                    <p class="text-xs text-gray-300 leading-relaxed">{{ $detailItem->description }}</p>
                                </div>
                            @endif

                            @if($detailItem->version)
                                <div class="md:col-span-2 p-4 rounded-lg bg-gray-800/30 border border-white/5">
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Versión</label>
                                    <p class="text-xs text-gray-300">{{ $detailItem->version }}</p>
                                </div>
                            @endif
                        </div>

                    @elseif($viewMode === 'competencies')
                        {{-- Competency detail --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="md:col-span-2">
                                <div class="flex items-start gap-3 p-4 rounded-lg bg-gradient-to-br from-amber-500/5 to-amber-500/[0.02] border border-amber-500/10">
                                    <div class="w-12 h-12 rounded-lg bg-amber-500/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-white">{{ $detailItem->name }}</h2>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $detailItem->referent?->name ?? 'Referente no asignado' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 rounded-lg bg-gray-800/30 border border-white/5">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Área de Formación</label>
                                <span class="text-xs text-gray-300">{{ $detailItem->pensum?->asignatura_name ?? $detailItem->pensum?->fullname ?? '—' }}</span>
                            </div>

                            <div class="p-4 rounded-lg bg-gray-800/30 border border-white/5">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Grado</label>
                                <span class="text-xs text-gray-300">{{ $detailItem->pensum?->grado?->name ?? '—' }}</span>
                            </div>

                            <div class="p-4 rounded-lg bg-gray-800/30 border border-white/5">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Indicadores Asociados</label>
                                <span class="inline-flex items-center gap-1.5 text-sm font-bold text-white">
                                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    {{ $detailItem->indicators_count }}
                                </span>
                            </div>

                            <div class="p-4 rounded-lg bg-gray-800/30 border border-white/5">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">ID Registro</label>
                                <span class="text-xs text-gray-300 font-mono">#{{ $detailItem->id }}</span>
                            </div>

                            @if($detailItem->description)
                                <div class="md:col-span-2 p-4 rounded-lg bg-gray-800/30 border border-white/5">
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Descripción</label>
                                    <p class="text-xs text-gray-300 leading-relaxed">{{ $detailItem->description }}</p>
                                </div>
                            @endif
                        </div>

                    @elseif($viewMode === 'indicators')
                        {{-- Indicator detail --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="md:col-span-2">
                                <div class="flex items-start gap-3 p-4 rounded-lg bg-gradient-to-br from-blue-500/5 to-blue-500/[0.02] border border-blue-500/10">
                                    <div class="w-12 h-12 rounded-lg bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-white">{{ $detailItem->description }}</h2>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            <code class="bg-gray-800 px-1.5 py-0.5 rounded">{{ $detailItem->code ?? 'Sin código' }}</code>
                                            <span class="ml-2">· {{ $detailItem->competency?->name ?? 'Competencia no asignada' }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 rounded-lg bg-gray-800/30 border border-white/5">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Nivel Esperado</label>
                                @php $level = $expectedLevels[$detailItem->expected_level] ?? ['label' => 'No definido', 'color' => 'default']; @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold
                                    {{ $level['color'] === 'success' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                    {{ $level['color'] === 'info' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : '' }}
                                    {{ $level['color'] === 'warning' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                    {{ $level['color'] === 'default' ? 'bg-gray-800/50 text-gray-500 border border-white/5' : '' }}">
                                    {{ $level['label'] }}
                                </span>
                            </div>

                            <div class="p-4 rounded-lg bg-gray-800/30 border border-white/5">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">ID Registro</label>
                                <span class="text-xs text-gray-300 font-mono">#{{ $detailItem->id }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="sticky bottom-0 bg-gray-900/95 backdrop-blur-sm border-t border-white/5 px-6 py-3 flex justify-end">
                    <button wire:click="closeDetailModal"
                        class="px-5 py-2 rounded-lg text-xs font-bold bg-gray-800/50 text-gray-300 hover:text-white border border-white/5 hover:border-white/10 transition-all duration-200">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ─── IMPORT MODAL ─── --}}
    <div x-data="{ showImportModal: @entangle('showImportModal') }"
         x-show="showImportModal"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-[10000] flex items-center justify-center p-4"
         style="display: none;">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="showImportModal = false"></div>
        <div class="relative bg-gray-900 border border-white/10 rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-2xl">
            {{-- Header --}}
                <div class="sticky top-0 bg-gray-900/95 backdrop-blur-sm border-b border-white/5 px-6 py-3 flex items-center justify-between z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-amber-500/10 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Importar desde JSON</h3>
                            <p class="text-[11px] text-gray-500 mt-0.5">Carga competencias e indicadores desde archivos blueprint</p>
                        </div>
                    </div>
                    <button @click="showImportModal = false"
                        class="w-7 h-7 rounded-lg bg-gray-800/50 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5 space-y-5">

                    {{-- Step 1: Upload JSON File --}}
                    <div x-data>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">
                            Archivo JSON
                        </label>
                        {{-- File upload dropzone --}}
                        <div
                            @click="$refs.fileInput.click()"
                            class="relative group cursor-pointer rounded-lg border-2 border-dashed border-white/10 hover:border-emerald-500/40 bg-gray-800/30 hover:bg-gray-800/50 transition-all duration-200 px-4 py-6">
                            <input type="file" wire:model="importJsonFile" accept=".json"
                                x-ref="fileInput"
                                class="hidden">

                            {{-- Loading state --}}
                            <div wire:loading wire:target="importJsonFile"
                                class="flex flex-col items-center gap-2">
                                <svg class="w-8 h-8 animate-spin text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span class="text-[11px] text-gray-400 font-medium">Cargando archivo...</span>
                            </div>

                            {{-- Default state (no file) --}}
                            <div wire:loading.remove wire:target="importJsonFile"
                                class="flex flex-col items-center gap-2">
                                @if($importJsonFile && method_exists($importJsonFile, 'getClientOriginalName'))
                                    {{-- File selected --}}
                                    <div class="w-10 h-10 rounded-full bg-emerald-500/10 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xs font-bold text-emerald-400">{{ $importJsonFile->getClientOriginalName() }}</p>
                                        <p class="text-[10px] text-gray-500 mt-0.5">Archivo cargado correctamente</p>
                                    </div>
                                @else
                                    {{-- No file --}}
                                    <div class="w-10 h-10 rounded-full bg-gray-800/80 flex items-center justify-center group-hover:bg-emerald-500/10 transition-all duration-200">
                                        <svg class="w-5 h-5 text-gray-500 group-hover:text-emerald-400 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xs font-bold text-gray-400 group-hover:text-emerald-400 transition-all duration-200">
                                            Seleccionar archivo JSON
                                        </p>
                                        <p class="text-[10px] text-gray-600 mt-0.5">Solo archivos .json</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @error('importJsonFile')
                            <p class="text-[10px] text-red-400 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Step 2: Select Referent (card grid) --}}
                    <div class="pt-4">
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">
                            Referente Normativo
                            <span class="text-gray-600 font-normal normal-case ml-2">Seleccione el referente para la importación</span>
                        </label>
                        <div class="space-y-1.5 max-h-52 overflow-y-auto pr-1">
                            @foreach($importReferents as $referent)
                                @php $isSelected = (int) $importReferentId === (int) $referent->id; @endphp
                                <button type="button" wire:click="$set('importReferentId', '{{ $referent->id }}')"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg border text-left transition-all duration-200
                                        {{ $isSelected
                                            ? 'border-emerald-500/50 bg-emerald-500/10 ring-1 ring-emerald-500/20'
                                            : 'border-white/5 bg-gray-800/30 hover:border-white/10 hover:bg-gray-800/50' }}">
                                    {{-- Selected indicator --}}
                                    <div class="shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-200
                                        {{ $isSelected ? 'border-emerald-400 bg-emerald-400' : 'border-gray-600' }}">
                                        @if($isSelected)
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                    {{-- Content --}}
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-semibold {{ $isSelected ? 'text-emerald-300' : 'text-gray-200' }} truncate">
                                            {{ $referent->name }}
                                        </p>
                                        @if($referent->pestudio)
                                            <p class="text-[10px] text-gray-500 mt-0.5 truncate">{{ $referent->pestudio->name }}</p>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                        @error('importReferentId')
                            <p class="text-[10px] text-red-400 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Step 3: Load Preview --}}
                    <div class="flex items-center justify-between pt-4 border-t border-white/5">
                        <p class="text-[10px] text-gray-500">
                            Los datos se importarán según el <code class="text-gray-400 bg-gray-800/50 px-1.5 py-0.5 rounded">area_formacion.pensumId</code> del JSON
                        </p>
                        <button wire:click="loadImportPreview" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200 disabled:opacity-50"
                            {{ !$importJsonFile || !$importReferentId ? 'disabled' : '' }}>
                            <span wire:loading.remove wire:target="loadImportPreview">
                                <svg class="w-3.5 h-3.5 inline -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"></path>
                                </svg>
                                Cargar vista previa
                            </span>
                            <span wire:loading wire:target="loadImportPreview">Cargando...</span>
                        </button>
                    </div>

                    {{-- Status Message --}}
                    @if($importStatus)
                        @php
                            $statusType = explode(':', $importStatus, 2)[0] ?? '';
                            $statusMsg = explode(':', $importStatus, 2)[1] ?? $importStatus;
                        @endphp
                        <div class="px-4 py-3 rounded-lg text-xs font-medium
                            {{ $statusType === 'ok' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                            {{ $statusType === 'warning' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                            {{ $statusType === 'error' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : '' }}">
                            {{ $statusMsg }}
                        </div>
                    @endif

                    {{-- Preview --}}
                    @if($importPreview)
                        <div class="space-y-4 pt-2 border-t border-white/5">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold text-white uppercase tracking-wider">
                                    Vista previa
                                    <span class="text-gray-500 font-normal normal-case ml-2">
                                        ({{ count($importPreview['competencias']) }} competencias · {{ count($importPreview['indicadores']) }} indicadores)
                                    </span>
                                </h4>
                                <button wire:click="importData" wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg text-xs font-bold bg-emerald-500 text-white hover:bg-emerald-600 transition-all duration-200 disabled:opacity-50">
                                    <span wire:loading.remove wire:target="importData">
                                        <svg class="w-3.5 h-3.5 inline -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"></path>
                                        </svg>
                                        Importar datos
                                    </span>
                                    <span wire:loading wire:target="importData">Importando...</span>
                                </button>
                            </div>

                            {{-- Loading overlay for import --}}
                            <div wire:loading.flex wire:target="importData" class="items-center gap-2 px-4 py-3 rounded-lg bg-gray-800/50 border border-white/5">
                                <svg class="w-4 h-4 animate-spin text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span class="text-xs text-gray-300">Importando datos, por favor espere...</span>
                            </div>

                            {{-- Competencies preview --}}
                            <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                @foreach($importPreview['competencias'] as $comp)
                                    <div class="px-4 py-3 rounded-lg bg-gray-800/30 border border-white/5">
                                        <div class="flex items-start gap-3">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-500/10 text-emerald-400 text-[10px] font-bold shrink-0">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                </svg>
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-semibold text-white">{{ $comp['nombre'] }}</p>
                                                @if(!empty($comp['descripcion']))
                                                    <p class="text-[10px] text-gray-400 mt-0.5 line-clamp-2">{{ $comp['descripcion'] }}</p>
                                                @endif
                                            </div>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20 shrink-0">
                                                {{ count(array_filter($importPreview['indicadores'] ?? [], fn($i) => ($i['competency_id'] ?? '') === ($comp['id'] ?? ''))) }} ind.
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="sticky bottom-0 bg-gray-900/95 backdrop-blur-sm border-t border-white/5 px-6 py-3 flex justify-end">
                    <button @click="showImportModal = false"
                        class="px-5 py-2 rounded-lg text-xs font-bold bg-gray-800/50 text-gray-300 hover:text-white border border-white/5 hover:border-white/10 transition-all duration-200">
                        Cerrar
                    </button>
                </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .fade-in {
        animation: fadeIn 0.4s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #020617; }
    ::-webkit-scrollbar-thumb { background: #064e3b; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #10b981; }
</style>
@endpush
