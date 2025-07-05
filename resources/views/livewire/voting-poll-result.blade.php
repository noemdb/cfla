<div wire:poll.3s="loadResults" class="space-y-6">
    @if ($showTitle)
        <!-- Título de la encuesta (solo si showTitle es true) -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-white mb-2">{{ $poll->title }}</h2>
            <p class="text-gray-300">Resultados en tiempo real</p>
        </div>
    @endif

    <!-- Estadísticas de la encuesta -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Total de votos -->
        <div class="bg-gradient-to-r from-gray-700/50 to-emerald-900/20 rounded-xl p-4 border border-gray-600/50">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-emerald-500/30 to-green-600/30 rounded-full flex items-center justify-center border border-emerald-500/50">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-emerald-400 text-sm font-medium">Total Votos</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($totalVotes) }}</p>
                </div>
            </div>
        </div>

        <!-- Número de opciones -->
        <div class="bg-gradient-to-r from-gray-700/50 to-blue-900/20 rounded-xl p-4 border border-gray-600/50">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-blue-500/30 to-indigo-600/30 rounded-full flex items-center justify-center border border-blue-500/50">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                        </path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-blue-400 text-sm font-medium">Opciones</p>
                    <p class="text-2xl font-bold text-white">{{ count($results) }}</p>
                </div>
            </div>
        </div>

        <!-- Última actualización -->
        <div class="bg-gradient-to-r from-gray-700/50 to-teal-900/20 rounded-xl p-4 border border-gray-600/50">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-teal-500/30 to-green-600/30 rounded-full flex items-center justify-center border border-teal-500/50">
                    <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-teal-400 text-sm font-medium">Actualizado</p>
                    <p class="text-lg font-bold text-white">{{ $lastUpdated }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Indicador de tiempo real -->
    <div class="flex items-center justify-center mb-6">
        <div
            class="flex items-center space-x-2 bg-gradient-to-r from-emerald-700/30 to-green-600/30 backdrop-blur-sm rounded-full px-4 py-2 border border-emerald-600/30">
            <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
            <span class="text-emerald-200 text-sm font-medium">Actualizando cada 3 segundos</span>
        </div>
    </div>

    <!-- Resultados por opción -->
    @if (count($results) > 0)
        <div class="space-y-4">
            @foreach ($results as $index => $result)
                <div
                    class="bg-gradient-to-r from-gray-700/50 to-emerald-900/20 rounded-xl p-6 border border-gray-600/50 transform transition-all duration-300 hover:scale-[1.02]">
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

                            <!-- Corona para el ganador -->
                            @if ($index === 0 && $result['votes'] > 0)
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                            @endif
                        </div>
                        <!-- Estadísticas -->
                        <div class="text-right">
                            <span class="text-2xl font-bold text-white">{{ $result['percentage'] }}%</span>
                            <p class="text-emerald-200 text-sm">{{ number_format($result['votes']) }} votos</p>
                        </div>
                    </div>

                    <!-- Barra de progreso -->
                    <div class="relative">
                        <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r {{ $result['color'] }} h-full rounded-full transition-all duration-1000 ease-out relative overflow-hidden"
                                style="width: {{ $result['percentage'] }}%">
                                <!-- Efecto de brillo animado -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-pulse">
                                </div>
                            </div>
                        </div>

                        <!-- Porcentaje en la barra si hay espacio -->
                        @if ($result['percentage'] > 15)
                            <div class="absolute inset-y-0 left-3 flex items-center">
                                <span
                                    class="text-white text-xs font-medium drop-shadow-lg">{{ $result['percentage'] }}%</span>
                            </div>
                        @endif
                    </div>

                    <!-- Información adicional -->
                    <div class="flex justify-between mt-2 text-xs text-gray-400">
                        <span>{{ number_format($result['votes']) }} de {{ number_format($totalVotes) }} votos</span>
                        @if ($totalVotes > 0)
                            <span>{{ number_format(($result['votes'] / $totalVotes) * 100, 1) }}% del total</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Estado sin votos -->
        <div class="text-center py-8">
            <div class="bg-gradient-to-r from-gray-700/50 to-emerald-900/20 rounded-xl p-8 border border-gray-600/50">
                <svg class="w-16 h-16 text-emerald-400 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                <h3 class="text-xl font-semibold text-white mb-2">Esperando votos</h3>
                <p class="text-emerald-200">Los resultados aparecerán aquí cuando se registren los primeros votos.</p>
            </div>
        </div>
    @endif
</div>
