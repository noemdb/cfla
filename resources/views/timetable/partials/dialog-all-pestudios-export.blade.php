{{-- Diálogo compartido (light): exportación de grillas por P.Estudio con --}}
{{-- elección de formato de salida y ajuste de filas del estimador --}}
{{-- (TimetableGridRowHeight). Sirve a dos instancias en el light: --}}
{{--  - «Exportar todos los P.Estudios…» — dropdown sin calendario; base = --}}
{{--    all-pestudios vía moduleRoutePrefix. --}}
{{--  - «PDF P.Estudio…» — dropdown con calendario abierto; base = --}}
{{--    $pestudioExportUrl (prop de render() que ya resuelve calendar/pestudio). --}}
{{-- Parámetros: $title, $subtitle, $openEvent, $exportUrl (opcional), $panelId, --}}
{{-- $withXls (el endpoint pestudio-preview no produce XLS). --}}
{{-- Respeta el patrón estructural de los demás diálogos compartidos: container --}}
{{-- autobalanceado (abre y cierra TODOS sus <div>; ver docs/timetable/README.md). --}}

@props(['title' => 'Exportar todos los P.Estudios', 'subtitle' => 'Horarios de todos los P.Estudios activos del lapso, sección por sección', 'openEvent' => 'open-all-pestudios-export', 'exportUrl' => null, 'panelId' => 'all-pestudios-export', 'withXls' => true])

    <div x-data="{ open: false, format: 'pdf', rowMode: 'conservative' }"
        x-on:{{ $openEvent }}.window="open = true"
        x-on:keydown.escape.window="open = false"
        x-cloak x-show="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 p-4"
        role="dialog" aria-modal="true" aria-label="{{ $title }}">
        <div x-on:click.stop
            class="w-full max-w-md overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-white/10 dark:bg-gray-900">
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-white/10">
                <div>
                    <h3 class="text-xs font-extrabold uppercase tracking-widest text-gray-700 dark:text-gray-200">
                        {{ $title }}
                    </h3>
                    <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                        {{ $subtitle }}
                    </p>
                </div>
                <button type="button" x-on:click="open = false"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-full text-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 dark:hover:bg-white/10 dark:hover:text-white"
                    aria-label="Cerrar">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="space-y-4 p-4">
                <div>
                    <label for="{{ $panelId }}-format"
                        class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                        Formato
                    </label>
                    <select id="{{ $panelId }}-format" x-model="format"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 dark:border-white/10 dark:bg-gray-800 dark:text-gray-100">
                        <option value="pdf">PDF (horizontal)</option>
                        <option value="html">HTML (vista imprimible)</option>
                        @if ($withXls)
                            <option value="xls">XLS (Excel)</option>
                        @endif
                    </select>
                </div>

                <div>
                    <label for="{{ $panelId }}-row-mode"
                        class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                        Ajuste de filas (temario)
                    </label>
                    <select id="{{ $panelId }}-row-mode" x-model="rowMode"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 dark:border-white/10 dark:bg-gray-800 dark:text-gray-100">
                        <option value="conservative">Conservador — con margen anti-partido (recomendado)</option>
                        <option value="strict">Estricto — filas al mínimo (calibración)</option>
                    </select>
                </div>

                <p x-cloak x-show="rowMode === 'strict'"
                    class="rounded-lg bg-amber-500/10 px-3 py-2 text-[11px] font-medium text-amber-700 dark:text-amber-300">
                    El modo Estricto genera cada fila con la altura justa del temario: úsalo para
                    calibrar lado a lado con el Conservador. Si una celda aparece partida, vuelve
                    al Conservador.
                </p>
                <p x-cloak x-show="format === 'html'"
                    class="rounded-lg bg-sky-500/10 px-3 py-2 text-[11px] font-medium text-sky-700 dark:text-sky-300">
                    El HTML abre la misma vista del PDF directamente en el navegador, apta para imprimir.
                </p>
                @if ($withXls)
                    <p x-cloak x-show="format === 'xls'"
                        class="rounded-lg bg-amber-500/10 px-3 py-2 text-[11px] font-medium text-amber-700 dark:text-amber-300">
                        El XLS conserva la estructura y paleta del PDF (teal, azul y ámbar), con una
                        hoja por cada P.Estudio del lapso.
                    </p>
                @endif
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-200 px-4 py-3 dark:border-white/10">
                <button type="button" x-on:click="open = false"
                    class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-bold text-gray-600 transition-colors hover:bg-gray-100 dark:border-white/10 dark:text-gray-300 dark:hover:bg-white/5">
                    Cancelar
                </button>
                <a :href="'{{ $exportUrl ?? route($moduleRoutePrefix.'.timetable.pdf.all-pestudios') }}?format=' + format + '&row_mode=' + rowMode"
                    target="_blank" rel="noopener" x-on:click="open = false"
                    class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white transition-colors hover:bg-emerald-700">
                    Generar
                </a>
            </div>
        </div>
    </div>
