{{-- Questions Tab --}}
<div class="space-y-4">
    {{-- Search & Filter Bar --}}
    <div class="flex flex-wrap items-center gap-3 pb-4 border-b border-white/5">
        <div class="relative flex-1 min-w-[200px] max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar preguntas..."
                class="w-full bg-gray-800/50 border border-white/10 rounded-xl pl-9 pr-3 py-1.5 text-xs text-gray-300 placeholder-gray-600 focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/20 transition-all duration-200">
            @if($search)
                <button wire:click="$set('search', '')" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            @endif
        </div>

        <select wire:model.live="filterType"
            class="bg-gray-800/50 border border-white/10 rounded-xl px-3 py-1.5 text-xs text-gray-300 focus:border-purple-500/50 transition-all duration-200">
            <option value="">Todos los tipos</option>
            <option value="multiple">Múltiple</option>
            <option value="open">Abierta</option>
            <option value="scale">Escala</option>
        </select>

        <select wire:model.live="filterSubject"
            class="bg-gray-800/50 border border-white/10 rounded-xl px-3 py-1.5 text-xs text-gray-300 focus:border-purple-500/50 transition-all duration-200 min-w-[150px]">
            <option value="">Todas las áreas</option>
            @foreach($subjects as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>

        <button wire:click="resetFilters"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200"
            title="Limpiar filtros">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Limpiar
        </button>

        <button wire:click="openQuestionModal"
            class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl text-xs font-bold bg-purple-500/10 text-purple-400 hover:bg-purple-500/20 border border-purple-500/20 transition-all duration-200 ml-auto">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            + Nueva Pregunta
        </button>
    </div>

    {{-- Results Summary --}}
    <div class="flex items-center justify-between">
        <p class="text-[11px] text-gray-500">
            Mostrando <span class="text-gray-400 font-medium">{{ $questions->firstItem() ?? 0 }}</span>–
            <span class="text-gray-400 font-medium">{{ $questions->lastItem() ?? 0 }}</span>
            de <span class="text-gray-400 font-medium">{{ $questions->total() }}</span> preguntas
        </p>
    </div>

    {{-- Questions Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Pregunta</th>
                    <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Tipo</th>
                    <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Área</th>
                    <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Dificultad</th>
                    <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</th>
                    <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $question)
                    <tr class="border-b border-white/5 hover:bg-white/[0.02] transition-colors">
                        <td class="py-3 px-4">
                            <p class="text-xs text-gray-300 max-w-[300px] truncate" title="{{ $question->pregunta }}">{{ $question->pregunta }}</p>
                            <span class="text-[10px] text-gray-600">{{ $question->created_at->format('d/m/Y') }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold {{ $question->tipo_pregunta === 'multiple' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : ($question->tipo_pregunta === 'open' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-green-500/10 text-green-400 border border-green-500/20') }}">
                                {{ $question->tipo_pregunta === 'multiple' ? 'Múltiple' : ($question->tipo_pregunta === 'open' ? 'Abierta' : 'Escala') }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-xs text-gray-400">{{ $question->asignatura_name ?? '—' }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-xs {{ $question->difficulty === 'easy' ? 'text-emerald-400' : ($question->difficulty === 'medium' ? 'text-amber-400' : 'text-red-400') }}">
                                {{ $question->difficulty === 'easy' ? 'Fácil' : ($question->difficulty === 'medium' ? 'Media' : 'Difícil') }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            @if($question->activo)
                                <span class="inline-flex items-center gap-1 text-xs text-emerald-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right">
                            <div class="inline-flex items-center gap-1">
                                <button wire:click="openQuestionModal({{ $question->id }})"
                                    title="Editar pregunta"
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button wire:click="confirmDeleteQuestion({{ $question->id }})"
                                    title="Eliminar pregunta"
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20 transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center">
                            <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            <p class="text-sm font-medium text-gray-400">
                                @if($search || $filterType || $filterSubject)
                                    No se encontraron preguntas
                                @else
                                    No hay preguntas registradas
                                @endif
                            </p>
                            @if($search || $filterType || $filterSubject)
                                <button wire:click="resetFilters" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                                    Limpiar filtros
                                </button>
                            @else
                                <button wire:click="openQuestionModal" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-purple-500/10 text-purple-400 hover:bg-purple-500/20 border border-purple-500/20 transition-all duration-200">
                                    + Crear primera pregunta
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($questions->hasPages())
        <div class="mt-4 pt-4 border-t border-white/5">
            {{ $questions->links('vendor.livewire.custom-tailwind') }}
        </div>
    @endif
</div>
