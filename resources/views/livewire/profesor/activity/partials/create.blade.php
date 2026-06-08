<div class="mb-6 p-5 bg-gray-800/50 border border-white/10 rounded-2xl shadow-lg">
    <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/5">
        <h4 class="text-xs font-bold text-white uppercase tracking-wider">
            <svg class="w-4 h-4 inline mr-1.5 -mt-0.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            {{ $modeEdit ? 'Editar Actividad' : 'Registro de la Actividad' }}
        </h4>
        <button wire:click="close" class="p-1 text-gray-500 hover:text-gray-300 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    @include('livewire.profesor.activity.form.fields')

    <div class="mt-4 pt-3 border-t border-white/5">
        <button wire:click="save"
            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 hover:text-emerald-300 rounded-xl border border-emerald-500/20 transition-all duration-200 text-xs font-bold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Guardar
        </button>
    </div>
</div>
