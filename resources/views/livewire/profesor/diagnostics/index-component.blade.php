<div>
    {{-- Loading Overlay --}}
    <div wire:loading.flex class="fixed inset-0 z-[9999] bg-black/70 backdrop-blur-sm flex items-center justify-center">
        <div class="flex items-center gap-3 px-6 py-2 rounded-lg bg-gray-900 border border-white/10">
            <svg class="w-5 h-5 animate-spin text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span class="text-sm text-gray-300 font-medium">Procesando...</span>
        </div>
    </div>

    {{-- Main card --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
        {{-- Header: Área de Formación Selector --}}
        <div class="border-b border-white/5 px-6 py-2">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">
                    <svg class="w-4 h-4 inline mr-1.5 -mt-0.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    {{ $profesor->full_name ?? 'Profesor' }}
                </h3>
                @if($profesor && $profesor->pensums->isNotEmpty())
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Área de Formación:</span>
                        <select wire:model.live="selectedPensumId"
                            class="bg-gray-800/50 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-gray-300 focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/20 transition-all duration-200 min-w-[200px]">
                            <option value="">Todas las áreas</option>
                            @foreach($profesor->pensums as $pensum)
                                <option value="{{ $pensum->id }}">{{ $pensum->full_name ?? $pensum->asignatura_name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="border-b border-white/5 px-6 py-2">
            <div class="flex flex-wrap items-center gap-3">
                {{-- Diagnóstico filter --}}
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3 h-3 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <select wire:model.live="filterDiagMainId"
                        class="bg-gray-800/50 border border-white/10 rounded-lg pl-8 pr-8 py-1.5 text-xs text-gray-300 focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/20 transition-all duration-200 appearance-none cursor-pointer min-w-[180px]">
                        <option value="">Todos los Diagnósticos</option>
                        @foreach($diagMains as $main)
                            <option value="{{ $main->id }}">{{ $main->name }}</option>
                        @endforeach
                    </select>
                    @if($filterDiagMainId)
                        <button wire:click="$set('filterDiagMainId', '')" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif
                </div>

                {{-- Grado filter --}}
                <div class="relative">
                    <select wire:model.live="filterGradoId"
                        class="bg-gray-800/50 border border-white/10 rounded-lg pl-3 pr-8 py-1.5 text-xs text-gray-300 focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/20 transition-all duration-200 appearance-none cursor-pointer min-w-[150px]">
                        <option value="">Todos los grados</option>
                        @foreach($list_grados as $grado)
                            <option value="{{ $grado->id }}">{{ $grado->name }}</option>
                        @endforeach
                    </select>
                    @if($filterGradoId)
                        <button wire:click="$set('filterGradoId', '')" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif
                </div>

                {{-- Sección filter --}}
                <div class="relative">
                    <select wire:model.live="filterSeccionId"
                        class="bg-gray-800/50 border border-white/10 rounded-lg pl-3 pr-8 py-1.5 text-xs text-gray-300 focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/20 transition-all duration-200 appearance-none cursor-pointer min-w-[150px]"
                        {{ empty($list_secciones) ? 'disabled' : '' }}>
                        <option value="">Todas las secciones</option>
                        @if(!empty($list_secciones))
                            @foreach($list_secciones as $seccion)
                                <option value="{{ $seccion->id }}">{{ $seccion->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @if($filterSeccionId)
                        <button wire:click="$set('filterSeccionId', '')" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="border-b border-white/5">
            <div class="flex w-full">
                <button wire:click="setActiveTab('dashboard')"
                    class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold uppercase tracking-wider transition-all duration-200 border-b-2 {{ $activeTab === 'dashboard' ? 'text-purple-400 border-purple-500' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-500' }}">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Dashboard
                </button>
                <button wire:click="setActiveTab('questions')"
                    class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold uppercase tracking-wider transition-all duration-200 border-b-2 {{ $activeTab === 'questions' ? 'text-purple-400 border-purple-500' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-500' }}">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                    Preguntas
                </button>
                <button wire:click="setActiveTab('sessions')"
                    class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold uppercase tracking-wider transition-all duration-200 border-b-2 {{ $activeTab === 'sessions' ? 'text-purple-400 border-purple-500' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-500' }}">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Sesiones
                </button>
                <button wire:click="setActiveTab('analytics')"
                    class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold uppercase tracking-wider transition-all duration-200 border-b-2 {{ $activeTab === 'analytics' ? 'text-purple-400 border-purple-500' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-500' }}">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Análisis
                </button>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="p-6">
            @if($activeTab === 'dashboard')
                @include('livewire.profesor.diagnostics.partials.dashboard')
            @elseif($activeTab === 'questions')
                @include('livewire.profesor.diagnostics.partials.questions')
            @elseif($activeTab === 'sessions')
                @include('livewire.profesor.diagnostics.partials.sessions')
            @elseif($activeTab === 'analytics')
                @include('livewire.profesor.diagnostics.partials.analytics')
            @endif
        </div>
    </div>

    {{-- Question Modal --}}
    @include('livewire.profesor.diagnostics.partials.question-modal')

    {{-- Session Detail Modal --}}
    @include('livewire.profesor.diagnostics.partials.session-modal')

    {{-- AI Report Modal --}}
    @if($SessionModalReport && $selectedReport)
        @include('livewire.profesor.diagnostics.partials.ai-report-modal')
    @endif
</div>
