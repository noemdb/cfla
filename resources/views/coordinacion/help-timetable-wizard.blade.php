{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║  HELP CONTENT — Wizard de Horario Escolar (Planning / Coordinación)         ║
║  Uso: @include('coordinacion.help-timetable-wizard')                        ║
║  NOTA: Este partial solo provee el contenido. El shell visual              ║
║  (botón + backdrop + slideover) lo provee <x-help-panel>.                  ║
╚══════════════════════════════════════════════════════════════════════════════╝
--}}

<x-help-panel
    name="helpTimetableWizard"
    title="Wizard de Horario"
    subtitle="Generación automática de horarios escolares (Lun–Vie, turnos M/T)"
    color="emerald"
    buttonClass="bottom-24 right-6">

    {{-- Intro --}}
    <div class="bg-emerald-50/50 dark:bg-slate-700/20 border border-emerald-200 dark:border-slate-600/30 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed">
            El <strong class="text-gray-900 dark:text-white">Wizard de Horario</strong> te guía en la creación del
            horario semanal de cada sección. Las lecciones a programar provienen de la
            <strong class="text-emerald-600 dark:text-emerald-400">carga académica</strong> (pevaluaciones) del
            lapso: no se duplican materias, secciones ni docentes. El motor resuelve
            automáticamente los cruces de docentes, aulas y secciones, y tú puedes ajustar
            el resultado a mano.
        </p>
    </div>

    {{-- Tabs navigation --}}
    <div x-data="{ tab: 'pasos' }">
        <div class="flex gap-1 bg-gray-100 dark:bg-slate-900/50 rounded-lg p-1 mb-6 overflow-x-auto" role="tablist">
            <button @click="tab = 'pasos'"
                    :class="tab === 'pasos' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Pasos
                </span>
            </button>
            <button @click="tab = 'reglas'"
                    :class="tab === 'reglas' ? 'bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 border-sky-200 dark:border-sky-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Reglas
                </span>
            </button>
            <button @click="tab = 'estados'"
                    :class="tab === 'estados' ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Estados
                </span>
            </button>
            <button @click="tab = 'faq'"
                    :class="tab === 'faq' ? 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-500/30 shadow-sm' : 'text-gray-500 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 border-transparent'"
                    class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                <span class="flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    FAQ
                </span>
            </button>
        </div>

        {{-- ── TAB: Pasos del Wizard ── --}}
        <div x-show="tab === 'pasos'" x-cloak>
            <div class="space-y-4">
                <div class="rounded-xl border border-emerald-200 dark:border-emerald-500/20 bg-emerald-50/50 dark:bg-emerald-500/5 p-4">
                    <h4 class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider mb-2">Los 5 pasos del wizard</h4>
                    <div class="space-y-3 text-sm text-gray-600 dark:text-slate-400">
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-[10px] font-bold shrink-0">1</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Calendario</strong>
                                <p class="leading-relaxed">Elige el lapso y crea un borrador con la duración del bloque (<em>minutos por período</em>). Un lapso admite varias alternativas: solo una puede estar activa. Luego define los <strong class="text-gray-900 dark:text-white">turnos</strong> (Mañana/Tarde) y genera los períodos de lunes a viernes.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-cyan-500/20 flex items-center justify-center text-cyan-400 text-[10px] font-bold shrink-0">2</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Aulas</strong>
                                <p class="leading-relaxed">Registra aulas y espacios (laboratorio, patio, cancha, taller). El botón <strong class="text-gray-900 dark:text-white">Carga masiva</strong> crea automáticamente un aula por cada grado/sección activo.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-teal-500/20 flex items-center justify-center text-teal-400 text-[10px] font-bold shrink-0">3</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Lecciones</strong>
                                <p class="leading-relaxed">Marca las pevaluaciones del lapso: cada una derivará sus bloques semanales desde las horas de la asignatura (teóricas y prácticas). Puedes ajustar el turno, el tipo de aula, la prioridad y fijar bloques (<em>locked</em>). También puedes importarlas desde CSV/Excel con la plantilla descargable.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400 text-[10px] font-bold shrink-0">4</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Disponibilidad</strong>
                                <p class="leading-relaxed">Grilla día × período por docente para marcar los bloques en los que NO puede dictar. El botón <strong class="text-gray-900 dark:text-white">Todo disponible</strong> parte de un preset limpio.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-amber-500/20 flex items-center justify-center text-amber-400 text-[10px] font-bold shrink-0">5</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Generar</strong>
                                <p class="leading-relaxed">Primero <strong class="text-gray-900 dark:text-white">Previsualizar (dry-run)</strong>: el motor resuelve el horario sin publicarlo y muestra el resultado, las lecciones sin asignar y el tiempo. Si es correcto, <strong class="text-gray-900 dark:text-white">Confirmar y publicar</strong>.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Después de publicar</h4>
                    <ul class="text-sm text-gray-600 dark:text-slate-400 space-y-2">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-500 mt-0.5 shrink-0">✏️</span>
                            <span><strong class="text-gray-900 dark:text-white">Editor manual</strong> — arrastra bloques en la grilla; los conflictos se validan en vivo antes de guardar. Los bloques fijados (<em>locked</em>) no se reasignan en regeneraciones.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-rose-500 mt-0.5 shrink-0">👥</span>
                            <span><strong class="text-gray-900 dark:text-white">Suplencias</strong> — registra ausencias de docentes, el sistema identifica los bloques afectados, sugiere suplentes y les notifica; ellos confirman o rechazan desde su bandeja.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-sky-500 mt-0.5 shrink-0">🔗</span>
                            <span><strong class="text-gray-900 dark:text-white">Exportar</strong> — PDF por sección, docente o aula, y enlaces públicos firmados (expiran) para compartir sin login.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ── TAB: Reglas del motor ── --}}
        <div x-show="tab === 'reglas'" x-cloak>
            <div class="space-y-4">
                <div class="rounded-xl border border-sky-200 dark:border-sky-500/20 bg-sky-50/50 dark:bg-sky-500/5 p-4">
                    <h4 class="text-xs font-bold text-sky-700 dark:text-sky-400 uppercase tracking-wider mb-2">Reglas duras (nunca se violan)</h4>
                    <ul class="text-sm text-gray-600 dark:text-slate-400 space-y-2">
                        <li class="flex items-start gap-2">
                            <span class="text-sky-500 mt-0.5 shrink-0">🚫</span>
                            <span><strong class="text-gray-900 dark:text-white">Docente</strong> — un docente no puede dictar dos lecciones en el mismo período.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-sky-500 mt-0.5 shrink-0">🚫</span>
                            <span><strong class="text-gray-900 dark:text-white">Sección / sub-grupo</strong> — una sección no puede tener dos lecciones simultáneas. Excepción: sub-grupos (grupos estables) distintos de la misma sección sí pueden ir en paralelo, cada uno con su profesor.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-sky-500 mt-0.5 shrink-0">🚫</span>
                            <span><strong class="text-gray-900 dark:text-white">Aula</strong> — un aula no puede alojar dos lecciones en el mismo período. Los bloques prácticos pueden exigir un tipo de aula (laboratorio, patio…).</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-sky-500 mt-0.5 shrink-0">🚫</span>
                            <span><strong class="text-gray-900 dark:text-white">Turno</strong> — cada sección pertenece a un turno (mañana/tarde); sus bloques solo se ubican en períodos de ese turno.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-sky-500 mt-0.5 shrink-0">🚫</span>
                            <span><strong class="text-gray-900 dark:text-white">Disponibilidad</strong> — los períodos marcados como no disponibles para un docente quedan excluidos.</span>
                        </li>
                    </ul>
                </div>
                <div class="rounded-xl border border-violet-200 dark:border-violet-500/20 bg-violet-50/50 dark:bg-violet-500/5 p-4">
                    <h4 class="text-xs font-bold text-violet-700 dark:text-violet-400 uppercase tracking-wider mb-2">Preferencias del motor (heurísticas)</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        Cuando hay varias soluciones válidas, el motor prefiere: distribuir los bloques
                        de una misma asignatura en <strong class="text-gray-900 dark:text-white">días distintos</strong> (no
                        consecutivos si hay más de uno), colocar los bloques <strong class="text-gray-900 dark:text-white">teóricos en períodos
                        tempranos</strong> y evitar <strong class="text-gray-900 dark:text-white">huecos</strong> en el turno del docente.
                        El <em>score de calidad</em> (porcentaje de lecciones asignadas) se guarda para comparar
                        regeneraciones.
                    </p>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Bloques por lección</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        Los bloques semanales se derivan de las horas de la asignatura:
                        <code class="text-xs bg-gray-100 dark:bg-slate-900/60 px-1.5 py-0.5 rounded">bloques = ceil(horas × 60 / minutos_del_período)</code>,
                        por separado para horas teóricas y prácticas. La asignatura define el valor por
                        defecto; puedes ajustarlo por lección.
                    </p>
                </div>
            </div>
        </div>

        {{-- ── TAB: Estados ── --}}
        <div x-show="tab === 'estados'" x-cloak>
            <div class="space-y-4">
                <div class="rounded-xl border border-amber-200 dark:border-amber-500/20 bg-amber-50/50 dark:bg-amber-500/5 p-4">
                    <h4 class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider mb-2">Estados del calendario</h4>
                    <div class="flex items-center justify-between text-sm py-2 flex-wrap gap-y-2">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400">📝 Borrador</span>
                        <span class="text-gray-500">→</span>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-sky-100 dark:bg-sky-500/15 text-sky-700 dark:text-sky-400">⚙️ Generando</span>
                        <span class="text-gray-500">→</span>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400">✅ Activo</span>
                        <span class="text-gray-500">→</span>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 dark:bg-gray-500/15 text-gray-600 dark:text-gray-400">🗄️ Archivado</span>
                    </div>
                    <ul class="text-sm text-gray-600 dark:text-slate-400 space-y-2 mt-2">
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5 shrink-0">📝</span>
                            <span><strong class="text-gray-900 dark:text-white">Borrador (draft)</strong> — editable; solo se pueden eliminar calendarios en este estado. Un lapso puede tener varios borradores para comparar alternativas.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-sky-500 mt-0.5 shrink-0">⚙️</span>
                            <span><strong class="text-gray-900 dark:text-white">Generando (generating)</strong> — el motor está resolviendo. No se dispara una segunda generación mientras corre.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-500 mt-0.5 shrink-0">✅</span>
                            <span><strong class="text-gray-900 dark:text-white">Activo (active)</strong> — es el horario vigente que ven docentes, estudiantes y dirección. <strong class="text-red-500">Solo UNO por lapso</strong>: activar una alternativa archiva automáticamente a la anterior.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-gray-500 mt-0.5 shrink-0">🗄️</span>
                            <span><strong class="text-gray-900 dark:text-white">Archivado (archived)</strong> — terminal, solo lectura. Conserva el historial para auditoría (no se borra).</span>
                        </li>
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Regeneración segura</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        Regenerar un calendario activo siempre pasa primero por <strong class="text-gray-900 dark:text-white">dry-run</strong>:
                        nunca se sobreescribe el horario publicado sin tu confirmación explícita. Los bloques
                        <strong class="text-gray-900 dark:text-white">fijados (locked)</strong> se preservan intactos en cualquier
                        regeneración. Antes de publicar, el sistema te muestra el <strong class="text-gray-900 dark:text-white">diff</strong>
                        real (cuántas lecciones cambian y qué docentes se ven afectados) y solo se notifica a los
                        docentes con cambios.
                    </p>
                </div>
            </div>
        </div>

        {{-- ── TAB: FAQ ── --}}
        <div x-show="tab === 'faq'" x-cloak>
            <div class="space-y-3">
                <div class="rounded-xl border border-rose-200 dark:border-rose-500/20 bg-rose-50/50 dark:bg-rose-500/5 p-4">
                    <h4 class="text-xs font-bold text-rose-700 dark:text-rose-400 mb-1">El selector "Turno y períodos" está vacío</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        El catálogo de turnos empieza vacío. En el paso 1 usa el formulario
                        <strong class="text-gray-900 dark:text-white">"Nuevo turno"</strong> para crear los turnos
                        Mañana (M) y Tarde (T) con sus horarios; luego el selector los mostrará y podrás generar los períodos.
                    </p>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white mb-1">"Los períodos de este turno ya están generados"</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        Cada turno solo puede generar períodos una vez por calendario. Para rehacerlos,
                        elimina el borrador y crea uno nuevo (solo se pueden eliminar borradores).
                    </p>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white mb-1">"El borrador no tiene horario generado"</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        Para <strong class="text-gray-900 dark:text-white">Activar</strong> un borrador directamente necesita
                        slots generados: ejecuta primero <em>Previsualizar</em> y <em>Confirmar y publicar</em>.
                    </p>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white mb-1">Hay lecciones sin asignar</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        El motor reporta las lecciones que no pudo ubicar (sin producir cruces). Quedan
                        listadas como conflictos <em>unassigned</em> en el editor: arrástralas a mano a los huecos
                        libres, o ajusta disponibilidad/aulas y regenera.
                    </p>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-600/30 bg-white dark:bg-slate-700/10 p-4">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white mb-1">"Otro usuario modificó este horario"</h4>
                    <p class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                        Dos usuarios editan el mismo calendario a la vez. Recarga la página para
                        continuar con la versión vigente.
                    </p>
                </div>
            </div>
        </div>
    </div>

</x-help-panel>
