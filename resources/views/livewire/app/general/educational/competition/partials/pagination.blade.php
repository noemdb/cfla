<div class="flex items-center justify-between px-4 py-3 sm:px-6">
    <div class="flex flex-1 justify-between sm:hidden">
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center rounded-lg border border-white/5 bg-gray-900 px-4 py-2 text-sm font-medium text-gray-500 cursor-default uppercase tracking-widest transition-all">
                Anterior
            </span>
        @else
            <button wire:click="previousPage" wire:loading.attr="disabled" class="relative inline-flex items-center rounded-lg border border-white/10 bg-gray-900 px-4 py-2 text-sm font-medium text-gray-300 hover:bg-emerald-500 hover:text-white hover:border-emerald-500 uppercase tracking-widest transition-all active:scale-95 shadow-lg">
                Anterior
            </button>
        @endif

        @if ($paginator->hasMorePages())
            <button wire:click="nextPage" wire:loading.attr="disabled" class="relative ml-3 inline-flex items-center rounded-lg border border-white/10 bg-gray-900 px-4 py-2 text-sm font-medium text-gray-300 hover:bg-emerald-500 hover:text-white hover:border-emerald-500 uppercase tracking-widest transition-all active:scale-95 shadow-lg">
                Siguiente
            </button>
        @else
            <span class="relative ml-3 inline-flex items-center rounded-lg border border-white/5 bg-gray-900 px-4 py-2 text-sm font-medium text-gray-500 cursor-default uppercase tracking-widest transition-all">
                Siguiente
            </span>
        @endif
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-400 font-medium">
                Mostrando <span class="font-bold text-gray-100">{{ $paginator->firstItem() }}</span> 
                a <span class="font-bold text-gray-100">{{ $paginator->lastItem() }}</span> 
                de <span class="font-bold text-gray-100 font-black">{{ $paginator->total() }}</span> resultados
            </p>
        </div>
        <div>
            <nav class="isolate inline-flex -space-x-px rounded-lg shadow-2xl bg-gray-900 p-1 border border-white/5" aria-label="Pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="relative inline-flex items-center rounded-lg px-2 py-2 text-gray-600 cursor-not-allowed">
                        <span class="sr-only">Anterior</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled" class="relative inline-flex items-center rounded-lg px-2 py-2 text-gray-400 hover:bg-emerald-500 hover:text-white transition-all duration-300">
                        <span class="sr-only">Anterior</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                        </svg>
                    </button>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-600">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="relative z-10 inline-flex items-center bg-emerald-500 rounded-lg px-4 py-2 text-sm font-black text-white shadow-lg shadow-emerald-500/20">
                                    {{ $page }}
                                </span>
                            @else
                                <button wire:click="gotoPage({{ $page }})" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-400 hover:bg-white/5 hover:text-emerald-400 rounded-lg transition-all duration-200">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled" class="relative inline-flex items-center rounded-lg px-2 py-2 text-gray-400 hover:bg-emerald-500 hover:text-white transition-all duration-300">
                        <span class="sr-only">Siguiente</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </button>
                @else
                    <span class="relative inline-flex items-center rounded-lg px-2 py-2 text-gray-600 cursor-not-allowed">
                        <span class="sr-only">Siguiente</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @endif
            </nav>
        </div>
    </div>
</div>
