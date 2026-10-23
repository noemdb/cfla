{{-- Diálogo compartido (wizard / light): totalización de bloques por docente (PDF / HTML / XLS). --}}
{{-- $idSuffix: sufijo de los ids de controles ('-light' en el light). --}}
{{-- $withCalendarFilter: añade &calendar=ID al ancla Generar (light). --}}

    {{-- Diálogo: totalización de bloques por docente (PDF / HTML / XLS). --}}
    <div x-data="{ open: false, format: 'pdf' }"
        x-on:open-teacher-totals.window="open = true"
        x-on:keydown.escape.window="open = false"
        x-cloak x-show="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 p-4"
        role="dialog" aria-modal="true" aria-label="Totalización por docente">
        <div x-on:click.stop
            class="w-full max-w-md overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-white/10 dark:bg-gray-900">
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-white/10">
                <div>
                    <h3 class="text-xs font-extrabold uppercase tracking-widest text-gray-700 dark:text-gray-200">
                        Totalización por docente
                    </h3>
                    <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                        Elige el formato de salida · PDF, HTML o XLS
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
                    <label for="teacher-totals-format{{ $idSuffix ?? '' }}"
                        class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                        Formato
                    </label>
                    <select id="teacher-totals-format{{ $idSuffix ?? '' }}" x-model="format"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 dark:border-white/10 dark:bg-gray-800 dark:text-gray-100">
                        <option value="pdf">PDF</option>
                        <option value="html">HTML</option>
                        <option value="xls">XLS</option>
                    </select>
                </div>

                <p x-cloak x-show="format === 'xls'"
                    class="rounded-lg bg-amber-500/10 px-3 py-2 text-[11px] font-medium text-amber-700 dark:text-amber-300">
                    El XLS agrega las columnas «Horas Administrativas» y «Horas Formación» vacías para llenado manual.
                </p>
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-200 px-4 py-3 dark:border-white/10">
                <button type="button" x-on:click="open = false"
                    class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-bold text-gray-600 transition-colors hover:bg-gray-100 dark:border-white/10 dark:text-gray-300 dark:hover:bg-white/5">
                    Cancelar
                </button>
                <a :href="'{{ route($moduleRoutePrefix.'.timetable.pdf.teacher-block-totals') }}?format=' + format @if (! empty($withCalendarFilter))+ '&calendar={{ (int) $calendarId }}' @endif"
                    target="_blank" rel="noopener" x-on:click="open = false"
                    class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white transition-colors hover:bg-emerald-700">
                    Generar
                </a>
            </div>
        </div>
    </div>
