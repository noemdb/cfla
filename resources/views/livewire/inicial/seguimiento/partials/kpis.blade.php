{{--
    KPI de la entidad activa, con los mismos filtros que el listado.

    Cuatro tarjetas (tres en el informe final, que no tiene campo de revisión).
    `role="status"` + `aria-live="polite"` anuncian el cambio al filtrar.
--}}
@php
    $cuartaEtiqueta = $entidad === 'eiplanningwks'
        ? 'Completos'
        : ($entidad === 'eifinalks' ? 'Emitidos' : 'Revisados');

    $cuartoValor = $entidad === 'eifinalks'
        ? $resumen['total']
        : ($resumen['completos'] ?? $resumen['revisados']);

    $ultima = $resumen['ultima_actualizacion'] ?? null;
    $ultimaTexto = $ultima ? \Illuminate\Support\Carbon::parse($ultima)->format('d/m/Y H:i') : '—';
@endphp

<section
    class="grid grid-cols-2 gap-3 {{ $resumen['sin_revision'] === null ? 'sm:grid-cols-3' : 'lg:grid-cols-4' }}"
    role="status" aria-live="polite">

    <article class="rounded-xl border border-white/10 bg-gray-900/60 p-4">
        <p class="text-[11px] font-medium uppercase tracking-wider text-gray-500">Registros</p>
        <p class="mt-1 text-2xl font-semibold text-gray-100 tabular-nums">{{ number_format($resumen['total']) }}</p>
        <p class="mt-0.5 text-[11px] text-gray-600">Actualizado: {{ $ultimaTexto }}</p>
    </article>

    <article class="rounded-xl border border-white/10 bg-gray-900/60 p-4">
        <p class="text-[11px] font-medium uppercase tracking-wider text-gray-500">Docentes</p>
        <p class="mt-1 text-2xl font-semibold text-cyan-300 tabular-nums">{{ number_format($resumen['docentes']) }}</p>
        <p class="mt-0.5 text-[11px] text-gray-600">Con documentos en estos filtros</p>
    </article>

    @if ($resumen['sin_revision'] !== null)
        <article class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-4">
            <p class="text-[11px] font-medium uppercase tracking-wider text-amber-400/80">Sin revisión</p>
            <p class="mt-1 text-2xl font-semibold text-amber-300 tabular-nums">{{ number_format($resumen['sin_revision']) }}</p>
            <p class="mt-0.5 text-[11px] text-amber-500/60">Pendientes de la Coordinación</p>
        </article>
    @endif

    <article class="rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-4">
        <p class="text-[11px] font-medium uppercase tracking-wider text-emerald-400/80">{{ $cuartaEtiqueta }}</p>
        <p class="mt-1 text-2xl font-semibold text-emerald-300 tabular-nums">{{ number_format((int) $cuartoValor) }}</p>
        <p class="mt-0.5 text-[11px] text-emerald-500/60">
            {{ $entidad === 'eiplanningwks' ? 'Con la semana completa' : ($entidad === 'eifinalks' ? 'Informes emitidos' : 'Con revisión registrada') }}
        </p>
    </article>
</section>
