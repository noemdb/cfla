<div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-4">

    <div
        class="flex items-center space-x-4 text-xs md:text-sm text-gray-300 font-mono bg-black/40 px-4 py-2 rounded-full backdrop-blur-md border border-white/10">
        <div class="font-bold text-emerald-400">{{ $category->name ?? 'General' }}</div>
        <div class="w-px h-4 bg-white/20"></div>
        <div>{{ $item->created_at->format('d M Y') ?? null }}</div>
    </div>

    <div class="flex justify-start">
        <button wire:click="showItem({{ $item->id }})"
            class="px-6 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-full shadow-lg shadow-emerald-500/30 transition-all hover:scale-105 active:scale-95 flex items-center gap-2">
            <span>Leer Más</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
            </svg>
        </button>
    </div>

</div>
