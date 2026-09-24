<div class="min-h-screen flex flex-col" x-data="{ init() {
        const key = 'diag-progress-' + @js($currentSessionId ?? 'none');
        const saved = localStorage.getItem(key);
        if (saved) { try { const p = JSON.parse(saved); if (p.progress) $wire.set('progress', p.progress); } catch(e){} }
        $wire.on('diag-progress-persist', ({progress, sessionId}) => {
            localStorage.setItem('diag-progress-' + sessionId, JSON.stringify({progress, at: Date.now()}));
        });
    } }">
    <!-- Header con progreso -->
    <!-- Updated header to use rounded card styling consistent with other sections -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div
                class="bg-gray-900/40 backdrop-blur-xl border border-white/5 rounded-lg p-8 mb-8 shadow-2xl relative overflow-hidden">
                <!-- Shine effect -->
                <div class="absolute inset-0 bg-gradient-to-tr from-white/5 to-transparent pointer-events-none"></div>
                <!-- Added status indicator to top right of container -->
                <div class="flex justify-end mb-2">
                    @if ($isReviewMode)
                        <span class="text-sm bg-blue-600 px-3 py-1 rounded-lg">Modo Revisión</span>
                    @elseif($showAnsweredQuestions)
                        <button type="button" wire:click="toggleQuestionView" wire:loading.attr="disabled" class="text-sm bg-green-600 hover:bg-green-700 px-3 py-1 rounded-lg transition-colors">Preguntas Contestadas</button>
                    @else
                        <button type="button" wire:click="toggleQuestionView" wire:loading.attr="disabled" wire:target="toggleQuestionView" class="text-sm bg-orange-600 hover:bg-orange-700 px-3 py-1 rounded-lg transition-colors">Preguntas Pendientes</button>
                    @endif
                </div>

                <div class="flex items-center justify-between mb-6">
                    <div>
                        <!-- Removed status spans from h2 title -->
                        <h2 class="text-lg font-semibold text-white">
                            {{ $selectedPensum->asignatura->full_name ?? 'Diagnóstico' }}
                        </h2>
                        <!-- Fixed question count display logic to show correct counts -->
                        <p class="text-gray-400 mt-1">
                            @if ($showAnsweredQuestions)
                                Pregunta {{ $currentQuestionIndex + 1 }} de {{ count($answeredQuestionIds) }}
                                ({{ count($answeredQuestionIds) }} contestadas)
                            @else
                                Pregunta {{ $currentQuestionIndex + 1 }} de {{ count($unansweredQuestionIds) }}
                                ({{ count($unansweredQuestionIds) }} pendientes)
                            @endif
                        </p>
                    </div>

                    <div class="flex space-x-3">
                        <!-- Fixed modal button to use correct Livewire method -->
                        @if (!$isReviewMode && count($answeredQuestionIds) > 0)
                            <button wire:click="openAnsweredQuestionsModal"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Ver Contestadas ({{ count($answeredQuestionIds) }})</span>
                            </button>
                        @endif

                        <button wire:click="backToDashboard"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            Salir
                        </button>
                    </div>
                </div>

                <!-- Barra de progreso -->
                <div class="w-full bg-gray-700 rounded-full h-3 mb-2">
                    <div class="bg-gradient-to-r from-green-500 to-green-400 h-3 rounded-full transition-all duration-500"
                        style="width: {{ $progress }}%">
                    </div>
                </div>
                <div class="text-right text-sm text-gray-400">
                    {{ $progress }}% completado
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido de la pregunta -->
    <div class="flex-1 container mx-auto px-4 py-2">
        @if ($currentQuestion)
            <div class="max-w-4xl mx-auto" wire:key="question-block-{{ $currentQuestionId }}"
                x-data="{ answered: {{ $selectedAnswer ? 'true' : 'false' }} }"
                @input="answered = true" @change="answered = true">
                <!-- Pregunta -->
                <div
                    class="bg-gray-900/40 backdrop-blur-xl border border-white/5 rounded-lg p-8 mb-8 shadow-2xl relative overflow-hidden">
                    <!-- Shine effect -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-white/5 to-transparent pointer-events-none">
                    </div>
                    <div class="flex items-start space-x-4 mb-6">
                        <div
                            class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-semibold">
                            {{ $currentQuestionIndex + 1 }}
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-white mb-2">
                                {{ $currentQuestion->pregunta }}
                            </h3>

                            <!-- Metadatos de la pregunta -->
                            <div class="flex space-x-4 text-sm text-gray-400">
                                <span class="bg-gray-700 px-2 py-1 rounded">
                                    {{ ucfirst($currentQuestion->difficulty) }}
                                </span>
                                <span class="bg-gray-700 px-2 py-1 rounded">
                                    Peso: {{ $currentQuestion->weighing }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Opciones de respuesta -->
                    <div class="space-y-3">
                        @if ($currentQuestion->tipo_pregunta === 'multiple')
                            @foreach ($currentQuestion->options as $option)
                                <label
                                    class="flex items-center p-4 bg-gray-800/50 border border-white/5 rounded-lg transition-all duration-200
                                    {{ $isReviewMode || $showAnsweredQuestions ? 'cursor-default' : 'hover:bg-gray-700/50 hover:border-emerald-500/30 cursor-pointer' }}">
                                    <input type="radio" wire:model="selectedAnswer"
                                        name="question-{{ $currentQuestion->id }}"
                                        value="{{ $option->opcion }}"
                                        {{ $isReviewMode || $showAnsweredQuestions ? 'disabled' : '' }}
                                        class="w-4 h-4 text-green-600 bg-gray-600 border-gray-500 focus:ring-green-500
                                        {{ $isReviewMode || $showAnsweredQuestions ? 'opacity-50 cursor-not-allowed' : '' }}">
                                    <span
                                        class="ml-3 text-white {{ ($isReviewMode || $showAnsweredQuestions) && $selectedAnswer === $option->opcion ? 'font-semibold text-green-400' : '' }}">
                                        {{ $option->opcion }}
                                    </span>
                                </label>
                            @endforeach
                        @elseif($currentQuestion->tipo_pregunta === 'scale')
                            <fieldset class="flex items-center justify-between p-4 bg-gray-700 rounded-lg">
                                <legend class="sr-only">Selecciona un valor de 1 a 10 para tu respuesta</legend>
                                <span class="text-gray-400" aria-hidden="true">1 (Muy bajo)</span>
                                <div class="flex space-x-2">
                                    @for ($i = 1; $i <= 10; $i++)
                                        <label
                                            class="{{ $isReviewMode || $showAnsweredQuestions ? 'cursor-default' : 'cursor-pointer' }}">
                                            <input type="radio" wire:model="selectedAnswer"
                                                name="question-{{ $currentQuestion->id }}"
                                                value="{{ $i }}" aria-label="{{ $i }} de 10"
                                                {{ $isReviewMode || $showAnsweredQuestions ? 'disabled' : '' }}
                                                class="sr-only">
                                            <div
                                                class="w-8 h-8 rounded-full border-2 border-gray-500 flex items-center justify-center text-sm
                                                {{ $selectedAnswer == $i ? 'bg-green-600 border-green-600 text-white' : ($isReviewMode || $showAnsweredQuestions ? 'opacity-50' : 'hover:border-green-400') }}">
                                                {{ $i }}
                                            </div>
                                        </label>
                                    @endfor
                                </div>
                                <span class="text-gray-400" aria-hidden="true">10 (Muy alto)</span>
                            </fieldset>
                        @elseif($currentQuestion->tipo_pregunta === 'open')
                            <textarea wire:model="selectedAnswer" rows="4"
                                placeholder="{{ $isReviewMode || $showAnsweredQuestions ? '' : 'Escribe tu respuesta aquí...' }}"
                                {{ $isReviewMode || $showAnsweredQuestions ? 'readonly' : '' }}
                                class="w-full p-4 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-transparent
                                {{ $isReviewMode || $showAnsweredQuestions ? 'opacity-75 cursor-default' : '' }}">
                            </textarea>
                        @endif
                    </div>
                </div>

                <!-- Navegación -->
                <div class="flex justify-between items-center">
                    <button wire:click="previousQuestion" wire:loading.attr="disabled" wire:target="previousQuestion,nextQuestion,saveAnswer" @if ($currentQuestionIndex === 0 || $isProcessing) disabled @endif
                        class="bg-gray-600 hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-2 rounded-lg flex items-center space-x-2 transition-all duration-200">
                        @if ($isProcessing)
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                        @endif
                        <span>Anterior</span>
                    </button>

                    <div class="text-center text-gray-400">
                        <div class="text-sm">Pregunta {{ $currentQuestionIndex + 1 }} de {{ count($questionIds) }}</div>
                        @if ($isReviewMode)
                            <div class="text-xs text-blue-400 mt-1">Solo lectura</div>
                        @elseif($showAnsweredQuestions)
                            <div class="text-xs text-green-400 mt-1">Ya contestada</div>
                        @endif
                    </div>

                    @if ($isReviewMode || $showAnsweredQuestions)
                        @if ($currentQuestionIndex < count($questionIds) - 1)
                            <button wire:click="nextQuestion"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center space-x-2 transition-all duration-200">
                                <span>Siguiente</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        @else
                            <button wire:click="backToDashboard"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg flex items-center space-x-2 transition-all duration-200">
                                <span>{{ $isReviewMode ? 'Finalizar Revisión' : 'Volver al Dashboard' }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        @endif
                    @else
                        <button wire:click="{{ $currentQuestionIndex === count($questionIds) - 1 ? 'confirmFinish' : 'nextQuestion' }}" wire:loading.attr="disabled" wire:target="nextQuestion,confirmFinish,saveAnswer" :disabled="!answered"
                            class="bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-2 rounded-lg flex items-center space-x-2 transition-all duration-200">
                            <span>
                                @if ($currentQuestionIndex === count($questionIds) - 1)
                                    @if ($isProcessing)
                                        Finalizando...
                                    @else
                                        Finalizar
                                    @endif
                                @else
                                    @if ($isProcessing)
                                        Guardando...
                                    @else
                                        Siguiente
                                    @endif
                                @endif
                            </span>
                            @if ($isProcessing)
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            @endif
                        </button>
                    @endif
                </div>
            </div>
        @else
            <!-- Updated empty state message to be more accurate -->
            <div class="max-w-4xl mx-auto text-center">
                <div class="bg-gray-900/40 backdrop-blur-xl border border-white/5 rounded-lg p-8 shadow-2xl">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <h3 class="text-lg font-medium text-white mb-2">
                        No hay preguntas pendientes
                    </h3>
                    <p class="text-gray-400 mb-6">
                        Has completado todas las preguntas disponibles en esta área.
                    </p>
                    <button wire:click="backToDashboard"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                        Volver al Dashboard
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal de respuestas (partial compartido) -->
    @include('livewire.diagnostic.partials.answers-modal')

    @if ($currentQuestion)
        <div class="fixed bottom-4 inset-x-0 z-40 pointer-events-none">
            <div class="max-w-4xl mx-auto px-4 flex justify-start">
                <div wire:ignore
                    x-data="{
                        seconds: 0,
                        timer: null,
                        get label() {
                            const p = (n) => String(n).padStart(2, '0');
                            return p(Math.floor(this.seconds / 3600)) + ':' + p(Math.floor((this.seconds % 3600) / 60)) + ':' + p(this.seconds % 60);
                        },
                        init() { this.timer = setInterval(() => this.seconds++, 1000); },
                        destroy() { clearInterval(this.timer); }
                    }"
                    class="pointer-events-auto w-16 h-16 rounded-full flex flex-col items-center justify-center bg-gray-900/80 backdrop-blur-md border border-white/10 shadow-lg"
                    title="Tiempo transcurrido">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-[10px] font-mono text-gray-300 leading-none mt-0.5"
                        x-text="label">00:00:00</span>
                </div>
            </div>
        </div>
    @endif
</div>
