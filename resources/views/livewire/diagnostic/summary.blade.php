<div class="min-h-screen bg-gray-900 text-white p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-lg font-bold text-center mb-2">Diagnóstico Completado</h1>
            <p class="text-gray-400 text-center">{{ $selectedPensum->asignatura->full_name ?? 'Área Académica' }}</p>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-800 rounded-lg p-6 text-center">
                <div class="text-lg font-bold text-green-400 mb-2">
                    {{ $sessionStats['total_answers'] ?? 0 }}
                </div>
                <div class="text-gray-400">Preguntas Respondidas</div>
            </div>

            <div class="bg-gray-800 rounded-lg p-6 text-center">
                <div class="text-lg font-bold text-blue-400 mb-2">
                    {{ number_format($sessionStats['average_progress'] ?? 0, 1) }}%
                </div>
                <div class="text-gray-400">Progreso Promedio</div>
            </div>

            <div class="bg-gray-800 rounded-lg p-6 text-center">
                <div class="text-lg font-bold text-purple-400 mb-2">
                    {{ $sessionStats['completed_sessions'] ?? 0 }}
                </div>
                <div class="text-gray-400">Sesiones Completadas</div>
            </div>
        </div>

        <!-- Results -->
        @if (!empty($results) && ($results['total_answered'] ?? 0) > 0)
            <div class="bg-gray-800 rounded-lg p-6 mb-8">
                <h2 class="text-lg font-semibold text-white mb-4">Resultados</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-emerald-400">
                            {{ number_format($results['precision'], 1) }}%</div>
                        <div class="text-gray-400 text-sm mt-1">Precisión (selección múltiple)</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-400">
                            {{ $results['correct_answers'] }}/{{ $results['total_answered'] }}</div>
                        <div class="text-gray-400 text-sm mt-1">Respuestas correctas</div>
                    </div>
                </div>

                @if (!empty($results['by_difficulty']))
                    <div class="mt-6 space-y-3">
                        @foreach (['easy' => 'Fácil', 'medium' => 'Medio', 'hard' => 'Difícil'] as $key => $label)
                            @if (isset($results['by_difficulty'][$key]))
                                @php
                                    $diff = $results['by_difficulty'][$key];
                                    $pct = $diff['total'] > 0 ? round(($diff['correct'] / $diff['total']) * 100) : 0;
                                @endphp
                                <div>
                                    <div class="flex justify-between text-sm text-gray-300 mb-1">
                                        <span>{{ $label }}</span>
                                        <span>{{ $diff['correct'] }}/{{ $diff['total'] }} ({{ $pct }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-700 rounded-full h-2">
                                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <!-- Completion Message -->
        <div class="bg-green-900/20 border border-green-500/30 rounded-lg p-6 mb-8">
            <div class="flex items-center mb-2">
                <svg class="w-8 h-8 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="text-lg font-semibold text-green-400">¡Felicitaciones!</h2>
            </div>
            <p class="text-gray-300 mb-2">
                Has completado exitosamente el diagnóstico para
                <strong>{{ $selectedPensum->asignatura->full_name ?? 'esta área' }}</strong>.
                Tus respuestas han sido registradas y serán utilizadas para mejorar las estrategias de aprendizaje.
            </p>
            <div class="flex flex-wrap gap-3">
                <button wire:click="openAnsweredQuestionsModal"
                    class="text-gray-400 hover:text-white underline transition-colors duration-200">
                    Ver Respuestas Detalladas
                </button>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <button wire:click="backToDashboard"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg font-medium transition-colors duration-200">
                Volver al Dashboard
            </button>

            <button wire:click="restartIdentification"
                class="px-6 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg font-medium transition-colors duration-200">
                Nuevo Diagnóstico
            </button>
        </div>
    </div>

    <!-- Modal de respuestas (partial compartido) -->
    @include('livewire.diagnostic.partials.answers-modal')
</div>
