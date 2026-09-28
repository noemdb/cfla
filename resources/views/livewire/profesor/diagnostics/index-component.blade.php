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
        <div class="border-b border-white/5 px-4 sm:px-6 py-2">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">
                    <svg class="w-4 h-4 inline mr-1.5 -mt-0.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    {{ $profesor->full_name ?? 'Profesor' }}
                </h3>
                @if(!empty($cargaPensums) && $cargaPensums->isNotEmpty())
                    <div class="flex items-center gap-2">
                        @if($lapso)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-white/5 text-gray-400 border border-white/10">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $lapso->name }}
                            </span>
                        @endif
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Área de Formación:</span>
                        <x-select placeholder="Todas las áreas" wire:model.live="selectedPensumId" searchable class="min-w-[200px]">
                            @foreach($cargaPensums as $pensum)
                                <x-select.option :label="$pensum->full_name ?? $pensum->asignatura_name" :value="$pensum->id" />
                            @endforeach
                        </x-select>
                    </div>
                @endif
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="border-b border-white/5 px-4 sm:px-6 py-2">
            <div class="flex flex-wrap items-center gap-3">
                {{-- Diagnóstico filter --}}
                <div class="relative">
                    <x-select placeholder="Todos los Diagnósticos" wire:model.live="filterDiagMainId" searchable class="min-w-[180px]">
                        @foreach($diagMains as $main)
                            <x-select.option :label="$main->name" :value="$main->id" />
                        @endforeach
                    </x-select>
                </div>

                {{-- Grado filter --}}
                <div class="relative">
                    <x-select placeholder="Todos los grados" wire:model.live="filterGradoId" searchable class="min-w-[150px]">
                        @foreach($list_grados as $grado)
                            <x-select.option :label="$grado->name" :value="$grado->id" />
                        @endforeach
                    </x-select>
                </div>

                {{-- Sección filter --}}
                <div class="relative">
                    <x-select placeholder="Todas las secciones" wire:model.live="filterSeccionId" searchable class="min-w-[150px]"
                        :disabled="empty($list_secciones)">
                        @if(!empty($list_secciones))
                            @foreach($list_secciones as $seccion)
                                <x-select.option :label="$seccion->name" :value="$seccion->id" />
                            @endforeach
                        @endif
                    </x-select>
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
        <div class="p-4 sm:p-6">
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
