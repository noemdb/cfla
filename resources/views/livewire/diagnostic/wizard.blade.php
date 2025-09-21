<div class="min-h-screen flex flex-col">
    <!-- Header con progreso -->
    <div class="bg-gray-800 border-b border-gray-700 p-4">
        <div class="container mx-auto">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-white">
                        {{ $selectedPensum->asignatura->full_name ?? 'Diagnóstico' }}
                        @if ($isReviewMode)
                            <span class="ml-2 text-sm bg-blue-600 px-2 py-1 rounded">Modo Revisión</span>
                        @elseif($showAnsweredQuestions)
                            <span class="ml-2 text-sm bg-green-600 px-2 py-1 rounded">Preguntas Contestadas</span>
                        @else
                            <span class="ml-2 text-sm bg-orange-600 px-2 py-1 rounded">Preguntas Pendientes</span>
                        @endif
                    </h2>
                    <!-- Fixed question count display logic to show correct counts -->
                    <p class="text-gray-400">
                        @if ($showAnsweredQuestions)
                            Pregunta {{ $currentQuestionIndex + 1 }} de {{ count($answeredQuestions) }}
                            ({{ count($answeredQuestions) }} contestadas)
                        @else
                            Pregunta {{ $currentQuestionIndex + 1 }} de {{ count($unansweredQuestions) }}
                            ({{ count($unansweredQuestions) }} pendientes)
                        @endif
                    </p>
                </div>

                <div class="flex space-x-2">
                    <!-- Fixed modal button to use correct Livewire method -->
                    @if (!$isReviewMode && count($answeredQuestions) > 0)
                        <button wire:click="openAnsweredQuestionsModal"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Ver Contestadas ({{ count($answeredQuestions) }})</span>
                        </button>
                    @endif

                    <button wire:click="backToDashboard"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                        Salir
                    </button>
                </div>
            </div>

            <!-- Barra de progreso -->
            <div class="w-full bg-gray-700 rounded-full h-3">
                <div class="bg-gradient-to-r from-green-500 to-green-400 h-3 rounded-full transition-all duration-500"
                    style="width: {{ $progress }}%">
                </div>
            </div>
            <div class="text-right text-sm text-gray-400 mt-1">
                {{ $progress }}% completado
            </div>
        </div>
    </div>

    <!-- Contenido de la pregunta -->
    <div class="flex-1 container mx-auto px-4 py-8">
        @if ($currentQuestion)
            <div class="max-w-4xl mx-auto">
                <!-- Pregunta -->
                <div class="bg-gray-800 rounded-xl p-8 mb-8">
                    <div class="flex items-start space-x-4 mb-6">
                        <div
                            class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-semibold">
                            {{ $currentQuestionIndex + 1 }}
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-medium text-white mb-2">
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
                                    class="flex items-center p-4 bg-gray-700 rounded-lg transition-colors
                                    {{ $isReviewMode || $showAnsweredQuestions ? 'cursor-default' : 'hover:bg-gray-600 cursor-pointer' }}">
                                    <input type="radio" wire:model.live="selectedAnswer" wire:change="$refresh"
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
                            <div class="flex items-center justify-between p-4 bg-gray-700 rounded-lg">
                                <span class="text-gray-400">1 (Muy bajo)</span>
                                <div class="flex space-x-2">
                                    @for ($i = 1; $i <= 10; $i++)
                                        <label
                                            class="{{ $isReviewMode || $showAnsweredQuestions ? 'cursor-default' : 'cursor-pointer' }}">
                                            <input type="radio" wire:model.live="selectedAnswer"
                                                wire:change="$refresh" value="{{ $i }}"
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
                                <span class="text-gray-400">10 (Muy alto)</span>
                            </div>
                        @elseif($currentQuestion->tipo_pregunta === 'open')
                            <textarea wire:model.live="selectedAnswer" wire:change="$refresh" rows="4"
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
                    <button wire:click="previousQuestion" @if ($currentQuestionIndex === 0 || $isProcessing) disabled @endif
                        class="bg-gray-600 hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200">
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
                        <div class="text-sm">Pregunta {{ $currentQuestionIndex + 1 }} de {{ count($questions) }}</div>
                        @if ($isReviewMode)
                            <div class="text-xs text-blue-400 mt-1">Solo lectura</div>
                        @elseif($showAnsweredQuestions)
                            <div class="text-xs text-green-400 mt-1">Ya contestada</div>
                        @endif
                    </div>

                    @if ($isReviewMode || $showAnsweredQuestions)
                        @if ($currentQuestionIndex < count($questions) - 1)
                            <button wire:click="nextQuestion"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200">
                                <span>Siguiente</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        @else
                            <button wire:click="backToDashboard"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200">
                                <span>{{ $isReviewMode ? 'Finalizar Revisión' : 'Volver al Dashboard' }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        @endif
                    @else
                        <button wire:click="nextQuestion" @if (!$this->canProceed) disabled @endif
                            class="bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition-all duration-200">
                            <span>
                                @if ($currentQuestionIndex === count($questions) - 1)
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
                <div class="bg-gray-800 rounded-xl p-8">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <h3 class="text-xl font-medium text-white mb-2">
                        No hay preguntas pendientes
                    </h3>
                    <p class="text-gray-400 mb-6">
                        Has completado todas las preguntas disponibles en esta área.
                    </p>
                    <button wire:click="backToDashboard"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg">
                        Volver al Dashboard
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Updated modal to use Livewire properties and methods -->
    @if ($showAnsweredModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    wire:click="closeAnsweredQuestionsModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold text-gray-900">
                                Preguntas Contestadas - {{ $selectedPensum->asignatura->full_name ?? 'Área' }}
                            </h2>
                            <button wire:click="closeAnsweredQuestionsModal"
                                class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="max-h-96 overflow-y-auto space-y-4">
                            @foreach ($this->getAnsweredQuestionsWithAnswers() as $index => $item)
                                <div class="bg-gray-50 rounded-lg p-4 border">
                                    <div class="flex items-start space-x-3 mb-3">
                                        <div
                                            class="bg-green-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-semibold">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 mb-2">
                                                {{ $item['question']->pregunta }}
                                            </h4>
                                            <div class="flex space-x-2 text-xs text-gray-500 mb-2">
                                                <span class="bg-gray-200 px-2 py-1 rounded">
                                                    {{ ucfirst($item['question']->difficulty) }}
                                                </span>
                                                <span class="bg-gray-200 px-2 py-1 rounded">
                                                    {{ ucfirst($item['question']->tipo_pregunta) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ml-9">
                                        <div class="bg-green-100 border-l-4 border-green-500 p-3 rounded">
                                            <p class="text-sm font-medium text-green-800">Respuesta:</p>
                                            <p class="text-green-700">{{ $item['answer'] }}</p>
                                            <p class="text-xs text-green-600 mt-1">
                                                Contestada el
                                                {{ \Carbon\Carbon::parse($item['completed_at'])->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if (count($this->getAnsweredQuestionsWithAnswers()) === 0)
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <p class="text-gray-500">No hay preguntas contestadas en esta área.</p>
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="closeAnsweredQuestionsModal"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Debug info for development -->
    @if (config('app.debug'))
        <div class="mt-4 p-2 bg-gray-900 rounded text-xs text-gray-400">
            Debug: selectedAnswer = "{{ $selectedAnswer }}" | canProceed = {{ $this->canProceed ? 'true' : 'false' }}
        </div>
    @endif
</div>
