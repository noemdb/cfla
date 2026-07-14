<div class="fixed inset-0 z-[9998] overflow-y-auto" wire:key="options-modal-{{ $optionQuestionId }}">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeOptions"></div>

    {{-- Modal panel --}}
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-2xl bg-gray-900 border border-white/10 rounded-xl shadow-2xl overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-white/5 bg-gray-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">
                            Gestionar Opciones
                        </h3>
                        <p class="text-xs text-gray-500">
                            @php $optQuestion = \App\Models\app\Educational\DebateQuestion::find($optionQuestionId); @endphp
                            @if($optQuestion)
                                {{ Str::limit($optQuestion->text, 60) }}
                            @endif
                        </p>
                    </div>
                </div>
                <button wire:click="closeOptions"
                    class="p-1.5 text-gray-500 hover:text-white hover:bg-white/5 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 max-h-[75vh] overflow-y-auto">
                <div class="space-y-5">

                    {{-- Existing options list --}}
                    @php $optionsList = \App\Models\app\Educational\DebateOption::where('question_id', $optionQuestionId)->orderBy('created_at')->get(); @endphp

                    @if($optionsList->isEmpty())
                        <div class="text-center py-8 bg-gray-800/20 border border-dashed border-white/5 rounded-xl">
                            <svg class="w-10 h-10 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-sm text-gray-500">No hay opciones registradas</p>
                            <p class="text-xs text-gray-600 mt-1">Agregue la primera opción usando el formulario de abajo.</p>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach($optionsList as $option)
                                <div class="flex items-start gap-3 bg-gray-800/30 border border-white/5 rounded-xl p-3 {{ $option->status_option_correct ? 'ring-1 ring-emerald-500/30 border-emerald-500/30' : '' }} {{ $option->status_wrong_answer ? 'ring-1 ring-red-500/30 border-red-500/30' : '' }}">
                                    {{-- Badge/Icon --}}
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

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-200">{{ $option->text }}</p>
                                        @if($option->observation)
                                            <p class="text-xs text-gray-500 italic mt-0.5">📝 {{ $option->observation }}</p>
                                        @endif
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex items-center gap-1 shrink-0">
                                        <button wire:click="toggleOptionCorrect({{ $option->id }})"
                                            title="{{ $option->status_option_correct ? 'Quitar como correcta' : 'Marcar como correcta' }}"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold transition-all duration-200 {{ $option->status_option_correct ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-gray-700/50 text-gray-500 hover:bg-emerald-500/20 hover:text-emerald-400 border border-white/10' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                        <button wire:click="setEditOption({{ $option->id }})"
                                            title="Editar opción"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all duration-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button wire:click="deleteOption({{ $option->id }})"
                                            title="Eliminar opción"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20 transition-all duration-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Divider --}}
                    <div class="border-t border-white/5 pt-4">
                        <h4 class="text-xs font-bold text-gray-300 uppercase tracking-wider mb-3">
                            {{ $editOptionId ? 'Editar Opción' : 'Nueva Opción' }}
                        </h4>

                        {{-- Option form --}}
                        <div class="space-y-3">
                            {{-- Text --}}
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Texto de la opción</label>
                                <textarea wire:model="optionForm.text" rows="2"
                                    class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-violet-500/50 resize-y"
                                    placeholder="Escriba la opción aquí..."></textarea>
                                @error('optionForm.text') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Observation --}}
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Observación (opcional)</label>
                                <textarea wire:model="optionForm.observation" rows="1"
                                    class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-violet-500/50 resize-y"
                                    placeholder="Observación adicional..."></textarea>
                                @error('optionForm.observation') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Flags --}}
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="optionForm.status_option_correct" value="1"
                                        class="rounded border-white/10 bg-gray-800/50 text-emerald-500 focus:ring-emerald-500/20">
                                    <span class="text-[11px] text-gray-400">Opción correcta</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="optionForm.status_wrong_answer" value="1"
                                        class="rounded border-white/10 bg-gray-800/50 text-red-500 focus:ring-red-500/20">
                                    <span class="text-[11px] text-gray-400">Opción incorrecta</span>
                                </label>
                            </div>

                            {{-- Buttons --}}
                            <div class="flex items-center gap-2 pt-1">
                                @if($editOptionId)
                                    <button wire:click="setCreateOption"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                                        Cancelar edición
                                    </button>
                                @endif
                                <button wire:click="saveOption"
                                    class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl text-[11px] font-bold bg-violet-500/10 text-violet-400 hover:bg-violet-500/20 border border-violet-500/20 transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    {{ $editOptionId ? 'Guardar Cambios' : 'Agregar Opción' }}
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-3 border-t border-white/5 bg-gray-800/30 flex items-center justify-end">
                <button wire:click="closeOptions"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
