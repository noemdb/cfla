{{-- Paso 1: Filtrar (compartido: manager de campoConocimiento y wizard de creación) --}}
<div class="bg-white/5 border border-white/10 rounded-lg p-4">
    <h4 class="text-xs font-bold text-gray-300 mb-3 flex items-center gap-2">
        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
        </svg>
        Filtrar Asignaturas
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Plan de Estudio</label>
            <select wire:model.live="wizardFilterPestudio"
                class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                <option value="">Todos</option>
                @foreach($pestudios as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Grado (opcional)</label>
            <select wire:model.live="wizardFilterGrado"
                class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                <option value="">Todos los grados</option>
                @foreach($gradosList as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Buscar asignatura</label>
            <input type="text" wire:model.live.debounce.300ms="wizardSearch" placeholder="Nombre o código..."
                class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
        </div>
        <div class="flex gap-2 items-end">
            <button type="button" wire:click="nextStepWizard"
                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-300 text-sm font-bold">
                Ver Asignaturas
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>
    <div class="flex items-center justify-between mt-3">
        <p class="text-[10px] text-gray-600">
            @php
                $count = $this->availableSubjects->count();
            @endphp
            {{ $count }} asignatura(s) disponibles
            @if($wizardFilterPestudio)
                para el plan de estudio seleccionado
            @endif
            @if($wizardOnlyUnassigned)
                · solo pensums sin área
            @endif
            <span class="text-gray-500">· {{ $this->unassignedPensumCount }} pensum(s) sin área en total</span>
        </p>
        <button type="button" wire:click="toggleOnlyUnassigned"
            class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-lg border transition-all duration-200 {{ $wizardOnlyUnassigned ? 'bg-amber-500/15 text-amber-300 border-amber-500/30' : 'bg-white/5 text-gray-400 hover:text-white border-white/10' }}"
            title="Mostrar solo pensums no asociados a ninguna área de conocimiento">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6m12 0a6 6 0 11-12 0 6 6 0 0112 0z"/>
            </svg>
            Solo pensums sin área
            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[9px] {{ $wizardOnlyUnassigned ? 'bg-amber-500/30 text-amber-200' : 'bg-white/10 text-gray-500' }}">
                {{ $wizardOnlyUnassigned ? '✓' : '' }}
            </span>
        </button>
    </div>
</div>
