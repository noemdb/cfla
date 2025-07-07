<div>
    @if (!$isCompleted)
        <!-- Indicador de carga del fingerprint -->
        @if ($isLoadingFingerprint)
            <div class="bg-yellow-800/90 backdrop-blur-sm rounded-xl border border-yellow-700/50 shadow-2xl p-6 mb-6">
                <div class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-yellow-300" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <div class="text-yellow-300">
                        <div class="font-semibold">Preparando sistema de votación...</div>
                        <div class="text-sm text-yellow-400">Generando identificación segura del dispositivo</div>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    @if ($fingerprintAttempts > 0)
                        <div class="text-yellow-400 text-sm mb-2">Intento {{ $fingerprintAttempts }} de
                            {{ $maxFingerprintAttempts }}</div>
                    @endif
                    <div class="space-x-2">
                        <button wire:click="retryFingerprintGeneration"
                            class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm transition-colors">
                            Reintentar
                        </button>
                        {{-- <button wire:click="forceSetFingerprint"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors">
                            Continuar sin identificación avanzada
                        </button> --}}
                    </div>
                    {{-- <div class="text-xs text-yellow-500 mt-2">
                        Si el problema persiste, puedes continuar con identificación básica
                    </div> --}}
                </div>
            </div>
        @endif

        <!-- Barra de progreso -->
        <div class="bg-gray-800/90 backdrop-blur-sm rounded-xl border border-gray-700/50 shadow-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Progreso del Asistente</h3>
                <span class="text-sm text-gray-400">{{ $currentPollIndex + 1 }} de {{ $totalPolls }} encuestas</span>
            </div>
            <!-- Barra de progreso visual -->
            <div class="w-full bg-gray-700 rounded-full h-3 mb-4">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-500 ease-out"
                    style="width: {{ (($currentPollIndex + 1) / $totalPolls) * 100 }}%"></div>
            </div>
            <!-- Progreso numérico -->
            <div class="flex justify-between text-sm text-gray-400">
                <span>{{ round((($currentPollIndex + 1) / $totalPolls) * 100) }}% completado</span>
                <span>{{ $totalPolls - ($currentPollIndex + 1) }} restantes</span>
            </div>
        </div>

        <!-- Encuesta actual -->
        @if ($currentPoll)
            <div
                class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700/50 shadow-2xl overflow-hidden {{ $isLoadingFingerprint ? 'opacity-50 pointer-events-none' : '' }}">
                <!-- Header de la encuesta -->
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-white mb-2">{{ $currentPoll['title'] }}</h2>
                            <div class="flex items-center space-x-4 text-green-100">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3a4 4 0 118 0v4m-4 8a4 4 0 11-8 0v-4h8v4z"></path>
                                    </svg>
                                    {{ count($currentPoll['options']) }} opciones
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
                            <div class="text-green-100 text-sm">de {{ $totalPolls }}</div>
                        </div>
                    </div>
                </div>

                <!-- Contenido de la encuesta -->
                <div class="p-8">
                    @if ($successMessage || $errorMessage)
                        <div
                            class="mb-6 p-4 rounded-lg border {{ $successMessage ? 'bg-green-900/50 border-green-700 text-green-300' : 'bg-red-900/50 border-red-700 text-red-300' }}">
                            <div class="flex items-center">
                                @if ($successMessage)
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ $successMessage }}
                                @else
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    {{ $errorMessage }}
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (!$hasVoted && !$isLoadingFingerprint)
                        <!-- Opciones de votación -->
                        <div class="space-y-3 mb-8">
                            @foreach ($currentPoll['options'] as $option)
                                <button wire:click="selectOption({{ $option['id'] }})"
                                    class="w-full p-4 text-left rounded-lg border-2 transition-all duration-200 {{ $selectedOption == $option['id'] ? 'border-green-500 bg-green-900/30 text-green-300' : 'border-gray-600 bg-gray-700/50 text-gray-300 hover:border-gray-500 hover:bg-gray-700/70' }}"
                                    {{ $isVoting ? 'disabled' : '' }}>
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium">{{ $option['label'] ?? null }}</span>
                                        @if ($selectedOption == $option['id'])
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
                            @if (!$hasVoted && !$isLoadingFingerprint)
                                <button wire:click="skipPoll"
                                    class="px-4 py-2 text-gray-400 border border-gray-600 rounded-lg transition-all duration-200 opacity-50 cursor-not-allowed"
                                    disabled title="Debes votar antes de omitir esta encuesta">
                                    Omitir
                                </button>
                            @endif

                            <button wire:click="nextPoll"
                                class="flex items-center px-6 py-2 rounded-lg transition-all duration-200 {{ $hasVoted || $isLoadingFingerprint ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800' : 'bg-gray-600 text-gray-400 cursor-not-allowed opacity-50' }}"
                                {{ !$hasVoted && !$isLoadingFingerprint ? 'disabled' : '' }}
                                title="{{ !$hasVoted && !$isLoadingFingerprint ? 'Debes votar antes de continuar' : '' }}">
                                {{ $currentPollIndex == $totalPolls - 1 ? 'Finalizar' : 'Siguiente' }}
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

        @if (!$hasVoted && !$isLoadingFingerprint && $currentPoll)
            <div class="mt-4 p-3 bg-yellow-700/20 border border-yellow-700/30 rounded-lg">
                <p class="text-yellow-200 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <strong>Información:</strong> Debes confirmar tu voto antes de poder continuar a la siguiente
                    encuesta.
                </p>
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
                        <div class="text-3xl font-bold text-white mb-2">{{ $totalPolls }}</div>
                        <div class="text-blue-100">Encuestas Revisadas</div>
                    </div>
                    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-lg p-6 text-center">
                        <div class="text-3xl font-bold text-white mb-2">{{ $votedCount }}</div>
                        <div class="text-green-100">Participaciones</div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg p-6 text-center">
                        <div class="text-3xl font-bold text-white mb-2">{{ $votedCount }}</div>
                        <div class="text-purple-100">Códigos QR</div>
                    </div>
                </div>

                @if ($votedCount > 0)
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
                                        @if ($session->votes->isNotEmpty())
                                            <p class="text-green-400 text-sm mt-1">
                                                Opción seleccionada: {{ $session->votes->first()->option->label }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="m-2 p-2 bg-white rounded-lg">
                                        {!! $this->generateQRCode($session->uuid) !!}
                                    </div>
                                </div>
                                <button wire:click="showParticipationDetails({{ $session->id }})"
                                    class="inline-flex items-center text-green-400 hover:text-green-300 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    Ver detalles de participación
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Acciones finales -->
                <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                    @if (Route::has('voting.asistent'))
                        <a href="{{ route('voting.asistent') }}"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white font-semibold rounded-lg hover:from-gray-700 hover:to-gray-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            </svg>
                            Volver al Inicio
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Alert de Voto Confirmado -->
    @if ($showVoteAlert)
        <div x-data="{ show: @entangle('showVoteAlert') }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" x-show="show"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <!-- Alert Content -->
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="inline-block w-full max-w-lg my-8 overflow-hidden text-left align-middle transition-all transform bg-gray-800 shadow-2xl rounded-2xl border border-gray-700"
                    x-show="show" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.stop>

                    <!-- Header del Alert -->
                    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-bold text-white">¡Voto Registrado Exitosamente!</h3>
                                <p class="text-green-100 text-sm">Tu participación ha sido confirmada</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido del Alert -->
                    <div class="px-6 py-4">
                        <!-- Información del voto -->
                        <div class="mb-4">
                            <h4 class="text-white font-semibold mb-2">{{ $voteAlertData['pollTitle'] ?? '' }}</h4>
                            <div class="bg-green-900/30 border border-green-700 rounded-lg p-3">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span
                                        class="text-green-300 text-sm">{{ $voteAlertData['selectedOption'] ?? '' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Progreso -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-300 text-sm">Progreso del Asistente</span>
                                <span
                                    class="text-white text-sm font-semibold">{{ $voteAlertData['progressPercentage'] ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-500"
                                    style="width: {{ $voteAlertData['progressPercentage'] ?? 0 }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-400 mt-1">
                                <span>{{ $voteAlertData['completedPolls'] ?? 0 }} completadas</span>
                                <span>{{ $voteAlertData['remainingPolls'] ?? 0 }} restantes</span>
                            </div>
                        </div>

                        <!-- Estadísticas -->
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-400">
                                    {{ $voteAlertData['completedPolls'] ?? 0 }}</div>
                                <div class="text-xs text-gray-400">Completadas</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-400">
                                    {{ $voteAlertData['remainingPolls'] ?? 0 }}</div>
                                <div class="text-xs text-gray-400">Restantes</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-400">
                                    {{ $voteAlertData['totalPolls'] ?? 0 }}</div>
                                <div class="text-xs text-gray-400">Total</div>
                            </div>
                        </div>

                        <!-- Mensaje según el estado -->
                        @if (isset($voteAlertData['isLastPoll']) && $voteAlertData['isLastPoll'])
                            <div class="bg-purple-900/30 border border-purple-700 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-purple-400 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12z"></path>
                                    </svg>
                                    <span class="text-purple-300 text-sm font-medium">¡Felicidades! Has completado
                                        todas las encuestas disponibles.</span>
                                </div>
                            </div>
                        @else
                            <div class="bg-blue-900/30 border border-blue-700 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-blue-300 text-sm">
                                        @if (($voteAlertData['remainingPolls'] ?? 0) == 1)
                                            Queda <strong>1 encuesta</strong> más por participar.
                                        @else
                                            Quedan <strong>{{ $voteAlertData['remainingPolls'] ?? 0 }}
                                                encuestas</strong> más por participar.
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Footer del Alert -->
                    <div class="bg-gray-700/30 px-6 py-4 border-t border-gray-600">
                        <div
                            class="flex flex-col sm:flex-row items-center justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                            @if (isset($voteAlertData['isLastPoll']) && $voteAlertData['isLastPoll'])
                                <button wire:click="continueToNextPoll"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12z"></path>
                                    </svg>
                                    Ver Resumen Final
                                </button>
                            @else
                                <button wire:click="closeVoteAlert"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                                    Quedarse Aquí
                                </button>
                                <button wire:click="continueToNextPoll"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    Continuar a la Siguiente
                                </button>
                            @endif
                        </div>

                        <!-- Mensaje informativo -->
                        <div class="mt-3 text-center">
                            <p class="text-gray-400 text-xs">
                                💡 Ahora puedes continuar a la siguiente encuesta o quedarte para revisar los resultados
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Incluir el modal de participación -->
    @include('livewire.modals.participation')

</div>

@section('script')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Iniciando sistema avanzado de detección de IP...');

            let fingerprintGenerated = false;
            let detectedIPs = new Set();

            // Función mejorada para generar fingerprint
            function generateAdvancedFingerprint() {
                try {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    ctx.textBaseline = 'top';
                    ctx.font = '14px Arial';
                    ctx.fillText('Fingerprint Canvas', 2, 2);

                    const webglCanvas = document.createElement('canvas');
                    const gl = webglCanvas.getContext('webgl') || webglCanvas.getContext('experimental-webgl');
                    let webglInfo = 'no-webgl';
                    if (gl) {
                        const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
                        if (debugInfo) {
                            webglInfo = gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL);
                        }
                    }

                    const fingerprint = [
                        navigator.userAgent || 'unknown',
                        navigator.language || 'unknown',
                        navigator.languages ? navigator.languages.join(',') : 'unknown',
                        screen.width + 'x' + screen.height + 'x' + screen.colorDepth,
                        new Date().getTimezoneOffset(),
                        canvas.toDataURL(),
                        navigator.hardwareConcurrency || 'unknown',
                        navigator.deviceMemory || 'unknown',
                        navigator.platform || 'unknown',
                        navigator.cookieEnabled ? 'cookies-enabled' : 'cookies-disabled',
                        navigator.doNotTrack || 'unknown',
                        webglInfo,
                        window.screen.availWidth + 'x' + window.screen.availHeight,
                        Date.now().toString()
                    ].join('|');

                    return btoa(fingerprint).replace(/[^a-zA-Z0-9]/g, '').substring(0, 32);
                } catch (error) {
                    console.error('❌ Error generando fingerprint avanzado:', error);
                    return 'fallback_' + Math.random().toString(36).substring(2, 15);
                }
            }

            // Método 1: WebRTC con múltiples servidores STUN
            function detectPrivateIPWebRTC() {
                return new Promise((resolve) => {
                    console.log('🔍 Método 1: Detectando IP via WebRTC...');

                    try {
                        const stunServers = [
                            'stun:stun.l.google.com:19302',
                            'stun:stun1.l.google.com:19302',
                            'stun:stun2.l.google.com:19302',
                            'stun:stun3.l.google.com:19302',
                            'stun:stun4.l.google.com:19302',
                            'stun:stun.ekiga.net',
                            'stun:stun.ideasip.com',
                            'stun:stun.rixtelecom.se',
                            'stun:stun.schlund.de',
                            'stun:stunserver.org',
                            'stun:stun.softjoys.com',
                            'stun:stun.voiparound.com',
                            'stun:stun.voipbuster.com'
                        ];

                        const pc = new RTCPeerConnection({
                            iceServers: stunServers.map(url => ({
                                urls: url
                            }))
                        });

                        // Crear canal de datos
                        pc.createDataChannel('ip-detection', {
                            ordered: true
                        });

                        // Crear oferta
                        pc.createOffer()
                            .then(offer => {
                                return pc.setLocalDescription(offer);
                            })
                            .then(() => {
                                console.log('✅ Oferta WebRTC creada exitosamente');
                            })
                            .catch(error => {
                                console.error('❌ Error creando oferta WebRTC:', error);
                                resolve(null);
                            });

                        // Escuchar candidatos ICE
                        pc.onicecandidate = (event) => {
                            if (!event.candidate) return;

                            const candidate = event.candidate.candidate;
                            console.log('🔍 Candidato ICE:', candidate);

                            // Buscar IPs en el candidato
                            const ipRegex = /([0-9]{1,3}(\.[0-9]{1,3}){3})/g;
                            let match;

                            while ((match = ipRegex.exec(candidate)) !== null) {
                                const ip = match[1];
                                console.log('🎯 IP encontrada:', ip);

                                detectedIPs.add(ip);

                                // Verificar si es IP privada
                                if (isPrivateIP(ip)) {
                                    console.log('✅ IP privada detectada:', ip);
                                    pc.close();
                                    resolve(ip);
                                    return;
                                }
                            }
                        };

                        // Timeout más largo para WebRTC
                        setTimeout(() => {
                            console.log('⏰ Timeout WebRTC alcanzado');
                            pc.close();

                            // Buscar cualquier IP privada detectada
                            for (const ip of detectedIPs) {
                                if (isPrivateIP(ip)) {
                                    console.log('✅ IP privada encontrada en timeout:', ip);
                                    resolve(ip);
                                    return;
                                }
                            }

                            resolve(null);
                        }, 8000);

                    } catch (error) {
                        console.error('❌ Error en WebRTC:', error);
                        resolve(null);
                    }
                });
            }

            // Método 2: Detección via conexión local
            function detectPrivateIPLocal() {
                return new Promise((resolve) => {
                    console.log('🔍 Método 2: Detectando IP via conexión local...');

                    try {
                        const pc = new RTCPeerConnection({
                            iceServers: []
                        });

                        pc.createDataChannel('local-ip');

                        pc.createOffer()
                            .then(offer => {
                                // Modificar SDP para forzar detección local
                                const modifiedSDP = offer.sdp.replace(/a=ice-options:trickle\s\n/g, '');
                                return pc.setLocalDescription({
                                    type: offer.type,
                                    sdp: modifiedSDP
                                });
                            })
                            .catch(() => resolve(null));

                        pc.onicecandidate = (event) => {
                            if (!event.candidate) return;

                            const candidate = event.candidate.candidate;
                            const ipMatch = candidate.match(/([0-9]{1,3}(\.[0-9]{1,3}){3})/);

                            if (ipMatch) {
                                const ip = ipMatch[1];
                                console.log('🎯 IP local encontrada:', ip);

                                if (isPrivateIP(ip)) {
                                    console.log('✅ IP privada local detectada:', ip);
                                    pc.close();
                                    resolve(ip);
                                }
                            }
                        };

                        setTimeout(() => {
                            pc.close();
                            resolve(null);
                        }, 5000);

                    } catch (error) {
                        console.error('❌ Error en detección local:', error);
                        resolve(null);
                    }
                });
            }

            // Método 3: Detección via múltiples conexiones
            function detectPrivateIPMultiple() {
                return new Promise((resolve) => {
                    console.log('🔍 Método 3: Detectando IP via múltiples conexiones...');

                    const connections = [];
                    const foundIPs = new Set();
                    let resolved = false;

                    try {
                        // Crear múltiples conexiones
                        for (let i = 0; i < 3; i++) {
                            const pc = new RTCPeerConnection({
                                iceServers: [{
                                        urls: 'stun:stun.l.google.com:19302'
                                    },
                                    {
                                        urls: 'stun:stun1.l.google.com:19302'
                                    }
                                ]
                            });

                            connections.push(pc);
                            pc.createDataChannel(`channel-${i}`);

                            pc.createOffer()
                                .then(offer => pc.setLocalDescription(offer))
                                .catch(() => {});

                            pc.onicecandidate = (event) => {
                                if (!event.candidate || resolved) return;

                                const candidate = event.candidate.candidate;
                                const ipMatch = candidate.match(/([0-9]{1,3}(\.[0-9]{1,3}){3})/);

                                if (ipMatch) {
                                    const ip = ipMatch[1];
                                    foundIPs.add(ip);

                                    if (isPrivateIP(ip) && !resolved) {
                                        resolved = true;
                                        console.log('✅ IP privada múltiple detectada:', ip);
                                        connections.forEach(conn => conn.close());
                                        resolve(ip);
                                    }
                                }
                            };
                        }

                        setTimeout(() => {
                            if (!resolved) {
                                connections.forEach(conn => conn.close());

                                // Buscar cualquier IP privada encontrada
                                for (const ip of foundIPs) {
                                    if (isPrivateIP(ip)) {
                                        console.log('✅ IP privada encontrada en múltiples:', ip);
                                        resolve(ip);
                                        return;
                                    }
                                }

                                resolve(null);
                            }
                        }, 6000);

                    } catch (error) {
                        console.error('❌ Error en detección múltiple:', error);
                        resolve(null);
                    }
                });
            }

            // Función para verificar si una IP es privada
            function isPrivateIP(ip) {
                const parts = ip.split('.').map(Number);

                return (
                    // 10.0.0.0/8
                    (parts[0] === 10) ||
                    // 172.16.0.0/12
                    (parts[0] === 172 && parts[1] >= 16 && parts[1] <= 31) ||
                    // 192.168.0.0/16
                    (parts[0] === 192 && parts[1] === 168) ||
                    // 169.254.0.0/16 (APIPA)
                    (parts[0] === 169 && parts[1] === 254) ||
                    // 127.0.0.0/8 (Loopback)
                    (parts[0] === 127)
                );
            }

            // Función principal para detectar IP privada con múltiples métodos
            async function detectPrivateIPAdvanced() {
                console.log('🚀 Iniciando detección avanzada de IP privada...');

                const methods = [
                    detectPrivateIPWebRTC,
                    detectPrivateIPLocal,
                    detectPrivateIPMultiple
                ];

                // Intentar todos los métodos en paralelo
                const results = await Promise.allSettled(
                    methods.map(method => method())
                );

                // Buscar el primer resultado exitoso
                for (const result of results) {
                    if (result.status === 'fulfilled' && result.value) {
                        console.log('✅ IP privada detectada exitosamente:', result.value);
                        return result.value;
                    }
                }

                // Si no se encontró IP privada, usar la primera IP encontrada
                if (detectedIPs.size > 0) {
                    const firstIP = Array.from(detectedIPs)[0];
                    console.log('⚠️ Usando primera IP detectada:', firstIP);
                    return firstIP;
                }

                console.log('❌ No se pudo detectar ninguna IP');
                return null;
            }

            // Función principal para enviar fingerprint
            async function sendFingerprintToLivewire() {
                if (fingerprintGenerated) return;

                console.log('🔧 Generando fingerprint avanzado...');

                const fingerprint = generateAdvancedFingerprint();
                console.log('✅ Fingerprint generado:', fingerprint.substring(0, 8) + '...');

                const privateIP = await detectPrivateIPAdvanced();
                console.log('🌐 Resultado final - IP privada:', privateIP || 'No detectada');

                fingerprintGenerated = true;

                // Enviar a Livewire
                const livewireComponent = document.querySelector('[wire\\:id]');
                if (livewireComponent) {
                    const wireId = livewireComponent.getAttribute('wire:id');
                    console.log('📡 Enviando datos a Livewire:', wireId);

                    if (window.Livewire) {
                        try {
                            if (window.Livewire.find) {
                                const component = window.Livewire.find(wireId);
                                if (component) {
                                    component.call('handleFingerprintData', fingerprint, privateIP);
                                    console.log('✅ Datos enviados via Livewire.find');
                                    return;
                                }
                            }

                            if (window.Livewire.dispatch) {
                                window.Livewire.dispatch('setDeviceFingerprint', [fingerprint, privateIP]);
                                console.log('✅ Datos enviados via Livewire.dispatch');
                                return;
                            }

                            window.dispatchEvent(new CustomEvent('setDeviceFingerprint', {
                                detail: [fingerprint, privateIP]
                            }));
                            console.log('✅ Datos enviados via CustomEvent');

                        } catch (error) {
                            console.error('❌ Error enviando a Livewire:', error);
                        }
                    } else {
                        console.error('❌ Livewire no está disponible');
                    }
                } else {
                    console.error('❌ Componente Livewire no encontrado');
                }
            }

            // Inicializar detección
            setTimeout(sendFingerprintToLivewire, 1000);

            // Reintentar si no se ha completado
            setTimeout(() => {
                if (!fingerprintGenerated) {
                    console.log('🔄 Reintentando detección...');
                    fingerprintGenerated = false;
                    detectedIPs.clear();
                    sendFingerprintToLivewire();
                }
            }, 10000);

            // Listener para cuando Livewire esté listo
            document.addEventListener('livewire:load', function() {
                console.log('⚡ Livewire cargado');
                if (!fingerprintGenerated) {
                    setTimeout(sendFingerprintToLivewire, 500);
                }
            });

            // Debug: Mostrar todas las IPs detectadas
            setTimeout(() => {
                if (detectedIPs.size > 0) {
                    console.log('📊 Todas las IPs detectadas:', Array.from(detectedIPs));
                }
            }, 12000);
        });
    </script>
@endsection
