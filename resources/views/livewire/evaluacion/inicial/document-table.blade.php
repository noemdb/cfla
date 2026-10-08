{{--
    Tabla de revisión de un documento del módulo de Educación Inicial.

    Componente: App\Livewire\Evaluacion\Inicial\EvaluacionDocumentComponent
                (y sus 6 subclases, una por documento)

    ─────────────────────────────────────────────────────────────────────────────
    UNA SOLA VISTA PARA LOS 6 DOCUMENTOS
    ─────────────────────────────────────────────────────────────────────────────
    El legacy tenía 6 tablas Blade casi idénticas (187, 178, 117, 101, 117 y
    61 líneas) que solo cambiaban en los encabezados. Aquí las columnas llegan
    como DATOS desde cada subclase (`columnas()` → `[etiqueta, callable]`) y la
    tabla es una sola. Añadir una columna es tocar una línea del componente, no
    un archivo de Blade.

    ─────────────────────────────────────────────────────────────────────────────
    ESCRITURA LIMITADA
    ─────────────────────────────────────────────────────────────────────────────
    Solo hay botón de gestión, y solo si el documento tiene campo de revisión
    (`$campoRevision`). El informe final llega aquí con `$campoRevision = null` y
    no muestra ninguna acción de escritura: no hay un `if` en el marcado que
    dependa de un flag del usuario, depende de si el DATO admite revisión.
--}}
<div class="space-y-4">

    @if ($items->isEmpty())
        <div class="rounded-xl border border-dashed border-white/10 py-14 text-center">
            <p class="text-sm text-gray-400">
                No hay registros de <span class="text-gray-200">{{ strtolower($titulo) }}</span>
                para estos filtros.
            </p>
            <p class="mt-1 text-xs text-gray-600">
                Quita algún filtro del formulario de arriba paraAmpliar la búsqueda.
            </p>
        </div>
    @else
        <div class="overflow-x-auto rounded-xl border border-white/10">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-xs uppercase tracking-wider text-gray-400">
                    <tr>
                        @foreach ($columnas as [$etiqueta, $valor])
                            <th scope="col" class="px-3 py-2.5 text-left font-medium whitespace-nowrap">
                                {{ $etiqueta }}
                            </th>
                        @endforeach

                        @if ($campoRevision)
                            <th scope="col" class="px-3 py-2.5 text-left font-medium whitespace-nowrap">
                                {{ $rotuloRevision }}
                            </th>
                        @endif

                        <th scope="col" class="px-3 py-2.5 text-right font-medium">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-white/5">
                    @foreach ($items as $item)
                        <tr class="align-top hover:bg-white/[0.02]">
                            @foreach ($columnas as [$etiqueta, $valor])
                                <td class="px-3 py-2.5 text-gray-300">
                                    @php $celda = $valor($item); @endphp
                                    {{ $celda instanceof \Illuminate\Support\Stringable ? $celda : ($celda ?? '—') }}
                                </td>
                            @endforeach

                            @if ($campoRevision)
                                <td class="px-3 py-2.5">
                                    @if (filled($item->{$campoRevision}))
                                        <span class="block max-w-[18rem] truncate text-gray-400"
                                              title="{{ $item->{$campoRevision} }}">
                                            {{ $item->{$campoRevision} }}
                                        </span>
                                    @else
                                        <span class="text-xs italic text-gray-600">Sin {{ mb_strtolower($rotuloRevision) }}</span>
                                    @endif
                                </td>
                            @endif

                            <td class="px-3 py-2.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    {{-- Ver detalle: la misma lectura en página propia,
                                         sin modal, para revisar sin perder el lugar. --}}
                                    <a href="{{ $urls[$item->id]['detalle'] }}" target="_blank" title="Ver detalle"
                                        class="p-1.5 rounded-md bg-white/5 hover:bg-white/10 border border-white/5 text-gray-400 hover:text-gray-200 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    <a href="{{ $urls[$item->id]['pdf'] }}" target="_blank" title="Formato imprimible"
                                            class="p-1.5 rounded-md bg-white/5 hover:bg-white/10 border border-white/5 text-gray-400 hover:text-gray-200 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z" />
                                            </svg>
                                    </a>

                                    @if ($campoRevision)
                                        <button type="button" wire:click="openRevision({{ $item->id }})"
                                            title="Gestionar {{ mb_strtolower($rotuloRevision) }}"
                                            class="p-1.5 rounded-md bg-amber-500/15 hover:bg-amber-500/25 border border-amber-500/20 text-amber-300 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- ═══ MODAL DE REVISIÓN (escritura del campo del componente) ═══ --}}
    @if ($showRevision)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true"
            wire:key="revision-{{ $entidad }}-{{ $selectedId }}">
            <div class="flex min-h-full items-end justify-center p-4 sm:items-start sm:pt-16 bg-gray-950/70 backdrop-blur-sm"
                wire:click.self="closeRevision">

                <div class="w-full max-w-2xl rounded-xl bg-gray-900 border border-white/10 shadow-2xl"
                    x-data="{ open: true }" x-show="open"
                    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0">

                    <div class="flex items-center justify-between px-5 py-4 border-b border-white/10">
                        <h3 class="text-base font-semibold text-gray-100">
                            Gestionar {{ mb_strtolower($rotuloRevision) }}
                            <span class="block text-xs font-normal text-gray-500">{{ $titulo }} · N° {{ $selectedId }}</span>
                        </h3>
                        <button type="button" wire:click="closeRevision" aria-label="Cerrar"
                            class="p-1.5 rounded-lg text-gray-500 hover:text-gray-200 hover:bg-white/5 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-5 py-4">
                        <label for="revision" class="block text-xs font-medium text-gray-400 mb-1.5">
                            {{ $rotuloRevision }} de la Coordinación de Evaluación
                        </label>
                        <textarea id="revision" rows="5" wire:model="revision"
                            placeholder="Escriba aquí la {{ mb_strtolower($rotuloRevision) }}…"
                            class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none"></textarea>
                        @error('revision')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-[11px] text-gray-600">
                            La Coordinación solo escribe este campo; el resto del documento lo mantiene la docente.
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-white/10 bg-gray-900/60 rounded-b-xl">
                        <button type="button" wire:click="closeRevision"
                            class="rounded-lg px-4 py-2 text-sm text-gray-300 hover:bg-white/5 transition-colors">
                            Cancelar
                        </button>
                        <button type="button" wire:click="saveRevision"
                            class="rounded-lg bg-amber-600 hover:bg-amber-500 px-4 py-2 text-sm font-medium text-white transition-colors">
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>