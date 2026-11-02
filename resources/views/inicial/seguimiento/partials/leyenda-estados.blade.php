{{--
    Leyenda de estados del seguimiento (solo lectura).

    Se abre desde el botón «Leyenda de estados» de la cabecera. El estado
    `leyenda` vive en el `x-data` que envuelve al botón y a este parcial.

    Blueprint: blueprint/inicial · doc 11 · §7.2 y §9.4.
--}}
<div x-show="leyenda" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    x-on:keydown.escape.window="leyenda = false"
    role="dialog" aria-modal="true" aria-labelledby="leyenda-titulo">

    {{-- Fondo --}}
    <div class="absolute inset-0 bg-black/70" x-on:click="leyenda = false" aria-hidden="true"></div>

    {{-- Panel --}}
    <div class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-xl border border-white/10 bg-gray-900 shadow-2xl">
        <header class="sticky top-0 z-10 flex items-start justify-between gap-3 border-b border-white/10 bg-gray-900/95 px-5 py-4 backdrop-blur">
            <div>
                <h2 id="leyenda-titulo" class="text-sm font-semibold text-gray-100">
                    Leyenda de estados
                </h2>
                <p class="mt-0.5 text-xs text-gray-500">
                    Seguimiento · Educación Inicial
                </p>
            </div>

            <button type="button" x-on:click="leyenda = false"
                class="shrink-0 rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-white/10 hover:text-white"
                aria-label="Cerrar leyenda">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </header>

        <div class="px-5 py-4">
            <p class="rounded-lg border border-cyan-500/20 bg-cyan-500/5 px-3 py-2 text-xs leading-relaxed text-gray-400">
                Hay <span class="font-medium text-gray-200">dos ejes independientes</span>:
                el <span class="font-medium text-gray-200">contenido</span> del documento
                (sin iniciar, en progreso o completo) y la
                <span class="font-medium text-gray-200">revisión</span> de Coordinación
                (sin revisión o revisado). Un mismo plan puede estar «Completo» y «Sin revisión»
                a la vez. El filtro admite una sola opción, así que no se combinan.
            </p>

            <dl class="mt-4 divide-y divide-white/5">
                {{-- Contenido --}}
                <div class="flex flex-wrap items-start gap-3 py-3">
                    <dt class="w-28 shrink-0">
                        <span class="inline-flex items-center rounded-md border border-white/10 bg-white/5 px-2 py-0.5 text-[10px] font-medium text-gray-400">
                            Sin iniciar
                        </span>
                    </dt>
                    <dd class="min-w-0 flex-1 text-xs leading-relaxed text-gray-400">
                        El documento no tiene <span class="text-gray-300">ningún contenido</span> registrado.
                        En la planificación semanal son 0 de 50 estrategias; en los demás documentos,
                        ninguna actividad, estrategia o posición según corresponda.
                        <span class="mt-1 block text-[11px] text-gray-500">
                            <span class="font-medium text-gray-400">Ejemplo:</span>
                            plan creado con fechas y diagnóstico, pero con la rejilla de 50 celdas vacía.
                        </span>
                    </dd>
                </div>

                <div class="flex flex-wrap items-start gap-3 py-3">
                    <dt class="w-28 shrink-0">
                        <span class="inline-flex items-center rounded-md border border-amber-500/20 bg-amber-500/10 px-2 py-0.5 text-[10px] font-medium text-amber-300">
                            En progreso
                        </span>
                    </dt>
                    <dd class="min-w-0 flex-1 text-xs leading-relaxed text-gray-400">
                        Hay contenido, pero la rejilla semanal <span class="text-gray-300">no está completa</span>:
                        de 1 a 49 celdas escritas. Es el tramo intermedio entre «Sin iniciar» y «Completo».
                        <span class="mt-1 block text-[11px] text-gray-500">
                            <span class="font-medium text-gray-400">Ejemplo:</span>
                            40 de 50 estrategias escritas; faltan 10 celdas por completar.
                        </span>
                    </dd>
                </div>

                <div class="flex flex-wrap items-start gap-3 py-3">
                    <dt class="w-28 shrink-0">
                        <span class="inline-flex items-center rounded-md border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 text-[10px] font-medium text-emerald-300">
                            Completo
                        </span>
                    </dt>
                    <dd class="min-w-0 flex-1 text-xs leading-relaxed text-gray-400">
                        La rejilla semanal tiene las <span class="text-gray-300">50 celdas</span> con texto
                        (5 días × 10 momentos). Solo existe en la planificación semanal: los demás documentos
                        no tienen una rejilla acotada.
                        <span class="mt-1 block text-[11px] text-gray-500">
                            <span class="font-medium text-gray-400">Ejemplo:</span>
                            plan con 50/50 estrategias, una por cada día y momento de la rutina.
                        </span>
                    </dd>
                </div>

                {{-- Revisión --}}
                <div class="flex flex-wrap items-start gap-3 py-3">
                    <dt class="w-28 shrink-0">
                        <span class="inline-flex items-center rounded-md border border-amber-500/20 bg-amber-500/10 px-2 py-0.5 text-[10px] font-medium text-amber-300">
                            Sin revisión
                        </span>
                    </dt>
                    <dd class="min-w-0 flex-1 text-xs leading-relaxed text-gray-400">
                        Coordinación <span class="text-gray-300">todavía no escribió</span> la observación
                        (o la recomendación, en el plan de evaluación). No dice nada sobre la calidad ni
                        la completitud del documento.
                        <span class="mt-1 block text-[11px] text-gray-500">
                            <span class="font-medium text-gray-400">Ejemplo:</span>
                            plan completo, pero con el campo de observación vacío.
                        </span>
                    </dd>
                </div>

                <div class="flex flex-wrap items-start gap-3 py-3">
                    <dt class="w-28 shrink-0">
                        <span class="inline-flex items-center rounded-md border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 text-[10px] font-medium text-emerald-300">
                            Revisado
                        </span>
                    </dt>
                    <dd class="min-w-0 flex-1 text-xs leading-relaxed text-gray-400">
                        Coordinación <span class="text-gray-300">ya registró</span> la revisión: el campo
                        de observación (o recomendación) tiene texto. Es independiente del contenido.
                        <span class="mt-1 block text-[11px] text-gray-500">
                            <span class="font-medium text-gray-400">Ejemplo:</span>
                            «Ajustar la planificación del martes y ampliar el diagnóstico».
                        </span>
                    </dd>
                </div>

                <div class="flex flex-wrap items-start gap-3 py-3">
                    <dt class="w-28 shrink-0">
                        <span class="inline-flex items-center rounded-md border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 text-[10px] font-medium text-emerald-300">
                            Emitido
                        </span>
                    </dt>
                    <dd class="min-w-0 flex-1 text-xs leading-relaxed text-gray-400">
                        El informe final se <span class="text-gray-300">emitió firmado</span> y se archiva
                        tal cual. Es el único documento sin campo de revisión, por eso no aparece en el
                        filtro de estados.
                        <span class="mt-1 block text-[11px] text-gray-500">
                            <span class="font-medium text-gray-400">Ejemplo:</span>
                            informe final de un estudiante, listo para imprimir.
                        </span>
                    </dd>
                </div>
            </dl>

            <p class="mt-2 border-t border-white/5 pt-3 text-[11px] leading-relaxed text-gray-500">
                El filtro «En progreso» excluye las semanas completas: los tres tramos de contenido
                (0, 1–49 y 50) son excluyentes y suman el total. El informe final no tiene estado
                de revisión ni de contenido.
            </p>
        </div>
    </div>
</div>
