@extends('layouts.dashboard')

@section('title', 'Detalles de la Encuesta - Votaciones')

@section('content')
    <div class="fade-in max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.voting.dashboard') }}"
                    class="p-2 text-gray-400 hover:text-emerald-400 bg-white/5 hover:bg-emerald-500/10 rounded-xl border border-white/5 transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-extrabold text-white mb-1">Detalles de la Encuesta</h1>
                    <p class="text-emerald-400 font-medium">Información técnica y seguimiento de participación.</p>
                </div>
            </div>

            <!-- Global Actions -->
            <div class="flex items-center gap-3">
                @if ($poll->enable)
                    <form action="{{ route('admin.voting.polls.stop', $poll) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="px-5 py-2.5 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white rounded-xl border border-red-500/20 transition-all duration-300 font-bold text-xs uppercase tracking-widest flex items-center gap-2">
                            <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                            Detener
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.voting.polls.start', $poll) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="px-5 py-2.5 bg-emerald-500/10 hover:bg-emerald-500 text-emerald-500 hover:text-white rounded-xl border border-emerald-500/20 transition-all duration-300 font-bold text-xs uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                </path>
                            </svg>
                            Iniciar
                        </button>
                    </form>
                @endif

                <a href="{{ route('admin.voting.polls.edit', $poll) }}"
                    class="px-5 py-2.5 bg-blue-500/10 hover:bg-blue-500 text-blue-500 hover:text-white rounded-xl border border-blue-500/20 transition-all duration-300 font-bold text-xs uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Editar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar: Info & Stats -->
            <div class="space-y-8">
                <!-- Status Card -->
                <div
                    class="diagnostic-card bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-3xl relative overflow-hidden group">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 blur-3xl -mr-16 -mt-16 group-hover:bg-emerald-500/10 transition-all duration-500">
                    </div>

                    <div class="flex items-start justify-between mb-6">
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Estado de Votación</p>
                            <div class="flex items-center gap-2">
                                <span
                                    class="w-2 h-2 rounded-full {{ $poll->enable ? 'bg-emerald-500 animate-pulse' : 'bg-gray-500' }}"></span>
                                <h3 class="text-xl font-black text-white uppercase">
                                    {{ $poll->enable ? 'En Curso' : 'Inactiva' }}</h3>
                            </div>
                        </div>
                        <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>

                    @if ($poll->enable && $poll->time_remaining)
                        <div
                            class="bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 rounded-2xl flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-xs font-bold text-emerald-400 uppercase tracking-tight">Vence en:
                                {{ $poll->time_remaining }}</p>
                        </div>
                    @endif
                </div>

                <!-- Technical Details -->
                <div class="diagnostic-card bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-3xl">
                    <h3
                        class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-6 py-2 px-3 bg-white/5 rounded-lg border border-white/5 inline-block">
                        Ficha Técnica</h3>

                    <div class="space-y-6">
                        <div class="group/item">
                            <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-1.5 ml-1">Token de
                                Acceso</p>
                            <div class="flex items-center gap-2">
                                <code
                                    class="bg-gray-800/50 border border-white/5 px-3 py-2 rounded-xl text-sm text-emerald-400 font-bold flex-1">{{ $poll->access_token }}</code>
                                <button onclick="copyToClipboard('{{ $poll->access_token }}')"
                                    class="p-2 text-gray-400 hover:text-white bg-white/5 hover:bg-emerald-500 rounded-xl border border-white/5 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-between items-center py-4 border-t border-white/5">
                            <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Duración</p>
                            <p class="text-sm font-bold text-white">{{ $poll->time_active }} min</p>
                        </div>

                        @if ($poll->date)
                            <div class="flex justify-between items-center py-4 border-t border-white/5">
                                <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Inicio</p>
                                <p class="text-sm font-bold text-white">{{ $poll->date->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif

                        <div class="flex justify-between items-center py-4 border-t border-white/5">
                            <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Creación</p>
                            <p class="text-sm font-bold text-white">{{ $poll->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="diagnostic-card bg-blue-500/5 border border-blue-500/10 p-5 rounded-2xl text-center">
                        <p class="text-[10px] font-bold text-blue-400/60 uppercase tracking-widest mb-1">Opciones</p>
                        <p class="text-2xl font-black text-white">{{ $poll->options->count() }}</p>
                    </div>
                    <div class="diagnostic-card bg-purple-500/5 border border-purple-500/10 p-5 rounded-2xl text-center">
                        <p class="text-[10px] font-bold text-purple-400/60 uppercase tracking-widest mb-1">Sesiones</p>
                        <p class="text-2xl font-black text-white">{{ $poll->sessions->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Main Content: Results & History -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Share Link Box -->
                @if ($poll->enable)
                    <div
                        class="diagnostic-card bg-emerald-500/10 border border-emerald-500/20 p-8 rounded-3xl relative overflow-hidden group">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 blur-3xl -mr-16 -mt-16 group-hover:bg-emerald-500/10 transition-all duration-500">
                        </div>
                        <h3
                            class="text-emerald-400 font-bold text-xs uppercase tracking-widest mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z">
                                </path>
                            </svg>
                            Enlace para Participar
                        </h3>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div
                                class="flex-1 bg-gray-900/50 border border-white/10 rounded-2xl px-5 py-4 text-emerald-400 font-mono text-xs truncate">
                                {{ url('/poll/voting/' . $poll->access_token) }}
                            </div>
                            <button onclick="copyToClipboard('{{ url('/poll/voting/' . $poll->access_token) }}')"
                                class="px-8 py-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl transition-all duration-300 shadow-xl shadow-emerald-500/20 font-bold uppercase tracking-widest text-[10px]">
                                COPIAR ENLACE
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Results Section -->
                <div
                    class="diagnostic-card bg-gray-900/40 backdrop-blur-md border border-white/5 p-8 rounded-3xl relative overflow-hidden group">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 blur-3xl -mr-16 -mt-16 group-hover:bg-blue-500/10 transition-all duration-500">
                    </div>

                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-2xl font-bold text-white mb-1">{{ $poll->title }}</h2>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Resultados en Tiempo Real
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-black text-emerald-400">{{ $poll->votes_count }}</p>
                            <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Votos Totales</p>
                        </div>
                    </div>

                    @if ($poll->votes_count > 0)
                        <div class="space-y-8">
                            @foreach ($poll->options as $option)
                                @php
                                    $percentage =
                                        $poll->votes_count > 0
                                            ? round(($option->votes_count / $poll->votes_count) * 100)
                                            : 0;
                                @endphp
                                <div class="space-y-3">
                                    <div class="flex justify-between items-end">
                                        <div class="space-y-1">
                                            <p class="text-sm font-bold text-white">{{ $option->label }}</p>
                                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">
                                                {{ $option->votes_count }} Votos registrados</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xl font-black text-emerald-400">{{ $percentage }}%</p>
                                        </div>
                                    </div>
                                    <div
                                        class="relative w-full h-3 bg-white/5 rounded-full overflow-hidden border border-white/5">
                                        <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-emerald-600 to-emerald-400 rounded-full transition-all duration-1000 ease-out"
                                            style="width: {{ $percentage }}%">
                                            <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-16 opacity-40">
                            <svg class="w-16 h-16 mx-auto mb-6 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <p class="text-sm font-bold uppercase tracking-widest">Sin participación registrada todavía</p>

                            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-md mx-auto">
                                @foreach ($poll->options as $option)
                                    <div
                                        class="bg-white/5 p-3 rounded-xl border border-white/5 text-xs text-gray-400 font-medium">
                                        {{ $option->label }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($poll->votes_count > 0)
                        <div class="mt-12 pt-8 border-t border-white/5 flex justify-center">
                            <form action="{{ route('admin.voting.polls.reset', $poll) }}" method="POST"
                                onsubmit="return confirmReset()">
                                @csrf
                                <button type="submit"
                                    class="px-8 py-3 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white rounded-2xl border border-red-500/20 transition-all duration-300 font-bold uppercase tracking-widest text-[10px] flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                    Reiniciar Resultados
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Session History -->
                <div
                    class="diagnostic-card bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-3xl overflow-hidden">
                    <div class="p-8 border-b border-white/5 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Últimas 10 Sesiones
                        </h3>
                        <span
                            class="px-3 py-1 bg-white/5 rounded-lg text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ $poll->sessions->count() }}
                            Total</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr
                                    class="bg-white/5 text-[10px] font-black uppercase tracking-widest text-gray-500 border-b border-white/5">
                                    <th class="px-8 py-5">Estado</th>
                                    <th class="px-8 py-5">Dirección IP</th>
                                    <th class="px-8 py-5">Fecha / Hora</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 text-sm">
                                @forelse($poll->sessions->take(10) as $session)
                                    <tr class="group hover:bg-white/[0.02] transition-colors">
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest {{ $session->voted ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-gray-500/10 text-gray-400 border border-gray-500/20' }}">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full {{ $session->voted ? 'bg-emerald-500' : 'bg-gray-500' }}"></span>
                                                {{ $session->voted ? 'Votó' : 'Pendiente' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 whitespace-nowrap text-white/70 font-mono">
                                            {{ $session->ip ?? '0.0.0.0' }}</td>
                                        <td class="px-8 py-5 whitespace-nowrap text-gray-500 font-medium">
                                            {{ $session->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3"
                                            class="px-8 py-20 text-center text-gray-600 font-bold uppercase tracking-widest text-xs italic">
                                            No hay actividad registrada aún</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <x-notification title="Éxito" description="{{ session('success') }}" icon="success" />
    @endif

    @if (session('error'))
        <x-notification title="Error" description="{{ session('error') }}" icon="error" />
    @endif
@endsection

@section('script')
    @parent
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Se asume que WireUI gestionará las notificaciones si están disponibles,
                // de lo contrario, creamos un fallback visual premium
                $wireui.notify({
                    title: '¡Copiado!',
                    description: 'El texto se ha copiado al portapapeles con éxito.',
                    icon: 'success'
                });
            }).catch(err => {
                console.error('Error al copiar:', err);
            });
        }

        function confirmReset() {
            return confirm(
                '¿ESTÁS ABSOLUTAMENTE SEGURO?\n\nEsta acción ELIMINARÁ todos los votos y sesiones de esta encuesta.\n\nEsta operación es irreversible.'
                );
        }
    </script>
@endsection
