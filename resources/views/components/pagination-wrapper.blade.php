<div class="px-5 py-3 border-t border-white/5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
            {{-- Items per page --}}
            <div class="flex items-center gap-2">
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Ver</label>
                <div class="relative">
                    <select wire:model.live="paginate"
                        class="appearance-none bg-gray-800/50 border border-white/10 text-gray-300 rounded-lg pl-3 pr-8 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all cursor-pointer">
                        <option value="15">15</option>
                        <option value="30">30</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                        <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Results count --}}
            @if($paginator->total() > 0)
                <p class="text-[11px] text-gray-500">
                    <span class="font-medium text-gray-400">{{ $paginator->firstItem() }}</span>
                    <span>–</span>
                    <span class="font-medium text-gray-400">{{ $paginator->lastItem() }}</span>
                    <span> de </span>
                    <span class="font-medium text-gray-400">{{ $paginator->total() }}</span>
                    <span> resultados</span>
                </p>
            @endif
        </div>

        {{-- Pagination links --}}
        <div>
            {{ $paginator->links('vendor.livewire.custom-tailwind') }}
        </div>
    </div>
</div>
