<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-white mb-4">
            Diagnóstico Académico
        </h1>
        <p class="text-gray-300 text-lg mb-6">
            Comprueba tus conocimientos en las diferentes áreas de formación
        </p>
        
        <!-- Added guide button in header section -->
        <div class="flex justify-center">
            <button 
                wire:click="showGuide" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors font-semibold flex items-center space-x-2 shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Guía de Participación</span>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    @if (!empty($sessionStats))
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            <div class="bg-gradient-to-r from-green-800 to-green-600 p-6 rounded-lg">
                <div class="text-2xl font-bold">{{ $sessionStats['total_sessions'] }}</div>
                <div class="text-green-200">Sesiones Totales</div>
            </div>
            <div class="bg-gradient-to-r from-blue-800 to-blue-600 p-6 rounded-lg">
                <div class="text-2xl font-bold">{{ $sessionStats['completed_sessions'] }}</div>
                <div class="text-blue-200">Completadas</div>
            </div>
            <div class="bg-gradient-to-r from-purple-800 to-purple-600 p-6 rounded-lg">
                <div class="text-2xl font-bold">{{ $sessionStats['total_answers'] }}</div>
                <div class="text-purple-200">Respuestas</div>
            </div>
            <div class="bg-gradient-to-r from-yellow-800 to-yellow-600 p-6 rounded-lg">
                <div class="text-2xl font-bold">{{ round($sessionStats['average_progress']) }}%</div>
                <div class="text-yellow-200">Progreso Promedio</div>
            </div>
        </div>
    @endif

    <!-- Areas de Formación -->
    <p class="text-gray-300 text-lg">
        Bienvenido/a <strong>{{ $currentStudent->full_name }}</strong> . Puedes realizar tu diagnóstico.
    </p>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($pensums as $pensum)
            <div class="diagnostic-card rounded-xl p-6 border @if($pensum['is_completed']) border-green-500 bg-gradient-to-br from-gray-800 to-green-900/20 @else border-gray-700 @endif hover:border-green-500 cursor-pointer"
                wire:click="startDiagnostic({{ $pensum['id'] }})">

                <!-- Progress Ring -->
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <!-- Added completion icon for completed areas -->
                        <div class="flex items-center mb-2">
                            <h3 class="text-xl font-semibold text-white">
                                {{ $pensum['name'] }}
                            </h3>
                            @if($pensum['is_completed'])
                                <svg class="w-5 h-5 text-green-400 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                        {{-- <p class="text-gray-400 text-sm">
                            {{ $pensum['description'] }}
                        </p> --}}
                    </div>

                    <div class="relative w-16 h-16">
                        <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-gray-700" stroke="currentColor" stroke-width="3" fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <!-- Enhanced progress ring color for completed areas -->
                            <path class="@if($pensum['is_completed']) text-green-400 @else text-green-500 @endif progress-ring" stroke="currentColor" stroke-width="3"
                                fill="none" stroke-linecap="round"
                                stroke-dasharray="{{ $pensum['progress_percentage'] }}, 100"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xs font-semibold @if($pensum['is_completed']) text-green-300 @else text-white @endif">
                                {{ $pensum['progress_percentage'] }}%
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="flex justify-between items-center mb-4">
                    <div class="text-sm text-gray-400">
                        <span class="@if($pensum['is_completed']) text-green-300 @else text-green-400 @endif">{{ $pensum['completed_questions'] }}</span>
                        / {{ $pensum['total_questions'] }} preguntas
                    </div>

                    @if ($pensum['is_completed'])
                        <!-- Enhanced completed badge styling -->
                        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg">
                            ✓ Completado
                        </span>
                    @else
                        <span class="bg-yellow-600 text-white px-2 py-1 rounded-full text-xs">
                            En progreso
                        </span>
                    @endif
                </div>

                <!-- Difficulty Distribution -->
                <div class="mb-3">
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="text-xs text-gray-400 font-medium">Distribución por dificultad</span>
                    </div>
                    <!-- Added container for bars and labels -->
                    <div class="space-y-2">
                        <!-- Progress bars -->
                        <div class="flex space-x-2">
                            @foreach (['easy' => 'Fácil', 'medium' => 'Medio', 'hard' => 'Difícil'] as $level => $label)
                                @if (isset($pensum['difficulty_distribution'][$level]))
                                    <div class="flex-1 bg-gray-700 rounded-full h-2">
                                        <div class="@if($level === 'easy') bg-gradient-to-r from-green-500 to-green-400 @elseif($level === 'medium') bg-gradient-to-r from-yellow-500 to-yellow-400 @else bg-gradient-to-r from-red-500 to-red-400 @endif h-2 rounded-full"
                                            style="width: {{ ($pensum['difficulty_distribution'][$level] / $pensum['total_questions']) * 100 }}%">
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <!-- Added text labels below progress bars -->
                        <div class="flex space-x-2">
                            @foreach (['easy' => 'Fácil', 'medium' => 'Medio', 'hard' => 'Difícil'] as $level => $label)
                                @if (isset($pensum['difficulty_distribution'][$level]))
                                    <div class="flex-1 text-center">
                                        <span class="text-xs @if($level === 'easy') text-green-400 @elseif($level === 'medium') text-yellow-400 @else text-red-400 @endif font-medium">
                                            {{ $label }}
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $pensum['difficulty_distribution'][$level] }}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <!-- Modified button to prevent event bubbling and call specific methods -->
                    <button
                        wire:click.stop="@if ($pensum['is_completed']) reviewAnswers({{ $pensum['id'] }}) @else startDiagnostic({{ $pensum['id'] }}) @endif"
                        class="@if($pensum['is_completed']) bg-green-500 hover:bg-green-600 @else bg-green-600 hover:bg-green-700 @endif text-white px-6 py-2 rounded-lg transition-colors font-semibold">
                        @if ($pensum['is_completed'])
                            Ver Respuestas
                        @else
                            Continuar Diagnóstico
                        @endif
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="text-gray-400 text-lg">
                    No hay áreas de diagnóstico disponibles en este momento.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Added modal for reviewing answers -->
    @if ($showAnsweredModal && $selectedPensum)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            wire:click="closeAnsweredQuestionsModal">
            <div class="bg-gray-800 rounded-lg p-6 max-w-4xl max-h-[80vh] overflow-y-auto m-4" wire:click.stop>
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white">
                        Respuestas - {{ $selectedPensum->asignatura->full_name ?? 'Área' }}
                    </h2>
                    <button wire:click="closeAnsweredQuestionsModal" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Questions and Answers List -->
                <div class="space-y-4">
                    @php
                        $answeredQuestions = $this->getAnsweredQuestionsWithAnswers();
                    @endphp

                    @forelse($answeredQuestions as $index => $item)
                        <div class="bg-gray-700 rounded-lg p-4">
                            <!-- Question -->
                            <div class="mb-3">
                                <div class="flex items-start justify-between mb-2">
                                    <h4 class="text-lg font-semibold text-white">
                                        Pregunta {{ $index + 1 }}
                                    </h4>
                                    <div class="flex items-center space-x-2">
                                        @if ($item['question']->difficulty)
                                            @php
                                                $difficultyClasses = match ($item['question']->difficulty) {
                                                    'easy' => 'bg-green-600 text-white',
                                                    'medium' => 'bg-yellow-600 text-white',
                                                    default => 'bg-red-600 text-white',
                                                };
                                            @endphp
                                            <span class="px-2 py-1 rounded-full text-xs {{ $difficultyClasses }}">
                                                {{ ucfirst($item['question']->difficulty) }}
                                            </span>
                                        @endif
                                        <span class="text-xs text-gray-400">
                                            {{ \Carbon\Carbon::parse($item['completed_at'])->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                </div>
                                <p class="text-gray-300">{{ $item['question']->pregunta }}</p>
                            </div>

                            <!-- Answer -->
                            <div class="border-t border-gray-600 pt-3">
                                <div class="flex items-center mb-2">
                                    <span class="text-sm font-medium text-gray-400 mr-2">Tu respuesta:</span>
                                </div>

                                @if ($item['question']->tipo_pregunta === 'multiple')
                                    <!-- Multiple choice answer -->
                                    <div class="space-y-2">
                                        @foreach ($item['question']->options as $option)
                                            @php
                                                $isSelected = $option->opcion === $item['answer'];
                                                $bgClass = $isSelected
                                                    ? 'bg-green-600 text-white'
                                                    : 'bg-gray-600 text-gray-300';
                                                $borderClass = $isSelected
                                                    ? 'border-white bg-white'
                                                    : 'border-gray-400';
                                            @endphp

                                            <div class="flex items-center p-2 rounded {{ $bgClass }}">
                                                <span
                                                    class="w-6 h-6 rounded-full border-2 flex items-center justify-center mr-3 {{ $borderClass }}">
                                                    @if ($isSelected)
                                                        <svg class="w-3 h-3 text-green-600" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                    @endif
                                                </span>
                                                {{ $option->opcion }}
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($item['question']->tipo_pregunta === 'scale')
                                    <!-- Scale answer -->
                                    <div class="flex items-center space-x-2">
                                        <span class="text-2xl font-bold text-green-400">{{ $item['answer'] }}</span>
                                        <span class="text-gray-400">/ 10</span>
                                        <div class="flex-1 bg-gray-600 rounded-full h-2 ml-4">
                                            <div class="bg-green-500 h-2 rounded-full"
                                                style="width: {{ ($item['answer'] / 10) * 100 }}%"></div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Open text answer -->
                                    <div class="bg-gray-600 p-3 rounded">
                                        <p class="text-white">{{ $item['answer'] }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-400">No hay respuestas guardadas para esta área.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    @endif
</div>
