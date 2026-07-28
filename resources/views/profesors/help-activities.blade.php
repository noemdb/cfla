{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║  HELP CONTENT — Plan de Actividades (Profesor)                             ║
║  Uso: @include('profesors.help-activities')                               ║
║  NOTA: Este partial solo provee el contenido. El shell visual              ║
║  (botón + backdrop + slideover) lo provee <x-help-panel>.                  ║
╚══════════════════════════════════════════════════════════════════════════════╝
--}}

<x-help-panel
    name="helpProfesorActivities"
    title="Guía del Plan de Actividades"
    subtitle="Gestión de actividades de planificación académica"
    color="emerald">

    {{-- Intro text --}}
    <div class="bg-emerald-50/50 dark:bg-slate-700/20 border border-emerald-200 dark:border-slate-600/30 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">
            Desde este módulo puedes <strong class="text-gray-900 dark:text-white">gestionar las actividades</strong>
            de tus planes de evaluación (Pevas). Cada plan de evaluación agrupa las actividades
            académicas de una <strong class="text-gray-900 dark:text-white">asignatura, sección y lapso</strong>
            específicos.
        </p>
    </div>

    {{-- Tabs navigation --}}
    <div x-data="{ tab: 'vista' }">
        <div class="flex gap-1 bg-gray-100 dark:bg-slate-900/50 rounded-lg p-1 mb-6 overflow-x-auto" role="tablist">
            <button @click="tab = 'vista'"
                    :class="tab === 'vista' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
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
            <button @click="tab = 'acciones'"
                    :class="tab === 'acciones' ? 'bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 border-sky-200 dark:border-sky-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Acciones
                </span>
            </button>
        </div>

        {{-- ── TAB: Vista General ── --}}
        <div x-show="tab === 'vista'" x-cloak>
            <div class="space-y-4">
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Planes de Evaluación</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        La vista principal muestra todos tus <strong class="text-gray-900 dark:text-white">Planes de Evaluación (Peva)</strong>
                        organizados en tarjetas. Cada tarjeta contiene la información de la asignatura, grado, sección, lapso
                        y las actividades asociadas. Desde aquí puedes <strong class="text-emerald-600 dark:text-emerald-400">crear nuevas actividades</strong>,
                        ver el detalle o generar PDFs.
                    </p>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Pestañas de Lapso</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        En la parte superior del listado hay pestañas para cambiar rápidamente entre lapsos.
                        Cada lapso muestra sus planes de evaluación correspondientes. El lapso activo se resalta
                        con un badge <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400">● Activo</span>.
                    </p>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">PDFs: Formato y Resumen</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        Cada plan de evaluación tiene dos opciones de PDF:
                    </p>
                    <ul class="text-sm text-gray-600 dark:text-slate-400 space-y-1.5 mt-2">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-500 mt-0.5 shrink-0">📄</span>
                            <span><strong class="text-gray-900 dark:text-white">Formato</strong> — PDF completo con 9 columnas: todas las actividades con fechas, logros, indicadores y evaluación.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-500 mt-0.5 shrink-0">📋</span>
                            <span><strong class="text-gray-900 dark:text-white">Resumen</strong> — PDF compacto con 6 columnas: solo actividades que tienen descripción, ideal para entrega.</span>
                        </li>
                    </ul>
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
                            <span class="text-amber-500 mt-0.5 shrink-0">📚</span>
                            <span><strong class="text-gray-900 dark:text-white">Plan de Estudio</strong> — filtra los planes de evaluación por pensum de estudio.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5 shrink-0">🎓</span>
                            <span><strong class="text-gray-900 dark:text-white">Grado/Año</strong> — se actualiza automáticamente al seleccionar un plan de estudio (filtro en cascada).</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5 shrink-0">🏫</span>
                            <span><strong class="text-gray-900 dark:text-white">Sección</strong> — se actualiza al seleccionar un grado.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5 shrink-0">📅</span>
                            <span><strong class="text-gray-900 dark:text-white">Lapso</strong> — selector de lapso para filtrar por período académico.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ── TAB: Acciones ── --}}
        <div x-show="tab === 'acciones'" x-cloak>
            <div class="space-y-4">
                <div class="rounded-xl border border-sky-200 dark:border-sky-500/20 bg-sky-50/50 dark:bg-sky-500/5 p-4">
                    <h4 class="text-xs font-bold text-sky-700 dark:text-sky-400 uppercase tracking-wider mb-2">Crear / Editar Actividades</h4>
                    <ol class="text-sm text-gray-600 dark:text-slate-400 space-y-2 list-decimal list-inside">
                        <li class="leading-relaxed">Haz clic en <strong class="text-gray-900 dark:text-white">Agregar Actividad</strong> en la tarjeta del plan de evaluación.</li>
                        <li class="leading-relaxed">Completa los campos: tema, fecha inicial/final, logros e indicadores de evaluación.</li>
                        <li class="leading-relaxed">Selecciona el <strong class="text-gray-900 dark:text-white">tipo de evaluación</strong> (diagnóstica, formativa, sumativa) y los <strong class="text-gray-900 dark:text-white">recursos</strong> necesarios.</li>
                        <li class="leading-relaxed">Guarda la actividad. Aparecerá automáticamente en el listado del plan de evaluación.</li>
                    </ol>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Indicadores de Logro</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        Cada actividad puede tener <strong class="text-gray-900 dark:text-white">indicadores de logro</strong> asociados.
                        Estos se cargan desde el módulo de diagnóstico y te permiten medir el cumplimiento
                        de los objetivos pedagógicos de cada actividad.
                    </p>
                </div>
            </div>
        </div>
    </div>

</x-help-panel>
