@extends('layouts.vote')

@section('title', 'Encuestas Activas')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-green-900 via-green-800 to-green-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-800/80 to-green-700/80 backdrop-blur-sm border-b border-green-600/30">
                <div class="py-12">
                    <div class="text-center">
                        <h1 class="text-4xl font-bold text-white mb-4">Encuestas Activas</h1>
                        <p class="text-green-100 text-lg max-w-2xl mx-auto">
                            Participa en las encuestas disponibles y comparte tu opinión
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <!-- Active Polls -->
                    <div class="bg-gradient-to-br from-green-700 to-green-600 rounded-2xl p-6 text-white shadow-2xl">
                        <div class="flex items-center">
                            <div class="p-3 bg-white/20 rounded-full backdrop-blur-sm">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-green-100 text-sm font-medium">Encuestas Activas</p>
                                <p class="text-3xl font-bold">{{ $polls->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Votes -->
                    <div class="bg-gradient-to-br from-emerald-700 to-emerald-600 rounded-2xl p-6 text-white shadow-2xl">
                        <div class="flex items-center">
                            <div class="p-3 bg-white/20 rounded-full backdrop-blur-sm">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-emerald-100 text-sm font-medium">Votos Totales</p>
                                <p class="text-3xl font-bold">{{ $totalVotes }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- With Time Limit -->
                    <div class="bg-gradient-to-br from-teal-700 to-teal-600 rounded-2xl p-6 text-white shadow-2xl">
                        <div class="flex items-center">
                            <div class="p-3 bg-white/20 rounded-full backdrop-blur-sm">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-teal-100 text-sm font-medium">Con Tiempo Límite</p>
                                <p class="text-3xl font-bold">{{ $pollsWithTimeLimit }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Polls Grid -->
                @if ($polls->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($polls as $poll)
                            <div
                                class="bg-gradient-to-br from-green-600 to-green-700 rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 hover:-translate-y-2 hover:shadow-3xl border border-green-500/30">
                                <!-- Card Header -->
                                <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 relative overflow-hidden">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent">
                                    </div>
                                    <div class="relative">
                                        <div class="flex items-center justify-between mb-2">
                                            @if ($poll->enable)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white backdrop-blur-sm">
                                                    <div class="w-2 h-2 bg-green-300 rounded-full mr-2 animate-pulse"></div>
                                                    Activa
                                                </span>
                                            @endif
                                            @if ($poll->time_active)
                                                <span class="text-green-100 text-xs">
                                                    {{ $poll->time_active }} min
                                                </span>
                                            @endif
                                        </div>
                                        <h3 class="text-xl font-bold text-white mb-2 line-clamp-2">{{ $poll->title }}</h3>
                                        @if ($poll->description)
                                            <p class="text-green-100 text-sm line-clamp-2">{{ $poll->description }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="p-6 space-y-4">
                                    <!-- Poll Information -->
                                    <div class="space-y-3">
                                        <div class="flex items-center text-green-100 text-sm">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $poll->date ? \Carbon\Carbon::parse($poll->date)->format('d/m/Y H:i') : 'Sin fecha específica' }}
                                        </div>

                                        <!-- Poll Options Preview -->
                                        @if ($poll->options && $poll->options->count() > 0)
                                            <div class="space-y-2">
                                                <p class="text-green-200 text-sm font-medium">Opciones disponibles:</p>
                                                <div class="space-y-1">
                                                    @foreach ($poll->options->take(3) as $option)
                                                        <div
                                                            class="bg-gray-50/10 backdrop-blur-sm rounded-lg px-3 py-2 border border-green-500/20">
                                                            <span class="text-green-100 text-sm">{{ $option->label }}</span>
                                                        </div>
                                                    @endforeach
                                                    @if ($poll->options->count() > 3)
                                                        <div class="text-green-200 text-xs text-center py-1">
                                                            +{{ $poll->options->count() - 3 }} opciones más
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Poll Stats -->
                                    <div
                                        class="bg-gradient-to-r from-gray-50/5 to-green-50/10 backdrop-blur-sm rounded-xl p-4 border border-green-500/20">
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

                                    <!-- Time Remaining -->
                                    @if ($poll->time_active)
                                        @php
                                            $endTime = \Carbon\Carbon::parse($poll->date)->addMinutes(
                                                $poll->time_active,
                                            );
                                            $timeRemaining = $endTime->diffInMinutes(now());
                                            $isExpiringSoon = $timeRemaining <= 30;
                                        @endphp

                                        @if ($timeRemaining > 0)
                                            <div
                                                class="bg-gradient-to-r {{ $isExpiringSoon ? 'from-amber-600/20 to-orange-600/20 border-amber-500/30' : 'from-green-600/20 to-green-700/20 border-green-500/30' }} backdrop-blur-sm rounded-lg p-3 border">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 {{ $isExpiringSoon ? 'text-amber-400' : 'text-green-400' }} mr-2"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span
                                                        class="{{ $isExpiringSoon ? 'text-amber-200' : 'text-green-200' }} text-sm">
                                                        {{ $timeRemaining }} minutos restantes
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div
                                            class="bg-gradient-to-r from-green-600/20 to-green-700/20 backdrop-blur-sm rounded-lg p-3 border border-green-500/30">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-green-400 mr-2" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="text-green-200 text-sm">Sin límite de tiempo</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Card Footer -->
                                <div class="p-6 pt-0">
                                    <div class="flex space-x-3">
                                        <!-- Participate Button -->
                                        @php
                                            $canVote =
                                                !$poll->time_active ||
                                                \Carbon\Carbon::parse($poll->date)
                                                    ->addMinutes($poll->time_active)
                                                    ->isFuture();
                                        @endphp

                                        @if ($canVote)
                                            <a href="{{ route('poll.voting.show', $poll->access_token) }}"
                                                class="flex-1 bg-gradient-to-r from-green-400 to-green-500 hover:from-green-300 hover:to-green-400 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl text-center">
                                                <div class="flex items-center justify-center">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Participar Ahora
                                                </div>
                                            </a>
                                        @else
                                            <button disabled
                                                class="flex-1 bg-gradient-to-r from-gray-600 to-gray-700 text-gray-300 font-semibold py-3 px-4 rounded-xl cursor-not-allowed text-center">
                                                <div class="flex items-center justify-center">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    No Disponible
                                                </div>
                                            </button>
                                        @endif

                                        <!-- View Results Button -->
                                        <a href="{{ route('poll.voting.result', $poll->access_token) }}"
                                            class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-500 hover:to-green-600 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                                </path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Load More Button (if needed) -->
                    @if ($polls->hasPages())
                        <div class="mt-12 flex justify-center">
                            <div
                                class="bg-gradient-to-r from-green-800/60 to-green-700/60 backdrop-blur-sm rounded-2xl p-4 border border-green-600/30">
                                {{ $polls->links('pagination::tailwind') }}
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-16">
                        <div
                            class="bg-gradient-to-br from-green-800/40 to-green-900/40 backdrop-blur-sm rounded-3xl p-12 border border-green-700/30 max-w-2xl mx-auto">
                            <svg class="w-24 h-24 text-green-400 mx-auto mb-6" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            <h3 class="text-2xl font-bold text-white mb-4">No hay encuestas activas</h3>
                            <p class="text-green-200 text-lg mb-8">
                                Actualmente no hay encuestas disponibles para participar.
                                Vuelve más tarde para ver nuevas encuestas.
                            </p>
                            <a href="{{ route('home') }}"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-500 hover:to-green-600 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Volver al Inicio
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('style')
    @parent
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Enhanced shadow effects */
        .shadow-2xl {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .shadow-3xl {
            box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.3);
        }

        .hover\:shadow-3xl:hover {
            box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.3);
        }

        /* Professional hover animations */
        .group:hover .group-hover\:scale-110 {
            transform: scale(1.1);
        }

        .hover\:-translate-y-2:hover {
            transform: translateY(-0.5rem);
        }

        .hover\:scale-105:hover {
            transform: scale(1.05);
        }

        /* Dark green gradient backgrounds */
        .bg-gradient-to-br.from-green-900.via-green-800.to-green-900 {
            background: linear-gradient(135deg, #14532d 0%, #166534 50%, #14532d 100%);
        }

        .bg-gradient-to-br.from-green-600.to-green-700 {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
        }

        .bg-gradient-to-r.from-green-700.to-green-600 {
            background: linear-gradient(90deg, #15803d 0%, #16a34a 100%);
        }

        .bg-gradient-to-r.from-green-500.to-green-600 {
            background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%);
        }

        .bg-gradient-to-r.from-green-400.to-green-500 {
            background: linear-gradient(90deg, #4ade80 0%, #22c55e 100%);
        }

        .hover\:from-green-300.hover\:to-green-400:hover {
            background: linear-gradient(90deg, #86efac 0%, #4ade80 100%);
        }

        /* Backdrop blur effects */
        .backdrop-blur-sm {
            backdrop-filter: blur(4px);
        }

        /* Border enhancements */
        .border-green-500\/20 {
            border-color: rgba(34, 197, 94, 0.2);
        }

        .border-green-500\/30 {
            border-color: rgba(34, 197, 94, 0.3);
        }

        .border-green-400\/30 {
            border-color: rgba(74, 222, 128, 0.3);
        }

        .border-amber-400\/30 {
            border-color: rgba(251, 191, 36, 0.3);
        }

        .border-red-400\/30 {
            border-color: rgba(248, 113, 113, 0.3);
        }

        /* Background opacity effects */
        .bg-green-800\/50 {
            background-color: rgba(22, 101, 52, 0.5);
        }

        .bg-green-700\/50 {
            background-color: rgba(21, 128, 61, 0.5);
        }

        .bg-green-500\/20 {
            background-color: rgba(34, 197, 94, 0.2);
        }

        .bg-emerald-500\/20 {
            background-color: rgba(16, 185, 129, 0.2);
        }

        .bg-amber-600\/20 {
            background-color: rgba(217, 119, 6, 0.2);
        }

        .bg-yellow-600\/20 {
            background-color: rgba(202, 138, 4, 0.2);
        }

        .bg-red-600\/20 {
            background-color: rgba(220, 38, 38, 0.2);
        }

        .bg-rose-600\/20 {
            background-color: rgba(225, 29, 72, 0.2);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .grid-cols-1.md\:grid-cols-2.lg\:grid-cols-3 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
                gap: 1.5rem;
            }
        }

        @media (min-width: 768px) {
            .grid-cols-1.md\:grid-cols-2.lg\:grid-cols-3 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 2rem;
            }
        }

        @media (min-width: 1024px) {
            .grid-cols-1.md\:grid-cols-2.lg\:grid-cols-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 2rem;
            }
        }

        /* Smooth transitions */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }

        /* Enhanced focus states */
        a:focus,
        button:focus {
            outline: 2px solid #4ade80;
            outline-offset: 2px;
        }

        /* Professional typography */
        .font-bold {
            font-weight: 700;
            letter-spacing: -0.025em;
        }

        .font-semibold {
            font-weight: 600;
            letter-spacing: -0.025em;
        }
    </style>
@endsection
