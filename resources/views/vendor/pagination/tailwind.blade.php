@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginación" class="flex items-center justify-between">
        {{-- Mobile --}}
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-gray-800 border border-gray-700 cursor-default leading-5 rounded-lg">
                    Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-gray-300 bg-gray-800 border border-gray-700 leading-5 rounded-lg hover:text-white hover:bg-gray-700 hover:border-emerald-500/30 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all duration-200">
                    Anterior
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-bold text-gray-300 bg-gray-800 border border-gray-700 leading-5 rounded-lg hover:text-white hover:bg-gray-700 hover:border-emerald-500/30 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all duration-200">
                    Siguiente
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-gray-800 border border-gray-700 cursor-default leading-5 rounded-lg">
                    Siguiente
                </span>
            @endif
        </div>

        {{-- Desktop --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-400 leading-5">
                    Mostrando
                    @if ($paginator->firstItem())
                        <span class="font-semibold text-gray-200">{{ $paginator->firstItem() }}</span>
                        al
                        <span class="font-semibold text-gray-200">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    de
                    <span class="font-semibold text-gray-200">{{ $paginator->total() }}</span>
                    resultados
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rounded-lg overflow-hidden border border-gray-700/50 divide-x divide-gray-700/50">
                    {{-- Anterior --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Anterior">
                            <span class="relative inline-flex items-center px-3 py-2.5 text-sm font-medium text-gray-600 bg-gray-800/80 cursor-default leading-5" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2.5 text-sm font-bold text-gray-400 bg-gray-800/80 leading-5 hover:text-white hover:bg-gray-700 focus:z-10 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500/40 transition-all duration-200" aria-label="Anterior">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                    @endif

                    {{-- Elementos --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-3.5 py-2.5 -ml-px text-sm font-medium text-gray-500 bg-gray-800/80 border-0 leading-5">{{ $element }}</span>
                            </span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-3.5 py-2.5 -ml-px text-sm font-bold text-white bg-emerald-500/20 border-0 leading-5">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-3.5 py-2.5 -ml-px text-sm font-medium text-gray-400 bg-gray-800/80 leading-5 hover:text-white hover:bg-gray-700 focus:z-10 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500/40 transition-all duration-200" aria-label="Ir a página {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Siguiente --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2.5 -ml-px text-sm font-bold text-gray-400 bg-gray-800/80 leading-5 hover:text-white hover:bg-gray-700 focus:z-10 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500/40 transition-all duration-200" aria-label="Siguiente">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Siguiente">
                            <span class="relative inline-flex items-center px-3 py-2.5 -ml-px text-sm font-medium text-gray-600 bg-gray-800/80 cursor-default leading-5" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
