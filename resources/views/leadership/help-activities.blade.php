{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║  HELP CONTENT — Plan de Actividades · Resumen de Actividades              ║
║  Basado en blueprint/leadership/implementations.md                         ║
║  Uso: @include('leadership.help-activities')                              ║
║  NOTA: Este partial solo provee el contenido. El shell visual              ║
║  (botón + backdrop + slideover) lo provee <x-help-panel>.                  ║
╚══════════════════════════════════════════════════════════════════════════════╝
--}}

<x-help-panel
    name="helpActivities"
    title="Guía del Plan de Actividades"
    subtitle="Revisión y control de calidad pedagógica"
    color="emerald">

    {{-- Intro text --}}
    <div class="bg-emerald-50/50 dark:bg-slate-700/20 border border-emerald-200 dark:border-slate-600/30 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">
            Esta vista te permite <strong class="text-gray-900 dark:text-white">revisar y supervisar</strong>
            los planes de evaluación y sus actividades registradas por los profesores.
            Aquí puedes <strong class="text-emerald-600 dark:text-emerald-400">comentar, aprobar o solicitar revisión</strong>
            de cada actividad, y mantener observaciones de coordinación por plan de evaluación.
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
            <button @click="tab = 'actividades'"
                    :class="tab === 'actividades' ? 'bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 border-sky-200 dark:border-sky-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Actividades
                </span>
            </button>
            <button @click="tab = 'acciones'"
                    :class="tab === 'acciones' ? 'bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-300 border-violet-200 dark:border-violet-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Acciones
                </span>
            </button>
        </div>

        {{-- ═══ TAB: VISTA GENERAL ═══ --}}
        <div x-show="tab === 'vista'" x-transition:enter="transition-opacity duration-200">
            <div class="space-y-3">
                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: true }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué ves en esta pantalla?</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Estructura general</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4 space-y-3">
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">La pantalla está organizada en 3 zonas principales:</p>
                            <ul class="space-y-2 text-sm text-gray-700 dark:text-slate-300">
                                <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Barra de filtros</strong> en la parte superior con selectores en cascada, toggles y paginación.</span></li>
                                <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-slate-200">Listado de planes</strong> (pevaluacions) con dos modos de visualización: Grid y Tabla. Cada plan muestra su asignatura, sección, profesor y actividades.</span></li>
                                <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><strong class="text-gray-800 dark:text-slate-200">Barra de lapsos</strong> (solo en modo Tabla) con pestañas para cambiar entre períodos académicos.</span></li>
                            </ul>
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
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Modo Grid vs Tabla</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Toggle de visualización</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4 space-y-3">
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">Usa el toggle <strong class="text-gray-900 dark:text-white">Grid / Tabla</strong> arriba del listado para cambiar entre los dos modos:</p>
                            <div class="flex flex-col gap-1.5 text-sm">
                                <div class="p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Grid (Bento)</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Cards compactos responsivos, ideales para un vistazo rápido del total, aprobadas y pendientes.</p>
                                </div>
                                <div class="p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Tabla (Expandible)</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Lista vertical expandible con pestañas de lapso en la parte superior. Cada plan se expande para mostrar sus actividades en tabs internas.</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-slate-400 italic">La preferencia se guarda en <code class="text-[10px] bg-gray-200 dark:bg-slate-700 px-1 py-0.5 rounded font-mono">localStorage</code>.</p>
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
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Filtros disponibles</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Selectores en cascada</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4 space-y-3">
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">Usa los selectores para acotar los resultados. Los filtros se aplican en tiempo real (<code class="text-[10px] bg-gray-200 dark:bg-slate-700 px-1 py-0.5 rounded font-mono">wire:model.live</code>):</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div class="p-2 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400">Plan Estudio</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Filtra por plan de estudio.</p>
                                </div>
                                <div class="p-2 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400">Profesor</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Selecciona un profesor específico.</p>
                                </div>
                                <div class="p-2 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400">Grado / Año</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Filtra por grado académico.</p>
                                </div>
                                <div class="p-2 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400">Sección</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Filtra por sección (depende del grado).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Toggles adicionales</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Observaciones · En revisión · Estado</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4 space-y-3">
                            <div class="flex flex-col gap-2">
                                <div class="p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Toggle Observaciones</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Muestra solo los planes que tienen observaciones del coordinador registradas.</p>
                                </div>
                                <div class="p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Toggle En revisión</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Muestra solo los planes que tienen al menos una actividad pendiente de aprobación.</p>
                                </div>
                                <div class="p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Estado (segmented)</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5"><strong>Pendientes:</strong> planes con al menos una actividad en revisión. <strong>Aprobadas:</strong> planes con todas las actividades aprobadas.</p>
                                </div>
                                <div class="p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:text-slate-400">Actividades</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Filtra planes que tienen o no tienen actividades registradas.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ TAB: ACTIVIDADES ═══ --}}
        <div x-show="tab === 'actividades'" x-transition:enter="transition-opacity duration-200">
            <div class="space-y-3">
                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: true }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-sky-50 dark:bg-sky-500/15 border border-sky-200 dark:border-sky-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Actividades por plan</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Evaluación y comentarios</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4 space-y-3">
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">Cada plan de evaluación (<strong class="text-gray-900 dark:text-white">pevaluacion</strong>) contiene una o más actividades. Al expandir un plan (modo Tabla) ves:</p>
                            <ul class="space-y-2 text-sm text-gray-700 dark:text-slate-300">
                                <li class="flex items-start gap-2"><span class="text-sky-600 dark:text-sky-400 mt-0.5">▸</span><strong class="text-gray-800 dark:text-slate-200">Tabs de actividades</strong> — navegación entre actividades individuales.</span></li>
                                <li class="flex items-start gap-2"><span class="text-sky-600 dark:text-sky-400 mt-0.5">▸</span><strong class="text-gray-800 dark:text-slate-200">Indicador de calidad</strong> — compara el conteo de palabras de la enseñanza contra el promedio del área.</span></li>
                                <li class="flex items-start gap-2"><span class="text-sky-600 dark:text-sky-400 mt-0.5">▸</span><strong class="text-gray-800 dark:text-slate-200">Comentario del Jefe de Área</strong> — puedes agregar/editar un comentario y cambiar el estado (Aprobado / En revisión).</span></li>
                            </ul>
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">En el <strong class="text-gray-900 dark:text-white">modo Grid</strong>, cada card muestra un resumen compacto: total de actividades, cuántas aprobadas y cuántas en revisión.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Aprobación de actividades</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Flujo de revisión</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4 space-y-3">
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">Cada actividad puede tener uno de estos estados:</p>
                            <div class="flex flex-col gap-2">
                                <div class="p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-emerald-200 dark:border-emerald-500/20">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">✓ Aprobado</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">La actividad cumple con los criterios pedagógicos y ha sido aprobada por el Jefe de Área.</p>
                                </div>
                                <div class="p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-amber-200 dark:border-amber-500/20">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">● En revisión</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">La actividad necesita ajustes. El profesor debe modificarla y el Jefe de Área debe re-evaluarla.</p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">Para cambiar el estado, haz clic en <strong class="text-emerald-600 dark:text-emerald-400">Agregar / Editar</strong> dentro del plan expandido. También puedes ver una vista previa detallada de la actividad con el botón <strong>Vista Previa</strong>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ TAB: ACCIONES ═══ --}}
        <div x-show="tab === 'acciones'" x-transition:enter="transition-opacity duration-200">
            <div class="space-y-3">
                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: true }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Observaciones del Coordinador</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Solo para usuarios de planificación</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4 space-y-3">
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">El botón <strong class="text-cyan-600 dark:text-cyan-400">Observación</strong> (icono de lápiz) permite al coordinador de planificación registrar notas a nivel de plan de evaluación (<strong class="text-gray-900 dark:text-white">pevaluacion</strong>).</p>
                            <div class="bg-amber-50/50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/10 rounded-lg p-3">
                                <p class="text-xs text-amber-700/80 dark:text-amber-300/80 flex items-start gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span><strong class="text-amber-800 dark:text-amber-200">Usuarios Leadership:</strong> el botón de observaciones está oculto porque los Jefes de Área no gestionan observaciones. Solo visible para usuarios del módulo de planificación.</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-purple-50 dark:bg-purple-500/15 border border-purple-200 dark:border-purple-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Descargas PDF</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Resumen y Plan Completo</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4 space-y-3">
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">Cada plan de evaluación tiene dos opciones de descarga PDF:</p>
                            <div class="flex flex-col gap-2">
                                <div class="p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-sky-600 dark:text-sky-400">Resumen PDF</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Vista resumida del plan de evaluación con datos del profesor, sección y actividades principales.</p>
                                </div>
                                <div class="p-2.5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">Plan Completo PDF</span>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Documento completo del plan de evaluación con todas las actividades, indicadores de logro y observaciones.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-slate-700/20 border border-gray-200 dark:border-slate-600/30 rounded-lg overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-sky-50 dark:bg-sky-500/15 border border-sky-200 dark:border-sky-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Vista Previa de Actividad</h3>
                                <p class="text-[10px] text-gray-500 dark:text-slate-500 uppercase tracking-wider">Inspección detallada</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition-all duration-200">
                        <div class="px-4 pb-4 space-y-3">
                            <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">El botón <strong class="text-sky-600 dark:text-sky-400">Vista Previa</strong> abre un modal con la información completa de la actividad seleccionada:</p>
                            <ul class="space-y-1.5 text-sm text-gray-700 dark:text-slate-300">
                                <li class="flex items-start gap-2"><span class="text-sky-500 mt-0.5">▸</span>Fechas de inicio y fin</li>
                                <li class="flex items-start gap-2"><span class="text-sky-500 mt-0.5">▸</span>Tema generador / Énfasis</li>
                                <li class="flex items-start gap-2"><span class="text-sky-500 mt-0.5">▸</span>Tejido temático / Tema Indispensable</li>
                                <li class="flex items-start gap-2"><span class="text-sky-500 mt-0.5">▸</span>Enseñanza con estructura INICIO · DESARROLLO · CIERRE</li>
                                <li class="flex items-start gap-2"><span class="text-sky-500 mt-0.5">▸</span>Aprendizaje, Actividad Evaluativa, Indicadores de Logro y ODS</li>
                                <li class="flex items-start gap-2"><span class="text-sky-500 mt-0.5">▸</span>Indicador de calidad (conteo de palabras vs promedio del área)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-8 pt-4 border-t border-gray-200 dark:border-slate-700/50">
            <p class="text-[10px] text-gray-400 dark:text-slate-500 text-center">
                Documentación basada en <code class="text-[10px] bg-gray-100 dark:bg-slate-700 px-1 py-0.5 rounded font-mono">blueprint/leadership/implementations.md</code>
                · Plan de Actividades v1
            </p>
        </div>
    </div>
</x-help-panel>
