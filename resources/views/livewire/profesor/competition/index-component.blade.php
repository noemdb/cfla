<div>
    {{-- Loading --}}
    <div wire:loading class="mb-3">
        <div class="flex items-center gap-2 text-sm text-emerald-400 font-medium">
            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Procesando...
        </div>
    </div>

    {{-- Main card --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
        <div class="p-6">
            {{-- Title --}}
            <div class="flex items-center justify-between mb-3 pb-3 border-b border-white/5">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">
                    Áreas de Formación Asignadas
                </h3>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {{-- Left: Pensum list (col-4) --}}
                <div class="lg:col-span-4">
                    @include('livewire.profesor.competition.table.index')
                </div>

                {{-- Right: Questions panel (col-8) --}}
                <div class="lg:col-span-8">
                    @if($modeQuestion && $selectedPensumId)
                        @livewire('profesor.competition.question-component', [
                            'pensumId' => $selectedPensumId
                        ], key($selectedPensumId))
                    @else
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <svg class="w-16 h-16 text-gray-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <p class="text-sm font-medium text-gray-400">Seleccione un área de formación</p>
                            <p class="text-xs text-gray-600 mt-1">Presione <span class="text-emerald-400 font-medium">"Preguntas"</span> en la lista de la izquierda.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
