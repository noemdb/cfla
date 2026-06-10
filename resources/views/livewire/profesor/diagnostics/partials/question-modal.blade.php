{{-- Question Wizard Modal --}}
@if($showQuestionModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeQuestionModal"></div>
        <div class="relative bg-gray-900 border border-white/10 rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto shadow-2xl">
            {{-- Header --}}
            <div class="sticky top-0 bg-gray-900/95 backdrop-blur-sm border-b border-white/5 px-6 py-4 flex items-center justify-between z-10">
                <div>
                    <h3 class="text-sm font-bold text-white">
                        {{ $editingQuestionId ? 'Editar Pregunta' : 'Nueva Pregunta' }}
                    </h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">Paso {{ $currentStep }} de 3</p>
                </div>
                <button wire:click="closeQuestionModal" class="w-7 h-7 rounded-lg bg-gray-800/50 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Steps Indicator --}}
            <div class="px-6 py-4 border-b border-white/5">
                <div class="flex items-center gap-2">
                    @foreach(['Tipo y Área', 'Contenido', 'Configuración'] as $i => $stepName)
                        @php $stepNum = $i + 1; @endphp
                        <button wire:click="goToStep({{ $stepNum }})" class="flex items-center gap-2 group">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-[10px] font-bold transition-all duration-200
                                {{ $currentStep > $stepNum ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 cursor-pointer hover:bg-emerald-500/20' : ($currentStep === $stepNum ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'bg-gray-800/50 text-gray-500 border border-white/5 cursor-default') }}">
                                @if($currentStep > $stepNum)
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    {{ $stepNum }}
                                @endif
                            </span>
                            <span class="text-[10px] font-medium {{ $currentStep === $stepNum ? 'text-purple-400' : 'text-gray-500' }} hidden sm:inline">{{ $stepName }}</span>
                        </button>
                        @if(!$loop->last)
                            <div class="flex-1 h-px {{ $currentStep > $stepNum ? 'bg-emerald-500/30' : 'bg-white/5' }} mx-1"></div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Step Content --}}
            <div class="px-6 py-5 space-y-5">
                @if($currentStep === 1)
                    {{-- Step 1: Tipo y Área --}}
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Área de Formación</label>
                        <select wire:model="form.pensum_id"
                            class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/20 transition-all duration-200">
                            <option value="">Seleccione un área</option>
                            @foreach($profesor->pensums as $pensum)
                                <option value="{{ $pensum->id }}">{{ $pensum->asignatura_name ?? $pensum->full_name }}</option>
                            @endforeach
                        </select>
                        @error('form.pensum_id') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Diagnóstico</label>
                        <select wire:model="form.diag_main_id"
                            class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/20 transition-all duration-200">
                            <option value="">Seleccione diagnóstico</option>
                            @foreach($diagMains as $diag)
                                <option value="{{ $diag->id }}">{{ $diag->name }}</option>
                            @endforeach
                        </select>
                        @error('form.diag_main_id') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Tipo de Pregunta</label>
                        <div class="grid grid-cols-3 gap-3">
                            <button wire:click="$set('form.tipo_pregunta', 'multiple')"
                                class="relative p-4 rounded-xl border-2 transition-all duration-200 text-center
                                {{ $form['tipo_pregunta'] === 'multiple' ? 'border-purple-500/50 bg-purple-500/10' : 'border-white/5 bg-gray-800/30 hover:border-white/10' }}">
                                <svg class="w-6 h-6 mx-auto mb-1.5 {{ $form['tipo_pregunta'] === 'multiple' ? 'text-purple-400' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                <span class="block text-[11px] font-bold {{ $form['tipo_pregunta'] === 'multiple' ? 'text-purple-400' : 'text-gray-400' }}">Múltiple</span>
                                <span class="text-[9px] text-gray-500">Selección única</span>
                            </button>
                            <button wire:click="$set('form.tipo_pregunta', 'open')"
                                class="relative p-4 rounded-xl border-2 transition-all duration-200 text-center
                                {{ $form['tipo_pregunta'] === 'open' ? 'border-amber-500/50 bg-amber-500/10' : 'border-white/5 bg-gray-800/30 hover:border-white/10' }}">
                                <svg class="w-6 h-6 mx-auto mb-1.5 {{ $form['tipo_pregunta'] === 'open' ? 'text-amber-400' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                <span class="block text-[11px] font-bold {{ $form['tipo_pregunta'] === 'open' ? 'text-amber-400' : 'text-gray-400' }}">Abierta</span>
                                <span class="text-[9px] text-gray-500">Respuesta libre</span>
                            </button>
                            <button wire:click="$set('form.tipo_pregunta', 'scale')"
                                class="relative p-4 rounded-xl border-2 transition-all duration-200 text-center
                                {{ $form['tipo_pregunta'] === 'scale' ? 'border-green-500/50 bg-green-500/10' : 'border-white/5 bg-gray-800/30 hover:border-white/10' }}">
                                <svg class="w-6 h-6 mx-auto mb-1.5 {{ $form['tipo_pregunta'] === 'scale' ? 'text-green-400' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span class="block text-[11px] font-bold {{ $form['tipo_pregunta'] === 'scale' ? 'text-green-400' : 'text-gray-400' }}">Escala</span>
                                <span class="text-[9px] text-gray-500">Valoración 1-5</span>
                            </button>
                        </div>
                        @error('form.tipo_pregunta') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                @elseif($currentStep === 2)
                    {{-- Step 2: Contenido --}}
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Pregunta</label>
                        <textarea wire:model="form.pregunta" rows="3"
                            class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 placeholder-gray-600 focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/20 transition-all duration-200 resize-none"
                            placeholder="Escriba el enunciado de la pregunta..."></textarea>
                        @error('form.pregunta') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if($form['tipo_pregunta'] === 'multiple')
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Opciones de Respuesta</label>
                            <div class="space-y-2">
                                @foreach($form['options'] as $index => $option)
                                    <div class="flex items-center gap-2">
                                        <button wire:click="markCorrect({{ $index }})"
                                            class="shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-200
                                            {{ isset($option['is_correct']) && $option['is_correct'] ? 'border-emerald-500 bg-emerald-500/20' : 'border-white/10 hover:border-white/20' }}">
                                            @if(isset($option['is_correct']) && $option['is_correct'])
                                                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @endif
                                        </button>
                                        <input type="text" wire:model="form.options.{{ $index }}.text"
                                            class="flex-1 bg-gray-800/50 border border-white/10 rounded-xl px-3 py-1.5 text-xs text-gray-300 placeholder-gray-600 focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/20 transition-all duration-200"
                                            placeholder="Opción {{ chr(65 + $index) }}">
                                        <button wire:click="removeOption({{ $index }})" class="text-gray-500 hover:text-red-400 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            @if(count($form['options']) < 6)
                                <button wire:click="addOption"
                                    class="mt-2 inline-flex items-center gap-1 text-[11px] text-gray-500 hover:text-purple-400 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Añadir opción
                                </button>
                            @endif
                            @error('form.options') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    @elseif($form['tipo_pregunta'] === 'open')
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Respuesta Esperada (opcional)</label>
                            <textarea wire:model="form.expected_answer" rows="3"
                                class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 placeholder-gray-600 focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/20 transition-all duration-200 resize-none"
                                placeholder="Indique la respuesta esperada o criterios de evaluación..."></textarea>
                        </div>
                    @elseif($form['tipo_pregunta'] === 'scale')
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Configuración de Escala</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] text-gray-500 mb-1">Valor mínimo</label>
                                    <input type="number" wire:model="form.scale_min" min="0" max="10"
                                        class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-1.5 text-xs text-gray-300 focus:border-purple-500/50 transition-all duration-200">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-500 mb-1">Valor máximo</label>
                                    <input type="number" wire:model="form.scale_max" min="1" max="10"
                                        class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-1.5 text-xs text-gray-300 focus:border-purple-500/50 transition-all duration-200">
                                </div>
                            </div>
                        </div>
                    @endif

                @elseif($currentStep === 3)
                    {{-- Step 3: Configuración y Preview --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Orden</label>
                            <input type="number" wire:model="form.orden" min="1"
                                class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-purple-500/50 transition-all duration-200">
                            @error('form.orden') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Ponderación</label>
                            <input type="number" wire:model="form.ponderacion" min="1" max="100"
                                class="w-full bg-gray-800/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:border-purple-500/50 transition-all duration-200">
                            @error('form.ponderacion') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Dificultad</label>
                        <div class="flex gap-3">
                            <button wire:click="$set('form.difficulty', 'easy')"
                                class="flex-1 p-3 rounded-xl border-2 text-center transition-all duration-200
                                {{ $form['difficulty'] === 'easy' ? 'border-emerald-500/50 bg-emerald-500/10' : 'border-white/5 bg-gray-800/30 hover:border-white/10' }}">
                                <span class="block text-xs font-bold {{ $form['difficulty'] === 'easy' ? 'text-emerald-400' : 'text-gray-400' }}">Fácil</span>
                            </button>
                            <button wire:click="$set('form.difficulty', 'medium')"
                                class="flex-1 p-3 rounded-xl border-2 text-center transition-all duration-200
                                {{ $form['difficulty'] === 'medium' ? 'border-amber-500/50 bg-amber-500/10' : 'border-white/5 bg-gray-800/30 hover:border-white/10' }}">
                                <span class="block text-xs font-bold {{ $form['difficulty'] === 'medium' ? 'text-amber-400' : 'text-gray-400' }}">Media</span>
                            </button>
                            <button wire:click="$set('form.difficulty', 'hard')"
                                class="flex-1 p-3 rounded-xl border-2 text-center transition-all duration-200
                                {{ $form['difficulty'] === 'hard' ? 'border-red-500/50 bg-red-500/10' : 'border-white/5 bg-gray-800/30 hover:border-white/10' }}">
                                <span class="block text-xs font-bold {{ $form['difficulty'] === 'hard' ? 'text-red-400' : 'text-gray-400' }}">Difícil</span>
                            </button>
                        </div>
                        @error('form.difficulty') <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model="form.activo" id="question_active"
                            class="w-4 h-4 rounded border-white/10 bg-gray-800/50 text-purple-500 focus:ring-purple-500/20">
                        <label for="question_active" class="text-xs text-gray-400">Pregunta activa</label>
                    </div>

                    {{-- Preview --}}
                    @if($form['pregunta'])
                        <div class="bg-gray-800/30 border border-white/5 rounded-xl p-4">
                            <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">Vista previa</h4>
                            <p class="text-sm text-gray-200 mb-3">{{ $form['pregunta'] }}</p>
                            @if($form['tipo_pregunta'] === 'multiple')
                                <div class="space-y-1.5">
                                    @foreach($form['options'] as $option)
                                        <div class="flex items-center gap-2 p-2 rounded-lg {{ isset($option['is_correct']) && $option['is_correct'] ? 'bg-emerald-500/10 border border-emerald-500/20' : 'bg-gray-800/30 border border-white/5' }}">
                                            <span class="w-4 h-4 rounded-full border-2 {{ isset($option['is_correct']) && $option['is_correct'] ? 'border-emerald-500 bg-emerald-500/20' : 'border-white/10' }}"></span>
                                            <span class="text-xs {{ isset($option['is_correct']) && $option['is_correct'] ? 'text-emerald-300' : 'text-gray-400' }}">{{ $option['text'] ?? 'Opción vacía' }}</span>
                                            @if(isset($option['is_correct']) && $option['is_correct'])
                                                <svg class="w-3.5 h-3.5 text-emerald-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($form['tipo_pregunta'] === 'open')
                                <textarea disabled rows="2"
                                    class="w-full bg-gray-800/30 border border-white/5 rounded-xl px-3 py-2 text-xs text-gray-500"
                                    placeholder="Respuesta libre..."></textarea>
                            @elseif($form['tipo_pregunta'] === 'scale')
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] text-gray-500">{{ $form['scale_min'] ?? 1 }}</span>
                                    <div class="flex-1 h-2 bg-gray-700/50 rounded-full">
                                        <div class="w-0 h-2 bg-purple-500 rounded-full"></div>
                                    </div>
                                    <span class="text-[10px] text-gray-500">{{ $form['scale_max'] ?? 5 }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                @endif
            </div>

            {{-- Footer --}}
            <div class="sticky bottom-0 bg-gray-900/95 backdrop-blur-sm border-t border-white/5 px-6 py-4 flex items-center justify-between">
                <button wire:click="closeQuestionModal"
                    class="px-4 py-2 rounded-xl text-xs font-bold bg-gray-800/50 text-gray-400 hover:text-white border border-white/5 hover:border-white/10 transition-all duration-200">
                    Cancelar
                </button>
                <div class="flex items-center gap-2">
                    @if($currentStep > 1)
                        <button wire:click="prevStep"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-gray-800/50 text-gray-300 hover:text-white border border-white/5 hover:border-white/10 transition-all duration-200">
                            Anterior
                        </button>
                    @endif
                    @if($currentStep < 3)
                        <button wire:click="nextStep"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-purple-500/10 text-purple-400 hover:bg-purple-500/20 border border-purple-500/20 transition-all duration-200">
                            Siguiente
                        </button>
                    @else
                        <button wire:click="saveQuestion" wire:loading.attr="disabled"
                            class="px-6 py-2 rounded-xl text-xs font-bold bg-purple-500 text-white hover:bg-purple-600 transition-all duration-200 disabled:opacity-50">
                            <span wire:loading.remove wire:target="saveQuestion">Guardar Pregunta</span>
                            <span wire:loading wire:target="saveQuestion">Guardando...</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
