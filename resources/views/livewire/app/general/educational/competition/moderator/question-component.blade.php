<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-emerald-500/20 pb-4">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-emerald-500/10 rounded-lg">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h5 class="text-lg font-bold text-white uppercase tracking-tight">
                    {{ $debate->name }}
                </h5>
                <p class="text-[10px] text-emerald-500 font-black tracking-widest uppercase">Gestión de Interrogantes
                </p>
            </div>
            @if ($debate->status_active)
                <span class="flex h-2 w-2 relative ml-2">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
            @endif
        </div>

        <div class="flex items-center space-x-2">
            @if ($debate->status_active)
                <button wire:click="setOffline({{ $debate->id }})"
                    class="p-2 hover:bg-red-500/20 rounded-lg text-red-400 transition-all duration-300 group"
                    title="Desactivar Debate">
                    <svg class="w-5 h-5 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            @else
                <button wire:click="setOnline({{ $debate->id }})"
                    class="p-2 hover:bg-emerald-500/20 rounded-lg text-emerald-400 transition-all duration-300 group"
                    title="Activar Debate">
                    <svg class="w-5 h-5 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- Filtros: Categoría + Ponderación --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="space-y-4 relative group">
            <x-select placeholder="Seleccione Categoría" wire:model.live="category" :options="$list_category"
                class="bg-gray-900 border-emerald-500/20 text-white focus:border-emerald-500 focus:ring-emerald-500" />

            {{-- Controles de filtro por estado --}}
            <div class="flex flex-wrap items-center gap-3 pt-1">

                {{-- Toggle: Sin Responder --}}
                <label for="filter-unanswered" class="group flex items-center gap-2.5 cursor-pointer select-none">
                    <div class="relative">
                        <input id="filter-unanswered" type="checkbox" wire:model.live="filterUnanswered"
                            class="sr-only peer" />
                        <div
                            class="w-9 h-5 bg-gray-700 peer-focus:outline-none rounded-full peer
                            peer-checked:bg-orange-600 transition-colors duration-300">
                        </div>
                        <div
                            class="absolute top-[2px] left-[2px] w-4 h-4 bg-white rounded-full shadow
                            transition-transform duration-300 peer-checked:translate-x-4">
                        </div>
                    </div>
                    <span
                        class="text-[11px] font-bold uppercase tracking-widest
                        text-gray-400 group-hover:text-orange-400 transition-colors duration-200">
                        Sin responder
                    </span>
                </label>

                {{-- Toggle: Solo Respondidas --}}
                <label for="filter-answered" class="group flex items-center gap-2.5 cursor-pointer select-none">
                    <div class="relative">
                        <input id="filter-answered" type="checkbox" wire:model.live="filterAnswered"
                            class="sr-only peer" />
                        <div
                            class="w-9 h-5 bg-gray-700 peer-focus:outline-none rounded-full peer
                            peer-checked:bg-emerald-600 transition-colors duration-300">
                        </div>
                        <div
                            class="absolute top-[2px] left-[2px] w-4 h-4 bg-white rounded-full shadow
                            transition-transform duration-300 peer-checked:translate-x-4">
                        </div>
                    </div>
                    <span
                        class="text-[11px] font-bold uppercase tracking-widest
                        text-gray-400 group-hover:text-emerald-400 transition-colors duration-200">
                        Solo Respondidas
                    </span>
                </label>

                </label>

            </div>
        </div>
        <div class="space-y-2">
            <x-select placeholder="Seleccione Ponderación" wire:model.live="weighting" :options="$list_weighting"
                class="bg-gray-900 border-emerald-500/20 text-white focus:border-emerald-500 focus:ring-emerald-500" />
            {{-- Checkboxes de selección múltiple --}}
            @if ($list_weighting->isNotEmpty())
                <div class="flex flex-wrap gap-2 pt-0.5">
                    @foreach ($list_weighting as $val)
                        @php $checked = in_array($val, $selectedWeightings); @endphp
                        <label for="w-{{ $val }}"
                            class="flex items-center gap-1.5 cursor-pointer select-none px-3 py-1.5 rounded-lg border text-xs font-bold uppercase tracking-wider transition-all duration-200
                                {{ $checked
                                    ? 'bg-emerald-600 border-emerald-400 text-white shadow-md shadow-emerald-500/20'
                                    : 'bg-gray-900/50 border-emerald-500/15 text-gray-400 hover:border-emerald-500/40 hover:text-emerald-300' }}">
                            <input id="w-{{ $val }}" type="checkbox" wire:model.live="selectedWeightings"
                                value="{{ $val }}" class="sr-only" />
                            @if ($checked)
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                            {{ $val }} pts
                        </label>
                    @endforeach
                </div>
            @endif
        </div>
    </div>



    <div class="mt-8">
        @if ($questions->isNotEmpty())
            @include('livewire.app.general.educational.competition.moderator.partials.questions')
        @else
            <div class="text-center py-12 bg-gray-900/40 rounded-lg border border-dashed border-emerald-500/20">
                <svg class="w-12 h-12 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="text-gray-500 font-medium italic">
                    @if ($category)
                        No se encontraron interrogantes para esta categoría.
                    @else
                        Por favor, seleccione una categoría para visualizar las preguntas.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
