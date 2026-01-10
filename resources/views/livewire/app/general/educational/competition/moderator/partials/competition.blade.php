<div class="m-1 w-full">

    <div class="m-2 w-full diagnostic-card rounded-xl p-6 shadow-2xl border border-emerald-500/20">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-emerald-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h5 class="text-2xl font-bold tracking-tight text-white">
                    {{ $competition->name }}
                </h5>
                @if ($competition->status_active)
                    <span class="flex h-3 w-3 relative">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                @endif
            </div>

            <div class="flex items-center space-x-2">
                @if ($competition->status_active)
                    <button wire:click="setOffline({{ $competition->id }})"
                        class="p-2 hover:bg-red-500/20 rounded-lg text-red-400 transition-colors" title="Desactivar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                @else
                    <button wire:click="setOnline({{ $competition->id }})"
                        class="p-2 hover:bg-emerald-500/20 rounded-lg text-emerald-400 transition-colors"
                        title="Activar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div x-data="{ open: false }"
                class="bg-gray-900/40 rounded-lg p-4 border border-emerald-500/10 backdrop-blur-sm">
                <button @click="open = ! open"
                    class="flex items-center justify-between w-full text-sm font-bold text-emerald-300 hover:text-emerald-200 transition-colors">
                    <span>Información Detallada</span>
                    <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform duration-200" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse @click.outside="open = false" class="mt-3 space-y-2">
                    <p class="text-sm text-gray-300 leading-relaxed">
                        {{ $competition->description }}
                    </p>
                    <div class="flex items-center justify-between pt-2 border-t border-emerald-500/10">
                        <span class="text-xs text-gray-400 italic">Misión: {{ $competition->motive }}</span>
                        <span class="text-xs font-semibold text-emerald-400 bg-emerald-500/10 px-2 py-1 rounded">Fecha:
                            {{ $competition->date }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
