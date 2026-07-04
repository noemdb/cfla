{{-- resources/views/vendor/livewire/bootstrap-4.blade.php --}}
@if ($paginator->hasPages())
    @php(isset($this->numberOfPaginatorsRendered[$paginator->getPageName()]) ? $this->numberOfPaginatorsRendered[$paginator->getPageName()]++ : $this->numberOfPaginatorsRendered[$paginator->getPageName()] = 1)
    
    <div>
        <nav class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 py-2">
            {{-- Resumen de resultados --}}
            <div class="text-center text-sm-left w-100 w-sm-auto order-2 order-sm-1">
                <p class="text-muted mb-0 small">
                    <span class="font-weight-bold text-dark">{{ $paginator->firstItem() ?? 0 }}</span>
                    {!! __('al') !!}
                    <span class="font-weight-bold text-dark">{{ $paginator->lastItem() ?? 0 }}</span>
                    {!! __('de') !!}
                    <span class="font-weight-bold text-dark">{{ $paginator->total() }}</span>
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
                    <span class="page-link text-dark font-weight-bold">
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
            <div class="form-row align-items-center">
                <div class="col-auto">
                    <label for="pageSelect{{ $paginator->getPageName() }}" class="col-form-label col-form-label-sm text-muted mb-0">
                        Ir a página:
                    </label>
                </div>
                <div class="col">
                    <select class="form-control form-control-sm" 
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
    </div>
@else
    {{-- Sin resultados --}}
    <div class="text-center py-3">
        <p class="text-muted mb-0 small">
            <span class="font-weight-bold text-dark">{{ $paginator->total() }}</span>
            {!! __('registros encontrados') !!}
        </p>
    </div>
@endif

@section('stylesheets')
@parent
<style>
    /* Estilos personalizados para la paginación Bootstrap 4.3 */
    .pagination .page-link {
        border-radius: 0.25rem;
        border: 1px solid #dee2e6;
        color: #6c757d;
        transition: all 0.15s ease-in-out;
        min-width: 2.25rem;
        text-align: center;
        margin: 0 1px;
    }

    .pagination .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #495057;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 123, 255, 0.3);
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
        opacity: 0.6;
    }

    .pagination .page-link:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        outline: 0;
    }

    /* Mejoras responsive */
    @media (max-width: 576px) {
        .pagination .page-item:not(.active) {
            margin: 1px;
        }
        
        .pagination .page-link {
            min-width: 2rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .pagination {
            margin-bottom: 0.5rem !important;
        }
    }

    /* Estilos para el selector de página en móviles */
    @media (max-width: 576px) {
        .form-row .col-form-label-sm {
            font-size: 0.875rem;
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
        }
        
        .form-control-sm {
            height: calc(1.8125rem + 2px);
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
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
            // Scroll suave al top de la tabla o contenedor
            const table = document.querySelector('.table-responsive') || document.querySelector('.card-body');
            if (table) {
                table.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                // Fallback: scroll to top de la página
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    });
});
</script>
@endsection