<div class="relative p-6 space-y-6 rounded-2xl border border-white/10">
    <button type="button" wire:click="close"
        class="absolute top-6 right-6 p-1.5 bg-white/10 hover:bg-red-500/20 rounded-lg text-gray-400 hover:text-red-400 transition-all duration-200 z-10"
        title="Cerrar">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>

    <div class="flex items-center gap-3 mb-2 pr-10">
        <div class="w-14 h-14 rounded-full bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400 text-lg font-bold">
            {{ strtoupper(substr($this->profesor->name ?? '?', 0, 1)) }}{{ strtoupper(substr($this->profesor->lastname ?? '', 0, 1)) }}
        </div>
        <div>
            <h3 class="text-lg font-bold text-white">{{ $this->profesor->full_name }}</h3>
            <p class="text-sm text-gray-400 font-mono">{{ $this->profesor->ci_profesor }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Tipo Facilitador</span>
            <p class="text-sm text-gray-300 mt-1">{{ $this->profesor->ti_teacher ?? '—' }}</p>
        </div>
        <div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Género</span>
            <p class="text-sm text-gray-300 mt-1">{{ $this->profesor->gender === 'M' ? 'Masculino' : 'Femenino' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-3">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Carga Académica</span>
            <p class="text-sm text-gray-300 mt-1">{{ $this->profesor->pevaluacions_count }}</p>
        </div>
        <div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Actividades</span>
            <p class="text-sm text-gray-300 mt-1">{{ $this->profesor->activities_count }}</p>
        </div>
        <div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Usuario</span>
            <p class="text-sm text-gray-300 mt-1">{{ $this->profesor->user?->username ?? '—' }}</p>
        </div>
    </div>

    @if($this->profesor->email || $this->profesor->cellphone)
    <div class="grid grid-cols-2 gap-3">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Email</span>
            <p class="text-sm text-gray-300 mt-1">{{ $this->profesor->email ?? '—' }}</p>
        </div>
        <div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Celular</span>
            <p class="text-sm text-gray-300 mt-1">{{ $this->profesor->cellphone ?? '—' }}</p>
        </div>
    </div>
    @endif

    <div class="flex justify-end border-t border-white/5 pt-4">
        <x-button flat label="Cerrar" wire:click="close" />
    </div>
</div>
