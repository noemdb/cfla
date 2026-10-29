{{--
    Estado vacío: explica qué falta y ofrece limpiar los filtros sin recargar.
--}}
<div class="rounded-xl border border-dashed border-white/10 py-16 text-center">
    <svg class="mx-auto h-10 w-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    </svg>

    <p class="mt-3 text-sm text-gray-400">
        No hay registros de {{ \App\Services\Inicial\RegistroInicial::titulo($entidad) }} para estos filtros.
    </p>

    <button type="button" wire:click="limpiarFiltros"
        class="mt-3 text-sm text-cyan-400 hover:text-cyan-300 transition-colors">
        Limpiar filtros
    </button>
</div>
