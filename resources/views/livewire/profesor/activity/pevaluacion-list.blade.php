<div wire:key="pevaluacion-list-{{ $lapsoId }}">
    {{-- Lapso NavTabs (reactivos) --}}
    @if($lapsos->isNotEmpty())
    <div class="border-b border-white/5">
        <nav class="flex overflow-x-auto">
            @foreach($lapsos as $lapsoItem)
                @php $tabLapsoId = $lapsoItem->id; @endphp
                <button wire:click="$set('lapsoId', {{ $tabLapsoId }})"
                    class="flex-1 px-4 py-2 text-[11px] font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap
                    {{ $lapsoId == $tabLapsoId ? 'text-emerald-400 border-emerald-500 bg-emerald-500/5' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-600' }}">
                    <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ $lapsoItem->name }}
                    <span class="block text-[8px] font-normal text-gray-500 normal-case">{{ $lapsoItem->code }}</span>
                </button>
            @endforeach
        </nav>
    </div>
    @endif

    {{-- Body --}}
    <div class="p-4">

        {{-- Search Filters (livewire internos) --}}
        <div class="mb-3">
            <form wire:submit.prevent="$refresh" class="grid grid-cols-1 md:grid-cols-4 gap-2">
                <input type="hidden" wire:model="lapsoId" name="lapso_id">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Plan de Estudio</label>
                    <select wire:model.live="pestudio_id"
                        class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-2.5 py-1.5 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
                        <option value="">Todos</option>
                        @foreach($list_pestudio as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Grado</label>
                    <select wire:model.live="grado_id"
                        class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-2.5 py-1.5 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
                        <option value="">Seleccione</option>
                        @foreach($list_grado as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Sección</label>
                    <select wire:model.live="seccion_id"
                        class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-2.5 py-1.5 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
                        <option value="">Seleccione</option>
                        @foreach($list_seccion as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 hover:text-emerald-300 rounded-lg border border-emerald-500/20 transition-all duration-200 text-[11px] font-bold">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Buscar
                    </button>
                    <button type="button" wire:click="resetFilters"
                        class="inline-flex items-center justify-center px-2.5 py-1.5 bg-white/5 hover:bg-white/10 text-gray-400 rounded-lg border border-white/5 transition-all duration-200 text-[11px] font-bold">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        {{-- Subtitle + View Toggle --}}
        <div class="flex items-center justify-between mb-2">
            <p class="text-[11px] text-gray-400 font-medium">
                <span class="text-emerald-400">Listado</span> de Áreas de Formación
            </p>
            <div x-data="{ mode: localStorage.getItem('students-view-mode') || 'table' }" x-init="$watch('mode', val => { localStorage.setItem('students-view-mode', val); window.dispatchEvent(new CustomEvent('students-view-mode-changed', { detail: { mode: val } })) })">
                <button @click="mode = 'grid'"
                    :class="mode === 'grid' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-gray-800/50 text-gray-500 border-white/5 hover:text-gray-300'"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border transition-all duration-200 text-[10px] font-bold"
                    title="Vista Grid">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span class="hidden sm:inline">Grid</span>
                </button>
                <button @click="mode = 'table'"
                    :class="mode === 'table' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-gray-800/50 text-gray-500 border-white/5 hover:text-gray-300'"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border transition-all duration-200 text-[10px] font-bold"
                    title="Vista Tabla">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span class="hidden sm:inline">Tabla</span>
                </button>
            </div>
        </div>

        {{-- Content: uses existing table.index partial --}}
        @include('profesors.activities.table.index')

        {{-- Pagination --}}
        @if(method_exists($pevaluacions, 'links'))
            <div class="mt-3 pt-3 border-t border-white/5">
                {{ $pevaluacions->appends(request()->query())->links('vendor.pagination.custom-tailwind') }}
            </div>
        @endif
    </div>
</div>
