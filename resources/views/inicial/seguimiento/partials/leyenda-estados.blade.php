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
                a la vez. Los filtros de avance y revisión son independientes y se pueden combinar.
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
                        El documento aún no tiene actividad pedagógica registrada.
                        En los planes con rejilla no hay estrategias (ni actividades en el plan especial);
                        en evaluación no hay posiciones. La planificación semanal también queda sin iniciar
                        si no tiene estrategias ni resúmenes.
                        <span class="mt-1 block text-[11px] text-gray-500">
                            <span class="font-medium text-gray-400">Ejemplo:</span>
                            plan recién creado con sus campos de cabecera, pero sin estrategias, actividades ni posiciones.
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
                        Hay contenido, pero falta completar algún requisito. Semanal exige las 50 estrategias y
                        todos los resúmenes esperados; quincenal, proyecto y especial exigen 50 estrategias.
                        En evaluación, cada posición registrada debe tener aprendizaje alcanzado.
                        <span class="mt-1 block text-[11px] text-gray-500">
                            <span class="font-medium text-gray-400">Ejemplo:</span>
                            40 de 50 estrategias; o una evaluación con posiciones aún sin aprendizaje registrado.
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
                        El requisito depende del documento: semanal tiene 50 estrategias y todos los resúmenes
                        esperados; quincenal, proyecto y especial tienen 50 estrategias. En evaluación, debe
                        existir al menos una posición y todas deben tener aprendizaje alcanzado.
                        <span class="mt-1 block text-[11px] text-gray-500">
                            <span class="font-medium text-gray-400">Ejemplo:</span>
                            plan semanal con 50/50 estrategias y sus resúmenes completos; evaluación con resultados registrados en todas las posiciones.
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
                        Seguimiento <span class="text-gray-300">todavía no escribió</span> la revisión independiente
                        del campo `revision_seguimiento`. No dice nada sobre la calidad ni
                        la completitud del documento.
                        <span class="mt-1 block text-[11px] text-gray-500">
                            <span class="font-medium text-gray-400">Ejemplo:</span>
                            plan completo, pero con `revision_seguimiento` vacío.
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
                        `revision_seguimiento` tiene texto. Es independiente del contenido.
                        <span class="mt-1 block text-[11px] text-gray-500">
                            <span class="font-medium text-gray-400">Ejemplo:</span>
                            «Ajustar la planificación del martes y ampliar el diagnóstico».
                        </span>
                    </dd>
                </div>

            </dl>

            <p class="mt-2 border-t border-white/5 pt-3 text-[11px] leading-relaxed text-gray-500">
                Cada documento queda en un solo estado de avance. Los filtros de avance y revisión se pueden
                combinar.
            </p>
        </div>
    </div>
</div>
