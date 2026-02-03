@extends('layouts.dashboard')

@section('title', 'Votaciones - Panel de Control')

@section('content')

    <div class="fade-in">
        <!-- Header Section -->
        <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white mb-2">Panel de Votaciones</h1>
                <p class="text-emerald-400 font-medium">Gestiona tus encuestas y monitorea resultados en tiempo real.</p>
            </div>
            <a href="{{ route('admin.voting.polls.create') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl transition-all duration-300 shadow-lg shadow-emerald-500/20 font-bold group">
                <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                Nueva Encuesta
            </a>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="diagnostic-card bg-emerald-500/10 border border-emerald-500/20 p-6 rounded-2xl">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-emerald-300 text-sm font-medium">Total Encuestas</p>
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
                <p class="text-white text-3xl font-bold">{{ $stats['total_polls'] }}</p>
            </div>

            <div class="diagnostic-card bg-blue-500/10 border border-blue-500/20 p-6 rounded-2xl">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-blue-300 text-sm font-medium">Activas</p>
                    <div class="flex items-center">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-2 animate-pulse"></span>
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="text-white text-3xl font-bold">{{ $stats['active_polls'] }}</p>
            </div>

            <div class="diagnostic-card bg-purple-500/10 border border-purple-500/20 p-6 rounded-2xl">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-purple-300 text-sm font-medium">Total Votos</p>
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <p class="text-white text-3xl font-bold">{{ $stats['total_votes'] }}</p>
            </div>

            <div class="diagnostic-card bg-amber-500/10 border border-amber-500/20 p-6 rounded-2xl">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-amber-300 text-sm font-medium">Inactivas</p>
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-white text-3xl font-bold">{{ $stats['finished_polls'] }}</p>
            </div>
        </div>

        <div class="space-y-6">
            <h2 class="text-xl font-bold text-white mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
                Encuestas Registradas
            </h2>

            @if ($polls->isEmpty())
                <div
                    class="diagnostic-card bg-gray-900/40 backdrop-blur-md border border-white/5 p-12 text-center rounded-3xl">
                    <div class="w-20 h-20 bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">No hay encuestas registradas</h3>
                    <p class="text-gray-400 mb-8 max-w-sm mx-auto">Crea tu primera encuesta para comenzar a recolectar
                        opiniones.</p>
                    <a href="{{ route('admin.voting.polls.create') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-xl border border-emerald-500/20 transition-all duration-300 font-bold uppercase tracking-widest text-xs">
                        Crear Mi Primera Encuesta
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6">
                    @foreach ($polls as $poll)
                        <div
                            class="diagnostic-card bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-3xl overflow-hidden group hover:border-emerald-500/30 transition-all duration-300">
                            <div class="p-6 md:p-8">
                                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-3 mb-4">
                                            <h3
                                                class="text-xl font-bold text-white group-hover:text-emerald-400 transition-colors">
                                                {{ $poll->title }}</h3>
                                            <span
                                                class="px-3 py-1 text-[10px] font-bold uppercase tracking-widest rounded-full {{ $poll->enable ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-gray-500/10 text-gray-400 border border-gray-500/20' }}">
                                                {{ $poll->enable ? 'Activa' : 'Inactiva' }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                                            <div class="flex items-center text-gray-400">
                                                <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                                    </path>
                                                </svg>
                                                <span>{{ $poll->access_token }}</span>
                                            </div>
                                            <div class="flex items-center text-gray-400">
                                                <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>{{ $poll->time_active }} min</span>
                                            </div>
                                            <div class="flex items-center text-gray-400">
                                                <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 6h16M4 12h16M4 18h7"></path>
                                                </svg>
                                                <span>{{ $poll->options->count() }} opc</span>
                                            </div>
                                            <div class="flex items-center text-gray-400">
                                                <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>{{ $poll->votes_count }} votos</span>
                                            </div>
                                        </div>

                                        @if ($poll->enable && $poll->time_remaining)
                                            <div
                                                class="mt-4 inline-flex items-center px-3 py-1 bg-amber-500/5 text-amber-400 text-xs font-bold rounded-lg border border-amber-500/10">
                                                <span
                                                    class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-2 animate-pulse"></span>
                                                Tiempo restante: {{ $poll->time_remaining }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2">
                                        @if ($poll->enable)
                                            <form action="{{ route('admin.voting.polls.stop', $poll) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="flex items-center gap-2 px-4 py-2.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-xl border border-red-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H10a1 1 0 01-1-1v-4z">
                                                        </path>
                                                    </svg>
                                                    Detener
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.voting.polls.start', $poll) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="flex items-center gap-2 px-4 py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-xl border border-emerald-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                                        </path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Iniciar
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('admin.voting.polls.edit', $poll) }}"
                                            class="flex items-center gap-2 px-4 py-2.5 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 rounded-xl border border-blue-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            Editar
                                        </a>

                                        <form action="{{ route('admin.voting.polls.reset', $poll) }}" method="POST"
                                            onsubmit="return confirmReset('{{ $poll->title }}', {{ $poll->votes_count }})">
                                            @csrf
                                            <button type="submit"
                                                class="flex items-center gap-2 px-4 py-2.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 rounded-xl border border-amber-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                    </path>
                                                </svg>
                                                Reset
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.voting.polls.destroy', $poll) }}" method="POST"
                                            onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta encuesta?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="flex items-center justify-center p-2.5 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white rounded-xl border border-red-500/20 transition-all duration-300 group/del">
                                                <svg class="w-5 h-5 group-hover/del:scale-110 transition-transform"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                @if ($poll->enable)
                                    <div
                                        class="mt-8 pt-6 border-t border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-emerald-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p
                                                    class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">
                                                    Enlace Público</p>
                                                <p
                                                    class="text-sm text-emerald-400 truncate font-mono bg-emerald-500/5 px-3 py-1.5 rounded-lg border border-emerald-500/10">
                                                    {{ url('/poll/voting/' . $poll->access_token) }}
                                                </p>
                                            </div>
                                        </div>
                                        <button
                                            onclick="copyToClipboard('{{ url('/poll/voting/' . $poll->access_token) }}')"
                                            class="inline-flex items-center justify-center px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-xl border border-white/10 transition-all duration-300 text-xs font-bold">
                                            Copiar Enlace
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="diagnostic-card bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-3xl">
                <h3 class="text-lg font-bold text-white mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                    Tráfico y Visitas
                </h3>
                <livewire:visits-dashboard />
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                window.$wireui.notify({
                    title: '¡Copiado!',
                    description: 'El enlace ha sido copiado al portapapeles.',
                    icon: 'success'
                });
            });
        }
    </script>

    </div>
    </div>


@endsection

@section('script')
    @parent
    <script>
        function confirmReset(pollTitle, voteCount) {
            const message = `¿Estás seguro de que quieres REINICIAR la encuesta "${pollTitle}"?\n\n` +
                `Esta acción eliminará:\n` +
                `• ${voteCount} votos registrados\n` +
                `• Todas las sesiones de votación\n` +
                `• El historial de participación\n\n` +
                `La encuesta se detendrá y podrá iniciarse nuevamente desde cero.\n\n` +
                `Esta acción NO se puede deshacer.`;

            return confirm(message);
        }
    </script>
@endsection
