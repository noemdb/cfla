@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginación" class="flex items-center justify-between">
        {{-- Mobile --}}
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-bold text-gray-600 bg-gray-800/50 border border-white/5 cursor-default rounded-lg leading-5 select-none">
                    ← Anterior
                </span>
            @else
                <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')"
                    class="relative inline-flex items-center px-4 py-2 text-xs font-bold text-gray-300 bg-gray-800/50 border border-white/10 rounded-lg leading-5 hover:bg-gray-700 hover:text-white transition-all duration-200">
                    ← Anterior
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')"
                    class="relative inline-flex items-center px-4 py-2 ml-3 text-xs font-bold text-gray-300 bg-gray-800/50 border border-white/10 rounded-lg leading-5 hover:bg-gray-700 hover:text-white transition-all duration-200">
                    Siguiente →
                </button>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-xs font-bold text-gray-600 bg-gray-800/50 border border-white/5 cursor-default rounded-lg leading-5 select-none">
                    Siguiente →
                </span>
            @endif
        </div>

        {{-- Desktop --}}
        <div class="hidden sm:flex sm:items-center sm:justify-between w-full">
            <div>
                <p class="text-[11px] text-gray-500">
                    <span class="font-medium text-gray-400">{{ $paginator->firstItem() }}</span>
                    <span>–</span>
                    <span class="font-medium text-gray-400">{{ $paginator->lastItem() }}</span>
                    <span> de </span>
                    <span class="font-medium text-gray-400">{{ $paginator->total() }}</span>
                    <span> resultados</span>
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex items-center gap-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Anterior">
                            <span class="relative inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold text-gray-600 bg-gray-800/30 border border-white/5 cursor-default select-none">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </span>
                        </span>
                    @else
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" aria-label="Anterior"
                            class="relative inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold text-gray-400 bg-gray-800/30 border border-white/5 hover:bg-gray-700 hover:text-white hover:border-white/10 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold text-gray-500 bg-gray-800/30 border border-white/5 cursor-default select-none">{{ $element }}</span>
                            </span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 cursor-default select-none">{{ $page }}</span>
                                    </span>
                                @else
                                    <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                        wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}"
                                        class="relative inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold text-gray-400 bg-gray-800/30 border border-white/5 hover:bg-gray-700 hover:text-white hover:border-white/10 transition-all duration-200"
                                        aria-label="Ir a página {{ $page }}">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" aria-label="Siguiente"
                            class="relative inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold text-gray-400 bg-gray-800/30 border border-white/5 hover:bg-gray-700 hover:text-white hover:border-white/10 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    @else
                        <span aria-disabled="true" aria-label="Siguiente">
                            <span class="relative inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold text-gray-600 bg-gray-800/30 border border-white/5 cursor-default select-none">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
