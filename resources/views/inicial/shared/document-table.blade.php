{{--
    Tabla de SOLO LECTURA de un documento de Educación Inicial.

    Usada por las perspectivas de Planificación y Académico (render en servidor,
    sin Livewire: no hay estado que sincronizar). La de Evaluación es la vista
    `livewire/evaluacion/inicial/document-table.blade.php`, que además permite
    escribir el campo de revisión.

    ─────────────────────────────────────────────────────────────────────────────
    COLUMNAS COMPARTIDAS CON LA TABLA DE EVALUACIÓN
    ─────────────────────────────────────────────────────────────────────────────
    Las dos leen {@see \App\Services\Inicial\RegistroInicial::columnas()}, así que
    esta tabla y la de Evaluación enseñan los mismos datos en el mismo orden por
    construcción, no por copia. El legacy tenía 6 tablas Blade por perspectiva
    (12 en total) casi idénticas, con el bug ya documentado de los botones
    quincenales que apuntaban al formato SEMANAL.
--}}
@php
    $columnas = \App\Services\Inicial\RegistroInicial::columnas($entidad);
    $campoRevision = \App\Services\Inicial\RegistroInicial::campoRevision($entidad);
    $rotuloRevision = \App\Services\Inicial\RegistroInicial::rotuloRevision($entidad);
    $titulo = \App\Services\Inicial\RegistroInicial::titulo($entidad);
@endphp

@if ($documentos->isEmpty())
    <div class="rounded-xl border border-dashed border-white/10 py-14 text-center">
        <p class="text-sm text-gray-400">
            No hay registros de <span class="text-gray-200">{{ strtolower($titulo) }}</span> para estos filtros.
        </p>
    </div>
@else
    <div class="overflow-x-auto rounded-xl border border-white/10">
        <table class="min-w-full text-sm">
            <thead class="bg-white/5 text-xs uppercase tracking-wider text-gray-400">
                <tr>
                    @foreach ($columnas as [$etiqueta, $valor])
                        <th scope="col" class="px-3 py-2.5 text-left font-medium whitespace-nowrap">{{ $etiqueta }}</th>
                    @endforeach

                    @if ($mostrarRevision && $campoRevision)
                        <th scope="col" class="px-3 py-2.5 text-left font-medium whitespace-nowrap">
                            {{ $rotuloRevision }}
                        </th>
                    @endif

                    <th scope="col" class="px-3 py-2.5 text-right font-medium">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-white/5">
                @foreach ($documentos as $documento)
                    <tr class="align-top hover:bg-white/[0.02]">
                        @foreach ($columnas as [$etiqueta, $valor])
                            @php $celda = $valor($documento); @endphp
                            <td class="px-3 py-2.5 text-gray-300">
                                {{ $celda instanceof \Illuminate\Support\Stringable ? $celda : ($celda ?? '—') }}
                            </td>
                        @endforeach

                        @if ($mostrarRevision && $campoRevision)
                            <td class="px-3 py-2.5">
                                @if (filled($documento->{$campoRevision}))
                                    <span class="block max-w-[18rem] truncate text-gray-400"
                                          title="{{ $documento->{$campoRevision} }}">
                                        {{ $documento->{$campoRevision} }}
                                    </span>
                                @else
                                    <span class="text-xs italic text-gray-600">Sin {{ mb_strtolower($rotuloRevision) }}</span>
                                @endif
                            </td>
                        @endif

                        <td class="px-3 py-2.5 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route($rutaPrefijo.'.'.$entidad.'.show', $documento->id) }}"
                                    target="_blank" title="Ver detalle"
                                    class="text-[11px] px-2 py-1 rounded-md bg-white/5 hover:bg-white/10 border border-white/5 text-gray-400 hover:text-gray-200 transition-colors">
                                    Ver
                                </a>

                                {{-- El PDF se enlaza con SU PROPIA entidad: el bug del
                                     legacy era que el botón quincenal apuntaba al
                                     formato semanal. --}}
                                <a href="{{ route($rutaPrefijo.'.'.$entidad.'.format', $documento->id) }}"
                                    target="_blank" title="Formato imprimible"
                                    class="p-1.5 rounded-md bg-white/5 hover:bg-white/10 border border-white/5 text-gray-400 hover:text-gray-200 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif