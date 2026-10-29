{{--
    Pestañas por documento.

    Viven dentro del componente para que los enlaces se reconstruyan con
    `queryFiltros()` cada vez que cambia un filtro. Cada pestaña es una
    navegación GET completa: URL compartible y sin cargar seis listados.
--}}
<div class="border-b border-white/10 overflow-x-auto">
    <nav class="flex min-w-max gap-1" role="tablist" aria-label="Documentos de Educación Inicial">
        @foreach ($pestanas as $clave)
            <a href="{{ route('inicials.seguimiento.'.$clave.'.index', $this->queryFiltros()) }}"
                role="tab"
                aria-selected="{{ $entidad === $clave ? 'true' : 'false' }}"
                wire:navigate
                @class([
                    'inline-flex items-center gap-1.5 whitespace-nowrap rounded-t-lg border-b-2 px-3 py-2 text-xs font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500/60',
                    'border-cyan-400 text-cyan-300 bg-cyan-500/5' => $entidad === $clave,
                    'border-transparent text-gray-400 hover:text-gray-200 hover:bg-white/5' => $entidad !== $clave,
                ])>
                {{ \App\Services\Inicial\RegistroInicial::titulo($clave) }}
            </a>
        @endforeach
    </nav>
</div>
