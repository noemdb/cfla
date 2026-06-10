<div>
    <div class="space-y-2">
        @forelse($pensums as $pensum)
            @php
                $isSelected = $selectedPensumId == $pensum->id;
                $questionCount = $this->getQuestionCount($pensum->id);
            @endphp
            <div class="rounded-xl border transition-all duration-200 {{ $isSelected ? 'bg-emerald-500/10 border-emerald-500/30 ring-1 ring-emerald-500/20' : 'bg-gray-800/30 border-white/5 hover:bg-gray-800/50 hover:border-white/10' }}">
                <div class="p-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $pensum->grado?->name ?? '?' }}</span>
                            <p class="text-sm font-medium text-white truncate mt-0.5">{{ $pensum->asignatura?->name ?? '?' }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center gap-1 text-[10px] text-gray-500">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $questionCount }} preguntas
                                </span>
                                <span class="text-[10px] text-gray-600">{{ $pensum->grado?->pestudio?->name ?? '?' }}</span>
                            </div>
                        </div>
                        <button wire:click="{{ $isSelected ? 'closeQuestions' : 'setModeQuestions(' . $pensum->id . ')' }}"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold transition-all duration-200 {{ $isSelected ? 'bg-emerald-500/20 text-emerald-400' : 'bg-gray-700/50 text-gray-400 hover:bg-gray-700 hover:text-white border border-white/10' }}">
                            @if($isSelected)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <svg class="w-10 h-10 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <p class="text-sm text-gray-500">No hay áreas de formación asignadas</p>
                <p class="text-xs text-gray-600 mt-1">Consulte con el coordinador si no visualiza sus asignaciones.</p>
            </div>
        @endforelse
    </div>
</div>
