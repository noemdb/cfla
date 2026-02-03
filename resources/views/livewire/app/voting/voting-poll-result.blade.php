<div wire:poll.3s="loadResults" class="space-y-8 fade-in">
    @if ($showTitle)
        <!-- Poll Title -->
        <div class="text-center mb-10">
            <h2 class="text-4xl font-black text-white mb-2 tracking-tight">{{ $poll->title }}</h2>
            <p class="text-emerald-400 font-medium uppercase tracking-widest text-[10px]">Resultados en Tiempo Real</p>
        </div>
    @endif

    <!-- Poll Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Votes -->
        <div
            class="diagnostic-card bg-gray-900/40 backdrop-blur-md rounded-2xl p-6 border border-white/5 relative overflow-hidden group">
            <div
                class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/5 blur-2xl -mr-12 -mt-12 group-hover:bg-emerald-500/10 transition-all">
            </div>
            <div class="flex items-center gap-4 relative">
                <div
                    class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center border border-emerald-500/20">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Total Votos</p>
                    <p class="text-3xl font-black text-white leading-none">{{ number_format($totalVotes) }}</p>
                </div>
            </div>
        </div>

        <!-- Options Count -->
        <div
            class="diagnostic-card bg-gray-900/40 backdrop-blur-md rounded-2xl p-6 border border-white/5 relative overflow-hidden group">
            <div
                class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 blur-2xl -mr-12 -mt-12 group-hover:bg-blue-500/10 transition-all">
            </div>
            <div class="flex items-center gap-4 relative">
                <div
                    class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center border border-blue-500/20">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Opciones</p>
                    <p class="text-3xl font-black text-white leading-none">{{ count($results) }}</p>
                </div>
            </div>
        </div>

        <!-- Last Updated -->
        <div
            class="diagnostic-card bg-gray-900/40 backdrop-blur-md rounded-2xl p-6 border border-white/5 relative overflow-hidden group">
            <div
                class="absolute top-0 right-0 w-24 h-24 bg-purple-500/5 blur-2xl -mr-12 -mt-12 group-hover:bg-purple-500/10 transition-all">
            </div>
            <div class="flex items-center gap-4 relative">
                <div
                    class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center border border-purple-500/20">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Actualizado</p>
                    <p class="text-lg font-black text-white uppercase">{{ $lastUpdated }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Indicator -->
    <div class="flex items-center justify-center mb-8">
        <div
            class="flex items-center gap-3 px-5 py-2.5 bg-emerald-500/10 backdrop-blur-xl rounded-full border border-emerald-500/20 shadow-xl shadow-emerald-500/5">
            <div class="relative flex h-2 w-2">
                <span
                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </div>
            <span class="text-emerald-400 text-xs font-black uppercase tracking-widest">En Vivo • Cada 3s</span>
        </div>
    </div>

    <!-- Results per Option -->
    @if (count($results) > 0)
        <div class="grid grid-cols-1 gap-6">
            @foreach ($results as $index => $result)
                <div
                    class="diagnostic-card bg-gray-900/40 backdrop-blur-md rounded-[2rem] p-8 border border-white/5 relative overflow-hidden group hover:bg-gray-900/60 transition-all duration-500">
                    @if ($index === 0 && $result['votes'] > 0)
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 blur-3xl -mr-16 -mt-16 group-hover:bg-emerald-500/10 transition-all duration-700">
                        </div>
                    @endif

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-8 relative">
                        <div class="flex items-center gap-5">
                            <!-- Rank Number -->
                            <div
                                class="flex items-center justify-center w-10 h-10 bg-gradient-to-br {{ $index === 0 ? 'from-emerald-500 to-emerald-700 shadow-lg shadow-emerald-500/20' : 'from-gray-700 to-gray-800' }} rounded-xl text-white font-black text-sm">
                                @if ($index === 0 && $result['votes'] > 0)
                                    <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                        </path>
                                    </svg>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </div>

                            <div class="space-y-1">
                                <h3 class="text-xl font-bold text-white tracking-tight">{{ $result['label'] }}</h3>
                                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                    {{ number_format($result['votes']) }} de {{ number_format($totalVotes) }} votos
                                </p>
                            </div>
                        </div>

                        <div class="text-left sm:text-right">
                            <span
                                class="text-4xl font-black text-white tracking-tighter">{{ $result['percentage'] }}%</span>
                            <div class="h-1 w-full bg-emerald-500/10 rounded-full mt-1">
                                <div class="h-full bg-emerald-500 rounded-full"
                                    style="width: {{ $result['percentage'] }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="relative w-full h-4 bg-white/5 rounded-full overflow-hidden border border-white/5 mb-4">
                        <div class="absolute inset-y-0 left-0 bg-gradient-to-r {{ $index === 0 ? 'from-emerald-600 to-emerald-400' : 'from-blue-600 to-blue-400' }} rounded-full transition-all duration-1000 ease-out"
                            style="width: {{ $result['percentage'] }}%">
                            <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    @if ($totalVotes > 0)
                        <div
                            class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-gray-600 px-1">
                            <span>Tendencia: {{ $index === 0 ? 'Líder' : 'Posición ' . ($index + 1) }}</span>
                            <span
                                class="text-emerald-500/60">{{ number_format(($result['votes'] / $totalVotes) * 100, 1) }}%
                                de Participación</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <!-- No Votes State -->
        <div
            class="text-center py-20 diagnostic-card bg-gray-900/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 border-dashed">
            <div
                class="w-20 h-20 bg-emerald-500/5 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-emerald-500/10">
                <svg class="w-10 h-10 text-emerald-500/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
            </div>
            <h3 class="text-2xl font-black text-white uppercase tracking-tight mb-2">Esperando Participación</h3>
            <p class="text-gray-500 font-medium max-w-xs mx-auto text-sm leading-relaxed">
                Los resultados aparecerán aquí automáticamente en tiempo real cuando se registren los primeros votos.
            </p>
        </div>
    @endif
</div>
