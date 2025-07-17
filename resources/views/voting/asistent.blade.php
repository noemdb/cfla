@extends('layouts.vote')

@section('title', 'Asistente de Participación')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-green-900 to-gray-900">
        <!-- Header -->
        <div class="max-w-4xl mx-auto rounded px-4 bg-gray-800/90 backdrop-blur-sm border-b border-gray-700/50">
            <div class="sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-white mb-2">
                            Asistente de Participación
                        </h1>
                        <p class="text-gray-300 text-lg">
                            Participa en todas las encuestas activas de manera guiada y secuencial
                        </p>
                    </div>

                    <div class="mt-4 lg:mt-0 lg:ml-8">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-lg px-4 py-3">
                                <div class="text-2xl font-bold text-white">{{ $polls->count() }}</div>
                                <div class="text-green-100 text-sm">Encuestas Activas</div>
                            </div>
                            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg px-4 py-3">
                                <div class="text-2xl font-bold text-white" id="total-options">
                                    {{ $polls->sum(fn($poll) => $poll->options->count()) }}</div>
                                <div class="text-blue-100 text-sm">Opciones Totales</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Indicador de tiempo real -->
                <div class="mt-4 flex items-center justify-between">
                    {{--
                    <div class="flex items-center space-x-2 text-green-400">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-sm">Actualizando en tiempo real</span>
                    </div>
                    --}}
                    <div class="text-gray-400 text-sm">
                        Última actualización: <span id="last-update">{{ now()->format('H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="max-w-4xl mx-auto py-8">
            @if ($polls->count() > 0)
                <livewire:voting-poll-asistent :polls="$polls" />
            @else
                <!-- Estado vacío -->
                <div class="text-center py-16">
                    <div class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700/50 shadow-2xl p-12">
                        <div
                            class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-600 to-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">No hay encuestas activas</h3>
                        <p class="text-gray-400 text-lg mb-8">
                            Actualmente no hay encuestas disponibles para participar.
                        </p>
                        {{--
                        <div class="space-y-4">
                            <a href="{{ route('voting.index') }}"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 7 4-4 4 4">
                                    </path>
                                </svg>
                                Ver Todas las Encuestas
                            </a>
                        </div>
                        --}}
                    </div>
                </div>
            @endif
        </div>

        <!-- Navegación inferior -->
        <div class="max-w-4xl mx-auto  bg-gray-800/90 backdrop-blur-sm border-t border-gray-700/50 mt-12">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col sm:flex-row items-center justify-between space-y-2 sm:space-y-0">
                    <div class="flex items-center space-x-6">
                        {{--
                        <a href="{{ route('voting.index') }}"
                            class="flex items-center text-gray-300 hover:text-white transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            </svg>
                            Todas las Encuestas
                        </a>
                        --}}

                        {{--
                        <a href="{{ route('voting.results') }}"
                            class="flex items-center text-gray-300 hover:text-white transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9z">
                                </path>
                            </svg>
                            Ver Resultados
                        </a>
                        --}}

                        <a href="{{ route('voting.guia') }}"
                            class="flex items-center text-gray-300 hover:text-white transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            Guía de Uso
                        </a>
                    </div>

                    <div class="text-gray-400 text-sm">
                        <strong>SAEFL</strong> Módulo de Votación v1.0
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection


@section('script')
@parent
<script>
        // Auto-refresh cada 30 segundos
        setInterval(function() {
            document.getElementById('last-update').textContent = new Date().toLocaleTimeString();
            // Opcional: recargar la página para obtener nuevas encuestas
            // window.location.reload();
        }, 30000);

        // Notificación de actualización
        function showUpdateNotification() {
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('Sistema de Votación', {
                    body: 'Nuevas encuestas disponibles',
                    icon: '/favicon.ico'
                });
            }
        }

        // Solicitar permisos de notificación
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    </script>
@endsection
