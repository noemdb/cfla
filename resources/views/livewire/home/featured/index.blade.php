<h5
    class="text-lg md:text-xl lg:text-2xl flex items-center text-emerald-800 dark:text-emerald-100 font-bold mb-3 group-hover:text-emerald-600 dark:group-hover:text-emerald-300 transition-colors duration-300">
    <span class="text-xs text-gray-500 dark:text-gray-500 font-mono pr-2 opacity-50">{{ $item->id ?? null }}. </span>
    {{-- <x-icon name="{{ $category->iconClass ?? null }}" class="w-6 h-6 mr-1" /> --}} {{ $item->title ?? null }}
</h5>

<div class="font-normal text-gray-700 dark:text-gray-300 text-sm md:text-base leading-relaxed">
    {{ $item->description ?? null }}</div>

<div class="font-light text-sm border-t border-emerald-500/20 mt-4 pt-4 max-w-full overflow-hidden text-wrap word-break">
    <div
        class="text-gray-500 dark:text-gray-400 line-clamp-3 group-hover:text-gray-900 dark:group-hover:text-gray-300 transition-colors duration-300">
        {{ Str::limit($item->body, 500, '...') ?? null }}
    </div>
</div>

<div
    class="border-t border-emerald-500/20 mt-4 pt-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-500 font-mono">
    <span>{{ $item->created_at->format('d M Y') ?? null }}</span>
</div>

<div class="flex justify-end py-2">
    <button wire:click="showItem({{ $item->id ?? null }})"
        class="w-full sm:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors text-sm font-medium shadow-lg hover:shadow-emerald-500/30">
        Más...
    </button>
</div>
