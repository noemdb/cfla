<div class="fixed inset-0 overflow-y-auto z-[60]" wire:key="create-activity-modal">
    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="close"></div>

    <div class="relative min-h-screen flex items-start justify-center pt-8 pb-12">
        <div class="relative w-[95vw] max-w-[95vw] bg-gray-900 border border-white/10 rounded-lg shadow-2xl overflow-hidden" @click.away="close">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-3 border-b border-white/5 bg-gray-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">
                            {{ $modeEdit ? 'Editar Actividad' : 'Registro de la Actividad' }}
                        </h3>
                        <p class="text-xs text-gray-500">
                            {{ $pevaluacion->pensum->asignatura->name ?? 'Asignatura' }} · {{ $pevaluacion->seccion->name ?? '' }}
                        </p>
                    </div>
                </div>
                <button wire:click="close"
                    class="p-1.5 text-gray-500 hover:text-white hover:bg-white/5 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 max-h-[75vh] overflow-y-auto">
                @include('livewire.profesor.activity.form.fields')
            </div>

            {{-- Footer --}}
            <div class="px-6 py-3 border-t border-white/5 bg-gray-800/30 flex items-center justify-between">
                <button wire:click="close"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                    Cancelar
                </button>
                <button wire:click="save"
                    class="inline-flex items-center gap-2 px-6 py-2 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white border border-emerald-400/20 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>
