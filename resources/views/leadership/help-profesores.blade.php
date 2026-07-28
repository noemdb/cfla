{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║  HELP CONTENT — Indicadores de Profesores (Jefe de Área)                   ║
║  Uso: @include('leadership.help-profesores')                              ║
║  NOTA: Este partial solo provee el contenido. El shell visual              ║
║  (botón + backdrop + slideover) lo provee <x-help-panel>.                  ║
╚══════════════════════════════════════════════════════════════════════════════╝
--}}

<x-help-panel
    name="helpProfesores"
    title="Guía de Indicadores de Profesores"
    subtitle="Supervisión de rendimiento docente por área"
    color="amber">

    {{-- Intro text --}}
    <div class="bg-amber-50/50 dark:bg-slate-700/20 border border-amber-200 dark:border-slate-600/30 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">
            Como <strong class="text-gray-900 dark:text-white">Jefe de Área</strong>, puedes consultar los
            <strong class="text-amber-600 dark:text-amber-400">indicadores de rendimiento</strong> de los profesores
            asignados a tus áreas de conocimiento. El panel te permite seleccionar un profesor y visualizar
            su información general, KPIs, carga académica, actividades de planificación, lecciones LMS
            y gráficos de flujo.
        </p>
    </div>

    {{-- Tabs navigation --}}
    <div x-data="{ tab: 'sidebar' }">
        <div class="flex gap-1 bg-gray-100 dark:bg-slate-900/50 rounded-lg p-1 mb-6 overflow-x-auto" role="tablist">
            <button @click="tab = 'sidebar'"
                    :class="tab === 'sidebar' ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Sidebar
                </span>
            </button>
            <button @click="tab = 'kpis'"
                    :class="tab === 'kpis' ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    KPIs
                </span>
            </button>
            <button @click="tab = 'informacion'"
                    :class="tab === 'informacion' ? 'bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 border-sky-200 dark:border-sky-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Info General
                </span>
            </button>
            <button @click="tab = 'charts'"
                    :class="tab === 'charts' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    Gráficos
                </span>
            </button>
        </div>

        {{-- ── TAB: Sidebar ── --}}
        <div x-show="tab === 'sidebar'" x-cloak>
            <div class="space-y-4">
                <div class="rounded-xl border border-amber-200 dark:border-amber-500/20 bg-amber-50/50 dark:bg-amber-500/5 p-4">
                    <h4 class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider mb-2">Lista de Profesores</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        La barra lateral izquierda muestra todos los profesores asignados a tus áreas de conocimiento.
                        Puedes <strong class="text-gray-900 dark:text-white">colapsarla</strong> horizontalmente usando el botón
                        <code class="px-1 py-0.5 rounded text-[11px] bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-300 font-mono">&lt;&lt;</code>
                        en la parte superior para que solo se vean las iniciales de cada profesor.
                    </p>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Búsqueda y Filtros</h4>
                    <ul class="text-sm text-gray-600 dark:text-slate-400 space-y-2">
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5 shrink-0">🔍</span>
                            <span><strong class="text-gray-900 dark:text-white">Buscar profesor</strong> — filtra en tiempo real por nombre o apellido. Muestra el contador de resultados.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5 shrink-0">📅</span>
                            <span><strong class="text-gray-900 dark:text-white">Filtrar por lapso</strong> — al seleccionar un lapso, todos los indicadores y KPIs se recalculan para ese período.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ── TAB: KPIs ── --}}
        <div x-show="tab === 'kpis'" x-cloak>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">IEE</div>
                        <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                            <strong class="text-gray-900 dark:text-white">Índice de Eficiencia Educativa</strong>.
                            Mide el porcentaje de eficiencia del profesor en sus planes de evaluación.
                            Valores menores a <span class="text-red-400 font-semibold">70%</span> se marcan en rojo.
                        </p>
                    </div>
                    <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">IRE</div>
                        <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                            <strong class="text-gray-900 dark:text-white">Índice de Rendimiento Educativo</strong>.
                            Porcentaje de rendimiento académico del profesor. Al igual que el IEE,
                            valores bajo <span class="text-red-400 font-semibold">70%</span> se destacan en rojo.
                        </p>
                    </div>
                    <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Notas (Real)</div>
                        <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                            Cantidad de notas reales cargadas vs la <strong class="text-gray-900 dark:text-white">meta</strong>
                            esperada para el lapso seleccionado.
                        </p>
                    </div>
                    <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Carga</div>
                        <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                            Total de <strong class="text-gray-900 dark:text-white">Planes de Evaluación (Pevas)</strong>
                            asociados al profesor en el lapso activo o seleccionado.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── TAB: Información General ── --}}
        <div x-show="tab === 'informacion'" x-cloak>
            <div class="space-y-4">
                <div class="rounded-xl border border-sky-200 dark:border-sky-500/20 bg-sky-50/50 dark:bg-sky-500/5 p-4">
                    <h4 class="text-xs font-bold text-sky-700 dark:text-sky-400 uppercase tracking-wider mb-2">Secciones del panel</h4>
                    <ul class="text-sm text-gray-600 dark:text-slate-400 space-y-3">
                        <li class="flex items-start gap-2">
                            <span class="text-sky-500 mt-0.5 shrink-0 font-bold">1</span>
                            <span><strong class="text-gray-900 dark:text-white">Información General</strong> — datos del profesor: nombre, CI, email, teléfono, celular y estado (activo/inactivo).</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-sky-500 mt-0.5 shrink-0 font-bold">2</span>
                            <span><strong class="text-gray-900 dark:text-white">Carga Académica</strong> — lista de asignaturas que dicta y las secciones donde es guía/tutor.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-sky-500 mt-0.5 shrink-0 font-bold">3</span>
                            <span><strong class="text-gray-900 dark:text-white">Actividades de Planificación</strong> — total de actividades, aprobadas y pendientes, con barra de progreso.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-sky-500 mt-0.5 shrink-0 font-bold">4</span>
                            <span><strong class="text-gray-900 dark:text-white">Lecciones LMS</strong> — resumen de lecciones por estado: borradores, programadas, publicadas y archivadas.</span>
                        </li>
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Estado del Profesor</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        El badge de estado <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400">● Activo</span>
                        o <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-red-100 dark:bg-red-500/15 text-red-700 dark:text-red-400">● Inactivo</span>
                        indica si el profesor está actualmente activo en el sistema.
                    </p>
                </div>
            </div>
        </div>

        {{-- ── TAB: Gráficos ── --}}
        <div x-show="tab === 'charts'" x-cloak>
            <div class="space-y-4">
                <div class="rounded-xl border border-emerald-200 dark:border-emerald-500/20 bg-emerald-50/50 dark:bg-emerald-500/5 p-4">
                    <h4 class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider mb-2">Gráficos de Flujo</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        Tres gráficos de área (<strong class="text-gray-900 dark:text-white">ApexCharts</strong>) que muestran
                        la evolución temporal de la actividad del profesor:
                    </p>
                    <ul class="text-sm text-gray-600 dark:text-slate-400 space-y-2 mt-3">
                        <li class="flex items-start gap-2">
                            <span class="w-3 h-3 rounded-full bg-amber-500 mt-1 shrink-0"></span>
                            <span><strong class="text-gray-900 dark:text-white">Flujo de Actividades</strong> — cantidad de actividades creadas por día.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-3 h-3 rounded-full bg-sky-500 mt-1 shrink-0"></span>
                            <span><strong class="text-gray-900 dark:text-white">Flujo de Lecciones</strong> — cantidad de lecciones LMS creadas por día.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 mt-1 shrink-0"></span>
                            <span><strong class="text-gray-900 dark:text-white">Aprobadas vs Pendientes</strong> — comparativa diaria de actividades aprobadas y pendientes.</span>
                        </li>
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Rango de tiempo</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        Usa los botones <strong class="text-gray-900 dark:text-white">7 días / 30 días / 3 meses / Todo</strong>
                        para ajustar la ventana de tiempo de los gráficos. Al cambiar el rango, los datos se recalculan automáticamente.
                    </p>
                </div>
            </div>
        </div>
    </div>

</x-help-panel>
