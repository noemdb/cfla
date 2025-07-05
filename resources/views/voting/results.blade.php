@extends('layouts.vote')

@section('title', 'Resultados de Encuestas en Tiempo Real')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-green-900 to-gray-900 py-8 px-4">
        <div class="container-fluid w-full">
            <!-- Header -->
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center px-4 py-2 bg-emerald-500/20 backdrop-blur-sm rounded-full text-emerald-300 text-sm font-medium border border-emerald-500/30 mb-4">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Resultados en Tiempo Real
                </div>
                <h1 class="text-4xl font-bold text-white mb-2">Dashboard de Resultados</h1>
                <p class="text-gray-300">Monitoreo en vivo de todas las encuestas activas</p>
            </div>

            <!-- Estadísticas generales -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gray-800/90 backdrop-blur-sm rounded-2xl shadow-2xl border border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-gray-900 via-emerald-900 to-gray-900 px-6 py-4 border-b border-emerald-800/50">
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-emerald-500/30 to-green-600/30 rounded-full flex items-center justify-center border border-emerald-500/50">
                                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-emerald-400 font-medium">Encuestas Activas</p>
                                <p class="text-2xl font-bold text-white">{{ $polls->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/90 backdrop-blur-sm rounded-2xl shadow-2xl border border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-gray-900 via-blue-900 to-gray-900 px-6 py-4 border-b border-blue-800/50">
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-blue-500/30 to-indigo-600/30 rounded-full flex items-center justify-center border border-blue-500/50">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-blue-400 font-medium">Total Votos</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ $polls->sum(function ($poll) {
                                        return \App\Models\VotingVote::whereHas('option', function ($query) use ($poll) {
                                            $query->where('poll_id', $poll->id);
                                        })->count();
                                    }) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/90 backdrop-blur-sm rounded-2xl shadow-2xl border border-gray-700 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-gray-900 via-teal-900 to-gray-900 px-6 py-4 border-b border-teal-800/50">
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-teal-500/30 to-green-600/30 rounded-full flex items-center justify-center border border-teal-500/50">
                                <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-teal-400 font-medium">Total Opciones</p>
                                <p class="text-2xl font-bold text-white">
                                    {{ $polls->sum(function ($poll) {return $poll->options->count();}) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Indicador de actualización en tiempo real -->
            <div class="flex items-center justify-center mb-8">
                <div
                    class="flex items-center space-x-2 bg-gradient-to-r from-emerald-700/50 to-green-600/50 backdrop-blur-sm rounded-full px-6 py-3 border border-emerald-600/30">
                    <div class="w-3 h-3 bg-emerald-400 rounded-full animate-pulse"></div>
                    <span class="text-emerald-100 font-medium">Actualizando resultados cada 3 segundos</span>
                </div>
            </div>

            <!-- Resultados de cada encuesta - Grid Layout (Deck Cards) -->
            @forelse($polls as $poll)
                @if ($loop->first)
                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                @endif

                <div
                    class="bg-gray-800/90 backdrop-blur-sm rounded-2xl shadow-2xl border border-gray-700 overflow-hidden h-fit">
                    <!-- Header de la encuesta -->
                    <div
                        class="bg-gradient-to-r from-gray-900 via-emerald-900 to-gray-900 px-6 py-4 border-b border-emerald-800/50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <h2 class="text-lg font-bold text-white mb-2 truncate" title="{{ $poll->title }}">
                                    {{ $poll->title }}
                                </h2>
                                <div class="flex flex-col space-y-1 text-emerald-200 text-xs">
                                    <div class="flex items-center">
                                        <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="truncate">{{ $poll->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                            </path>
                                        </svg>
                                        <span class="font-mono truncate">{{ substr($poll->access_token, 0, 8) }}...</span>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-2 flex-shrink-0">
                                <div
                                    class="inline-flex items-center px-2 py-1 bg-emerald-500/20 text-emerald-300 text-xs rounded-full border border-emerald-500/30">
                                    <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full mr-1 animate-pulse"></div>
                                    Activa
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido de resultados -->
                    <div class="p-4">
                        @livewire('voting-poll-result', ['access_token' => $poll->access_token, 'showTitle' => false], key($poll->id))
                    </div>
                </div>

                @if ($loop->last) </div> @endif
    @empty
        <!-- Estado sin encuestas -->
        <div class="text-center py-16">
            <div class="bg-gray-800/90 backdrop-blur-sm rounded-2xl shadow-2xl border border-gray-700 p-12">
                <svg class="w-20 h-20 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                <h3 class="text-2xl font-bold text-white mb-4">No hay encuestas activas</h3>
                <p class="text-gray-300 mb-6">Actualmente no hay encuestas disponibles para mostrar resultados.
                </p>
                <a href="{{ route('poll.voting.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-emerald-500/50 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Ver Encuestas Disponibles
                </a>
            </div>
        </div>
        @endforelse

        <!-- Botones de navegación -->
        <div class="flex justify-center space-x-4 mt-12">
            <a href="{{ route('poll.voting.index') }}"
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-gray-500/50 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a Encuestas
            </a>

            <button onclick="window.location.reload()"
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-emerald-500/50 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
                Actualizar Manualmente
            </button>
        </div>
    </div>
    </div>
@endsection

@section('script')
    @parent
    <script>
        // Auto-refresh de la página cada 30 segundos como respaldo
        setInterval(() => {
            if (document.visibilityState === 'visible') {
                // Solo actualizar si la página está visible
                console.log('Auto-refresh ejecutado');
            }
        }, 30000);

        // Mostrar notificación cuando se actualicen los datos
        document.addEventListener('livewire:load', function() {
            Livewire.on('dataUpdated', () => {
                showNotification('Datos actualizados', 'success');
            });
        });

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300 ${
    type === 'success' ? 'bg-green-600 text-white' :
    type === 'error' ? 'bg-red-600 text-white' :
    type === 'info' ? 'bg-blue-600 text-white' :
    'bg-gray-600 text-white'
}`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
    </script>
@endsection
