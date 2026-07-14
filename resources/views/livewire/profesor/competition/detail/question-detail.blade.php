<div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="detail-modal-{{ $detailQuestion->id }}">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeDetail"></div>

    {{-- Modal panel --}}
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-3xl bg-gray-900 border border-white/10 rounded-lg shadow-2xl overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-3 border-b border-white/5 bg-gray-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Detalle de la Pregunta</h3>
                        <p class="text-xs text-gray-500">
                            {{ $detailQuestion->pensum?->asignatura?->name ?? '?' }} ·
                            {{ $detailQuestion->pensum?->grado?->name ?? '?' }}
                        </p>
                    </div>
                </div>
                <button wire:click="closeDetail"
                    class="p-1.5 text-gray-500 hover:text-white hover:bg-white/5 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 max-h-[75vh] overflow-y-auto">
                <div class="space-y-6">

                    {{-- Question text --}}
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">Pregunta</label>
                        <div class="bg-gray-800/40 border border-white/5 rounded-lg p-4">
                            <p class="text-sm text-gray-200 leading-relaxed whitespace-pre-wrap">{{ $detailQuestion->text }}</p>
                        </div>
                    </div>

                    {{-- Info grid --}}
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-3">
                            <span class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Debate</span>
                            <span class="text-xs font-medium text-gray-200">
                                {{ $detailQuestion->debate?->name ?? '—' }}
                            </span>
                        </div>
                        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-3">
                            <span class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Categoría</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                {{ $detailQuestion->category }}
                            </span>
                        </div>
                        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-3">
                            <span class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Tiempo</span>
                            <span class="text-xs font-mono font-medium text-gray-200">{{ $detailQuestion->time }} segundos</span>
                        </div>
                        <div class="bg-gray-800/30 border border-white/5 rounded-lg p-3">
                            <span class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Ponderación</span>
                            <span class="text-xs font-mono font-medium text-gray-200">{{ $detailQuestion->weighting }} pts</span>
                        </div>
                    </div>

                    {{-- Status & Meta row --}}
                    <div class="flex items-center gap-3 flex-wrap text-[11px] text-gray-500">
                        <span class="flex items-center gap-1.5">
                            Estado:
                            @if($detailQuestion->status_active)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Activo</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-red-500/10 text-red-400 border border-red-500/20">Inactivo</span>
                            @endif
                        </span>
                        @if($detailQuestion->user)
                            <span>👤 {{ $detailQuestion->user->username }}</span>
                        @endif
                        <span>📅 {{ $detailQuestion->created_at?->format('d/m/Y H:i') }}</span>
                        @if($detailQuestion->updated_at && $detailQuestion->updated_at->ne($detailQuestion->created_at))
                            <span>🔄 {{ $detailQuestion->updated_at?->format('d/m/Y H:i') }}</span>
                        @endif
                    </div>

                    {{-- Observation --}}
                    @if($detailQuestion->observation)
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">Observación</label>
                            <div class="bg-gray-800/40 border border-white/5 rounded-lg p-3">
                                <p class="text-xs text-gray-400 italic">{{ $detailQuestion->observation }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Attachment --}}
                    @if($detailQuestion->attachment)
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">Archivo adjunto</label>
                            <a href="{{ $detailQuestion->attachment_url }}" target="_blank"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Ver adjunto
                            </a>
                        </div>
                    @endif

                    {{-- Options --}}
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">
                            Opciones ({{ $detailQuestion->options->count() }})
                        </label>
                        @if($detailQuestion->options->isEmpty())
                            <div class="text-center py-6 bg-gray-800/20 border border-dashed border-white/5 rounded-lg">
                                <svg class="w-8 h-8 text-gray-700 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-xs text-gray-600">Esta pregunta no tiene opciones registradas</p>
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach($detailQuestion->options as $option)
                                    <div class="flex items-start gap-3 bg-gray-800/30 border border-white/5 rounded-lg p-3 {{ $option->status_option_correct ? 'ring-1 ring-emerald-500/30 border-emerald-500/30' : '' }} {{ $option->status_wrong_answer ? 'ring-1 ring-red-500/30 border-red-500/30' : '' }}">
                                        {{-- Option badge --}}
                                        <div class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold shrink-0
                                            {{ $option->status_option_correct ? 'bg-emerald-500/20 text-emerald-400' : ($option->status_wrong_answer ? 'bg-red-500/20 text-red-400' : 'bg-gray-700/50 text-gray-400') }}">
                                            @if($option->status_option_correct)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @elseif($option->status_wrong_answer)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            @else
                                                {{ $loop->iteration }}
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-200 leading-relaxed">{{ $option->text }}</p>
                                            @if($option->observation)
                                                <p class="text-[11px] text-gray-500 italic mt-1">📝 {{ $option->observation }}</p>
                                            @endif
                                            @if($option->attachment)
                                                <a href="{{ $option->attachment_url }}" target="_blank"
                                                    class="inline-flex items-center gap-1 text-[10px] text-emerald-400 hover:text-emerald-300 underline mt-1">
                                                    📎 Ver adjunto
                                                </a>
                                            @endif
                                        </div>

                                        {{-- Option labels --}}
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            @if($option->status_option_correct)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                    Correcta
                                                </span>
                                            @endif
                                            @if($option->status_wrong_answer)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-red-500/10 text-red-400 border border-red-500/20">
                                                    Incorrecta
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-3 border-t border-white/5 bg-gray-800/30 flex items-center justify-end">
                <button wire:click="closeDetail"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
