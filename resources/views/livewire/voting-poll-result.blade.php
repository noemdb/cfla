<div wire:poll.3s="loadResults" class="space-y-8">
    <!-- Estadísticas generales -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total de votos -->
        <div
            class="bg-gradient-to-br from-green-800/60 to-green-900/60 backdrop-blur-sm rounded-2xl p-6 border border-green-700/30">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-r from-green-600 to-green-700 rounded-full">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-green-200 text-sm font-medium">Total de Votos</p>
                    <p class="text-3xl font-bold text-white">{{ number_format($totalVotes) }}</p>
                </div>
            </div>
        </div>

        <!-- Número de opciones -->
        <div
            class="bg-gradient-to-br from-green-800/60 to-green-900/60 backdrop-blur-sm rounded-2xl p-6 border border-green-700/30">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-full">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-green-200 text-sm font-medium">Opciones</p>
                    <p class="text-3xl font-bold text-white">{{ count($results) }}</p>
                </div>
            </div>
        </div>

        <!-- Última actualización -->
        <div
            class="bg-gradient-to-br from-green-800/60 to-green-900/60 backdrop-blur-sm rounded-2xl p-6 border border-green-700/30">
            <div class="flex items-center">
                <div class="p-3 bg-gradient-to-r from-teal-600 to-teal-700 rounded-full">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-green-200 text-sm font-medium">Actualizado</p>
                    <p class="text-xl font-bold text-white">{{ $lastUpdated }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Indicador de actualización en tiempo real -->
    <div class="flex items-center justify-center mb-8">
        <div
            class="flex items-center space-x-2 bg-gradient-to-r from-green-700/50 to-green-600/50 backdrop-blur-sm rounded-full px-4 py-2 border border-green-600/30">
            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
            <span class="text-green-100 text-sm font-medium">Actualizando en tiempo real</span>
        </div>
    </div>

    <!-- Resultados de la encuesta -->
    <div class="space-y-6">
        <h2 class="text-2xl font-bold text-white mb-6 text-center">Resultados de la Encuesta</h2>

        @if (count($results) > 0)
            @foreach ($results as $index => $result)
                <div
                    class="bg-gradient-to-br from-green-800/40 to-green-900/40 backdrop-blur-sm rounded-2xl p-6 border border-green-700/30 transform transition-all duration-300 hover:scale-[1.02]">
                    <!-- Header de la opción -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <!-- Posición -->
                            <div
                                class="flex items-center justify-center w-8 h-8 bg-gradient-to-r {{ $result['color'] }} rounded-full text-white font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <!-- Nombre de la opción -->
                            <h3 class="text-lg font-semibold text-white">{{ $result['label'] }}</h3>
                        </div>
                        <!-- Porcentaje -->
                        <div class="text-right">
                            <span class="text-2xl font-bold text-white">{{ $result['percentage'] }}%</span>
                            <p class="text-green-200 text-sm">{{ number_format($result['votes']) }} votos</p>
                        </div>
                    </div>

                    <!-- Barra de progreso -->
                    <div class="relative">
                        <div class="w-full bg-green-950/50 rounded-full h-4 overflow-hidden border border-green-800/30">
                            <div class="bg-gradient-to-r {{ $result['color'] }} h-full rounded-full transition-all duration-1000 ease-out relative overflow-hidden"
                                style="width: {{ $result['percentage'] }}%">
                                <!-- Efecto de brillo -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-pulse">
                                </div>
                            </div>
                        </div>
                        <!-- Indicador de porcentaje en la barra -->
                        @if ($result['percentage'] > 10)
                            <div class="absolute inset-y-0 left-2 flex items-center">
                                <span class="text-white text-xs font-medium">{{ $result['percentage'] }}%</span>
                            </div>
                        @endif
                    </div>

                    <!-- Información adicional para la opción ganadora -->
                    @if ($index === 0 && $result['votes'] > 0)
                        <div class="mt-4 flex items-center space-x-2">
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <span class="text-yellow-400 text-sm font-medium">Opción más votada</span>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <!-- Estado sin votos -->
            <div class="text-center py-12">
                <div
                    class="bg-gradient-to-br from-green-800/40 to-green-900/40 backdrop-blur-sm rounded-2xl p-8 border border-green-700/30">
                    <svg class="w-16 h-16 text-green-400 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    <h3 class="text-xl font-semibold text-white mb-2">Aún no hay votos</h3>
                    <p class="text-green-200">Los resultados aparecerán aquí cuando se registren los primeros votos.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Información de la encuesta -->
    <div
        class="mt-12 bg-gradient-to-br from-green-800/30 to-green-900/30 backdrop-blur-sm rounded-2xl p-6 border border-green-700/30">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-lg font-semibold text-white mb-2">Información de la Encuesta</h4>
                <div class="space-y-2 text-green-200">
                    <p><span class="font-medium">Fecha de inicio:</span>
                        {{ $poll->date ? \Carbon\Carbon::parse($poll->date)->format('d/m/Y H:i') : 'No especificada' }}
                    </p>
                    @if ($poll->time_active)
                        <p><span class="font-medium">Duración:</span> {{ $poll->time_active }} minutos</p>
                    @else
                        <p><span class="font-medium">Duración:</span> Sin límite de tiempo</p>
                    @endif
                </div>
            </div>
            <div>
                <h4 class="text-lg font-semibold text-white mb-2">Estado</h4>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-green-200">Encuesta activa y recibiendo votos</span>
                </div>
            </div>
        </div>
    </div>
</div>
