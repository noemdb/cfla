<div class="text-center my-6">
    <div x-data="{ open: false }">
        <button @click="open = ! open" class="btn-diagnostic group">
            <svg class="w-5 h-5 mr-2 transition-transform group-hover:scale-110" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span x-text="open ? 'Ocultar Estadísticas' : 'Visualizar Resultados'"></span>
        </button>

        <div x-show="open" x-collapse class="mt-6">
            <div class="diagnostic-card rounded-2xl p-8 border border-emerald-500/20 shadow-2xl backdrop-blur-xl">
                <div class="flex items-center justify-center space-x-3 mb-8">
                    <div class="h-px bg-gradient-to-r from-transparent via-emerald-500/50 to-transparent flex-1"></div>
                    <h4 class="text-3xl font-extrabold text-white tracking-tight uppercase">Dashboard de Resultados</h4>
                    <div class="h-px bg-gradient-to-r from-transparent via-emerald-500/50 to-transparent flex-1"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($seccions as $item)
                        <div
                            class="bg-gray-900/40 border border-emerald-500/20 rounded-xl p-6 transition-all duration-300 hover:scale-[1.02] hover:bg-gray-900/60 group">
                            <div class="text-emerald-400 text-xs font-bold uppercase tracking-wider mb-2">Sección</div>
                            <h4
                                class="text-4xl font-black text-white mb-4 group-hover:text-emerald-300 transition-colors">
                                {{ $item->name }}
                            </h4>
                            <div class="flex items-end justify-between">
                                <span class="text-gray-400 text-sm font-medium">Puntaje Acumulado:</span>
                                <span class="text-3xl font-bold text-emerald-400">
                                    {{ $competition->getTotalScoreForSection($item->id) }}<span
                                        class="text-xs ml-1 text-emerald-600">PTS</span>
                                </span>
                            </div>
                            <div class="mt-4 w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-emerald-600 to-emerald-400 h-full rounded-full"
                                    style="width: 75%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-gray-500 italic">
                            No se han registrado secciones participantes todavía.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
