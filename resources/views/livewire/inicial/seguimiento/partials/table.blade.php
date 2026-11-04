{{--
    Listado en tabla para consulta y revisión de seguimiento.

    ─────────────────────────────────────────────────────────────────────────────
    POR QUÉ LA FILA ESTÁ AGRUPADA
    ─────────────────────────────────────────────────────────────────────────────
    Una columna por dato (N°, dos fechas, nivel, docente, área, contadores,
    diagnóstico, estado, revisión y acciones) obligaba a `nowrap` y a
    `overflow-x-auto`: la tabla desbordaba incluso en escritorio.

    Aquí la información se agrupa en dos celdas —documento y contenido— y la
    tabla queda en cinco columnas sin scroll horizontal:

      · md+  → tabla fija de 5 columnas.
      · <md  → cada fila se apila como tarjeta; tampoco hay scroll lateral.

    Los datos de la fila salen de `RegistroInicial::filaTabla()`, que lee los
    contadores `*_count` de `withCount()` (sin N+1). Estado y Revisión se
    calculan con el servicio, igual que en las tarjetas.
--}}
@php
    $servicio = app(\App\Services\Inicial\SeguimientoInicial::class);
    $campoRevision = \App\Services\Inicial\RegistroInicial::campoRevision($entidad)
        ? 'revision_seguimiento'
        : null;

    // La etiqueta del contenido depende del documento (Diagnóstico,
    // Justificación, Observaciones, Título). Se toma una vez de la primera
    // fila; la tabla solo se renderiza cuando hay resultados.
    $primera = $documentos->items()[0] ?? null;
    $etiquetaContenido = $primera
        ? \App\Services\Inicial\RegistroInicial::filaTabla($entidad, $primera)['contenidoEtiqueta']
        : 'Contenido';
@endphp

<div class="rounded-xl border border-white/10 bg-gray-900/40">
    <table class="w-full table-fixed text-sm">
        <caption class="sr-only">
            Seguimiento de {{ \App\Services\Inicial\RegistroInicial::titulo($entidad) }}.
            Documentos de consulta con revisión de seguimiento independiente.
        </caption>

        {{-- Con revisión: 34 + 25 + 11 + 11 + 19 = 100 --}}
        @if ($campoRevision)
            <colgroup>
                <col class="w-[34%]">
                <col class="w-[25%]">
                <col class="w-[11%]">
                <col class="w-[11%]">
                <col class="w-[19%]">
            </colgroup>
        {{-- Sin revisión: 38 + 27 + 12 + 23 = 100 --}}
        @else
            <colgroup>
                <col class="w-[38%]">
                <col class="w-[27%]">
                <col class="w-[12%]">
                <col class="w-[23%]">
            </colgroup>
        @endif

        <thead class="hidden bg-white/5 md:table-header-group">
            <tr>
                <th scope="col" class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400">
                    Documento
                </th>
                <th scope="col" class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400">
                    {{ $etiquetaContenido }}
                </th>
                <th scope="col" class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400">
                    Estado
                </th>

                @if ($campoRevision)
                    <th scope="col" class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400">
                        Revisión
                    </th>
                @endif

                <th scope="col" class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-400">
                    Acciones
                </th>
            </tr>
        </thead>

        <tbody class="block divide-y divide-white/5 md:table-row-group">
            @foreach ($documentos as $documento)
                @php
                    $fila = \App\Services\Inicial\RegistroInicial::filaTabla($entidad, $documento);
                    $estado = $servicio->estado($entidad, $documento);
                    $revisado = $campoRevision && filled($documento->{$campoRevision});
                    $requiereAtencion = $campoRevision
                        && ($estado['clave'] === 'sin_iniciar' || ! $revisado);

                    $variante = [
                        'emerald' => 'border-emerald-500/20 bg-emerald-500/10 text-emerald-300',
                        'amber' => 'border-amber-500/20 bg-amber-500/10 text-amber-300',
                        'cyan' => 'border-cyan-500/20 bg-cyan-500/10 text-cyan-300',
                        'gray' => 'border-white/10 bg-white/5 text-gray-400',
                    ][$estado['variante']] ?? 'border-white/10 bg-white/5 text-gray-400';

                    $contadores = collect($fila['contadores'])
                        ->filter(fn ($valor) => $valor !== null)
                        ->map(fn ($valor, $etiqueta) => (int) $valor.' '.mb_strtolower($etiqueta))
                        ->implode(' · ');
                @endphp

                <tr wire:key="doc-{{ $entidad }}-{{ $documento->id }}"
                    @class([
                        'block p-4 transition-colors hover:bg-white/[0.03] md:table-row md:p-0',
                        'bg-amber-500/[0.04]' => $requiereAtencion,
                    ])>

                    {{-- Documento: nivel · sección · área / docente / N° · período --}}
                    <td class="block md:table-cell md:px-3 md:py-2.5 md:align-top">
                        <p class="text-sm font-medium text-gray-100 break-words">
                            {{ $fila['principal'] }}
                        </p>

                        @if ($fila['secundario'])
                            <p class="mt-0.5 truncate text-xs text-gray-500">
                                {{ $fila['secundario'] }}
                            </p>
                        @endif

                        @if ($fila['meta'])
                            <p class="mt-0.5 truncate text-[11px] text-gray-600">
                                {{ $fila['meta'] }}
                            </p>
                        @endif
                        <p class="mt-0.5 text-[11px] text-gray-600">
                            Actualizado: {{ $documento->updated_at?->format('d/m/Y H:i') ?? '—' }}
                        </p>
                    </td>

                    {{-- Contenido: diagnóstico, justificación, observaciones o título --}}
                    <td class="mt-3 block md:mt-0 md:table-cell md:px-3 md:py-2.5 md:align-top">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 md:hidden">
                            {{ $fila['contenidoEtiqueta'] }}
                        </p>
                        <p class="text-xs leading-relaxed text-gray-400 line-clamp-3">
                            {{ $fila['contenido'] ?: 'Sin contenido registrado.' }}
                        </p>

                        @if ($contadores !== '')
                            <p class="mt-1 text-[11px] tabular-nums text-gray-600">
                                {{ $contadores }}
                            </p>
                        @endif
                    </td>

                    {{-- Estado --}}
                    <td class="mt-3 flex flex-wrap items-center gap-2 md:mt-0 md:table-cell md:px-3 md:py-2.5 md:align-top">
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 md:hidden">
                            Estado
                        </span>
                        <span class="inline-flex items-center rounded-md border px-1.5 py-0.5 text-[10px] font-medium {{ $variante }}"
                            title="{{ $estado['detalle'] }}">
                            {{ $estado['etiqueta'] }}
                        </span>
                        @if ($requiereAtencion)
                            <span class="inline-flex items-center rounded-md border border-amber-500/30 bg-amber-500/10 px-1.5 py-0.5 text-[10px] font-medium text-amber-200">
                                Prioridad
                            </span>
                        @endif
                        @if ($entidad === 'eiplanningwks' && $estado['clave'] !== 'completo')
                            <span class="basis-full break-words text-[11px] leading-relaxed text-amber-200/90">
                                {{ $estado['detalle'] }}
                            </span>
                        @endif
                    </td>

                    {{-- Revisión --}}
                    @if ($campoRevision)
                        <td class="mt-2 flex items-center gap-2 md:mt-0 md:table-cell md:px-3 md:py-2.5 md:align-top">
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 md:hidden">
                                Revisión
                            </span>
                            <span @class([
                                    'inline-flex items-center rounded-md border px-1.5 py-0.5 text-[10px] font-medium',
                                    'border-emerald-500/20 bg-emerald-500/10 text-emerald-300' => $revisado,
                                    'border-amber-500/20 bg-amber-500/10 text-amber-300' => ! $revisado,
                                ])>
                                {{ $revisado ? 'Revisado' : 'Sin revisión' }}
                            </span>
                        </td>
                    @endif

                    {{-- Acciones --}}
                    <td class="mt-3 block md:mt-0 md:table-cell md:px-3 md:py-2.5 md:align-top">
                        <div class="flex flex-wrap items-center gap-1 md:justify-end">
                            <button type="button" wire:click="abrirDetalle({{ $documento->id }})"
                                class="whitespace-nowrap rounded-md px-2 py-1 text-xs font-medium text-gray-300 transition-colors hover:bg-white/10 hover:text-white">
                                Ver
                            </button>
                            <a href="{{ route('inicials.seguimiento.'.$entidad.'.show', $documento->id) }}" wire:navigate
                                class="whitespace-nowrap rounded-md px-2 py-1 text-xs font-medium text-cyan-300 transition-colors hover:bg-cyan-500/10 hover:text-cyan-200">
                                Ficha
                            </a>
                            <a href="{{ route('inicials.seguimiento.'.$entidad.'.format', $documento->id) }}" target="_blank" rel="noopener"
                                class="whitespace-nowrap rounded-md px-2 py-1 text-xs font-medium text-gray-400 transition-colors hover:bg-white/5 hover:text-gray-200">
                                Imprimir
                            </a>
                            @if ($campoRevision)
                                <button type="button" wire:click="revisarDetalle({{ $documento->id }})"
                                    title="{{ $revisado ? 'Editar revisión' : 'Registrar revisión' }}"
                                    class="whitespace-nowrap rounded-md px-2 py-1 text-xs font-medium text-amber-300 transition-colors hover:bg-amber-500/10 hover:text-amber-200">
                                    Revisión
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
