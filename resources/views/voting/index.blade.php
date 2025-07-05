@extends('layouts.vote')

@section('title', 'Encuestas Disponibles')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-green-900 to-gray-900 py-2 px-4">
        <div class="container-fluid w-full">
            <!-- Header -->
            <div class="text-center mb-12">
                <div
                    class="inline-flex items-center px-4 py-2 bg-emerald-500/20 backdrop-blur-sm rounded-full text-emerald-300 text-sm font-medium border border-emerald-500/30 mb-4">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                    Sistema de Votación Digital
                </div>
                <h1 class="text-4xl font-bold text-white mb-4">Encuestas Disponibles</h1>
                <p class="text-gray-300 text-lg">Selecciona una encuesta para participar y hacer escuchar tu voz</p>
            </div>

            <!-- Estadísticas generales -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <div class="bg-gray-800/60 backdrop-blur-sm rounded-2xl p-6 border border-emerald-500/30">
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-emerald-500/30 to-green-600/30 rounded-full flex items-center justify-center border border-emerald-500/50">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-emerald-400 font-medium">Encuestas Activas</p>
                            <p class="text-3xl font-bold text-white">{{ $activePolls }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/60 backdrop-blur-sm rounded-2xl p-6 border border-blue-500/30">
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
                            <p class="text-3xl font-bold text-white">{{ number_format($totalVotes) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/60 backdrop-blur-sm rounded-2xl p-6 border border-teal-500/30">
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-teal-500/30 to-green-600/30 rounded-full flex items-center justify-center border border-teal-500/50">
                            <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-teal-400 font-medium">Con Tiempo Límite</p>
                            <p class="text-3xl font-bold text-white">{{ $pollsWithTimeLimit }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón para ver resultados -->
            {{--
            <div class="text-center mb-8">
                <a href="{{ route('voting.results') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-emerald-500/50 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Ver Resultados en Tiempo Real
                </a>
            </div>
            --}}



            <!-- Grid de encuestas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($polls as $poll)
                    <div
                        class="bg-gradient-to-br from-green-600/20 to-green-700/20 backdrop-blur-sm rounded-2xl shadow-2xl border border-green-500/30 overflow-hidden transform transition-all duration-300 hover:-translate-y-2 hover:shadow-3xl">
                        <!-- Header de la tarjeta -->
                        <div
                            class="bg-gradient-to-r from-green-500/30 to-green-600/30 backdrop-blur-sm px-6 py-4 border-b border-green-500/30">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                                    <span class="text-green-200 text-sm font-medium">Activa</span>
                                </div>
                                <div class="text-green-200 text-sm">
                                    {{ $poll->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>

                        <!-- Contenido de la tarjeta -->
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-white mb-3 line-clamp-2">{{ $poll->title }}</h3>

                            @if ($poll->description)
                                <p class="text-gray-300 text-sm mb-4 line-clamp-3">{{ $poll->description }}</p>
                            @endif

                            <!-- Opciones preview -->
                            <div class="mb-4">
                                <p class="text-green-200 text-sm font-medium mb-2">Opciones disponibles:</p>
                                <div class="space-y-1">
                                    @foreach ($poll->options->take(3) as $option)
                                        <div class="bg-gray-50/10 rounded-lg px-3 py-2">
                                            <span class="text-gray-200 text-sm">{{ $option->label }}</span>
                                        </div>
                                    @endforeach
                                    @if ($poll->options->count() > 3)
                                        <div class="text-green-300 text-xs text-center py-1">
                                            +{{ $poll->options->count() - 3 }} opciones más
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Estadísticas -->
                            <div class="bg-gradient-to-r from-gray-50/5 to-green-50/5 rounded-xl p-4 mb-4">
                                <div class="grid grid-cols-2 gap-4 text-center">
                                    <div>
                                        <p class="text-2xl font-bold text-white">{{ $poll->votes_count ?? 0 }}</p>
                                        <p class="text-green-200 text-xs">Votos</p>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-white">{{ $poll->options->count() }}</p>
                                        <p class="text-green-200 text-xs">Opciones</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tiempo restante -->
                            @if ($poll->time_active)
                                <div class="mb-4">
                                    @php
                                        $endTime = \Carbon\Carbon::parse($poll->date)->addMinutes($poll->time_active);
                                        $timeRemaining = $endTime->diffInMinutes(now());
                                        $isExpired = $endTime->isPast();
                                    @endphp

                                    @if (!$isExpired)
                                        <div
                                            class="flex items-center space-x-2 bg-gradient-to-r from-amber-500/20 to-orange-500/20 rounded-lg px-3 py-2 border border-amber-500/30">
                                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-amber-200 text-sm">{{ $timeRemaining }} minutos
                                                restantes</span>
                                        </div>
                                    @else
                                        <div
                                            class="flex items-center space-x-2 bg-gradient-to-r from-red-500/20 to-red-600/20 rounded-lg px-3 py-2 border border-red-500/30">
                                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-red-200 text-sm">Tiempo expirado</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="mb-4">
                                    <div
                                        class="flex items-center space-x-2 bg-gradient-to-r from-green-500/20 to-emerald-500/20 rounded-lg px-3 py-2 border border-green-500/30">
                                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        <span class="text-green-200 text-sm">Sin límite de tiempo</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Botones de acción -->
                            <div class="space-y-2">
                                @php
                                    $canVote =
                                        !$poll->time_active ||
                                        !\Carbon\Carbon::parse($poll->date)->addMinutes($poll->time_active)->isPast();
                                @endphp

                                @if ($canVote)
                                    <a href="{{ route('poll.voting.show', $poll->access_token) }}"
                                        class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-500/50 shadow-lg">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Participar Ahora
                                    </a>
                                @else
                                    <button disabled
                                        class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-gray-300 font-medium rounded-xl cursor-not-allowed opacity-50">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        No Disponible
                                    </button>
                                @endif

                                <a href="{{ route('poll.voting.result', $poll->access_token) }}"
                                    class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-emerald-500/50 shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                    Ver Resultados
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Estado sin encuestas -->
                    <div class="col-span-full">
                        <div class="text-center py-16">
                            <div class="bg-gray-800/60 backdrop-blur-sm rounded-2xl p-12 border border-gray-700">
                                <svg class="w-20 h-20 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                    </path>
                                </svg>
                                <h3 class="text-2xl font-bold text-white mb-4">No hay encuestas disponibles</h3>
                                <p class="text-gray-300 mb-6">Actualmente no hay encuestas activas para participar.</p>
                                <button onclick="window.location.reload()"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-emerald-500/50 shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                    Actualizar
                                </button>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            @if ($polls instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-12">
                    {{ $polls->links() }}
                </div>
            @endif

            <!-- Botones de navegación -->
            <div class="flex justify-center space-x-4 my-4">
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
