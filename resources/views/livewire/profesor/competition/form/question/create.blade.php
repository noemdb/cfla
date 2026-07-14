<div>
    <div class="bg-gray-800/30 border border-white/5 rounded-lg p-5">
        <h4 class="text-xs font-bold text-gray-300 uppercase tracking-wider mb-3">Registrar Nueva Pregunta</h4>

        <div class="space-y-4">
            @include('livewire.profesor.competition.form.question.fields')
        </div>

        <div class="mt-5 pt-4 border-t border-white/5 flex items-center justify-between">
            <button wire:click="backToIndex"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                Cancelar
            </button>
            <button wire:click="save"
                class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-xs font-bold bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 hover:text-emerald-300 border border-emerald-500/20 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Guardar Pregunta
            </button>
        </div>
    </div>
</div>
