{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║  HELP CONTENT — Wizard de Lecciones LMS (Profesor)                         ║
║  Uso: @include('profesors.help-lesson-wizard')                            ║
║  NOTA: Este partial solo provee el contenido. El shell visual              ║
║  (botón + backdrop + slideover) lo provee <x-help-panel>.                  ║
╚══════════════════════════════════════════════════════════════════════════════╝
--}}

<x-help-panel
    name="helpLessonWizard"
    title="Wizard de Lecciones"
    subtitle="Creación de contenido educativo con IA"
    color="sky"
    buttonClass="bottom-24 right-6">

    {{-- Intro text --}}
    <div class="bg-sky-50/50 dark:bg-slate-700/20 border border-sky-200 dark:border-slate-600/30 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">
            El <strong class="text-gray-900 dark:text-white">Wizard de Lecciones</strong> te guía paso a paso
            en la creación de contenido educativo digital. Puedes redactar manualmente o usar las
            <strong class="text-sky-600 dark:text-sky-400">herramientas de IA</strong> para generar texto,
            imágenes, ilustraciones y diagramas automáticamente.
        </p>
    </div>

    {{-- Tabs navigation --}}
    <div x-data="{ tab: 'pasos' }">
        <div class="flex gap-1 bg-gray-100 dark:bg-slate-900/50 rounded-lg p-1 mb-6 overflow-x-auto" role="tablist">
            <button @click="tab = 'pasos'"
                    :class="tab === 'pasos' ? 'bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 border-sky-200 dark:border-sky-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Pasos
                </span>
            </button>
            <button @click="tab = 'ia'"
                    :class="tab === 'ia' ? 'bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-300 border-violet-200 dark:border-violet-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    IA
                </span>
            </button>
            <button @click="tab = 'estados'"
                    :class="tab === 'estados' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Estados
                </span>
            </button>
        </div>

        {{-- ── TAB: Pasos del Wizard ── --}}
        <div x-show="tab === 'pasos'" x-cloak>
            <div class="space-y-4">
                <div class="rounded-xl border border-sky-200 dark:border-sky-500/20 bg-sky-50/50 dark:bg-sky-500/5 p-4">
                    <h4 class="text-xs font-bold text-sky-700 dark:text-sky-400 uppercase tracking-wider mb-2">Los 4 pasos del wizard</h4>
                    <div class="space-y-3 text-sm text-gray-600 dark:text-slate-400">
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400 text-[10px] font-bold shrink-0">1</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Título y Descripción</strong>
                                <p class="leading-relaxed">Define el tema, nivel educativo y objetivos de aprendizaje. Puedes usar IA para generar todo con un clic.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-400 text-[10px] font-bold shrink-0">2</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Diapositivas (Secciones)</strong>
                                <p class="leading-relaxed">Crea, reordena y edita las diapositivas de la lección. Genera texto, imágenes, ilustraciones o diagramas con IA para cada sección, o sube un PDF para estructurar toda la lección automáticamente.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-amber-500/20 flex items-center justify-center text-amber-400 text-[10px] font-bold shrink-0">3</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Recursos y Enlaces</strong>
                                <p class="leading-relaxed">Adjunta archivos PDF, imágenes, videos y enlaces externos como material complementario.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-[10px] font-bold shrink-0">4</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Publicación</strong>
                                <p class="leading-relaxed">Revisa el contenido completo y decide: programar para una fecha futura o enviar a aprobación (si eres profesor) o publicar directamente (si eres planificador).</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Navegación</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        Usa los botones <strong class="text-gray-900 dark:text-white">Anterior</strong> y
                        <strong class="text-gray-900 dark:text-white">Siguiente</strong> para moverte entre pasos.
                        También puedes hacer clic en los indicadores de paso en la parte superior. Los cambios
                        se guardan automáticamente al avanzar.
                    </p>
                </div>
            </div>
        </div>

        {{-- ── TAB: IA ── --}}
        <div x-show="tab === 'ia'" x-cloak>
            <div class="space-y-4">
                <div class="rounded-xl border border-violet-200 dark:border-violet-500/20 bg-violet-50/50 dark:bg-violet-500/5 p-4">
                    <h4 class="text-xs font-bold text-violet-700 dark:text-violet-400 uppercase tracking-wider mb-2">Herramientas de IA</h4>
                    <ul class="text-sm text-gray-600 dark:text-slate-400 space-y-2">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-500 mt-0.5 shrink-0">✏️</span>
                            <span><strong class="text-gray-900 dark:text-white">Generar texto</strong> — redacta el contenido de una sección a partir de su título.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-violet-500 mt-0.5 shrink-0">🖼️</span>
                            <span><strong class="text-gray-900 dark:text-white">Generar imagen</strong> — crea una imagen original representativa del contenido.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-rose-500 mt-0.5 shrink-0">🎨</span>
                            <span><strong class="text-gray-900 dark:text-white">Generar ilustración</strong> — versión artística decorativa, ideal para portadas.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-cyan-500 mt-0.5 shrink-0">📊</span>
                            <span><strong class="text-gray-900 dark:text-white">Generar diagrama</strong> — diagramas Mermaid.js: flujos, mapas conceptuales, líneas de tiempo.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-500 mt-0.5 shrink-0">⚡</span>
                            <span><strong class="text-gray-900 dark:text-white">Generar lección completa</strong> — disponible en el Paso 1. Crea toda la lección desde un tema.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5 shrink-0">📄</span>
                            <span><strong class="text-gray-900 dark:text-white">Estructurar desde PDF</strong> — sube un PDF en el Paso 2 y la IA lo desglosa en secciones (INICIO, desarrollo y CIERRE) conservando su contenido.</span>
                        </li>
                    </ul>
                </div>
                <div class="rounded-xl border border-amber-200 dark:border-amber-500/20 bg-amber-50/50 dark:bg-amber-500/5 p-4">
                    <h4 class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider mb-2">Estructurar lección desde PDF</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        En el Paso 2 puedes subir un PDF y la IA lo convierte en diapositivas
                        (INICIO, desarrollo y CIERRE) conservando fielmente el contenido del documento.
                    </p>
                    <ul class="text-xs text-gray-500 dark:text-slate-500 space-y-1 mt-2">
                        <li>• Tamaño máximo: <strong class="text-gray-700 dark:text-slate-300">10 MB</strong> y <strong class="text-gray-700 dark:text-slate-300">15 páginas</strong>.</li>
                        <li>• Solo se toma en cuenta el <strong class="text-gray-700 dark:text-slate-300">texto</strong>; las imágenes y gráficos no se consideran.</li>
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Overlay de carga</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        Mientras la IA procesa, aparece un overlay con un spinner y el tiempo transcurrido.
                        Puedes cancelar la operación si el tiempo es excesivo. Los resultados se insertan
                        automáticamente al finalizar.
                    </p>
                </div>
            </div>
        </div>

        {{-- ── TAB: Estados ── --}}
        <div x-show="tab === 'estados'" x-cloak>
            <div class="space-y-4">
                <div class="rounded-xl border border-emerald-200 dark:border-emerald-500/20 bg-emerald-50/50 dark:bg-emerald-500/5 p-4">
                    <h4 class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider mb-2">Ciclo de vida de la lección</h4>
                    <div class="flex items-center justify-between text-sm py-2">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400">📝 Borrador</span>
                        <span class="text-gray-500">→</span>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-purple-100 dark:bg-purple-500/15 text-purple-700 dark:text-purple-400">📅 Programada</span>
                        <span class="text-gray-500">→</span>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400">🚀 Publicada</span>
                    </div>
                    <ul class="text-sm text-gray-600 dark:text-slate-400 space-y-2 mt-2">
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5 shrink-0">📝</span>
                            <span><strong class="text-gray-900 dark:text-white">Borrador (DRAFT)</strong> — estado inicial. Solo tú puedes verla y editarla.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-purple-500 mt-0.5 shrink-0">📅</span>
                            <span><strong class="text-gray-900 dark:text-white">Programada (SCHEDULED)</strong> — tiene fecha de publicación futura. Aún puedes editarla.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-500 mt-0.5 shrink-0">🚀</span>
                            <span><strong class="text-gray-900 dark:text-white">Publicada (PUBLISHED)</strong> — visible para los estudiantes. <strong class="text-red-500">No se puede editar</strong>.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</x-help-panel>
