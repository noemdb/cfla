<div class="py-8 px-4">
    <div class="container mx-auto max-w-4xl">
        @if ($showQRCode && $hasVoted)
            <!-- Sección QR Code después de votar -->
            <div class="mb-8">
                <!-- Header de confirmación -->
                <div class="text-center mb-8">
                    <div
                        class="inline-flex items-center px-4 py-2 bg-emerald-500/20 backdrop-blur-sm rounded-full text-emerald-300 text-sm font-medium border border-emerald-500/30 mb-4">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Participación Confirmada
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">¡Gracias por participar!</h1>
                    <p class="text-gray-300">Tu código QR de participación está listo</p>
                </div>

                <!-- Tarjeta QR -->
                <div
                    class="bg-gray-800/90 backdrop-blur-sm rounded-2xl shadow-2xl border border-gray-700 overflow-hidden mb-8">
                    <!-- Información de la encuesta -->
                    <div
                        class="bg-gradient-to-r from-gray-900 via-green-900 to-gray-900 px-6 py-6 border-b border-green-800/50">
                        <h2 class="text-xl font-bold text-white mb-2">{{ $poll->title }}</h2>
                        <div class="flex items-center text-sm text-emerald-300">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Participaste el {{ now()->format('d/m/Y \a \l\a\s H:i') }}
                        </div>
                    </div>

                    <!-- Código QR -->
                    <div class="p-8 text-center">
                        <div class="bg-white rounded-2xl p-6 inline-block shadow-lg mb-6">
                            {!! $qrCodeSvg !!}
                        </div>

                        <h3 class="text-lg font-semibold text-white mb-3">Tu Código QR de Participación</h3>
                        <p class="text-gray-300 text-sm mb-6">
                            Escanea este código para acceder a los detalles completos de tu participación y ver los
                            resultados actuales de la encuesta.
                        </p>

                        <!-- URL para copiar -->
                        <div class="bg-gray-700/50 rounded-lg p-4 mb-6 border border-gray-600">
                            <p class="text-xs text-gray-400 mb-2">O accede directamente con este enlace:</p>
                            <div class="flex items-center space-x-2">
                                <input type="text" value="{{ $participationUrl }}" readonly
                                    class="flex-1 bg-gray-800 text-white text-sm px-3 py-2 rounded border border-gray-600 focus:outline-none focus:border-emerald-500"
                                    id="participationUrl">
                                <button wire:click="copyParticipationUrl"
                                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="space-y-3">
                            <a href="{{ $participationUrl }}" target="_blank"
                                class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-emerald-500/50 shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                                Ver Detalles de Participación
                            </a>

                            <div class="flex space-x-3">
                                <button onclick="shareParticipation('{{ $participationUrl }}', '{{ $poll->title }}')"
                                    class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z">
                                        </path>
                                    </svg>
                                    Compartir
                                </button>

                                <button onclick="window.print()"
                                    class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                        </path>
                                    </svg>
                                    Imprimir
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="bg-gradient-to-r from-gray-800/50 to-green-900/20 px-6 py-4 border-t border-gray-700">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-emerald-400 mt-0.5 flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-gray-300">
                                <p class="font-medium text-white mb-1">Información importante:</p>
                                <ul class="space-y-1 text-xs">
                                    <li>• Este código QR es único para tu participación</li>
                                    <li>• Puedes acceder a él en cualquier momento</li>
                                    <li>• Los resultados se actualizan en tiempo real</li>
                                    <li>• Tu voto permanece completamente anónimo</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón para volver -->
                <div class="text-center">
                    <a href="{{ route('poll.voting.index') }}"
                        class="inline-flex items-center px-4 py-2 text-gray-400 hover:text-white transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Ver otras encuestas
                    </a>
                </div>
            </div>
        @else
            <!-- Sección de votación normal -->
            <div class="max-w-2xl mx-auto">
                @if ($poll)
                    <!-- Header de la encuesta -->
                    <div class="text-center mb-8">
                        <div
                            class="inline-flex items-center px-4 py-2 bg-emerald-500/20 backdrop-blur-sm rounded-full text-emerald-300 text-sm font-medium border border-emerald-500/30 mb-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                            Encuesta Activa
                        </div>
                        <h1 class="text-3xl font-bold text-white mb-2">{{ $poll->title }}</h1>
                        @if ($poll->description)
                            <p class="text-gray-300 mb-4">{{ $poll->description }}</p>
                        @endif
                        @if ($timeRemaining)
                            <div
                                class="inline-flex items-center px-3 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-sm border border-yellow-500/30">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $timeRemaining }}
                            </div>
                        @endif
                    </div>

                    <!-- Tarjeta de votación -->
                    <div
                        class="bg-gray-800/90 backdrop-blur-sm rounded-2xl shadow-2xl border border-gray-700 overflow-hidden">
                        @if ($errorState)
                            <!-- Estados de error -->
                            <div class="p-8 text-center">
                                @if ($errorState === 'poll_not_found')
                                    <div class="text-red-400 mb-4">
                                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-white mb-2">Encuesta no encontrada</h3>
                                    <p class="text-gray-300 mb-6">La encuesta que buscas no existe o ha sido eliminada.
                                    </p>
                                @elseif($errorState === 'poll_inactive')
                                    <div class="text-yellow-400 mb-4">
                                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-white mb-2">Encuesta inactiva</h3>
                                    <p class="text-gray-300 mb-6">Esta encuesta no está activa en este momento.</p>
                                @elseif($errorState === 'poll_expired')
                                    <div class="text-red-400 mb-4">
                                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-white mb-2">Encuesta expirada</h3>
                                    <p class="text-gray-300 mb-6">El tiempo para participar en esta encuesta ha
                                        terminado.</p>
                                @elseif($errorState === 'already_voted')
                                    <div class="text-emerald-400 mb-4">
                                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-white mb-2">Ya has participado</h3>
                                    <p class="text-gray-300 mb-6">¡Gracias! Ya has votado en esta encuesta.</p>
                                @endif

                                <button wire:click="refreshPoll"
                                    class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    Actualizar
                                </button>
                            </div>
                        @elseif($canVote && !$hasVoted)
                            <!-- Formulario de votación -->
                            <div class="p-8">
                                <h3 class="text-xl font-bold text-white mb-6 text-center">Selecciona tu opción</h3>

                                <div class="space-y-4 mb-8">
                                    @foreach ($poll->options as $option)
                                        <div wire:click="selectOption({{ $option->id }})"
                                            class="p-4 rounded-xl border-2 cursor-pointer transition-all duration-300 hover:scale-[1.02] {{ $selectedOption === $option->id ? 'border-emerald-500 bg-emerald-500/20 shadow-lg shadow-emerald-500/25' : 'border-gray-600 bg-gray-700/50 hover:border-gray-500 hover:bg-gray-700/70' }}">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="w-5 h-5 rounded-full border-2 {{ $selectedOption === $option->id ? 'border-emerald-500 bg-emerald-500' : 'border-gray-400' }} flex items-center justify-center">
                                                        @if ($selectedOption === $option->id)
                                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-white font-medium">{{ $option->label }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @error('selectedOption')
                                    <div
                                        class="mb-4 p-3 bg-red-500/20 border border-red-500/50 rounded-lg text-red-300 text-sm">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <button wire:click="vote" wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                    class="w-full py-4 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 disabled:from-gray-600 disabled:to-gray-600 text-white font-bold rounded-xl transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-emerald-500/50 shadow-lg">
                                    <span wire:loading.remove>
                                        Registrar Voto
                                    </span>
                                    <span wire:loading class="flex items-center justify-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Registrando...
                                    </span>
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>

    @section('style')
        @parent
        <!-- Estilos para impresión -->
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }

                .container,
                .container * {
                    visibility: visible;
                }

                .bg-gradient-to-br,
                .bg-gray-800\/90,
                .bg-gradient-to-r {
                    background: white !important;
                    color: black !important;
                }

                .text-white,
                .text-gray-300,
                .text-emerald-300 {
                    color: black !important;
                }

                .border-gray-700,
                .border-green-800\/50,
                .border-gray-600 {
                    border-color: #ccc !important;
                }

                button:not(.print-visible),
                .space-y-3>*:not(:first-child) {
                    display: none !important;
                }
            }
        </style>
    @endsection
</div>
