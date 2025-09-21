<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-white mb-4">
            Diagnóstico Académico
        </h1>
        <p class="text-gray-300 text-lg">
            Comprueba tus conocimientos en las diferentes áreas de formación
        </p>
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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($pensums as $pensum)
            <div class="diagnostic-card rounded-xl p-6 border border-gray-700 hover:border-green-500 cursor-pointer"
                wire:click="startDiagnostic({{ $pensum['id'] }})">

                <!-- Progress Ring -->
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-white mb-2">
                            {{ $pensum['name'] }}
                        </h3>
                        {{-- <p class="text-gray-400 text-sm">
                            {{ $pensum['description'] }}
                        </p> --}}
                    </div>

                    <div class="relative w-16 h-16">
                        <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-gray-700" stroke="currentColor" stroke-width="3" fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-green-500 progress-ring" stroke="currentColor" stroke-width="3"
                                fill="none" stroke-linecap="round"
                                stroke-dasharray="{{ $pensum['progress_percentage'] }}, 100"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xs font-semibold text-white">
                                {{ $pensum['progress_percentage'] }}%
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="flex justify-between items-center mb-4">
                    <div class="text-sm text-gray-400">
                        <span class="text-green-400">{{ $pensum['completed_questions'] }}</span>
                        / {{ $pensum['total_questions'] }} preguntas
                    </div>

                    @if ($pensum['is_completed'])
                        <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">
                            Completado
                        </span>
                    @else
                        <span class="bg-yellow-600 text-white px-2 py-1 rounded-full text-xs">
                            En progreso
                        </span>
                    @endif
                </div>

                <!-- Difficulty Distribution -->
                <div class="flex space-x-2">
                    @foreach (['easy' => 'Fácil', 'medium' => 'Medio', 'hard' => 'Difícil'] as $level => $label)
                        @if (isset($pensum['difficulty_distribution'][$level]))
                            <div class="flex-1 bg-gray-700 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-500 to-green-400 h-2 rounded-full"
                                    style="width: {{ ($pensum['difficulty_distribution'][$level] / $pensum['total_questions']) * 100 }}%">
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="mt-4 text-center">
                    <!-- Added wire:click actions for review and continue buttons -->
                    <button
                        wire:click="@if ($pensum['is_completed']) reviewAnswers({{ $pensum['id'] }}) @else startDiagnostic({{ $pensum['id'] }}) @endif"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors">
                        @if ($pensum['is_completed'])
                            Revisar Respuestas
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
</div>
