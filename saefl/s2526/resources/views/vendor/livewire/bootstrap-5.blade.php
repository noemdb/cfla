<div>
    @if ($paginator->hasPages())
        @php(isset($this->numberOfPaginatorsRendered[$paginator->getPageName()]) ? $this->numberOfPaginatorsRendered[$paginator->getPageName()]++ : $this->numberOfPaginatorsRendered[$paginator->getPageName()] = 1)

        <nav class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 py-2">
            {{-- Resumen de resultados --}}
            <div class="text-center text-sm-start w-100 w-sm-auto order-2 order-sm-1">
                <p class="text-muted mb-0 small">
                    <span class="fw-semibold text-dark">{{ $paginator->firstItem() ?? 0 }}</span>
                    {!! __('al') !!}
                    <span class="fw-semibold text-dark">{{ $paginator->lastItem() ?? 0 }}</span>
                    {!! __('de') !!}
                    <span class="fw-semibold text-dark">{{ $paginator->total() }}</span>
                    {!! __('registros') !!}
                </p>
            </div>

            {{-- Paginación --}}
            <ul class="
	            pagination 
			    pagination-sm 
			    mb-0 
			    flex-wrap justify-content-center 
			    flex-sm-nowrap justify-content-sm-end 
			    gap-1 
			    order-1 order-sm-2
            ">
                {{-- Enlace a primera página --}}
                @if (!$paginator->onFirstPage())
                    <li class="page-item">
                        <button type="button" 
                                class="page-link" 
                                wire:click="gotoPage(1, '{{ $paginator->getPageName() }}')"
                                wire:loading.attr="disabled"
                                title="Primera página"
                                aria-label="Primera página">
                            <i class="fas fa-angle-double-left"></i>
                        </button>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link" aria-hidden="true">
                            <i class="fas fa-angle-double-left"></i>
                        </span>
                    </li>
                @endif

                {{-- Enlace a página anterior --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                        <span class="page-link" aria-hidden="true">
                            <i class="fas fa-angle-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <button type="button" 
                                dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" 
                                class="page-link" 
                                wire:click="previousPage('{{ $paginator->getPageName() }}')" 
                                wire:loading.attr="disabled" 
                                rel="prev" 
                                aria-label="@lang('pagination.previous')"
                                title="Página anterior">
                            <i class="fas fa-angle-left"></i>
                        </button>
                    </li>
                @endif

                {{-- Números de página --}}
                @foreach ($elements as $element)
                    {{-- Separador "..." --}}
                    @if (is_string($element))
                        <li class="page-item disabled d-none d-sm-block" aria-disabled="true">
                            <span class="page-link">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array de páginas --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active d-none d-sm-block" 
                                    wire:key="paginator-{{ $paginator->getPageName() }}-{{ $this->numberOfPaginatorsRendered[$paginator->getPageName()] }}-page-{{ $page }}" 
                                    aria-current="page">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                {{-- Mostrar páginas cercanas a la actual --}}
                                @if ($page >= $paginator->currentPage() - 2 && $page <= $paginator->currentPage() + 2)
                                    <li class="page-item d-none d-sm-block" 
                                        wire:key="paginator-{{ $paginator->getPageName() }}-{{ $this->numberOfPaginatorsRendered[$paginator->getPageName()] }}-page-{{ $page }}">
                                        <button type="button" 
                                                class="page-link" 
                                                wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                                title="Ir a página {{ $page }}">
                                            {{ $page }}
                                        </button>
                                    </li>
                                @endif
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Indicador de página actual para móviles --}}
                <li class="page-item d-block d-sm-none">
                    <span class="page-link text-dark fw-semibold">
                        {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
                    </span>
                </li>

                {{-- Enlace a página siguiente --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <button type="button" 
                                dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" 
                                class="page-link" 
                                wire:click="nextPage('{{ $paginator->getPageName() }}')" 
                                wire:loading.attr="disabled" 
                                rel="next" 
                                aria-label="@lang('pagination.next')"
                                title="Página siguiente">
                            <i class="fas fa-angle-right"></i>
                        </button>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                        <span class="page-link" aria-hidden="true">
                            <i class="fas fa-angle-right"></i>
                        </span>
                    </li>
                @endif

                {{-- Enlace a última página --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <button type="button" 
                                class="page-link" 
                                wire:click="gotoPage({{ $paginator->lastPage() }}, '{{ $paginator->getPageName() }}')"
                                wire:loading.attr="disabled"
                                title="Última página"
                                aria-label="Última página">
                            <i class="fas fa-angle-double-right"></i>
                        </button>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link" aria-hidden="true">
                            <i class="fas fa-angle-double-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>

        {{-- Selector de página para móviles --}}
        <div class="d-block d-sm-none mt-2">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <label for="pageSelect{{ $paginator->getPageName() }}" class="form-label small text-muted mb-1">
                        Ir a página:
                    </label>
                </div>
                <div class="col">
                    <select class="form-select form-select-sm" 
                            id="pageSelect{{ $paginator->getPageName() }}"
                            wire:change="gotoPage($event.target.value, '{{ $paginator->getPageName() }}')">
                        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                            <option value="{{ $i }}" {{ $i == $paginator->currentPage() ? 'selected' : '' }}>
                                Página {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
    @else
        {{-- Sin resultados --}}
        <div class="text-center py-3">
            <p class="text-muted mb-0 small">
                <span class="fw-semibold text-dark">{{ $paginator->total() }}</span>
                {!! __('registros encontrados') !!}
            </p>
        </div>
    @endif
</div>

@section('stylesheets')
@parent
<style>
    /* Estilos personalizados para la paginación */
    .pagination .page-link {
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
        color: #6c757d;
        transition: all 0.15s ease-in-out;
        min-width: 2.5rem;
        text-align: center;
    }

    .pagination .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #565e64;
        transform: translateY(-1px);
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
        box-shadow: 0 0.125rem 0.25rem rgba(13, 110, 253, 0.3);
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
        opacity: 0.6;
    }

    .pagination .page-link:focus {
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        z-index: 3;
    }

    /* Responsive improvements */
    @media (max-width: 576px) {
        .pagination .page-item:not(.active) {
            margin: 0.125rem;
        }
        
        .pagination .page-link {
            min-width: 2.25rem;
            padding: 0.375rem 0.5rem;
            font-size: 0.75rem;
        }
    }
</style>
@endsection

@section('scripts')
@parent
<script>
document.addEventListener('livewire:load', function() {
    // Smooth scroll to top when paginating
    Livewire.hook('message.processed', (message, component) => {
        if (message.updateQueue.some(update => update.type === 'callMethod' && 
            ['gotoPage', 'previousPage', 'nextPage'].includes(update.payload.method))) {
            // Scroll suave al top de la tabla
            const table = document.querySelector('.table-responsive');
            if (table) {
                table.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    });
});
</script>
@endsection