{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║  HELP CONTENT — Monitor de Lecciones LMS (Jefe de Área)                    ║
║  Uso: @include('leadership.help-lessons')                                  ║
║  NOTA: Este partial solo provee el contenido. El shell visual              ║
║  (botón + backdrop + slideover) lo provee <x-help-panel>.                  ║
╚══════════════════════════════════════════════════════════════════════════════╝
--}}

<x-help-panel
    name="helpLessons"
    title="Guía del Monitor de Lecciones"
    subtitle="Publicación y supervisión de contenido LMS"
    color="sky">

    {{-- Intro text --}}
    <div class="bg-sky-50/50 dark:bg-slate-700/20 border border-sky-200 dark:border-slate-600/30 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">
            Como <strong class="text-gray-900 dark:text-white">Jefe de Área</strong>, puedes supervisar las
            <strong class="text-sky-600 dark:text-sky-400">lecciones LMS</strong> que los profesores de tu área han preparado.
            Desde aquí puedes <strong class="text-gray-900 dark:text-white">previsualizar</strong> el contenido antes de su publicación,
            <strong class="text-gray-900 dark:text-white">programar</strong> fechas de publicación y
            <strong class="text-emerald-600 dark:text-emerald-400">publicar</strong> lecciones cuando estén listas.
        </p>
    </div>

    {{-- Tabs navigation --}}
    <div x-data="{ tab: 'vista' }">
        <div class="flex gap-1 bg-gray-100 dark:bg-slate-900/50 rounded-lg p-1 mb-6 overflow-x-auto" role="tablist">
            <button @click="tab = 'vista'"
                    :class="tab === 'vista' ? 'bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 border-sky-200 dark:border-sky-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Vista Gral
                </span>
            </button>
            <button @click="tab = 'filtros'"
                    :class="tab === 'filtros' ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filtros
                </span>
            </button>
            <button @click="tab = 'publicar'"
                    :class="tab === 'publicar' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Publicar
                </span>
            </button>
        </div>

        {{-- ── TAB: Vista General ── --}}
        <div x-show="tab === 'vista'" x-cloak>
            <div class="space-y-4">
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Listado de Lecciones</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        Cada fila representa una <strong class="text-gray-900 dark:text-white">actividad</strong> que tiene contenido LMS
                        (secciones, recursos, enlaces). Las lecciones aparecen scoped a las <strong class="text-gray-900 dark:text-white">áreas de conocimiento</strong>
                        que tienes asignadas como Jefe de Área. El ícono de ojo (<svg class="w-3.5 h-3.5 inline align-text-bottom" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>) abre la vista previa de la lección.
                    </p>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Vista Previa de Lección</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        Al hacer clic en el ojo se abre un modal con el contenido completo de la lección: secciones con su material,
                        recursos descargables y enlaces de interés. Es la misma vista que vería un estudiante, útil para validar
                        el contenido antes de autorizar su publicación.
                    </p>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Estados de Publicación</h4>
                    <div class="flex flex-wrap gap-2 mt-1">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 dark:bg-slate-500/15 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-500/20">Borrador</span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20">Programado</span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">Publicado</span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-red-100 dark:bg-red-500/15 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-500/20">Archivado</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed mt-2">
                        Las lecciones <strong class="text-amber-600 dark:text-amber-400">programadas</strong> son las que están listas para publicarse
                        y tienen habilitado el botón de publicar. Las <strong class="text-emerald-600 dark:text-emerald-400">publicadas</strong>
                        ya están visibles para los estudiantes y no se pueden modificar desde aquí.
                    </p>
                </div>
            </div>
        </div>

        {{-- ── TAB: Filtros ── --}}
        <div x-show="tab === 'filtros'" x-cloak>
            <div class="space-y-4">
                <div class="rounded-xl border border-amber-200 dark:border-amber-500/20 bg-amber-50/50 dark:bg-amber-500/5 p-4">
                    <h4 class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider mb-2">Filtros disponibles</h4>
                    <ul class="text-sm text-gray-600 dark:text-slate-400 space-y-2">
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5 shrink-0">🔍</span>
                            <span><strong class="text-gray-900 dark:text-white">Buscar</strong> — filtra por tema (topic) de la actividad.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5 shrink-0">📚</span>
                            <span><strong class="text-gray-900 dark:text-white">Plan de Estudio / Grado / Sección</strong> — filtros en cascada: al seleccionar un plan de estudio se cargan solo los grados asociados.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5 shrink-0">👨‍🏫</span>
                            <span><strong class="text-gray-900 dark:text-white">Profesor</strong> — filtra por docente específico.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5 shrink-0">📅</span>
                            <span><strong class="text-gray-900 dark:text-white">Lapso</strong> — pestañas en la parte superior y selector en filtros para cambiar entre lapsos activos.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5 shrink-0">⏳</span>
                            <span><strong class="text-gray-900 dark:text-white">Estado</strong> — filtra por lecciones publicadas o programadas usando los checkboxes correspondientes.</span>
                        </li>
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Filtros de Actividades</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        Los filtros adicionales (Plan Estudio, Grado, Sección, Profesor) son los mismos que usa el módulo de Planificación
                        y están sincronizados: la selección de uno actualiza las opciones del siguiente en cadena.
                    </p>
                </div>
            </div>
        </div>

        {{-- ── TAB: Publicar ── --}}
        <div x-show="tab === 'publicar'" x-cloak>
            <div class="space-y-4">
                <div class="rounded-xl border border-emerald-200 dark:border-emerald-500/20 bg-emerald-50/50 dark:bg-emerald-500/5 p-4">
                    <h4 class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider mb-2">¿Cómo publicar una lección?</h4>
                    <ol class="text-sm text-gray-600 dark:text-slate-400 space-y-2 list-decimal list-inside">
                        <li class="leading-relaxed">Identifica una lección con estado <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400">Programado</span>.</li>
                        <li class="leading-relaxed">Usa el ojo (<svg class="w-3.5 h-3.5 inline align-text-bottom" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>) para previsualizar el contenido antes de publicar.</li>
                        <li class="leading-relaxed">Haz clic en <strong class="text-emerald-600 dark:text-emerald-400">Publicar</strong> para abrir el modal de confirmación.</li>
                        <li class="leading-relaxed">Confirma la acción. La lección cambiará a <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400">Publicado</span> y quedará visible para los estudiantes.</li>
                    </ol>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Notas importantes</h4>
                    <ul class="text-sm text-gray-600 dark:text-slate-400 space-y-2">
                        <li class="flex items-start gap-2">
                            <span class="text-gray-400 mt-0.5 shrink-0">•</span>
                            <span>Solo las lecciones con estado <strong class="text-amber-600 dark:text-amber-400">SCHEDULED</strong> (programadas) pueden publicarse.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-gray-400 mt-0.5 shrink-0">•</span>
                            <span>Las lecciones ya publicadas o en borrador no tienen el botón de publicar habilitado.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-gray-400 mt-0.5 shrink-0">•</span>
                            <span>Al publicar, se registra automáticamente el <strong class="text-gray-900 dark:text-white">usuario</strong> que publicó y la <strong class="text-gray-900 dark:text-white">fecha/hora</strong>.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-gray-400 mt-0.5 shrink-0">•</span>
                            <span>Las lecciones scoped a tus áreas: solo ves las que corresponden a las asignaturas de tus áreas de conocimiento.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</x-help-panel>
