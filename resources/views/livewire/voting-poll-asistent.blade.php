<div class="space-y-6">
    @if (!$isCompleted)
        <!-- Barra de progreso -->
        <div class="bg-gray-800/90 backdrop-blur-sm rounded-xl border border-gray-700/50 shadow-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Progreso del Asistente</h3>
                <span class="text-sm text-gray-400">
                    {{ $currentPollIndex + 1 }} de {{ $polls->count() }} encuestas
                </span>
            </div>

            <!-- Barra de progreso visual -->
            <div class="w-full bg-gray-700 rounded-full h-3 mb-4">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-500 ease-out"
                    style="width: {{ (($currentPollIndex + 1) / $polls->count()) * 100 }}%"></div>
            </div>

            <!-- Progreso numérico -->
            <div class="flex justify-between text-sm text-gray-400">
                <span>{{ round((($currentPollIndex + 1) / $polls->count()) * 100) }}% completado</span>
                <span>{{ $polls->count() - ($currentPollIndex + 1) }} restantes</span>
            </div>
        </div>

        <!-- Encuesta actual -->
        @if ($currentPoll)
            <div
                class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700/50 shadow-2xl overflow-hidden">
                <!-- Header de la encuesta -->
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-white mb-2">{{ $currentPoll->title }}</h2>
                            <div class="flex items-center space-x-4 text-green-100">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3a4 4 0 118 0v4m-4 8a4 4 0 11-8 0v-4h8v4z"></path>
                                    </svg>
                                    {{ $currentPoll->options->count() }} opciones
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Activa
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-white">{{ $currentPollIndex + 1 }}</div>
                            <div class="text-green-100 text-sm">de {{ $polls->count() }}</div>
                        </div>
                    </div>
                </div>

                <!-- Contenido de la encuesta -->
                <div class="p-8">
                    @if ($voteMessage)
                        <div
                            class="mb-6 p-4 rounded-lg border {{ $voteMessageType === 'success' ? 'bg-green-900/50 border-green-700 text-green-300' : ($voteMessageType === 'error' ? 'bg-red-900/50 border-red-700 text-red-300' : 'bg-blue-900/50 border-blue-700 text-blue-300') }}">
                            <div class="flex items-center">
                                @if ($voteMessageType === 'success')
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @elseif($voteMessageType === 'error')
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @endif
                                {{ $voteMessage }}
                            </div>
                        </div>
                    @endif

                    @if (!$hasVoted)
                        <!-- Opciones de votación -->
                        <div class="space-y-3 mb-8">
                            @foreach ($currentPoll->options as $option)
                                <button wire:click="selectOption({{ $option->id }})"
                                    class="w-full p-4 text-left rounded-lg border-2 transition-all duration-200 {{ $selectedOption == $option->id ? 'border-green-500 bg-green-900/30 text-green-300' : 'border-gray-600 bg-gray-700/50 text-gray-300 hover:border-gray-500 hover:bg-gray-700/70' }}"
                                    {{ $isVoting ? 'disabled' : '' }}>
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium">{{ $option->label }}</span>
                                        @if ($selectedOption == $option->id)
                                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        <!-- Botón de votar -->
                        @if ($selectedOption)
                            <button wire:click="submitVote"
                                class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold py-4 px-6 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                                {{ $isVoting ? 'disabled' : '' }}>
                                @if ($isVoting)
                                    <div class="flex items-center justify-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Registrando voto...
                                    </div>
                                @else
                                    <div class="flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Confirmar Voto
                                    </div>
                                @endif
                            </button>
                        @endif
                    @endif

                    <!-- Navegación -->
                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-700">
                        <button wire:click="previousPoll"
                            class="flex items-center px-4 py-2 text-gray-400 hover:text-white transition-colors duration-200 {{ $currentPollIndex == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $currentPollIndex == 0 ? 'disabled' : '' }}>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Anterior
                        </button>

                        <div class="flex space-x-3">
                            @if (!$hasVoted)
                                <button wire:click="skipPoll"
                                    class="px-4 py-2 text-gray-400 hover:text-white border border-gray-600 rounded-lg hover:border-gray-500 transition-all duration-200">
                                    Omitir
                                </button>
                            @endif

                            <button wire:click="nextPoll"
                                class="flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200">
                                {{ $currentPollIndex == $polls->count() - 1 ? 'Finalizar' : 'Siguiente' }}
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <!-- Resumen final -->
        <div class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700/50 shadow-2xl overflow-hidden">
            <!-- Header del resumen -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-8 py-6">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-white mb-2">🎉 ¡Asistente Completado!</h2>
                    <p class="text-green-100 text-lg">Has revisado todas las encuestas activas</p>
                </div>
            </div>

            <!-- Estadísticas del resumen -->
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-6 text-center">
                        <div class="text-3xl font-bold text-white mb-2">{{ $polls->count() }}</div>
                        <div class="text-blue-100">Encuestas Revisadas</div>
                    </div>
                    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-lg p-6 text-center">
                        <div class="text-3xl font-bold text-white mb-2">{{ count($completedSessions) }}</div>
                        <div class="text-green-100">Participaciones</div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg p-6 text-center">
                        <div class="text-3xl font-bold text-white mb-2">{{ count($completedSessions) }}</div>
                        <div class="text-purple-100">Códigos QR</div>
                    </div>
                </div>

                @if (count($completedSessions) > 0)
                    <h3 class="text-xl font-semibold text-white mb-6">Tus Participaciones</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        @foreach ($completedSessions as $session)
                            <div class="bg-gray-700/50 rounded-lg p-6 border border-gray-600">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-white mb-2">{{ $session->poll->title }}</h4>
                                        <p class="text-gray-400 text-sm">
                                            Participación registrada: {{ $session->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                    <div class="ml-4">
                                        {!! $this->generateQRCode($session->uuid) !!}
                                    </div>
                                </div>
                                <a href="{{ route('poll.participation.show', $session->uuid) }}"
                                    class="inline-flex items-center text-green-400 hover:text-green-300 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M7 7h10v10M7 7l10 10">
                                        </path>
                                    </svg>
                                    Ver detalles de participación
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Acciones finales -->
                <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('voting.results') }}"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9z">
                            </path>
                        </svg>
                        Ver Resultados
                    </a>
                    <a href="{{ route('voting.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white font-semibold rounded-lg hover:from-gray-700 hover:to-gray-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
