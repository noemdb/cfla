<div
    class="m-3 diagnostic-card rounded-2xl p-8 border border-emerald-500/20 shadow-2xl max-w-2xl mx-auto backdrop-blur-xl">
    <div class="flex flex-col items-center">
        <div class="p-3 bg-emerald-500/20 rounded-2xl mb-4">
            <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
        </div>

        <h5 class="mb-4 text-3xl font-black text-white tracking-tight uppercase">
            {{ $competition->name }}
        </h5>

        <p class="mb-6 text-gray-300 leading-relaxed text-lg max-w-lg">
            {{ $competition->description }}
        </p>

        <div x-data="{ open: false }" class="w-full">
            <button @click="open = ! open"
                class="text-emerald-400 font-bold hover:text-emerald-300 transition-colors flex items-center justify-center w-full group">
                <span x-text="open ? 'Cerrar detalles' : 'Leer misión completa'"></span>
                <svg :class="{ 'rotate-180': open }" class="w-4 h-4 ml-2 transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-collapse
                class="mt-4 p-4 bg-gray-900/50 rounded-lg border border-emerald-500/10 italic text-gray-400">
                {{ $competition->motive }}
            </div>
        </div>

        <div class="mt-8 flex items-center space-x-2 text-xs font-semibold tracking-widest text-emerald-500 uppercase">
            <span class="w-8 h-px bg-emerald-500/30"></span>
            <span>Establecida: {{ $competition->date }}</span>
            <span class="w-8 h-px bg-emerald-500/30"></span>
        </div>
    </div>
</div>
