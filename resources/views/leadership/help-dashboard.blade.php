{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║  HELP CONTENT — Panel de Seguimiento · Indicadores (Jefe de Área)          ║
║  Basado en blueprint/leadership/implementations.md                         ║
║  Uso: @include('leadership.help-dashboard')                              ║
║  NOTA: Este partial solo provee el contenido. El shell visual              ║
║  (botón + backdrop + slideover) lo provee <x-help-panel>.                  ║
╚══════════════════════════════════════════════════════════════════════════════╝
--}}

<x-help-panel
    name="helpDashboard"
    title="Guía del Panel de Seguimiento"
    subtitle="Para Jefes de Área (rol leadership)"
    color="indigo">

    {{-- Intro text --}}
    <div class="bg-indigo-50/50 dark:bg-slate-700/20 border border-indigo-200 dark:border-slate-600/30 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">
            Como <strong class="text-gray-900 dark:text-white">Jefe de Área (Seguimiento)</strong>, supervisas las actividades de planificación
            y lecciones LMS de los profesores asociados a tus áreas de conocimiento asignadas.
            Este panel te ofrece una <strong class="text-indigo-600 dark:text-indigo-400">visión consolidada</strong>
            de indicadores, profesores y actividades — todo scoped a tu área de supervisión.
        </p>
    </div>

    {{-- Tabs navigation --}}
    <div x-data="{ tab: 'resumen' }">
        <div class="flex gap-1 bg-gray-100 dark:bg-slate-900/50 rounded-lg p-1 mb-6 overflow-x-auto" role="tablist">
            <button @click="tab = 'resumen'"
                    :class="tab === 'resumen' ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Resumen
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
            <button @click="tab = 'indicadores'"
                    :class="tab === 'indicadores' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Indicadores
                </span>
            </button>
            <button @click="tab = 'areas'"
                    :class="tab === 'areas' ? 'bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-300 border-violet-200 dark:border-violet-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Áreas
                </span>
            </button>
        </div>

        {{-- ═══ TAB: RESUMEN ═══ --}}
        <div x-show="tab === 'resumen'" x-transition:enter="transition-opacity duration-200">
            <div class="space-y-3">
                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: true }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-500/15 border border-indigo-200 dark:border-indigo-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué ves en este panel?</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Vista general del dashboard</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4 space-y-3">
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">Este dashboard es tu <strong class="text-gray-900 dark:text-white">centro de monitoreo</strong> como Jefe de Área. Está organizado en 3 secciones principales:</p>
                            <ul class="space-y-2 text-sm text-gray-700 dark:text-slate-300">
                                <li class="flex items-start gap-2"><span class="text-indigo-600 dark:text-indigo-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">KPIs globales</strong> en la parte superior: total de actividades registradas, diagnósticos activos, profesores activos y lecciones (programadas/publicadas).</span></li>
                                <li class="flex items-start gap-2"><span class="text-indigo-600 dark:text-indigo-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Flujo de registros</strong> con gráficos de tendencia (actividades, lecciones, diagnósticos) con rangos de tiempo seleccionables.</span></li>
                                <li class="flex items-start gap-2"><span class="text-indigo-600 dark:text-indigo-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">3 tabs internas</strong> (Indicadores Principales, Profesores, Actividades) con datos scoped a tu área y filtros.</span></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué áreas supervisas?</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Alcance del rol leadership</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4 space-y-3">
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">Tu usuario tiene asignadas <strong class="text-gray-900 dark:text-white">una o más áreas de conocimiento</strong> vía la tabla <code class="text-[10px] bg-gray-200 dark:bg-slate-700 px-1 py-0.5 rounded font-mono">area_conocimiento_leader</code>. El sistema filtra automáticamente todos los datos (profesores, actividades, lecciones) para mostrar solo lo que corresponde a tus áreas.</p>
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">El botón <strong class="text-indigo-600 dark:text-indigo-400">Áreas</strong> (junto al botón Refrescar) abre el modal de áreas de conocimiento donde puedes explorar tu estructura completa de área → asignatura → pensums.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Lapso académico activo</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Selector de período</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4">
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">Los lapsos (trimestres/lapso académico) se muestran como pestañas en la barra de navegación secundaria. Los indicadores, profesores y actividades se filtran según el lapso seleccionado. Cada lapso muestra su rango de fechas (inicio — fin).</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ TAB: FILTROS ═══ --}}
        <div x-show="tab === 'filtros'" x-transition:enter="transition-opacity duration-200">
            <div class="space-y-3">
                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: true }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Filtros en cascada</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">peducativo → pestudio → grado → seccion</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4 space-y-3">
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">Los 4 selectores trabajan en <strong class="text-gray-900 dark:text-white">cascada descendente</strong>:</p>
                            <div class="flex flex-col gap-1.5 text-sm">
                                <div class="flex items-center gap-2 p-2 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    <span><strong class="text-gray-800 dark:text-slate-200">P.Educativo</strong> — filtra por programa educativo (raíz de la cascada)</span>
                                </div>
                                <div class="flex items-center gap-2 p-2 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    <span><strong class="text-gray-800 dark:text-slate-200">P.Estudio</strong> — se puebla según el P.Educativo seleccionado</span>
                                </div>
                                <div class="flex items-center gap-2 p-2 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    <span><strong class="text-gray-800 dark:text-slate-200">Grado</strong> — se puebla según el P.Estudio seleccionado</span>
                                </div>
                                <div class="flex items-center gap-2 p-2 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    <span><strong class="text-gray-800 dark:text-slate-200">Sección</strong> — se puebla según el Grado seleccionado</span>
                                </div>
                            </div>
                            <div class="bg-amber-50/50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/10 rounded-lg p-3">
                                <p class="text-xs text-amber-700/80 dark:text-amber-300/80 flex items-start gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Cada vez que cambias un filtro padre, los filtros hijos se resetean. Ej: cambiar P.Educativo limpia P.Estudio, Grado y Sección.</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Cómo afectan los filtros?</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Comportamiento</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4 space-y-3">
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">Los filtros afectan a <strong class="text-gray-900 dark:text-white">todos los datos</strong> del dashboard de las tabs internas. Los KPIs globales también se actualizan.</p>
                            <ul class="space-y-2 text-sm text-gray-700 dark:text-slate-300">
                                <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">P.Educativo</strong> — cambia los pestudios disponibles, resetea grado y sección.</span></li>
                                <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">P.Estudio</strong> — cambia los grados disponibles, resetea sección.</span></li>
                                <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Grado</strong> — cambia las secciones disponibles.</span></li>
                                <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Sección</strong> — recarga todos los datos scoped a la sección seleccionada.</span></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué hace el botón "Áreas"?</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Modal XXL de áreas de conocimiento</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4 space-y-3">
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">El botón <strong class="text-indigo-600 dark:text-indigo-400">Áreas</strong> abre un modal de tamaño XXL que muestra el árbol completo de tus áreas de conociemiento.</p>
                            <div class="flex flex-col gap-1.5 text-sm">
                                <div class="p-2 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50 text-gray-600 dark:text-slate-400">
                                    <span class="text-indigo-600 dark:text-indigo-400 font-semibold">Área de Conocimiento</span>
                                    <span class="text-gray-400 dark:text-slate-500"> → contiene asignaturas con sus respectivos pensums</span>
                                </div>
                                <div class="p-2 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50 text-gray-600 dark:text-slate-400">
                                    <span class="text-indigo-600 dark:text-indigo-400 font-semibold">Cada asignatura</span>
                                    <span class="text-gray-400 dark:text-slate-500"> → tiene pensums por grado y plan de estudio</span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">El modal incluye toggle Grid/Lista, filtros client-side en cascada y búsqueda textual.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ TAB: INDICADORES ═══ --}}
        <div x-show="tab === 'indicadores'" x-transition:enter="transition-opacity duration-200">
            <div class="space-y-3">
                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: true }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Indicadores Principales (Tab 1)</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">KPIs por programa educativo</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4 space-y-3">
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">Esta tab agrupa los indicadores por <strong class="text-gray-900 dark:text-white">Programa Educativo</strong>. Por cada peducativo ves:</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div class="p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Actividades Registradas</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Total de actividades de planificación registradas en el lapso seleccionado.</p>
                                </div>
                                <div class="p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Profesores con Carga</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Número de profesores que tienen al menos una actividad planificada en el período.</p>
                                </div>
                                <div class="p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-sky-600 dark:text-sky-400">Lecciones Registradas</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Lecciones LMS creadas (en cualquier estado: borrador, programado, publicado).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Indicadores de Profesores (Tab 2)</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">KPIs individuales por profesor</p>
                            </div>
                        </div>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4 space-y-3">
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">Muestra los profesores scoped a tu área con sus métricas:</p>
                            <div class="grid grid-cols-1 gap-2">
                                <div class="p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">IEE — Índice de Ejecución de Estrategias</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">% de actividades planificadas vs ejecutadas.</p>
                                </div>
                                <div class="p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">IRE — Índice de Registro de Estrategias</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">% de actividades registradas vs esperadas.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ TAB: ÁREAS ═══ --}}
        <div x-show="tab === 'areas'" x-transition:enter="transition-opacity duration-200">
            <div class="space-y-3">
                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: true }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Modal de Áreas de Conocimiento</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Navegación área → asignatura → pensum</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4">
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">El modal se abre al hacer clic en el botón <strong class="text-indigo-600 dark:text-indigo-400">Áreas</strong> en la cabecera del dashboard. Muestra en un modal XXL el árbol completo de áreas de conocimiento con sus asignaturas y pensums asociados.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Vistas disponibles</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Grid y Lista</p>
                            </div>
                        </div>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4">
                            <div class="flex flex-col gap-2">
                                <div class="p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Vista Tabla (Lista)</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Árbol vertical expandido con áreas → asignaturas → pensums.</p>
                                </div>
                                <div class="p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Vista Grid</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Cards en grid responsivo. Ideal para vista panorámica.</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-3 italic">La preferencia se guarda en localStorage.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-8 pt-4 border-t border-gray-200 dark:border-slate-700/50">
            <p class="text-[10px] text-gray-400 dark:text-slate-500 text-center">
                Documentación basada en <code class="text-[10px] bg-gray-100 dark:bg-slate-700 px-1 py-0.5 rounded font-mono">blueprint/leadership/implementations.md</code>
                · Panel de Seguimiento v2
            </p>
        </div>
    </div>
</x-help-panel>
