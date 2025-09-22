<div class="min-h-screen flex flex-col">
    <!-- Header con progreso -->
    <!-- Updated header to use rounded card styling consistent with other sections -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-gray-800 rounded-xl p-8 mb-8">
                <!-- Added status indicator to top right of container -->
                <div class="flex justify-end mb-4">
                    @if ($isReviewMode)
                        <span class="text-sm bg-blue-600 px-3 py-1 rounded-lg">Modo Revisión</span>
                    @elseif($showAnsweredQuestions)
                        <span class="text-sm bg-green-600 px-3 py-1 rounded-lg">Preguntas Contestadas</span>
                    @else
                        <span class="text-sm bg-orange-600 px-3 py-1 rounded-lg">Preguntas Pendientes</span>
                    @endif
                </div>

                <div class="flex items-center justify-between mb-6">
                    <div>
                        <!-- Removed status spans from h2 title -->
                        <h2 class="text-xl font-semibold text-white">
                            {{ $selectedPensum->asignatura->full_name ?? 'Diagnóstico' }}
                        </h2>
                        <!-- Fixed question count display logic to show correct counts -->
                        <p class="text-gray-400 mt-1">
                            @if ($showAnsweredQuestions)
                                Pregunta {{ $currentQuestionIndex + 1 }} de {{ count($answeredQuestions) }}
                                ({{ count($answeredQuestions) }} contestadas)
                            @else
                                Pregunta {{ $currentQuestionIndex + 1 }} de {{ count($unansweredQuestions) }}
                                ({{ count($unansweredQuestions) }} pendientes)
                            @endif
                        </p>
                    </div>

                    <div class="flex space-x-3">
                        <!-- Fixed modal button to use correct Livewire method -->
                        @if (!$isReviewMode && count($answeredQuestions) > 0)
                            <button wire:click="openAnsweredQuestionsModal"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Ver Contestadas ({{ count($answeredQuestions) }})</span>
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
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-gray-800 rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-700">
                    <h3 class="text-xl font-semibold">Respuestas Detalladas</h3>
                    <button wire:click="closeAnsweredQuestionsModal"
                        class="text-gray-400 hover:text-white transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    @if (count($this->getAnsweredQuestionsWithAnswers()) > 0)
                        <div class="space-y-6">
                            @foreach ($this->getAnsweredQuestionsWithAnswers() as $index => $item)
                                <div class="bg-gray-700 rounded-lg p-4">
                                    <!-- Question Header -->
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded">
                                                    Pregunta {{ $index + 1 }}
                                                </span>
                                                @if ($item['question']->difficulty)
                                                    @php
                                                        $difficultyClass = match ($item['question']->difficulty) {
                                                            'easy' => 'bg-green-600 text-white',
                                                            'medium' => 'bg-yellow-600 text-white',
                                                            'hard' => 'bg-red-600 text-white',
                                                            default => 'bg-gray-600 text-white',
                                                        };
                                                    @endphp
                                                    <span class="text-xs px-2 py-1 rounded {{ $difficultyClass }}">
                                                        {{ ucfirst($item['question']->difficulty) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <h4 class="font-medium text-white mb-2">{{ $item['question']->pregunta }}
                                            </h4>
                                        </div>
                                        <span class="text-xs text-gray-400 ml-4">
                                            {{ \Carbon\Carbon::parse($item['completed_at'])->format('d/m/Y H:i') }}
                                        </span>
                                    </div>

                                    <!-- Answer Section -->
                                    <div class="bg-gray-600 rounded p-3">
                                        <div class="text-sm text-gray-300 mb-1">Tu respuesta:</div>

                                        @if ($item['question']->tipo_pregunta === 'multiple')
                                            <div class="text-white font-medium">{{ $item['answer'] }}</div>

                                            @if ($item['question']->options->count() > 0)
                                                <div class="mt-3">
                                                    <div class="text-xs text-gray-400 mb-2">Opciones disponibles:</div>
                                                    <div class="space-y-1">
                                                        @foreach ($item['question']->options as $option)
                                                            <div
                                                                class="flex items-center text-sm
                                                            {{ $option->opcion === $item['answer'] ? 'text-green-400 font-medium' : 'text-gray-400' }}">
                                                                <span
                                                                    class="w-2 h-2 rounded-full mr-2
                                                                {{ $option->opcion === $item['answer'] ? 'bg-green-400' : 'bg-gray-500' }}">
                                                                </span>
                                                                {{ $option->opcion }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @elseif($item['question']->tipo_pregunta === 'scale')
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="text-white font-medium text-lg">{{ $item['answer'] }}</span>
                                                <span class="text-gray-400">/ 10</span>
                                                <div class="flex-1 bg-gray-700 rounded-full h-2 ml-3">
                                                    <div class="bg-blue-500 h-2 rounded-full"
                                                        style="width: {{ ($item['answer'] / 10) * 100 }}%"></div>
                                                </div>
                                            </div>
                                        @elseif($item['question']->tipo_pregunta === 'open')
                                            <div class="text-white bg-gray-700 rounded p-2 italic">
                                                "{{ $item['answer'] }}"
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p class="text-gray-400">No se encontraron respuestas para mostrar.</p>
                        </div>
                    @endif
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
