<!-- Modal de Detalles de Participación -->
<div x-data="{ show: @entangle('showParticipationModal') }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">

    <!-- Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" x-show="show"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @click="$wire.closeParticipationModal()">
    </div>

    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-gray-800 shadow-2xl rounded-2xl border border-gray-700"
            x-show="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.stop>

            @if ($selectedParticipation)
                <!-- Header del Modal -->
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-white">📊 Detalles de Participación</h2>
                            <p class="text-green-100 mt-1">Información completa de tu voto registrado</p>
                        </div>
                        <button wire:click="closeParticipationModal"
                            class="text-white hover:text-gray-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Contenido del Modal -->
                <div class="px-8 py-6">
                    <!-- Título de la encuesta -->
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-white mb-2">{{ $selectedParticipation->poll->title }}</h3>
                        @if ($selectedParticipation->poll->description)
                            <p class="text-gray-400">{{ $selectedParticipation->poll->description }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Información de la sesión -->
                        <div class="space-y-4">
                            <div class="bg-gray-700/50 rounded-lg p-4">
                                <h4 class="text-lg font-semibold text-white mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Información de la Sesión
                                </h4>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between items-center py-2 border-b border-gray-600">
                                        <span class="text-gray-400">UUID:</span>
                                        <span class="text-white font-mono text-xs bg-gray-600 px-2 py-1 rounded">
                                            {{ $selectedParticipation->uuid }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-gray-600">
                                        <span class="text-gray-400">Fecha de participación:</span>
                                        <span
                                            class="text-white">{{ $selectedParticipation->created_at->format('d/m/Y H:i:s') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-gray-600">
                                        <span class="text-gray-400">IP Privada:</span>
                                        <span class="text-white">{{ $selectedParticipation->private_ip }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2">
                                        <span class="text-gray-400">Fingerprint:</span>
                                        <span class="text-white font-mono text-xs bg-gray-600 px-2 py-1 rounded">
                                            {{ substr($selectedParticipation->fingerprint, 0, 12) }}...
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del voto -->
                            <div class="bg-gray-700/50 rounded-lg p-4">
                                <h4 class="text-lg font-semibold text-white mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Tu Voto
                                </h4>
                                @if ($selectedParticipation->votes->isNotEmpty())
                                    @foreach ($selectedParticipation->votes as $vote)
                                        <div class="bg-green-900/30 border border-green-700 rounded-lg p-4 mb-3">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    <span
                                                        class="text-green-300 font-medium">{{ $vote->option->label }}</span>
                                                </div>
                                                <div class="text-xs text-green-400">
                                                    {{ $vote->created_at->format('H:i:s') }}
                                                </div>
                                            </div>
                                            <div class="text-xs text-green-400 mt-2">
                                                Registrado: {{ $vote->created_at->format('d/m/Y H:i:s') }}
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-gray-400 text-center py-4 bg-gray-600/30 rounded-lg">
                                        <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                                            </path>
                                        </svg>
                                        No se encontraron votos para esta sesión
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Código QR -->
                        <div class="space-y-4">
                            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                                <h4 class="text-lg font-semibold text-white mb-4 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                        </path>
                                    </svg>
                                    Código QR de Verificación
                                </h4>
                                <div class="inline-block bg-white p-4 rounded-lg shadow-lg">
                                    {!! $this->generateLargeQRCode($selectedParticipation->uuid) !!}
                                </div>
                                <p class="text-gray-400 text-sm mt-3">
                                    Escanea este código para verificar tu participación
                                </p>
                                <div class="mt-3 text-xs text-gray-500 bg-gray-600/30 rounded px-3 py-2">
                                    UUID: {{ $selectedParticipation->uuid }}
                                </div>
                            </div>

                            <!-- Estadísticas adicionales -->
                            <div class="bg-gray-700/50 rounded-lg p-4">
                                <h4 class="text-lg font-semibold text-white mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9z">
                                        </path>
                                    </svg>
                                    Estadísticas
                                </h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Estado:</span>
                                        <span class="text-green-400 font-medium">✓ Confirmado</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Tiempo transcurrido:</span>
                                        <span
                                            class="text-white">{{ $selectedParticipation->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Expira:</span>
                                        <span
                                            class="text-white">{{ $selectedParticipation->expires_at ? $selectedParticipation->expires_at->format('d/m/Y H:i') : 'No expira' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer del Modal -->
                <div class="bg-gray-700/30 px-8 py-4 border-t border-gray-600">
                    <div
                        class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0 sm:space-x-4">
                        <div class="text-sm text-gray-400">
                            Participación verificada y registrada de forma segura
                        </div>
                        <div class="flex space-x-3">
                            @if (Route::has('poll.participation.show'))
                                <a href="{{ route('poll.participation.show', $selectedParticipation->uuid) }}"
                                    target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M7 7h10v10M7 7l10 10">
                                        </path>
                                    </svg>
                                    Ver en página completa
                                </a>
                            @endif
                            <button wire:click="closeParticipationModal"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
