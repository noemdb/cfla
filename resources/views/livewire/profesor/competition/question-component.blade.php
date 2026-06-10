<div>
    {{-- Loading --}}
    <div wire:loading class="mb-4">
        <div class="flex items-center gap-2 text-sm text-emerald-400 font-medium">
            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Procesando...
        </div>
    </div>

    {{-- Header with close button --}}
    <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/5">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-white">
                    {{ $pensum->asignatura?->name ?? '?' }}
                </h3>
                <p class="text-xs text-gray-500">
                    {{ $pensum->grado?->name ?? '?' }} · {{ $pensum->grado?->pestudio?->name ?? '?' }}
                </p>
            </div>
        </div>
        <button onclick="Livewire.dispatch('closeQuestions')"
            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Cerrar
        </button>
    </div>

    {{-- Questions table --}}
    @include('livewire.profesor.competition.table.questions')

    {{-- DETAIL MODAL --}}
    @if($showDetailModal && $detailQuestion)
        @include('livewire.profesor.competition.detail.question-detail')
    @endif

    {{-- OPTIONS MODAL --}}
    @if($showOptionsModal && $optionQuestionId)
        @include('livewire.profesor.competition.options.manage-options')
    @endif

    {{-- CREATE/EDIT MODAL --}}
    @if($showFormModal)
        <div class="fixed inset-0 z-[9997] overflow-y-auto" wire:key="question-modal-{{ $editId ?? 'new' }}">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="backToIndex"></div>

            {{-- Modal panel --}}
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative w-full max-w-2xl bg-gray-900 border border-white/10 rounded-2xl shadow-2xl overflow-hidden">

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-white/5 bg-gray-800/50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white uppercase tracking-wider">
                                    {{ $editId ? 'Editar Pregunta' : 'Registrar Nueva Pregunta' }}
                                </h3>
                                <p class="text-xs text-gray-500">
                                    {{ $pensum->asignatura?->name ?? '?' }} · {{ $pensum->grado?->name ?? '?' }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="backToIndex"
                            class="p-1.5 text-gray-500 hover:text-white hover:bg-white/5 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
                        <div class="space-y-4">
                            @include('livewire.profesor.competition.form.question.fields')
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-3 border-t border-white/5 bg-gray-800/30 flex items-center justify-between">
                        <button wire:click="backToIndex"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancelar
                        </button>
                        <button wire:click="save"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-xs font-bold bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 hover:text-emerald-300 border border-emerald-500/20 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ $editId ? 'Guardar Cambios' : 'Guardar Pregunta' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
