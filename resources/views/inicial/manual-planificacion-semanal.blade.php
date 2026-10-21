{{--
    Manual de usuario de la Planificación Semanal (Educación Inicial).

    Ruta: inicials.manual.eiplanningwks → /app/inicials/manual/eiplanningwks
    Controlador: App\Http\Controllers\Inicial\HomeInicialController::manualPlanificacionSemanal

    El manual está escrito para lectura fácil: frases cortas, un paso por
    línea, ejemplos concretos y diagramas. Se divide en los TRES registros
    reales del documento:
      1. Plan semanal (cabecera)
      2. Estrategias (rejilla 5 días × 10 momentos)
      3. Resúmenes por área de aprendizaje

    Los diagramas son SVG inline para que se impriman y se lean sin depender
    de imágenes externas. En móvil se permite desplazamiento horizontal para
    que el texto de los diagramas no quede diminuto.
--}}
<x-layouts.role>
    <div class="py-6">
        <style>
            /* En móvil el diagrama conserva un ancho legible y se desplaza.
               En escritorio ocupa el ancho de la columna. */
            .manual-diagram { min-width: 760px; }
            @media (min-width: 1024px) { .manual-diagram { min-width: 0; } }
            /* w-full + mx-4: el ancho útil descuenta los dos márgenes. */
            .manual-content { width: calc(100% - 2rem); }
            /* Los diagramas y formularios no crecen con el contenedor full width:
               conservan el ancho de la columna de contenido anterior. */
            .manual-figure { max-width: 966px; margin-left: auto; margin-right: auto; }

            /* Cada paso del plan lleva una pequeña vista del componente real.
               La vista se muestra debajo del texto, dentro de la misma tarjeta. */
            .manual-plan-body { max-width: 966px; margin-left: auto; margin-right: auto; }
            .manual-step {
                display: flex;
                flex-direction: column;
                gap: .7rem;
                border: 1px solid rgba(148, 163, 184, .12);
                border-radius: 12px;
                background: linear-gradient(180deg, rgba(15, 23, 42, .55), rgba(15, 23, 42, .28));
                padding: .75rem .9rem;
            }
            .manual-step-copy { display: block; min-width: 0; }
            .manual-step-text { display: block; font-size: .875rem; line-height: 1.6; color: #cbd5e1; }
            .manual-step-text strong { color: #f8fafc; }
            .manual-step-visual { width: 100%; max-width: 600px; min-width: 0; }

            /* Diagrama vertical: todos los pasos visibles, unidos por una línea.
               El color se adapta al registro con --tl-rgb. */
            .manual-timeline-block {
                --tl-rgb: 34, 211, 238;
                --tl-text: #67e8f9;
                --tl-end-rgb: 52, 211, 153;
                --tl-end-text: #6ee7b7;
                max-width: 966px;
                margin-left: auto;
                margin-right: auto;
            }
            .manual-timeline--amber {
                --tl-rgb: 245, 158, 11;
                --tl-text: #fbbf24;
                --tl-end-rgb: 245, 158, 11;
                --tl-end-text: #fbbf24;
            }
            .manual-timeline--purple {
                --tl-rgb: 192, 132, 252;
                --tl-text: #d8b4fe;
                --tl-end-rgb: 192, 132, 252;
                --tl-end-text: #d8b4fe;
            }
            .manual-timeline-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; }
            .manual-timeline-kicker { font-size: 9px; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--tl-text); }
            .manual-timeline-title { margin-top: .12rem; font-size: 1.1rem; font-weight: 800; letter-spacing: -.01em; color: #f8fafc; }
            .manual-timeline-subtitle { max-width: 44rem; margin-top: .45rem; font-size: .8rem; line-height: 1.5; color: #94a3b8; }
            .manual-timeline { position: relative; margin-top: 1rem; list-style: none; }
            .manual-timeline-item {
                position: relative;
                display: grid;
                grid-template-columns: 2rem minmax(0, 1fr);
                gap: .85rem;
            }
            .manual-timeline-item + .manual-timeline-item { margin-top: .8rem; }
            .manual-timeline-item:not(:last-child)::before {
                content: "";
                position: absolute;
                left: calc(1rem - 1px);
                top: 0;
                bottom: -.8rem;
                width: 2px;
                border-radius: 999px;
                background: linear-gradient(180deg, rgba(var(--tl-rgb), .42), rgba(var(--tl-rgb), .14));
            }
            .manual-timeline-item:first-child::before { top: 1.85rem; }
            .manual-timeline-marker {
                position: relative; z-index: 1;
                display: flex; justify-content: center;
                padding-top: .85rem;
            }
            .manual-timeline-dot {
                display: flex; align-items: center; justify-content: center; flex: none;
                width: 2rem; height: 2rem; border-radius: 999px;
                border: 1px solid rgba(var(--tl-rgb), .38);
                background: #0b1220; color: var(--tl-text);
                font-size: 9px; font-weight: 800; letter-spacing: .03em;
                box-shadow: 0 0 0 4px rgba(var(--tl-rgb), .04);
            }
            .manual-timeline-item:last-child .manual-timeline-dot {
                border-color: rgba(var(--tl-end-rgb), .45);
                color: var(--tl-end-text);
                box-shadow: 0 0 0 4px rgba(var(--tl-end-rgb), .05);
            }
            /* Fragmentos de interfaz dentro de cada paso: muestran la acción
               concreta del registro sin convertirse en un formulario real. */
            .manual-fn {
                border: 1px solid rgba(var(--tl-rgb), .18);
                border-radius: 10px;
                background: linear-gradient(180deg, rgba(var(--tl-rgb), .06), rgba(2, 6, 23, .36));
                padding: .55rem .65rem;
                color: #cbd5e1;
                font-size: 11px;
                line-height: 1.45;
            }
            .manual-fn-top { display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
            .manual-fn-title { font-size: 10px; font-weight: 700; color: #f8fafc; }
            .manual-fn-tag {
                flex: none;
                border: 1px solid rgba(var(--tl-rgb), .30);
                border-radius: 999px;
                background: rgba(var(--tl-rgb), .10);
                padding: .12rem .45rem;
                color: var(--tl-text);
                font-size: 8px;
                font-weight: 800;
                letter-spacing: .12em;
                text-transform: uppercase;
                white-space: nowrap;
            }
            .manual-fn-row { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .45rem; margin-top: .45rem; }
            .manual-fn-muted { color: #64748b; font-size: 10px; }
            .manual-fn-field {
                margin-top: .45rem;
                border: 1px solid rgba(148, 163, 184, .12);
                border-radius: 7px;
                background: rgba(2, 6, 23, .45);
                padding: .35rem .5rem;
            }
            .manual-fn-label { display: block; color: #64748b; font-size: 8px; font-weight: 700; letter-spacing: .10em; text-transform: uppercase; }
            .manual-fn-value { display: block; margin-top: .1rem; color: #e2e8f0; font-size: 11px; }
            .manual-fn-chips { display: flex; flex-wrap: wrap; gap: .3rem; margin-top: .45rem; }
            .manual-fn-chip {
                display: inline-flex; align-items: center; gap: .3rem;
                border: 1px solid rgba(148, 163, 184, .14);
                border-radius: 999px;
                background: rgba(255, 255, 255, .03);
                padding: .16rem .45rem;
                color: #94a3b8;
                font-size: 9.5px;
                white-space: nowrap;
            }
            .manual-fn-chip--active { border-color: rgba(var(--tl-rgb), .38); background: rgba(var(--tl-rgb), .14); color: var(--tl-text); }
            .manual-fn-chip-dot { width: 5px; height: 5px; border-radius: 999px; background: currentColor; }
            .manual-fn-btn {
                display: inline-flex; align-items: center; justify-content: center;
                border-radius: 6px;
                background: rgba(var(--tl-rgb), .88);
                padding: .24rem .55rem;
                color: #0b1220;
                font-size: 9.5px;
                font-weight: 800;
                white-space: nowrap;
            }
            .manual-fn-btn--ghost { border: 1px solid rgba(148, 163, 184, .16); background: rgba(255, 255, 255, .04); color: #cbd5e1; font-weight: 600; }
            .manual-fn-btn--danger { border: 1px solid rgba(248, 113, 113, .22); background: rgba(248, 113, 113, .10); color: #fca5a5; font-weight: 700; }
            .manual-fn-note { margin-top: .4rem; color: #64748b; font-size: 9.5px; line-height: 1.45; }
            .manual-fn-note strong { color: #cbd5e1; }
            .manual-fn-list { display: grid; gap: .35rem; margin-top: .45rem; }
            .manual-fn-item {
                display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .35rem;
                border: 1px solid rgba(148, 163, 184, .12);
                border-radius: 7px;
                background: rgba(2, 6, 23, .35);
                padding: .35rem .5rem;
            }
            .manual-fn-item-title { color: #e2e8f0; font-size: 10px; font-weight: 600; }
            .manual-fn-item-meta { color: #64748b; font-size: 9px; }
            .manual-fn-actions { display: flex; flex: none; align-items: center; gap: .3rem; }
            .manual-fn-mini-btn {
                border: 1px solid rgba(148, 163, 184, .16);
                border-radius: 5px;
                padding: .12rem .35rem;
                color: #94a3b8;
                font-size: 8.5px;
                font-weight: 600;
                white-space: nowrap;
            }
            .manual-fn-mini-btn--danger { border-color: rgba(248, 113, 113, .20); color: #fca5a5; }
            .manual-fn-split { display: grid; grid-template-columns: minmax(0, 1fr); gap: .4rem; }
            .manual-fn-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .3rem; margin-top: .45rem; }
            .manual-fn-cell {
                border: 1px solid rgba(148, 163, 184, .12);
                border-radius: 6px;
                background: rgba(2, 6, 23, .32);
                padding: .3rem .4rem;
            }
            .manual-fn-cell strong { display: block; color: #cbd5e1; font-size: 9.5px; }
            .manual-fn-cell span { color: #64748b; font-size: 8.5px; }
            .manual-fn-meter { height: 5px; margin-top: .45rem; overflow: hidden; border-radius: 999px; background: rgba(148, 163, 184, .12); }
            .manual-fn-meter > span { display: block; height: 100%; border-radius: inherit; background: rgba(var(--tl-rgb), .72); }
            .manual-fn-check { color: #6ee7b7; font-size: 9px; font-weight: 700; }
            @media (min-width: 640px) {
                .manual-fn-split { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .manual-fn-grid--3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            }
            .manual-ui {
                display: flex; flex-direction: column; justify-content: center;
                min-height: 88px;
                border: 1px solid rgba(34, 211, 238, .16);
                background: linear-gradient(180deg, rgba(15, 23, 42, .92), rgba(2, 6, 23, .94));
                border-radius: 10px;
                padding: 9px;
                color: #cbd5e1;
                font-size: 10px;
                line-height: 1.35;
                box-shadow: 0 12px 30px -24px rgba(34, 211, 238, .35);
            }
            .manual-ui-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 7px; }
            .manual-ui-title { font-size: 10px; font-weight: 700; color: #e2e8f0; }
            .manual-ui-badge {
                display: inline-flex; align-items: center; flex: none;
                border: 1px solid rgba(34, 211, 238, .25);
                background: rgba(34, 211, 238, .08);
                color: #67e8f9; border-radius: 999px; padding: 2px 6px;
                font-size: 8px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase;
            }
            .manual-ui-label { display: block; margin-bottom: 3px; font-size: 9px; color: #94a3b8; }
            .manual-ui-field {
                display: flex; align-items: center; justify-content: space-between; gap: 6px;
                min-height: 24px; border: 1px solid #334155; background: #0f172a;
                border-radius: 6px; padding: 4px 7px; color: #cbd5e1;
            }
            .manual-ui-field--muted { color: #64748b; }
            .manual-ui-grid { display: grid; gap: 6px; }
            .manual-ui-grid--2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .manual-ui-btn {
                display: inline-flex; align-items: center; justify-content: center; gap: 5px;
                border-radius: 6px; background: #0891b2; color: #ecfeff;
                padding: 5px 8px; font-size: 10px; font-weight: 700;
            }
            .manual-ui-btn--full { width: 100%; }
            .manual-ui-hint { display: flex; align-items: flex-start; gap: 4px; margin-top: 5px; font-size: 8.5px; color: #64748b; }
            .manual-ui-hint--ok { color: #6ee7b7; }
            .manual-ui-dot { display: inline-block; flex: none; width: 5px; height: 5px; margin-top: 4px; border-radius: 50%; background: #34d399; }
            .manual-ui-chevron { flex: none; color: #64748b; font-size: 9px; }
            .manual-ui-link { color: #67e8f9; font-weight: 700; }
            .manual-ui-crumb { display: flex; align-items: center; gap: 5px; font-size: 9.5px; color: #94a3b8; }
            .manual-ui-crumb-current { color: #67e8f9; }
            .manual-ui-empty { border: 1px dashed #334155; border-radius: 6px; padding: 8px; text-align: center; font-size: 9px; color: #64748b; }
            .manual-ui-textarea {
                min-height: 34px; border: 1px solid #334155; background: #0f172a;
                border-radius: 6px; padding: 4px 7px; color: #94a3b8; font-size: 9px; line-height: 1.4;
            }
        </style>
        <div class="w-full mx-4 manual-content space-y-8">

            {{-- ═══════════════════════════════════════════════════════════
                 CABECERA
                 ═══════════════════════════════════════════════════════════ --}}
            <header>
                <nav class="text-xs text-gray-500 mb-3" aria-label="Migas de pan">
                    <ol class="flex flex-wrap items-center gap-2">
                        <li><a href="{{ route('inicials.home') }}" class="hover:text-cyan-400 transition-colors">Educación Inicial</a></li>
                        <li aria-hidden="true">/</li>
                        <li><a href="{{ route('inicials.use-cases') }}" class="hover:text-cyan-400 transition-colors">Casos de uso</a></li>
                        <li aria-hidden="true">/</li>
                        <li class="text-gray-300" aria-current="page">Manual de planificación semanal</li>
                    </ol>
                </nav>

                <div class="rounded-2xl border border-cyan-500/20 bg-gradient-to-br from-cyan-500/10 via-gray-900/60 to-gray-900/60 p-5 sm:p-7">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-cyan-300">
                            Manual de usuario
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-[10px] font-semibold text-gray-400">
                            3 registros
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-[10px] font-semibold text-gray-400">
                            Lectura paso a paso
                        </span>
                    </div>

                    <h1 class="mt-4 text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                        Planificación semanal
                    </h1>
                    <p class="mt-2 max-w-3xl text-base leading-7 text-gray-300">
                        Aprende a llenar el plan de la semana en tres partes:
                        <strong class="text-cyan-300">el plan</strong>,
                        <strong class="text-amber-300">las estrategias</strong> y
                        <strong class="text-purple-300">los resúmenes por área</strong>.
                        No necesitas memorizar nada. Lee un paso, hazlo en el sistema y vuelve.
                    </p>

                    <div class="mt-4 flex flex-wrap gap-3 text-xs text-gray-400">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Tiempo estimado: 20 minutos
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Puedes guardar y continuar después
                        </span>
                    </div>
                </div>
            </header>

            {{-- ═══════════════════════════════════════════════════════════
                 CUERPO: ÍNDICE + CONTENIDO
                 ═══════════════════════════════════════════════════════════ --}}
            <div x-data="{ showToc: true }">
                {{-- Botón para ocultar o mostrar el índice lateral. --}}
                <div class="mb-3 flex justify-end">
                    <button type="button"
                        x-on:click="showToc = !showToc"
                        aria-expanded="true"
                        :aria-expanded="showToc ? 'true' : 'false'"
                        aria-controls="manual-toc"
                        class="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-gray-300 hover:border-cyan-500/30 hover:bg-cyan-500/10 hover:text-cyan-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                        <span x-text="showToc ? 'Ocultar índice' : 'Mostrar índice'">Ocultar índice</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                    {{-- Índice --}}
                    <aside id="manual-toc" x-show="showToc" class="mb-6 lg:mb-0 lg:col-span-1 lg:sticky lg:top-24 lg:self-start">
                    <nav class="rounded-2xl border border-white/10 bg-gray-900/60 p-4" aria-label="Contenido del manual">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Contenido</p>
                        <ol class="mt-3 grid grid-cols-2 gap-1.5 sm:grid-cols-3 lg:block lg:space-y-1">
                            <li><a href="#para-que-sirve" class="block rounded-lg px-2.5 py-1.5 text-xs text-gray-400 hover:bg-white/5 hover:text-cyan-300">Para qué sirve</a></li>
                            <li><a href="#antes-de-empezar" class="block rounded-lg px-2.5 py-1.5 text-xs text-gray-400 hover:bg-white/5 hover:text-cyan-300">Antes de empezar</a></li>
                            <li><a href="#registro-plan" class="block rounded-lg px-2.5 py-1.5 text-xs text-gray-400 hover:bg-white/5 hover:text-cyan-300">1. Registro del plan</a></li>
                            <li><a href="#registro-estrategias" class="block rounded-lg px-2.5 py-1.5 text-xs text-gray-400 hover:bg-white/5 hover:text-amber-300">2. Registro de estrategias</a></li>
                            <li><a href="#registro-resumenes" class="block rounded-lg px-2.5 py-1.5 text-xs text-gray-400 hover:bg-white/5 hover:text-purple-300">3. Registro de resúmenes</a></li>
                            <li><a href="#imprimir" class="block rounded-lg px-2.5 py-1.5 text-xs text-gray-400 hover:bg-white/5 hover:text-emerald-300">Imprimir</a></li>
                            <li><a href="#ruta-rapida" class="block rounded-lg px-2.5 py-1.5 text-xs text-gray-400 hover:bg-white/5 hover:text-cyan-300">Ruta rápida</a></li>
                            <li><a href="#problemas" class="block rounded-lg px-2.5 py-1.5 text-xs text-gray-400 hover:bg-white/5 hover:text-rose-300">Si algo sale mal</a></li>
                        </ol>
                    </nav>

                    <a href="{{ route('inicials.eiplanningwks.index') }}"
                        class="mt-3 flex items-center justify-center gap-2 rounded-xl border border-cyan-500/25 bg-cyan-500/10 px-3 py-2 text-xs font-semibold text-cyan-200 hover:bg-cyan-500/20 transition-colors">
                        Ir al módulo
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </aside>

                    <div class="space-y-8 lg:col-span-4" :style="showToc ? '' : 'grid-column: 1 / -1'">

                    {{-- ═══════════════════════════════════════════════════
                         PARA QUÉ SIRVE
                         ═══════════════════════════════════════════════════ --}}
                    <section id="para-que-sirve" class="scroll-mt-24 rounded-2xl border border-white/10 bg-gray-900/60 p-5 sm:p-7">
                        <h2 class="text-lg font-bold text-white">¿Para qué sirve este manual?</h2>
                        <p class="mt-2 text-base leading-7 text-gray-300">
                            La planificación semanal es el documento donde organizas una semana de trabajo con tu grupo.
                            Se arma en <strong class="text-white">tres registros</strong>. Cada registro guarda una parte distinta.
                            Al final, el sistema junta las tres partes y las imprime.
                        </p>

                        <figure class="manual-figure mt-5">
                            <p class="mb-2 text-[10px] text-gray-500 lg:hidden">Desliza el dibujo para verlo completo →</p>
                            <div class="overflow-x-auto rounded-xl border border-white/10 bg-gray-950/60 p-3">
                                <svg viewBox="0 0 920 300" class="manual-diagram w-full h-auto" role="img" aria-labelledby="mapa-manual-title mapa-manual-desc">
                                    <title id="mapa-manual-title">Los tres registros de la planificación semanal</title>
                                    <desc id="mapa-manual-desc">Primero se registra el plan, después las estrategias y al final los resúmenes por área. Luego se imprime el documento completo.</desc>
                                    <defs>
                                        <marker id="flecha-mapa" markerWidth="10" markerHeight="10" refX="8" refY="3" orient="auto" markerUnits="strokeWidth">
                                            <path d="M0,0 L0,6 L9,3 z" fill="#64748b" />
                                        </marker>
                                    </defs>

                                    {{-- Tarjeta 1 --}}
                                    <rect x="25" y="45" width="270" height="155" rx="16" fill="#0f172a" stroke="#22d3ee" stroke-width="1.5" />
                                    <circle cx="52" cy="72" r="15" fill="#22d3ee" />
                                    <text x="52" y="77" text-anchor="middle" font-family="ui-sans-serif, system-ui, sans-serif" font-size="14" font-weight="700" fill="#082f49">1</text>
                                    <text x="78" y="78" font-family="ui-sans-serif, system-ui, sans-serif" font-size="19" font-weight="700" fill="#e2e8f0">Plan semanal</text>
                                    <text x="45" y="105" font-family="ui-sans-serif, system-ui, sans-serif" font-size="13" fill="#94a3b8">La ficha de la semana</text>
                                    <text x="45" y="132" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#cbd5e1">• Nivel y sección</text>
                                    <text x="45" y="153" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#cbd5e1">• Fechas y semanas</text>
                                    <text x="45" y="174" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#cbd5e1">• Diagnóstico del grupo</text>

                                    {{-- Tarjeta 2 --}}
                                    <rect x="325" y="45" width="270" height="155" rx="16" fill="#0f172a" stroke="#fbbf24" stroke-width="1.5" />
                                    <circle cx="352" cy="72" r="15" fill="#fbbf24" />
                                    <text x="352" y="77" text-anchor="middle" font-family="ui-sans-serif, system-ui, sans-serif" font-size="14" font-weight="700" fill="#451a03">2</text>
                                    <text x="378" y="78" font-family="ui-sans-serif, system-ui, sans-serif" font-size="19" font-weight="700" fill="#e2e8f0">Estrategias</text>
                                    <text x="345" y="105" font-family="ui-sans-serif, system-ui, sans-serif" font-size="13" fill="#94a3b8">Qué haces en cada momento</text>
                                    <text x="345" y="132" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#cbd5e1">• 5 días de la semana</text>
                                    <text x="345" y="153" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#cbd5e1">• 10 momentos de la rutina</text>
                                    <text x="345" y="174" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#cbd5e1">• 50 celdas para escribir</text>

                                    {{-- Tarjeta 3 --}}
                                    <rect x="625" y="45" width="270" height="155" rx="16" fill="#0f172a" stroke="#c084fc" stroke-width="1.5" />
                                    <circle cx="652" cy="72" r="15" fill="#c084fc" />
                                    <text x="652" y="77" text-anchor="middle" font-family="ui-sans-serif, system-ui, sans-serif" font-size="14" font-weight="700" fill="#3b0764">3</text>
                                    <text x="678" y="78" font-family="ui-sans-serif, system-ui, sans-serif" font-size="19" font-weight="700" fill="#e2e8f0">Resúmenes</text>
                                    <text x="645" y="105" font-family="ui-sans-serif, system-ui, sans-serif" font-size="13" fill="#94a3b8">Lo que trabajas por área</text>
                                    <text x="645" y="132" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#cbd5e1">• Componente y objetivo</text>
                                    <text x="645" y="153" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#cbd5e1">• Aprendizaje esperado</text>
                                    <text x="645" y="174" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#cbd5e1">• Indicadores</text>

                                    {{-- Flechas --}}
                                    <line x1="295" y1="122" x2="320" y2="122" stroke="#64748b" stroke-width="2.5" marker-end="url(#flecha-mapa)" />
                                    <line x1="595" y1="122" x2="620" y2="122" stroke="#64748b" stroke-width="2.5" marker-end="url(#flecha-mapa)" />

                                    {{-- Impresión --}}
                                    <rect x="325" y="235" width="270" height="42" rx="12" fill="#052e2b" stroke="#34d399" stroke-width="1.5" />
                                    <text x="460" y="261" text-anchor="middle" font-family="ui-sans-serif, system-ui, sans-serif" font-size="14" font-weight="700" fill="#a7f3d0">Imprimir el plan completo</text>
                                    <path d="M760 200 L760 256 L600 256" fill="none" stroke="#34d399" stroke-width="2" marker-end="url(#flecha-mapa)" />

                                    <text x="25" y="25" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#64748b">Una semana se arma en tres registros, en este orden:</text>
                                </svg>
                            </div>
                            <figcaption class="mt-2 text-xs text-gray-500">
                                Primero el plan, después las estrategias y al final los resúmenes. Si haces las partes en otro orden, no se pierde nada: cada parte se guarda por separado.
                            </figcaption>
                        </figure>
                    </section>

                    {{-- ═══════════════════════════════════════════════════
                         ANTES DE EMPEZAR
                         ═══════════════════════════════════════════════════ --}}
                    <section id="antes-de-empezar" class="scroll-mt-24 rounded-2xl border border-white/10 bg-gray-900/60 p-5 sm:p-7">
                        <h2 class="text-lg font-bold text-white">Antes de empezar</h2>
                        <p class="mt-2 text-base leading-7 text-gray-300">
                            Prepara estos datos. Si te falta alguno, puedes empezar y completarlo después.
                        </p>

                        <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                            @foreach ([
                                'El nivel y la sección de tu grupo.',
                                'Las fechas de inicio y de culminación de la semana.',
                                'El diagnóstico: qué saben, qué les interesa y qué necesitan tus niños.',
                                'Si vas a vincular un proyecto de aula, tenlo creado.',
                                'Revisa que tu carga académica tenga las áreas de aprendizaje.',
                                'Un lugar tranquilo para leer y escribir sin prisa.',
                            ] as $item)
                                <li class="flex items-start gap-2.5 rounded-xl border border-white/10 bg-white/5 p-3">
                                    <span class="mt-0.5 shrink-0 w-5 h-5 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 flex items-center justify-center">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>
                                    <span class="text-sm leading-6 text-gray-300">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-5 rounded-xl border border-amber-500/25 bg-amber-500/5 p-4">
                            <p class="text-sm leading-6 text-amber-100/90">
                                <strong class="text-amber-300">No tienes que llenar todo hoy.</strong>
                                Guarda el plan y vuelve después a las estrategias y a los resúmenes. Lo que ya guardaste no se pierde.
                            </p>
                        </div>

                        <h3 class="mt-7 text-sm font-bold uppercase tracking-widest text-gray-500">Palabras que vas a ver</h3>
                        <dl class="mt-3 grid gap-3 sm:grid-cols-2">
                            @foreach ([
                                'Plan semanal' => 'La ficha que identifica la semana: grupo, fechas y diagnóstico.',
                                'Estrategia' => 'Lo que harás con los niños en un momento del día.',
                                'Momento de la rutina' => 'Cada parte del día: recibimiento, desayuno, trabajo libre, despedida…',
                                'Área de aprendizaje' => 'La materia o área que trabajas. Ejemplo: Lenguaje oral y escrito.',
                                'Resumen por área' => 'Una ficha breve con el objetivo, el aprendizaje esperado y los indicadores de un área.',
                                'Diagnóstico' => 'Lo que observas del grupo antes de planificar: intereses, necesidades y saberes.',
                            ] as $termino => $definicion)
                                <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                    <dt class="text-sm font-semibold text-cyan-200">{{ $termino }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-400">{{ $definicion }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>

                    {{-- ═══════════════════════════════════════════════════
                         REGISTRO 1 · PLAN
                         ═══════════════════════════════════════════════════ --}}
                    <section id="registro-plan" class="scroll-mt-24 rounded-2xl border border-cyan-500/20 bg-gray-900/60 p-5 sm:p-7">
                        <div class="flex items-center gap-3">
                            <span class="shrink-0 w-10 h-10 rounded-xl bg-cyan-500/15 border border-cyan-500/30 text-cyan-300 flex items-center justify-center text-lg font-extrabold">1</span>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-cyan-400">Primer registro</p>
                                <h2 class="text-lg font-bold text-white">Registro del plan semanal</h2>
                            </div>
                        </div>

                        <p class="mt-3 text-base leading-7 text-gray-300">
                            Es la ficha de identificación de la semana. Aquí dices <strong class="text-white">para quién</strong> es el plan,
                            <strong class="text-white">cuándo</strong> se trabaja y <strong class="text-white">qué observas</strong> en el grupo.
                            Sin este registro no hay dónde guardar las estrategias ni los resúmenes.
                        </p>

                        <figure class="manual-figure mt-5">
                            <p class="mb-2 text-[10px] text-gray-500 lg:hidden">Desliza el dibujo para verlo completo →</p>
                            <div class="overflow-x-auto rounded-xl border border-white/10 bg-gray-950/60 p-3">
                                <svg viewBox="0 0 920 520" class="manual-diagram w-full h-auto" role="img" aria-labelledby="form-manual-title form-manual-desc">
                                    <title id="form-manual-title">Formulario del plan semanal</title>
                                    <desc id="form-manual-desc">El formulario pide nivel, sección, área opcional, fechas, cantidad de semanas, proyecto opcional, diagnóstico y observación. A la derecha, una leyenda con letras identifica cada campo. Al final tiene el botón Guardar plan.</desc>
                                    <rect x="20" y="20" width="880" height="480" rx="18" fill="#0b1220" stroke="#334155" />
                                    <rect x="20" y="20" width="880" height="52" rx="18" fill="#111c2e" />
                                    <text x="45" y="53" font-family="ui-sans-serif, system-ui, sans-serif" font-size="18" font-weight="700" fill="#e2e8f0">Nueva planificación semanal</text>
                                    <text x="850" y="53" text-anchor="end" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#64748b">Los campos con * son obligatorios</text>

                                    {{-- Fila 1 --}}
                                    <text x="50" y="105" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#94a3b8">Nivel *</text>
                                    <rect x="50" y="113" width="190" height="42" rx="8" fill="#0f172a" stroke="#334155" />
                                    <text x="64" y="139" font-family="ui-sans-serif, system-ui, sans-serif" font-size="14" fill="#cbd5e1">Preescolar 4 años</text>

                                    <text x="260" y="105" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#94a3b8">Sección *</text>
                                    <rect x="260" y="113" width="190" height="42" rx="8" fill="#0f172a" stroke="#334155" />
                                    <text x="274" y="139" font-family="ui-sans-serif, system-ui, sans-serif" font-size="14" fill="#cbd5e1">B</text>

                                    <text x="470" y="105" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#94a3b8">Área de aprendizaje (opcional)</text>
                                    <rect x="470" y="113" width="190" height="42" rx="8" fill="#0f172a" stroke="#334155" />
                                    <text x="484" y="139" font-family="ui-sans-serif, system-ui, sans-serif" font-size="14" fill="#64748b">Sin área vinculada</text>

                                    {{-- Fila 2 --}}
                                    <text x="50" y="190" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#94a3b8">Fecha de inicio *</text>
                                    <rect x="50" y="198" width="190" height="42" rx="8" fill="#0f172a" stroke="#334155" />
                                    <text x="64" y="224" font-family="ui-sans-serif, system-ui, sans-serif" font-size="14" fill="#cbd5e1">05/10/2026</text>

                                    <text x="260" y="190" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#94a3b8">Fecha de culminación *</text>
                                    <rect x="260" y="198" width="190" height="42" rx="8" fill="#0f172a" stroke="#334155" />
                                    <text x="274" y="224" font-family="ui-sans-serif, system-ui, sans-serif" font-size="14" fill="#cbd5e1">09/10/2026</text>

                                    <text x="470" y="190" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#94a3b8">Cantidad de semanas *</text>
                                    <rect x="470" y="198" width="190" height="42" rx="8" fill="#0f172a" stroke="#334155" />
                                    <text x="484" y="224" font-family="ui-sans-serif, system-ui, sans-serif" font-size="14" fill="#cbd5e1">1</text>

                                    {{-- Fila 3 --}}
                                    <text x="50" y="275" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#94a3b8">Proyecto de aula vinculado (opcional)</text>
                                    <rect x="50" y="283" width="610" height="42" rx="8" fill="#0f172a" stroke="#334155" />
                                    <text x="64" y="309" font-family="ui-sans-serif, system-ui, sans-serif" font-size="14" fill="#64748b">Sin proyecto vinculado</text>

                                    {{-- Diagnóstico --}}
                                    <text x="50" y="360" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#94a3b8">Diagnóstico inicial *</text>
                                    <rect x="50" y="368" width="610" height="60" rx="8" fill="#0f172a" stroke="#334155" />
                                    <text x="64" y="392" font-family="ui-sans-serif, system-ui, sans-serif" font-size="13" fill="#94a3b8">Los niños muestran interés por los animales de la comunidad…</text>
                                    <text x="64" y="414" font-family="ui-sans-serif, system-ui, sans-serif" font-size="11" fill="#475569">Mínimo 10 caracteres. Se recomienda escribir 50 o más.</text>

                                    {{-- Observación + botón --}}
                                    <text x="50" y="448" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#94a3b8">Observación (opcional)</text>
                                    <rect x="50" y="456" width="400" height="34" rx="8" fill="#0f172a" stroke="#334155" />
                                    <rect x="470" y="448" width="190" height="42" rx="8" fill="#0891b2" />
                                    <text x="565" y="474" text-anchor="middle" font-family="ui-sans-serif, system-ui, sans-serif" font-size="14" font-weight="700" fill="#ecfeff">Guardar plan</text>

                                    {{-- Leyenda de campos, a la derecha del formulario --}}
                                    <rect x="680" y="80" width="200" height="390" rx="12" fill="#0f172a" stroke="#334155" />
                                    <text x="696" y="106" font-family="ui-sans-serif, system-ui, sans-serif" font-size="10" font-weight="700" fill="#67e8f9" letter-spacing="1">LEYENDA</text>
                                    <text x="696" y="124" font-family="ui-sans-serif, system-ui, sans-serif" font-size="10" fill="#64748b">Identifica cada campo</text>
                                    @foreach ([
                                        ['A', 'Nivel'],
                                        ['B', 'Sección'],
                                        ['C', 'Área de aprendizaje'],
                                        ['D', 'Fecha de inicio'],
                                        ['E', 'Fecha de culminación'],
                                        ['F', 'Cantidad de semanas'],
                                        ['G', 'Proyecto de aula'],
                                        ['H', 'Diagnóstico inicial'],
                                        ['I', 'Observación'],
                                        ['J', 'Guardar plan'],
                                    ] as [$numero, $campo])
                                        <circle cx="704" cy="{{ 154 + ($loop->iteration - 1) * 26 }}" r="7.5" fill="#0b1220" stroke="#155e75" stroke-width="1" />
                                        <text x="704" y="{{ 158 + ($loop->iteration - 1) * 26 }}" text-anchor="middle" font-family="ui-sans-serif, system-ui, sans-serif" font-size="8.5" font-weight="600" fill="#67e8f9">{{ $numero }}</text>
                                        <text x="720" y="{{ 158 + ($loop->iteration - 1) * 26 }}" font-family="ui-sans-serif, system-ui, sans-serif" font-size="10.5" fill="#cbd5e1">{{ $campo }}</text>
                                    @endforeach
                                    <text x="696" y="444" font-family="ui-sans-serif, system-ui, sans-serif" font-size="9" fill="#475569">Las letras señalan cada campo.</text>

                                    {{-- Números de los campos --}}
                                    @foreach ([
                                        [36, 132, 'A'],
                                        [246, 132, 'B'],
                                        [456, 132, 'C'],
                                        [36, 217, 'D'],
                                        [246, 217, 'E'],
                                        [456, 217, 'F'],
                                        [36, 302, 'G'],
                                        [36, 387, 'H'],
                                        [36, 467, 'I'],
                                        [456, 467, 'J'],
                                    ] as [$x, $y, $n])
                                        <circle cx="{{ $x }}" cy="{{ $y }}" r="10" fill="#0b1220" stroke="#155e75" stroke-width="1" />
                                        <text x="{{ $x }}" y="{{ $y + 3.5 }}" text-anchor="middle" font-family="ui-sans-serif, system-ui, sans-serif" font-size="9.5" font-weight="600" fill="#67e8f9">{{ $n }}</text>
                                    @endforeach
                                </svg>
                            </div>
                            <figcaption class="mt-2 text-xs text-gray-500">
                                La leyenda de la derecha identifica cada campo con una letra. La sección se activa cuando eliges el nivel.
                            </figcaption>
                        </figure>

                        <div class="manual-plan-body manual-timeline-block mt-6">
                        <div class="manual-timeline-head">
                            <div>
                                <p class="manual-timeline-kicker">Recorrido completo · 10 pasos</p>
                                <h3 class="manual-timeline-title">Paso a paso</h3>
                            </div>
                        </div>
                        <p class="manual-timeline-subtitle">
                            Los 10 pasos del plan semanal, en orden. Cada paso incluye una vista rápida del componente que usarás en el sistema.
                        </p>

                        <ol class="manual-timeline">
                            @foreach ([
                                'Entra a <strong class="text-white">Educación Inicial</strong> y abre <strong class="text-white">Planificación semanal</strong>. También puedes usar la tarjeta de la portada.',
                                'Pulsa el botón <strong class="text-white">Nueva planificación</strong>.',
                                'Elige el <strong class="text-white">Nivel</strong>. Después se activa la <strong class="text-white">Sección</strong>; elige la tuya.',
                                'Si quieres, elige un <strong class="text-white">Área de aprendizaje</strong>. Si no, deja “Sin área vinculada”.',
                                'Escribe la <strong class="text-white">Fecha de inicio</strong> y la <strong class="text-white">Fecha de culminación</strong>. La segunda no puede ser anterior a la primera.',
                                'Escribe la <strong class="text-white">Cantidad de semanas</strong>. Es un número manual; 1 es válido.',
                                'Si quieres, elige un <strong class="text-white">Proyecto de aula vinculado</strong>.',
                                'Escribe el <strong class="text-white">Diagnóstico inicial</strong>. Mínimo 10 caracteres; se recomienda 50 o más.',
                                'Si quieres, agrega una <strong class="text-white">Observación</strong>.',
                                'Pulsa <strong class="text-white">Guardar plan</strong>.',
                            ] as $paso)
                                <li class="manual-timeline-item">
                                    <div class="manual-timeline-marker" aria-hidden="true">
                                        <span class="manual-timeline-dot">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                    <div class="manual-step">
                                    <div class="manual-step-copy">
                                        <span class="manual-step-text">{!! $paso !!}</span>
                                    </div>

                                    <div class="manual-step-visual" aria-hidden="true">
                                        @switch($loop->iteration)
                                            @case(1)
                                                <div class="manual-ui">
                                                    <div class="manual-ui-top">
                                                        <span class="manual-ui-title">Educación Inicial</span>
                                                        <span class="manual-ui-badge">Portada</span>
                                                    </div>
                                                    <div class="manual-ui-crumb">
                                                        <span>Casos de uso</span>
                                                        <span class="manual-ui-chevron">›</span>
                                                        <span class="manual-ui-crumb-current">Planificación semanal</span>
                                                    </div>
                                                    <div class="manual-ui-field" style="margin-top: 6px;">
                                                        <span>Planificación semanal</span>
                                                        <span class="manual-ui-link">Abrir ›</span>
                                                    </div>
                                                </div>
                                                @break

                                            @case(2)
                                                <div class="manual-ui">
                                                    <div class="manual-ui-top">
                                                        <span class="manual-ui-title">Mis planes</span>
                                                        <span class="manual-ui-badge">Semanal</span>
                                                    </div>
                                                    <div class="manual-ui-empty">Aún no hay planes</div>
                                                    <div class="manual-ui-btn manual-ui-btn--full" style="margin-top: 6px;">+ Nueva planificación</div>
                                                </div>
                                                @break

                                            @case(3)
                                                <div class="manual-ui">
                                                    <div class="manual-ui-grid manual-ui-grid--2">
                                                        <div>
                                                            <span class="manual-ui-label">Nivel *</span>
                                                            <div class="manual-ui-field"><span>Preescolar 4 años</span><span class="manual-ui-chevron">▾</span></div>
                                                        </div>
                                                        <div>
                                                            <span class="manual-ui-label">Sección *</span>
                                                            <div class="manual-ui-field"><span>B</span><span class="manual-ui-chevron">▾</span></div>
                                                        </div>
                                                    </div>
                                                    <div class="manual-ui-hint"><span class="manual-ui-dot"></span> La sección se activa después del nivel.</div>
                                                </div>
                                                @break

                                            @case(4)
                                                <div class="manual-ui">
                                                    <div class="manual-ui-top">
                                                        <span class="manual-ui-title">Área de aprendizaje</span>
                                                        <span class="manual-ui-badge">Opcional</span>
                                                    </div>
                                                    <div class="manual-ui-field manual-ui-field--muted">
                                                        <span>Sin área vinculada</span>
                                                        <span class="manual-ui-chevron">▾</span>
                                                    </div>
                                                    <div class="manual-ui-hint">Solo las áreas de tu carga académica.</div>
                                                </div>
                                                @break

                                            @case(5)
                                                <div class="manual-ui">
                                                    <div class="manual-ui-grid manual-ui-grid--2">
                                                        <div>
                                                            <span class="manual-ui-label">Fecha de inicio *</span>
                                                            <div class="manual-ui-field">05/10/2026</div>
                                                        </div>
                                                        <div>
                                                            <span class="manual-ui-label">Culminación *</span>
                                                            <div class="manual-ui-field">09/10/2026</div>
                                                        </div>
                                                    </div>
                                                    <div class="manual-ui-hint manual-ui-hint--ok"><span class="manual-ui-dot"></span> La fecha final no puede ser anterior.</div>
                                                </div>
                                                @break

                                            @case(6)
                                                <div class="manual-ui">
                                                    <div class="manual-ui-top">
                                                        <span class="manual-ui-title">Cantidad de semanas *</span>
                                                        <span class="manual-ui-badge">Manual</span>
                                                    </div>
                                                    <div class="manual-ui-field"><span>1</span><span class="manual-ui-chevron">mín. 1</span></div>
                                                    <div class="manual-ui-hint">Es un número manual; 1 es válido.</div>
                                                </div>
                                                @break

                                            @case(7)
                                                <div class="manual-ui">
                                                    <div class="manual-ui-top">
                                                        <span class="manual-ui-title">Proyecto de aula</span>
                                                        <span class="manual-ui-badge">Opcional</span>
                                                    </div>
                                                    <div class="manual-ui-field manual-ui-field--muted">
                                                        <span>Sin proyecto vinculado</span>
                                                        <span class="manual-ui-chevron">▾</span>
                                                    </div>
                                                    <div class="manual-ui-hint">Puedes dejarlo vacío.</div>
                                                </div>
                                                @break

                                            @case(8)
                                                <div class="manual-ui">
                                                    <div class="manual-ui-top">
                                                        <span class="manual-ui-title">Diagnóstico inicial *</span>
                                                        <span class="manual-ui-badge">Mín. 10</span>
                                                    </div>
                                                    <div class="manual-ui-textarea">Los niños muestran interés por los animales de la comunidad…</div>
                                                    <div class="manual-ui-hint">Se recomiendan 50 o más para la revisión.</div>
                                                </div>
                                                @break

                                            @case(9)
                                                <div class="manual-ui">
                                                    <div class="manual-ui-top">
                                                        <span class="manual-ui-title">Observación</span>
                                                        <span class="manual-ui-badge">Opcional</span>
                                                    </div>
                                                    <div class="manual-ui-textarea" style="color: #64748b;">Agrega una nota para el coordinador…</div>
                                                </div>
                                                @break

                                            @case(10)
                                                <div class="manual-ui">
                                                    <div class="manual-ui-top">
                                                        <span class="manual-ui-title">Plan listo</span>
                                                        <span class="manual-ui-badge">Último paso</span>
                                                    </div>
                                                    <div class="manual-ui-field"><span>Preescolar 4 años · B</span><span class="manual-ui-chevron">Semana 1</span></div>
                                                    <div class="manual-ui-btn manual-ui-btn--full" style="margin-top: 6px;">Guardar plan</div>
                                                    <div class="manual-ui-hint manual-ui-hint--ok"><span class="manual-ui-dot"></span> El plan queda guardado.</div>
                                                </div>
                                                @break
                                        @endswitch
                                    </div>
                                    </div>
                                </li>
                            @endforeach
                        </ol>

                        <div class="mt-5 rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-400">Ejemplo</p>
                            <p class="mt-1 text-sm leading-6 text-emerald-100/90">
                                <strong>Nivel:</strong> Preescolar 4 años · <strong>Sección:</strong> B ·
                                <strong>Fechas:</strong> 05/10/2026 a 09/10/2026 · <strong>Semanas:</strong> 1 ·
                                <strong>Diagnóstico:</strong> “Los niños muestran interés por los animales de la comunidad.
                                Reconocen algunos nombres y disfrutan imitarlos.”
                            </p>
                        </div>

                        <h3 class="mt-6 text-sm font-bold uppercase tracking-widest text-gray-500">Revisa antes de guardar</h3>
                        <ul class="mt-3 grid gap-2 sm:grid-cols-2">
                            @foreach ([
                                'Elegiste nivel y sección.',
                                'La fecha de culminación es igual o posterior a la de inicio.',
                                'La cantidad de semanas es 1 o más.',
                                'El diagnóstico tiene al menos 10 caracteres.',
                            ] as $item)
                                <li class="flex items-start gap-2 text-sm text-gray-300">
                                    <svg class="mt-1 w-4 h-4 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>

                        <details class="group mt-5 rounded-xl border border-white/10 bg-white/5">
                            <summary class="cursor-pointer list-none px-4 py-3 text-sm font-semibold text-gray-200 flex items-center justify-between gap-3">
                                Errores comunes en este registro
                                <svg class="w-4 h-4 text-gray-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </summary>
                            <div class="px-4 pb-4 space-y-3 text-sm leading-6 text-gray-400">
                                <p><strong class="text-gray-200">“No me deja guardar”.</strong> Revisa que no falte un campo con asterisco (*), que la fecha final no sea anterior a la inicial y que las semanas sean 1 o más.</p>
                                <p><strong class="text-gray-200">“No aparece mi área”.</strong> Primero elige el nivel. Solo aparecen las áreas de tu carga académica en el lapso. Si sigue sin aparecer, consulta con Coordinación.</p>
                                <p><strong class="text-gray-200">“Quiero cambiar el plan”.</strong> En la tarjeta del plan, pulsa el lápiz. Cambia lo que necesites y vuelve a guardar. Las estrategias y los resúmenes no se borran.</p>
                                <p><strong class="text-gray-200">“Quiero borrar el plan”.</strong> Pulsa el basurero. Se borran también sus estrategias y resúmenes. No se puede deshacer.</p>
                            </div>
                        </details>

                        <div class="mt-4 rounded-xl border border-cyan-500/20 bg-cyan-500/5 p-4">
                            <p class="text-sm leading-6 text-cyan-100/90">
                                <strong class="text-cyan-300">Truco:</strong> si ya tienes un plan del período anterior,
                                usa <strong>Importar de s2526</strong> y edítalo. Así no empiezas de cero.
                            </p>
                        </div>

                        <h3 class="mt-6 text-sm font-bold uppercase tracking-widest text-gray-500">Cómo clonar un plan</h3>
                        <p class="mt-2 text-sm leading-6 text-gray-400">
                            Clonar sirve para repetir una semana que ya tienes armada, por ejemplo para la
                            semana siguiente o para ajustar el grupo después. La copia conserva el nivel, la sección,
                            el diagnóstico, las estrategias y los resúmenes del plan original. Las fechas se mueven
                            a la primera semana libre posterior para no duplicar la semana del grupo.
                        </p>
                        <ol class="mt-3 space-y-2 text-sm leading-6 text-gray-300">
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 shrink-0 w-6 h-6 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 flex items-center justify-center text-[11px] font-bold">1</span>
                                <span>En la tarjeta o en la tabla del plan, abre el menú <strong class="text-white">Más acciones</strong>.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 shrink-0 w-6 h-6 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 flex items-center justify-center text-[11px] font-bold">2</span>
                                <span>Pulsa <strong class="text-emerald-300">Clonar plan</strong> y revisa cuántas estrategias y resúmenes se copiarán, además de la semana libre que se asignará.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 shrink-0 w-6 h-6 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 flex items-center justify-center text-[11px] font-bold">3</span>
                                <span>Confirma con <strong class="text-white">Clonar</strong>. La copia aparece en tu listado y queda asignada a tu usuario.</span>
                            </li>
                        </ol>
                        <div class="mt-4 rounded-xl border border-emerald-500/25 bg-emerald-500/5 p-4">
                            <p class="text-sm leading-6 text-emerald-100/90">
                                <strong class="text-emerald-300">Importante:</strong> la copia es independiente y
                                se coloca en la primera semana libre. Si después editas la copia, el plan original
                                no cambia. El sistema guarda la copia completa en una sola operación: si algo falla,
                                no queda un plan a medias.
                            </p>
                        </div>
                        </div>
                    </section>

                    {{-- ═══════════════════════════════════════════════════
                         REGISTRO 2 · ESTRATEGIAS
                         ═══════════════════════════════════════════════════ --}}
                    <section id="registro-estrategias" class="scroll-mt-24 rounded-2xl border border-amber-500/20 bg-gray-900/60 p-5 sm:p-7">
                        <div class="flex items-center gap-3">
                            <span class="shrink-0 w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-300 flex items-center justify-center text-lg font-extrabold">2</span>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-amber-400">Segundo registro</p>
                                <h2 class="text-lg font-bold text-white">Registro de las estrategias</h2>
                            </div>
                        </div>

                        <p class="mt-3 text-base leading-7 text-gray-300">
                            Aquí escribes <strong class="text-white">qué harás con los niños</strong> en cada momento del día.
                            El sistema organiza la semana en <strong class="text-white">5 días</strong> y <strong class="text-white">10 momentos</strong>.
                            Eso da <strong class="text-white">50 celdas</strong>. Cada celda guarda un texto.
                        </p>

                        <figure class="manual-figure mt-5">
                            <p class="mb-2 text-[10px] text-gray-500 lg:hidden">Desliza el dibujo para verlo completo →</p>
                            <div class="overflow-x-auto rounded-xl border border-white/10 bg-gray-950/60 p-3">
                                <svg viewBox="0 0 920 540" class="manual-diagram w-full h-auto" role="img" aria-labelledby="grid-manual-title grid-manual-desc">
                                    <title id="grid-manual-title">Rejilla de estrategias de cinco días por diez momentos</title>
                                    <desc id="grid-manual-desc">Las columnas son los días de la semana y las filas son los momentos de la rutina. Cada cruce es una celda. El ejemplo resalta la celda de lunes y recibimiento.</desc>

                                    @php
                                        $diasManual = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                                        $momentosManual = [
                                            'Recibimiento',
                                            'Momento cívico',
                                            'Aseo / Desayuno',
                                            'Planificación',
                                            'Trabajo libre',
                                            'Orden y limpieza',
                                            'Intercambio',
                                            'Pequeños grupos',
                                            'Actividades colectivas',
                                            'Despedida',
                                        ];
                                    @endphp

                                    <text x="20" y="28" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#64748b">DÍA →</text>
                                    <text x="20" y="50" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#64748b">MOMENTO ↓</text>

                                    {{-- Cabecera de días --}}
                                    @foreach ($diasManual as $di => $d)
                                        <rect x="{{ 170 + ($di * 130) }}" y="30" width="130" height="30" rx="7" fill="#111c2e" stroke="#334155" />
                                        <text x="{{ 170 + ($di * 130) + 65 }}" y="50" text-anchor="middle" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" font-weight="700" fill="#e2e8f0">{{ $d }}</text>
                                    @endforeach

                                    {{-- Filas de momentos y celdas --}}
                                    @foreach ($momentosManual as $m => $momento)
                                        <text x="12" y="{{ 88 + ($m * 40) + 15 }}" font-family="ui-sans-serif, system-ui, sans-serif" font-size="11" fill="#94a3b8">{{ $momento }}</text>

                                        @foreach ($diasManual as $di => $d)
                                            @php $activa = $m === 0 && $di === 0; @endphp
                                            <rect x="{{ 170 + ($di * 130) }}" y="{{ 70 + ($m * 40) }}" width="130" height="40"
                                                fill="{{ $activa ? '#78350f' : '#0f172a' }}"
                                                stroke="{{ $activa ? '#f59e0b' : '#334155' }}"
                                                stroke-width="{{ $activa ? '2' : '1' }}" />
                                            @if ($activa)
                                                <text x="{{ 170 + ($di * 130) + 65 }}" y="{{ 70 + ($m * 40) + 24 }}" text-anchor="middle" font-family="ui-sans-serif, system-ui, sans-serif" font-size="11" font-weight="700" fill="#fde68a">Ejemplo</text>
                                            @endif
                                        @endforeach
                                    @endforeach

                                    {{-- Leyenda --}}
                                    <rect x="170" y="492" width="16" height="16" rx="3" fill="#78350f" stroke="#f59e0b" />
                                    <text x="194" y="504" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#cbd5e1">Celda activa (la que estás editando)</text>
                                    <rect x="430" y="492" width="16" height="16" rx="3" fill="#0f172a" stroke="#334155" />
                                    <text x="454" y="504" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#cbd5e1">Celda vacía</text>
                                    <text x="620" y="504" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" fill="#64748b">5 días × 10 momentos = 50 celdas</text>
                                </svg>
                            </div>
                            <figcaption class="mt-2 text-xs text-gray-500">
                                Esta es la rejilla completa. En pantalla no ves las 50 celdas a la vez: eliges un día y un momento, y escribes en la celda activa.
                            </figcaption>
                        </figure>

                        {{-- Maqueta del formulario completo con datos de ejemplo --}}
                        <figure class="manual-figure mt-5">
                            <div class="grid items-start gap-3 xl:grid-cols-[minmax(0,1fr)_215px]">
                                <div class="rounded-2xl border border-amber-500/25 bg-gray-950/60 p-3 sm:p-5">
                                <div class="flex flex-wrap items-start justify-between gap-3 border-b border-white/10 pb-3">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-amber-400">Así se ve la ventana con datos de ejemplo</p>
                                        <p class="mt-1 text-base font-semibold text-white">Estrategias · día × momento</p>
                                    </div>
                                    <span class="rounded-full border border-amber-500/30 bg-amber-500/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-amber-300">Ejemplo</span>
                                </div>

                                {{-- Progreso --}}
                                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-2.5">
                                    <p class="text-xs text-gray-300"><span class="font-bold text-white">5</span> de 50 celdas guardadas</p>
                                    <p class="text-xs font-medium text-emerald-400">3 días con contenido</p>
                                </div>

                                {{-- Días --}}
                                <div class="mt-3 flex flex-wrap gap-1.5 border-b border-white/10 pb-2" role="group" aria-label="Días de la semana">
                                    @foreach ([
                                        ['Lunes', '3/10', true, true],
                                        ['Martes', '1/10', false, true],
                                        ['Miércoles', '1/10', false, true],
                                        ['Jueves', '0/10', false, false],
                                        ['Viernes', '0/10', false, false],
                                    ] as [$dia, $progreso, $activo, $conContenido])
                                        <span class="rounded-lg border px-2.5 py-1.5 text-[11px] font-medium {{ $activo ? 'border-cyan-500/40 bg-cyan-500/20 text-cyan-200' : 'border-white/10 bg-white/5 text-gray-400' }}">
                                            {{ $dia }}
                                            <span class="ml-1 text-[10px] {{ $conContenido ? 'text-emerald-400' : 'text-gray-500' }}">{{ $progreso }}</span>
                                        </span>
                                    @endforeach
                                </div>

                                {{-- Momentos --}}
                                <div class="mt-3 flex flex-wrap gap-1.5" role="group" aria-label="Momentos de la rutina">
                                    @foreach ([
                                        ['Recibimiento', true, true],
                                        ['Momento cívico', false, false],
                                        ['Aseo / Desayuno', false, false],
                                        ['Planificación', false, true],
                                        ['Trabajo libre', false, false],
                                        ['Orden y limpieza', false, false],
                                        ['Intercambio', false, false],
                                        ['Pequeños grupos', false, false],
                                        ['Actividades colectivas', false, false],
                                        ['Despedida', false, true],
                                    ] as [$momento, $activo, $guardado])
                                        <span class="rounded-full border px-2.5 py-1 text-[11px] {{ $activo ? 'border-amber-500/40 bg-amber-500/20 text-amber-200' : 'border-white/10 bg-white/5 text-gray-400' }}">
                                            {{ $momento }}@if ($guardado)<span class="ml-1 text-emerald-400" aria-label="con contenido">●</span>@endif
                                        </span>
                                    @endforeach
                                </div>

                                {{-- Navegación entre momentos --}}
                                <div class="mt-3 flex items-center justify-between gap-3 text-[11px]">
                                    <span class="rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 text-gray-500 opacity-50">← Anterior</span>
                                    <span class="text-gray-400">Lunes · <span class="font-medium text-amber-300">Recibimiento</span></span>
                                    <span class="rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 text-gray-300">Siguiente →</span>
                                </div>

                                {{-- Celda activa --}}
                                <div class="mt-3 rounded-lg border border-white/10 bg-gray-800/40 p-3.5">
                                    <p class="text-[11px] font-medium text-gray-400">Estrategia · Lunes · Recibimiento</p>
                                    <div class="mt-2 rounded-lg border border-white/10 bg-gray-900/60 px-3 py-2.5 text-sm leading-6 text-gray-200">
                                        Saludo con la canción de los animales. Cada niño dice su animal favorito y lo imita.
                                    </div>
                                    <div class="mt-3 flex flex-wrap items-end justify-between gap-3">
                                        <div>
                                            <p class="text-[11px] text-gray-400">Orden</p>
                                            <p class="mt-1 w-12 rounded-lg border border-white/10 bg-gray-900/60 px-2.5 py-1.5 text-center text-sm text-gray-200">1</p>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="rounded-lg border border-red-500/20 bg-red-500/10 px-3 py-1.5 text-[11px] text-red-300">Eliminar</span>
                                            <span class="rounded-lg bg-amber-600 px-3 py-1.5 text-[11px] font-semibold text-white">Guardar y continuar</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pie del modal --}}
                                <div class="mt-3 flex flex-wrap items-center justify-end gap-2 border-t border-white/10 pt-3">
                                    <span class="rounded-lg border border-red-500/20 bg-red-500/10 px-3 py-1.5 text-[11px] text-red-300">Eliminar todas</span>
                                    <span class="rounded-lg px-3 py-1.5 text-[11px] text-gray-400">Cancelar</span>
                                    <span class="rounded-lg bg-gray-700 px-3 py-1.5 text-[11px] font-medium text-gray-100">Guardar todas</span>
                                </div>
                                </div>

                                {{-- Leyenda de campos, a la derecha de la ventana --}}
                                <aside class="rounded-xl border border-amber-500/20 bg-gray-950/70 p-3.5" aria-label="Leyenda de la ventana">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-amber-400">Leyenda de la ventana</p>
                                    <p class="mt-1 text-[11px] leading-5 text-gray-500">Identifica cada campo y cada acción.</p>

                                    <p class="mt-4 text-[9px] font-bold uppercase tracking-widest text-amber-400/70">Campos</p>
                                    <dl class="mt-2 space-y-2.5 text-[11px] leading-5">
                                        @foreach ([
                                            ['Progreso', 'Celdas guardadas y días con contenido.'],
                                            ['Día', 'Elige el día de la semana.'],
                                            ['Momento', 'Elige la parte de la rutina.'],
                                            ['Estrategia', 'Texto de la celda activa.'],
                                            ['Orden', 'Número opcional para ordenar.'],
                                        ] as [$campo, $texto])
                                            <div>
                                                <dt class="flex items-center gap-2 font-semibold text-gray-200">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                                                    {{ $campo }}
                                                </dt>
                                                <dd class="pl-3.5 text-gray-500">{{ $texto }}</dd>
                                            </div>
                                        @endforeach
                                    </dl>

                                    <p class="mt-4 text-[9px] font-bold uppercase tracking-widest text-amber-400/70">Acciones</p>
                                    <dl class="mt-2 space-y-2.5 text-[11px] leading-5">
                                        @foreach ([
                                            ['Eliminar', 'Borra la celda guardada.'],
                                            ['Eliminar todas', 'Vacía las 50 celdas del plan; conserva cabecera y resúmenes.'],
                                            ['Guardar y continuar', 'Guarda la celda y avanza.'],
                                            ['Guardar todas', 'Guarda todas las celdas.'],
                                            ['Cancelar', 'Cierra sin guardar cambios.'],
                                        ] as [$accion, $texto])
                                            <div>
                                                <dt class="flex items-center gap-2 font-semibold text-gray-200">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                                                    {{ $accion }}
                                                </dt>
                                                <dd class="pl-3.5 text-gray-500">{{ $texto }}</dd>
                                            </div>
                                        @endforeach
                                    </dl>
                                </aside>
                            </div>
                            <figcaption class="mt-2 text-xs text-gray-500">
                                La leyenda de la derecha explica cada campo y cada acción. Ejemplo ilustrativo con el plan de Preescolar 4 años, sección B, semana del 05/10/2026 al 09/10/2026.
                                En una celda nueva no aparece <strong class="text-gray-400">Eliminar</strong>: aparece después de guardar.
                                Los contadores y los puntos verdes cambian con lo que escribes.
                            </figcaption>
                        </figure>

                        <div class="manual-timeline-block manual-timeline--amber mt-6">
                            <div class="manual-timeline-head">
                                <div>
                                    <p class="manual-timeline-kicker">Recorrido completo · 8 pasos</p>
                                    <h3 class="manual-timeline-title">Paso a paso</h3>
                                </div>
                            </div>
                            <p class="manual-timeline-subtitle">
                                Los 8 pasos para registrar tus estrategias, en orden.
                            </p>

                            <ol class="manual-timeline">
                                @foreach ([
                                    'En la tarjeta del plan, pulsa <strong class="text-white">Estrategias</strong>.',
                                    'Arriba elige el <strong class="text-white">día</strong>: Lunes, Martes, Miércoles, Jueves o Viernes. Al lado verás cuántas celdas de ese día tienen texto (por ejemplo, 3/10).',
                                    'Elige el <strong class="text-white">momento de la rutina</strong>. Los botones van en el orden real del día, de Recibimiento a Despedida.',
                                    'Escribe en el cuadro grande. El título te recuerda el día y el momento: “Estrategia · Lunes · Recibimiento”.',
                                    'Si quieres ordenar varias estrategias, escribe un número en <strong class="text-white">Orden</strong>. Es opcional.',
                                    'Pulsa <strong class="text-white">Guardar y continuar</strong>. El sistema guarda esa celda y te lleva al siguiente momento.',
                                    'Repite con los momentos y días que necesites.',
                                    'Al final, pulsa <strong class="text-white">Guardar todas</strong> para guardar de una vez todas las celdas que tengan texto.',
                                ] as $paso)
                                    <li class="manual-timeline-item">
                                        <div class="manual-timeline-marker" aria-hidden="true">
                                            <span class="manual-timeline-dot">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                        <div class="manual-step">
                                            <div class="manual-step-copy">
                                                <span class="manual-step-text">{!! $paso !!}</span>
                                            </div>

                                            <div class="manual-step-visual" aria-hidden="true">
                                                @switch($loop->iteration)
                                                    @case(1)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Plan semanal · Preescolar 4 años B</span>
                                                                <span class="manual-fn-tag">Semana 1</span>
                                                            </div>
                                                            <div class="manual-fn-row">
                                                                <span class="manual-fn-muted">05/10/2026 → 09/10/2026</span>
                                                                <span class="manual-fn-btn">Estrategias</span>
                                                            </div>
                                                            <div class="manual-fn-chips">
                                                                <span class="manual-fn-chip manual-fn-chip--active">Estrategias <span>5/50</span></span>
                                                                <span class="manual-fn-chip">Resúmenes <span>3 áreas</span></span>
                                                            </div>
                                                        </div>
                                                        @break

                                                    @case(2)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Elige el día</span>
                                                                <span class="manual-fn-tag">Lunes · 3/10</span>
                                                            </div>
                                                            <div class="manual-fn-chips">
                                                                <span class="manual-fn-chip manual-fn-chip--active">Lunes <span>3/10</span></span>
                                                                <span class="manual-fn-chip">Martes <span>1/10</span></span>
                                                                <span class="manual-fn-chip">Miércoles <span>1/10</span></span>
                                                                <span class="manual-fn-chip">Jueves <span>0/10</span></span>
                                                                <span class="manual-fn-chip">Viernes <span>0/10</span></span>
                                                            </div>
                                                            <p class="manual-fn-note">El contador indica cuántas celdas de ese día ya tienen texto.</p>
                                                        </div>
                                                        @break

                                                    @case(3)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Elige el momento</span>
                                                                <span class="manual-fn-tag">Recibimiento</span>
                                                            </div>
                                                            <div class="manual-fn-chips">
                                                                <span class="manual-fn-chip manual-fn-chip--active"><span class="manual-fn-chip-dot"></span>Recibimiento</span>
                                                                <span class="manual-fn-chip">Momento cívico</span>
                                                                <span class="manual-fn-chip">Aseo / Desayuno</span>
                                                                <span class="manual-fn-chip">Planificación</span>
                                                                <span class="manual-fn-chip">Trabajo libre</span>
                                                            </div>
                                                            <p class="manual-fn-note">Los momentos siguen el orden real de la rutina, de la mañana a la despedida.</p>
                                                        </div>
                                                        @break

                                                    @case(4)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Estrategia · Lunes · Recibimiento</span>
                                                                <span class="manual-fn-tag">Celda activa</span>
                                                            </div>
                                                            <div class="manual-fn-field">
                                                                <span class="manual-fn-label">Estrategia</span>
                                                                <span class="manual-fn-value">Saludo con la canción de los animales. Cada niño dice su animal favorito y lo imita.</span>
                                                            </div>
                                                            <div class="manual-fn-row">
                                                                <span class="manual-fn-muted">Texto libre · se guarda por celda</span>
                                                                <span class="manual-fn-btn">Guardar y continuar</span>
                                                            </div>
                                                        </div>
                                                        @break

                                                    @case(5)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Ordenar estrategias</span>
                                                                <span class="manual-fn-tag">Opcional</span>
                                                            </div>
                                                            <div class="manual-fn-split">
                                                                <div class="manual-fn-field">
                                                                    <span class="manual-fn-label">Orden</span>
                                                                    <span class="manual-fn-value">1</span>
                                                                </div>
                                                                <div class="manual-fn-field">
                                                                    <span class="manual-fn-label">Uso</span>
                                                                    <span class="manual-fn-value">Ordena varias estrategias del mismo momento.</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @break

                                                    @case(6)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Celda guardada</span>
                                                                <span class="manual-fn-tag">Lunes · Recibimiento</span>
                                                            </div>
                                                            <div class="manual-fn-meter"><span style="width: 10%"></span></div>
                                                            <div class="manual-fn-row">
                                                                <span class="manual-fn-note" style="margin-top: 0;"><span class="manual-fn-check">✓</span> 5 de 50 celdas guardadas</span>
                                                                <span class="manual-fn-btn">Siguiente: Momento cívico →</span>
                                                            </div>
                                                        </div>
                                                        @break

                                                    @case(7)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Avance de la semana</span>
                                                                <span class="manual-fn-tag">3 días con contenido</span>
                                                            </div>
                                                            <div class="manual-fn-grid">
                                                                <div class="manual-fn-cell"><strong>Lunes</strong><span>3/10 celdas</span></div>
                                                                <div class="manual-fn-cell"><strong>Martes</strong><span>1/10 celdas</span></div>
                                                                <div class="manual-fn-cell"><strong>Miércoles</strong><span>1/10 celdas</span></div>
                                                                <div class="manual-fn-cell"><strong>Jueves</strong><span>0/10 celdas</span></div>
                                                                <div class="manual-fn-cell"><strong>Viernes</strong><span>0/10 celdas</span></div>
                                                            </div>
                                                        </div>
                                                        @break

                                                    @case(8)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Guardar todas las celdas</span>
                                                                <span class="manual-fn-tag">5 de 50</span>
                                                            </div>
                                                            <div class="manual-fn-row">
                                                                <span class="manual-fn-muted">Lunes 3 · Martes 1 · Miércoles 1</span>
                                                                <span class="manual-fn-btn">Guardar todas</span>
                                                            </div>
                                                            <p class="manual-fn-note">Se guardan solo las celdas que tienen texto.</p>
                                                        </div>
                                                        @break
                                                @endswitch
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </div>

                        <div class="mt-5 rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-400">Ejemplo</p>
                            <p class="mt-1 text-sm leading-6 text-emerald-100/90">
                                <strong>Día:</strong> Lunes · <strong>Momento:</strong> Recibimiento ·
                                <strong>Estrategia:</strong> “Saludo con la canción de los animales. Cada niño dice su animal favorito y lo imita.”
                            </p>
                        </div>

                        <div class="mt-5 rounded-xl border border-amber-500/25 bg-amber-500/5 p-4">
                            <p class="text-sm font-bold text-amber-300">Importante</p>
                            <ul class="mt-2 space-y-2 text-sm leading-6 text-amber-100/90">
                                <li>• Una celda es <strong>un día + un momento</strong>. No escribas la estrategia del martes en la celda del lunes.</li>
                                <li>• <strong>No hace falta llenar las 50 celdas.</strong> Con una sola celda con texto ya puedes guardar las estrategias.</li>
                                <li>• Se recomienda usar al menos <strong>3 días</strong>. Si usas menos, el sistema te avisa, pero no te bloquea.</li>
                                <li>• El contador de la tarjeta sube cuando <strong>guardas</strong>. Si escribes y no guardas, no sube.</li>
                            </ul>
                        </div>

                        <h3 class="mt-6 text-sm font-bold uppercase tracking-widest text-gray-500">Cómo borrar una estrategia</h3>
                        <p class="mt-2 text-sm leading-6 text-gray-400">
                            Abre la celda que ya tiene texto. Aparecerá el botón <strong class="text-rose-300">Eliminar</strong>.
                            Púlsalo y confirma. Esa celda quedará vacía y <strong>no se puede deshacer</strong>.
                        </p>

                        <h3 class="mt-6 text-sm font-bold uppercase tracking-widest text-gray-500">Cómo eliminar todas las estrategias</h3>
                        <p class="mt-2 text-sm leading-6 text-gray-400">
                            Si quieres empezar la semana de nuevo sin perder la cabecera ni los resúmenes, usa el
                            borrado masivo. Esta acción elimina <strong class="text-white">todas las celdas guardadas</strong>
                            del plan abierto, pero conserva el nivel, la sección, las fechas, el diagnóstico y los
                            resúmenes por área.
                        </p>
                        <ol class="mt-3 space-y-2 text-sm leading-6 text-gray-300">
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 shrink-0 w-6 h-6 rounded-full bg-rose-500/15 border border-rose-500/30 text-rose-300 flex items-center justify-center text-[11px] font-bold">1</span>
                                <span>Abre <strong class="text-white">Estrategias</strong> en el plan que quieres vaciar.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 shrink-0 w-6 h-6 rounded-full bg-rose-500/15 border border-rose-500/30 text-rose-300 flex items-center justify-center text-[11px] font-bold">2</span>
                                <span>En el pie de la ventana, pulsa <strong class="text-rose-300">Eliminar todas</strong>.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 shrink-0 w-6 h-6 rounded-full bg-rose-500/15 border border-rose-500/30 text-rose-300 flex items-center justify-center text-[11px] font-bold">3</span>
                                <span>Lee cuántas estrategias se eliminarán y confirma. La rejilla queda vacía y lista para volver a escribir.</span>
                            </li>
                        </ol>
                        <div class="mt-4 rounded-xl border border-rose-500/25 bg-rose-500/5 p-4">
                            <p class="text-sm leading-6 text-rose-100/90">
                                <strong class="text-rose-300">No se puede deshacer.</strong> Si solo quieres corregir
                                una celda, usa el botón <strong>Eliminar</strong> de esa celda. El borrado masivo es para
                                reiniciar la semana completa.
                            </p>
                        </div>

                        <details class="group mt-5 rounded-xl border border-white/10 bg-white/5">
                            <summary class="cursor-pointer list-none px-4 py-3 text-sm font-semibold text-gray-200 flex items-center justify-between gap-3">
                                Errores comunes en este registro
                                <svg class="w-4 h-4 text-gray-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </summary>
                            <div class="px-4 pb-4 space-y-3 text-sm leading-6 text-gray-400">
                                <p><strong class="text-gray-200">“Escribí y se perdió”.</strong> Seguramente no guardaste. Vuelve a escribirla y pulsa <strong>Guardar y continuar</strong> o <strong>Guardar todas</strong>.</p>
                                <p><strong class="text-gray-200">“El contador no cambia”.</strong> El contador se actualiza después de guardar. Si acabas de escribir, guarda la celda.</p>
                                <p><strong class="text-gray-200">“No encuentro un momento”.</strong> Recorre los botones de momento; están en el orden del día. Si la pantalla es pequeña, los botones se acomodan en varias filas.</p>
                                <p><strong class="text-gray-200">“Quiero copiar una estrategia”.</strong> No hay botón de copiar. Puedes escribirla de nuevo en la otra celda.</p>
                            </div>
                        </details>

                        <div class="mt-4 rounded-xl border border-amber-500/20 bg-amber-500/5 p-4">
                            <p class="text-sm leading-6 text-amber-100/90">
                                <strong class="text-amber-300">Truco:</strong> empieza por los momentos que más usas:
                                <strong>Recibimiento</strong>, <strong>Planificación</strong> y <strong>Despedida</strong>.
                                Después completa los demás.
                            </p>
                        </div>
                    </section>

                    {{-- ═══════════════════════════════════════════════════
                         REGISTRO 3 · RESÚMENES
                         ═══════════════════════════════════════════════════ --}}
                    <section id="registro-resumenes" class="scroll-mt-24 rounded-2xl border border-purple-500/20 bg-gray-900/60 p-5 sm:p-7">
                        <div class="flex items-center gap-3">
                            <span class="shrink-0 w-10 h-10 rounded-xl bg-purple-500/15 border border-purple-500/30 text-purple-300 flex items-center justify-center text-lg font-extrabold">3</span>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-purple-400">Tercer registro</p>
                                <h2 class="text-lg font-bold text-white">Registro de resúmenes por área de aprendizaje</h2>
                            </div>
                        </div>

                        <p class="mt-3 text-base leading-7 text-gray-300">
                            Es una <strong class="text-white">ficha breve por cada área</strong>. No es la planificación completa:
                            aquí resumes qué vas a trabajar, qué quieres lograr y cómo sabrás que el niño está aprendiendo.
                        </p>

                        <figure class="manual-figure mt-5">
                            <p class="mb-2 text-[10px] text-gray-500 lg:hidden">Desliza el dibujo para verlo completo →</p>
                            <div class="overflow-x-auto rounded-xl border border-white/10 bg-gray-950/60 p-3">
                                <svg viewBox="0 0 920 480" class="manual-diagram w-full h-auto" role="img" aria-labelledby="resumen-manual-title resumen-manual-desc">
                                    <title id="resumen-manual-title">Formulario de resumen por área</title>
                                    <desc id="resumen-manual-desc">A la izquierda está la lista de resúmenes guardados. En el centro está el formulario con área, componente, objetivo, aprendizaje esperado e indicadores. A la derecha, una leyenda con letras identifica cada campo. Al final está el botón Guardar resumen.</desc>

                                    {{-- Lista --}}
                                    <rect x="20" y="20" width="250" height="440" rx="16" fill="#0b1220" stroke="#334155" />
                                    <text x="38" y="50" font-family="ui-sans-serif, system-ui, sans-serif" font-size="13.5" font-weight="700" fill="#e2e8f0">Resúmenes guardados</text>
                                    <circle cx="246" cy="46" r="9" fill="#c084fc" />
                                    <text x="246" y="49.5" text-anchor="middle" font-family="ui-sans-serif, system-ui, sans-serif" font-size="9" font-weight="700" fill="#3b0764">A</text>
                                    <text x="38" y="68" font-family="ui-sans-serif, system-ui, sans-serif" font-size="10.5" fill="#64748b">Uno por área de aprendizaje</text>

                                    <rect x="38" y="86" width="214" height="60" rx="10" fill="#0f172a" stroke="#334155" />
                                    <text x="52" y="108" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" font-weight="700" fill="#c4b5fd">Lenguaje oral y escrito</text>
                                    <text x="52" y="126" font-family="ui-sans-serif, system-ui, sans-serif" font-size="10.5" fill="#94a3b8">Expresión oral</text>
                                    <text x="52" y="141" font-family="ui-sans-serif, system-ui, sans-serif" font-size="9.5" fill="#64748b">Objetivo: expresar ideas…</text>

                                    <rect x="38" y="156" width="214" height="60" rx="10" fill="#0f172a" stroke="#334155" />
                                    <text x="52" y="178" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" font-weight="700" fill="#c4b5fd">Procesos lógico-matemáticos</text>
                                    <text x="52" y="196" font-family="ui-sans-serif, system-ui, sans-serif" font-size="10.5" fill="#94a3b8">Números y formas</text>
                                    <text x="52" y="211" font-family="ui-sans-serif, system-ui, sans-serif" font-size="9.5" fill="#64748b">Objetivo: contar hasta…</text>

                                    <rect x="38" y="232" width="214" height="42" rx="10" fill="#1e1b4b" stroke="#c084fc" stroke-dasharray="5 4" />
                                    <text x="137" y="258" text-anchor="middle" font-family="ui-sans-serif, system-ui, sans-serif" font-size="12" font-weight="700" fill="#d8b4fe">+ Nuevo resumen</text>
                                    <circle cx="232" cy="253" r="9" fill="#c084fc" />
                                    <text x="232" y="256.5" text-anchor="middle" font-family="ui-sans-serif, system-ui, sans-serif" font-size="9" font-weight="700" fill="#3b0764">B</text>

                                    <text x="38" y="304" font-family="ui-sans-serif, system-ui, sans-serif" font-size="10" fill="#64748b">Cada área tiene su propia ficha.</text>
                                    <text x="38" y="320" font-family="ui-sans-serif, system-ui, sans-serif" font-size="10" fill="#64748b">Puedes editarla o eliminarla.</text>

                                    {{-- Formulario --}}
                                    <rect x="290" y="20" width="380" height="440" rx="16" fill="#0b1220" stroke="#334155" />
                                    <text x="308" y="50" font-family="ui-sans-serif, system-ui, sans-serif" font-size="14" font-weight="700" fill="#e2e8f0">Nuevo resumen</text>

                                    <text x="308" y="76" font-family="ui-sans-serif, system-ui, sans-serif" font-size="11" fill="#94a3b8">Área de aprendizaje *</text>
                                    <rect x="308" y="84" width="344" height="32" rx="7" fill="#0f172a" stroke="#334155" />
                                    <text x="320" y="105" font-family="ui-sans-serif, system-ui, sans-serif" font-size="11.5" fill="#cbd5e1">Lenguaje oral y escrito</text>

                                    <text x="308" y="136" font-family="ui-sans-serif, system-ui, sans-serif" font-size="11" fill="#94a3b8">Componente *</text>
                                    <rect x="308" y="144" width="344" height="32" rx="7" fill="#0f172a" stroke="#334155" />
                                    <text x="320" y="165" font-family="ui-sans-serif, system-ui, sans-serif" font-size="11.5" fill="#cbd5e1">Expresión oral</text>

                                    <text x="308" y="196" font-family="ui-sans-serif, system-ui, sans-serif" font-size="11" fill="#94a3b8">Objetivo *</text>
                                    <rect x="308" y="204" width="344" height="36" rx="7" fill="#0f172a" stroke="#334155" />
                                    <text x="320" y="227" font-family="ui-sans-serif, system-ui, sans-serif" font-size="11.5" fill="#cbd5e1">Expresar ideas y sentimientos con palabras…</text>

                                    <text x="308" y="260" font-family="ui-sans-serif, system-ui, sans-serif" font-size="11" fill="#94a3b8">Aprendizaje esperado *</text>
                                    <rect x="308" y="268" width="344" height="36" rx="7" fill="#0f172a" stroke="#334155" />
                                    <text x="320" y="291" font-family="ui-sans-serif, system-ui, sans-serif" font-size="11.5" fill="#cbd5e1">Nombra animales y describe cómo son…</text>

                                    <text x="308" y="324" font-family="ui-sans-serif, system-ui, sans-serif" font-size="11" fill="#94a3b8">Indicadores *</text>
                                    <rect x="308" y="332" width="344" height="36" rx="7" fill="#0f172a" stroke="#334155" />
                                    <text x="320" y="355" font-family="ui-sans-serif, system-ui, sans-serif" font-size="11.5" fill="#cbd5e1">Menciona el nombre y una característica…</text>

                                    <text x="308" y="394" font-family="ui-sans-serif, system-ui, sans-serif" font-size="9.5" fill="#64748b">Línea de investigación y énfasis curriculares son opcionales.</text>
                                    <rect x="470" y="412" width="182" height="36" rx="8" fill="#7c3aed" />
                                    <text x="561" y="435" text-anchor="middle" font-family="ui-sans-serif, system-ui, sans-serif" font-size="13" font-weight="700" fill="#f5f3ff">Guardar resumen</text>

                                    {{-- Leyenda de campos, a la derecha del formulario --}}
                                    <rect x="690" y="20" width="190" height="440" rx="12" fill="#0f172a" stroke="#334155" />
                                    <text x="706" y="46" font-family="ui-sans-serif, system-ui, sans-serif" font-size="10" font-weight="700" fill="#d8b4fe" letter-spacing="1">LEYENDA</text>
                                    <text x="706" y="62" font-family="ui-sans-serif, system-ui, sans-serif" font-size="10" fill="#64748b">Identifica cada campo</text>
                                    @foreach ([
                                        ['A', 'Resúmenes guardados'],
                                        ['B', 'Nuevo resumen'],
                                        ['C', 'Área de aprendizaje'],
                                        ['D', 'Componente'],
                                        ['E', 'Objetivo'],
                                        ['F', 'Aprendizaje esperado'],
                                        ['G', 'Indicadores'],
                                        ['H', 'Línea de investigación'],
                                        ['I', 'Énfasis curriculares'],
                                        ['J', 'Orden'],
                                        ['K', 'Guardar resumen'],
                                    ] as [$numero, $campo])
                                        <circle cx="714" cy="{{ 88 + ($loop->iteration - 1) * 26 }}" r="8" fill="#c084fc" />
                                        <text x="714" y="{{ 92 + ($loop->iteration - 1) * 26 }}" text-anchor="middle" font-family="ui-sans-serif, system-ui, sans-serif" font-size="9" font-weight="700" fill="#3b0764">{{ $numero }}</text>
                                        <text x="730" y="{{ 92 + ($loop->iteration - 1) * 26 }}" font-family="ui-sans-serif, system-ui, sans-serif" font-size="10" fill="#cbd5e1">{{ $campo }}</text>
                                    @endforeach
                                    <text x="706" y="412" font-family="ui-sans-serif, system-ui, sans-serif" font-size="9" fill="#475569">Las letras señalan cada campo.</text>
                                    <text x="706" y="428" font-family="ui-sans-serif, system-ui, sans-serif" font-size="9" fill="#475569">H, I y J son opcionales.</text>

                                    {{-- Números de los campos visibles --}}
                                    @foreach ([
                                        [280, 100, 'C'],
                                        [280, 160, 'D'],
                                        [280, 222, 'E'],
                                        [280, 286, 'F'],
                                        [280, 350, 'G'],
                                        [456, 430, 'K'],
                                    ] as [$x, $y, $n])
                                        <circle cx="{{ $x }}" cy="{{ $y }}" r="10" fill="#c084fc" />
                                        <text x="{{ $x }}" y="{{ $y + 4 }}" text-anchor="middle" font-family="ui-sans-serif, system-ui, sans-serif" font-size="{{ strlen($n) > 1 ? 8 : 10 }}" font-weight="700" fill="#3b0764">{{ $n }}</text>
                                    @endforeach
                                </svg>
                            </div>
                            <figcaption class="mt-2 text-xs text-gray-500">
                                La leyenda de la derecha identifica cada campo con una letra. Primero eliges el área; después completas los campos obligatorios y guardas.
                            </figcaption>
                        </figure>

                        <div class="manual-timeline-block manual-timeline--purple mt-6">
                            <div class="manual-timeline-head">
                                <div>
                                    <p class="manual-timeline-kicker">Recorrido completo · 11 pasos</p>
                                    <h3 class="manual-timeline-title">Paso a paso</h3>
                                </div>
                            </div>
                            <p class="manual-timeline-subtitle">
                                Los 11 pasos para registrar un resumen, en orden.
                            </p>

                            <ol class="manual-timeline">
                                @foreach ([
                                    'En la tarjeta del plan, pulsa <strong class="text-white">Resúmenes</strong>.',
                                    'Si ya hay resúmenes, los verás en la lista <strong class="text-white">Resúmenes guardados</strong>.',
                                    'En <strong class="text-white">Nuevo resumen</strong>, elige el <strong class="text-white">Momento</strong> si quieres filtrar las áreas. Es opcional.',
                                    'Elige el <strong class="text-white">Área de aprendizaje</strong>. Solo aparecen las áreas de tu carga académica.',
                                    'Escribe el <strong class="text-white">Componente</strong>.',
                                    'Escribe el <strong class="text-white">Objetivo</strong>.',
                                    'Escribe el <strong class="text-white">Aprendizaje esperado</strong>.',
                                    'Escribe los <strong class="text-white">Indicadores</strong>.',
                                    'Si quieres, completa <strong class="text-white">Línea de investigación</strong>, <strong class="text-white">Énfasis curriculares</strong> y <strong class="text-white">Orden</strong>.',
                                    'Pulsa <strong class="text-white">Guardar resumen</strong>.',
                                    'Repite para cada área. Para cambiar uno, usa el lápiz; para borrar, el basurero.',
                                ] as $paso)
                                    <li class="manual-timeline-item">
                                        <div class="manual-timeline-marker" aria-hidden="true">
                                            <span class="manual-timeline-dot">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                        <div class="manual-step">
                                            <div class="manual-step-copy">
                                                <span class="manual-step-text">{!! $paso !!}</span>
                                            </div>

                                            <div class="manual-step-visual" aria-hidden="true">
                                                @switch($loop->iteration)
                                                    @case(1)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Plan semanal · Preescolar 4 años B</span>
                                                                <span class="manual-fn-tag">Semana 1</span>
                                                            </div>
                                                            <div class="manual-fn-row">
                                                                <span class="manual-fn-muted">05/10/2026 → 09/10/2026</span>
                                                                <span class="manual-fn-btn">Resúmenes</span>
                                                            </div>
                                                            <div class="manual-fn-chips">
                                                                <span class="manual-fn-chip manual-fn-chip--active">Resúmenes <span>3 áreas</span></span>
                                                                <span class="manual-fn-chip">Estrategias <span>5/50</span></span>
                                                            </div>
                                                        </div>
                                                        @break

                                                    @case(2)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Resúmenes guardados</span>
                                                                <span class="manual-fn-tag">3 áreas</span>
                                                            </div>
                                                            <div class="manual-fn-list">
                                                                <div class="manual-fn-item">
                                                                    <div>
                                                                        <div class="manual-fn-item-title">Lenguaje oral y escrito</div>
                                                                        <div class="manual-fn-item-meta">Expresión oral</div>
                                                                    </div>
                                                                    <div class="manual-fn-actions">
                                                                        <span class="manual-fn-mini-btn">Editar</span>
                                                                        <span class="manual-fn-mini-btn manual-fn-mini-btn--danger">Eliminar</span>
                                                                    </div>
                                                                </div>
                                                                <div class="manual-fn-item">
                                                                    <div>
                                                                        <div class="manual-fn-item-title">Procesos lógico-matemáticos</div>
                                                                        <div class="manual-fn-item-meta">Números y formas</div>
                                                                    </div>
                                                                    <div class="manual-fn-actions">
                                                                        <span class="manual-fn-mini-btn">Editar</span>
                                                                        <span class="manual-fn-mini-btn manual-fn-mini-btn--danger">Eliminar</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <p class="manual-fn-note">Cada área tiene su propia ficha.</p>
                                                        </div>
                                                        @break

                                                    @case(3)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Nuevo resumen</span>
                                                                <span class="manual-fn-tag">Opcional</span>
                                                            </div>
                                                            <div class="manual-fn-field">
                                                                <span class="manual-fn-label">Momento</span>
                                                                <span class="manual-fn-value">Todo el día</span>
                                                            </div>
                                                            <div class="manual-fn-chips">
                                                                <span class="manual-fn-chip manual-fn-chip--active">Todo</span>
                                                                <span class="manual-fn-chip">Recibimiento</span>
                                                                <span class="manual-fn-chip">Planificación</span>
                                                                <span class="manual-fn-chip">Despedida</span>
                                                            </div>
                                                            <p class="manual-fn-note">El filtro solo ayuda a encontrar el área; no es obligatorio.</p>
                                                        </div>
                                                        @break

                                                    @case(4)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Área de aprendizaje *</span>
                                                                <span class="manual-fn-tag">Carga académica</span>
                                                            </div>
                                                            <div class="manual-fn-field">
                                                                <span class="manual-fn-label">Seleccionada</span>
                                                                <span class="manual-fn-value">Lenguaje oral y escrito</span>
                                                            </div>
                                                            <p class="manual-fn-note">Solo aparecen las áreas de tu nivel y sección.</p>
                                                        </div>
                                                        @break

                                                    @case(5)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Componente *</span>
                                                                <span class="manual-fn-tag">Obligatorio</span>
                                                            </div>
                                                            <div class="manual-fn-field">
                                                                <span class="manual-fn-label">Componente</span>
                                                                <span class="manual-fn-value">Expresión oral</span>
                                                            </div>
                                                        </div>
                                                        @break

                                                    @case(6)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Objetivo *</span>
                                                                <span class="manual-fn-tag">Obligatorio</span>
                                                            </div>
                                                            <div class="manual-fn-field">
                                                                <span class="manual-fn-label">Objetivo</span>
                                                                <span class="manual-fn-value">Expresar ideas y sentimientos con palabras.</span>
                                                            </div>
                                                        </div>
                                                        @break

                                                    @case(7)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Aprendizaje esperado *</span>
                                                                <span class="manual-fn-tag">Obligatorio</span>
                                                            </div>
                                                            <div class="manual-fn-field">
                                                                <span class="manual-fn-label">Logro esperado</span>
                                                                <span class="manual-fn-value">Nombra animales y describe cómo son.</span>
                                                            </div>
                                                        </div>
                                                        @break

                                                    @case(8)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Indicadores *</span>
                                                                <span class="manual-fn-tag">Obligatorio</span>
                                                            </div>
                                                            <div class="manual-fn-field">
                                                                <span class="manual-fn-label">Señal de logro</span>
                                                                <span class="manual-fn-value">Menciona el nombre y una característica del animal.</span>
                                                            </div>
                                                        </div>
                                                        @break

                                                    @case(9)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Campos opcionales</span>
                                                                <span class="manual-fn-tag">Puedes omitirlos</span>
                                                            </div>
                                                            <div class="manual-fn-grid manual-fn-grid--3">
                                                                <div class="manual-fn-cell">
                                                                    <strong>Línea de investigación</strong>
                                                                    <span>¿Qué animales viven en mi comunidad?</span>
                                                                </div>
                                                                <div class="manual-fn-cell">
                                                                    <strong>Énfasis curriculares</strong>
                                                                    <span>Respeto por los seres vivos.</span>
                                                                </div>
                                                                <div class="manual-fn-cell">
                                                                    <strong>Orden</strong>
                                                                    <span>1</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @break

                                                    @case(10)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Guardar resumen</span>
                                                                <span class="manual-fn-tag">Validación</span>
                                                            </div>
                                                            <div class="manual-fn-chips">
                                                                <span class="manual-fn-chip manual-fn-chip--active">Área ✓</span>
                                                                <span class="manual-fn-chip manual-fn-chip--active">Componente ✓</span>
                                                                <span class="manual-fn-chip manual-fn-chip--active">Objetivo ✓</span>
                                                                <span class="manual-fn-chip manual-fn-chip--active">Aprendizaje ✓</span>
                                                                <span class="manual-fn-chip manual-fn-chip--active">Indicadores ✓</span>
                                                            </div>
                                                            <div class="manual-fn-row">
                                                                <span class="manual-fn-note" style="margin-top: 0;"><span class="manual-fn-check">✓</span> Campos obligatorios completos</span>
                                                                <span class="manual-fn-btn">Guardar resumen</span>
                                                            </div>
                                                        </div>
                                                        @break

                                                    @case(11)
                                                        <div class="manual-fn">
                                                            <div class="manual-fn-top">
                                                                <span class="manual-fn-title">Resúmenes por área</span>
                                                                <span class="manual-fn-tag">3 guardados</span>
                                                            </div>
                                                            <div class="manual-fn-list">
                                                                <div class="manual-fn-item">
                                                                    <div>
                                                                        <div class="manual-fn-item-title">Lenguaje oral y escrito</div>
                                                                        <div class="manual-fn-item-meta">Expresión oral</div>
                                                                    </div>
                                                                    <div class="manual-fn-actions">
                                                                        <span class="manual-fn-mini-btn">Editar</span>
                                                                        <span class="manual-fn-mini-btn manual-fn-mini-btn--danger">Eliminar</span>
                                                                    </div>
                                                                </div>
                                                                <div class="manual-fn-item">
                                                                    <div>
                                                                        <div class="manual-fn-item-title">Procesos lógico-matemáticos</div>
                                                                        <div class="manual-fn-item-meta">Números y formas</div>
                                                                    </div>
                                                                    <div class="manual-fn-actions">
                                                                        <span class="manual-fn-mini-btn">Editar</span>
                                                                        <span class="manual-fn-mini-btn manual-fn-mini-btn--danger">Eliminar</span>
                                                                    </div>
                                                                </div>
                                                                <div class="manual-fn-item">
                                                                    <div>
                                                                        <div class="manual-fn-item-title">Relación con el ambiente</div>
                                                                        <div class="manual-fn-item-meta">Seres vivos</div>
                                                                    </div>
                                                                    <div class="manual-fn-actions">
                                                                        <span class="manual-fn-mini-btn">Editar</span>
                                                                        <span class="manual-fn-mini-btn manual-fn-mini-btn--danger">Eliminar</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <p class="manual-fn-note">Repite el formulario para cada área; la lista queda ordenada.</p>
                                                        </div>
                                                        @break
                                                @endswitch
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </div>

                        <h3 class="mt-6 text-sm font-bold uppercase tracking-widest text-gray-500">Qué escribir en cada campo</h3>
                        <div class="mt-3 overflow-x-auto rounded-xl border border-white/10">
                            <table class="min-w-full text-sm">
                                <thead class="bg-white/5 text-left text-[10px] uppercase tracking-widest text-gray-500">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">Campo</th>
                                        <th scope="col" class="px-4 py-3">Qué escribir</th>
                                        <th scope="col" class="px-4 py-3">Ejemplo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @foreach ([
                                        ['Área de aprendizaje', 'La materia o área que vas a resumir.', 'Lenguaje oral y escrito'],
                                        ['Componente', 'La parte del área que vas a trabajar.', 'Expresión oral'],
                                        ['Objetivo', 'Qué quieres lograr con el grupo en esa área.', 'Expresar ideas y sentimientos con palabras.'],
                                        ['Aprendizaje esperado', 'Qué debe aprender el niño, escrito como logro.', 'Nombra animales y describe cómo son.'],
                                        ['Indicadores', 'Señales que te dicen si el aprendizaje se está logrando.', 'Menciona el nombre y una característica del animal.'],
                                        ['Línea de investigación', 'Tema o pregunta que guía el proyecto. Opcional.', '¿Qué animales viven en mi comunidad?'],
                                        ['Énfasis curriculares', 'Aspectos que quieres destacar. Opcional.', 'Respeto por los seres vivos.'],
                                        ['Orden', 'Número para ordenar los resúmenes. Opcional.', '1'],
                                    ] as [$campo, $queEscribir, $ejemplo])
                                        <tr>
                                            <th scope="row" class="px-4 py-3 text-left font-semibold text-gray-200 whitespace-nowrap">{{ $campo }}</th>
                                            <td class="px-4 py-3 text-gray-400">{{ $queEscribir }}</td>
                                            <td class="px-4 py-3 text-gray-500 italic">{{ $ejemplo }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-5 rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-400">Ejemplo completo</p>
                            <p class="mt-1 text-sm leading-6 text-emerald-100/90">
                                <strong>Área:</strong> Lenguaje oral y escrito ·
                                <strong>Componente:</strong> Expresión oral ·
                                <strong>Objetivo:</strong> expresar ideas y sentimientos con palabras ·
                                <strong>Aprendizaje esperado:</strong> nombra animales y describe cómo son ·
                                <strong>Indicadores:</strong> menciona el nombre y una característica del animal.
                            </p>
                        </div>

                        <div class="mt-5 rounded-xl border border-purple-500/25 bg-purple-500/5 p-4">
                            <p class="text-sm font-bold text-purple-300">Importante</p>
                            <p class="mt-1 text-sm leading-6 text-purple-100/90">
                                Los campos obligatorios son el <strong>Área de aprendizaje</strong>, el <strong>Componente</strong>, el <strong>Objetivo</strong>,
                                el <strong>Aprendizaje esperado</strong> y los <strong>Indicadores</strong>. Si falta alguno, el resumen no se guarda.
                                <strong>Línea de investigación</strong> y <strong>Énfasis curriculares</strong> son opcionales.
                            </p>
                        </div>

                        <details class="group mt-5 rounded-xl border border-white/10 bg-white/5">
                            <summary class="cursor-pointer list-none px-4 py-3 text-sm font-semibold text-gray-200 flex items-center justify-between gap-3">
                                Errores comunes en este registro
                                <svg class="w-4 h-4 text-gray-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </summary>
                            <div class="px-4 pb-4 space-y-3 text-sm leading-6 text-gray-400">
                                <p><strong class="text-gray-200">“No aparece mi área”.</strong> Revisa el nivel y el lapso. Solo aparecen las áreas de tu carga académica. Si sigue sin aparecer, consulta con Coordinación.</p>
                                <p><strong class="text-gray-200">“No me deja guardar”.</strong> Completa Componente, Objetivo, Aprendizaje esperado e Indicadores.</p>
                                <p><strong class="text-gray-200">“Quiero cambiar un resumen”.</strong> Pulsa el lápiz en la lista, cambia lo que necesites y guarda otra vez.</p>
                                <p><strong class="text-gray-200">“Quiero borrar un resumen”.</strong> Pulsa el basurero y confirma. No se puede deshacer.</p>
                            </div>
                        </details>

                        <div class="mt-4 rounded-xl border border-purple-500/20 bg-purple-500/5 p-4">
                            <p class="text-sm leading-6 text-purple-100/90">
                                <strong class="text-purple-300">Truco:</strong> haz un resumen por cada área que trabajes.
                                Así la tabla impresa queda completa y ordenada.
                            </p>
                        </div>
                    </section>

                    {{-- ═══════════════════════════════════════════════════
                         IMPRIMIR
                         ═══════════════════════════════════════════════════ --}}
                    <section id="imprimir" class="scroll-mt-24 rounded-2xl border border-emerald-500/20 bg-gray-900/60 p-5 sm:p-7">
                        <div class="flex items-center gap-3">
                            <span class="shrink-0 w-10 h-10 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-400">Al final</p>
                                <h2 class="text-lg font-bold text-white">Imprimir o reimprimir</h2>
                            </div>
                        </div>

                        <ol class="mt-4 space-y-3">
                            @foreach ([
                                'Guarda el plan, las estrategias y los resúmenes.',
                                'En la tarjeta del plan, pulsa el icono de la <strong class="text-white">impresora</strong>.',
                                'Se abre una pestaña nueva con el formato.',
                                'Revisa la vista previa y usa <strong class="text-white">Ctrl + P</strong> (Windows) o <strong class="text-white">Cmd + P</strong> (Mac) para imprimir.',
                            ] as $paso)
                                <li class="flex items-start gap-3">
                                    <span class="mt-0.5 shrink-0 w-6 h-6 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 flex items-center justify-center text-[11px] font-bold">{{ $loop->iteration }}</span>
                                    <span class="text-sm leading-6 text-gray-300">{!! $paso !!}</span>
                                </li>
                            @endforeach
                        </ol>

                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            @foreach ([
                                ['Página 1', 'Cabecera: docente, grupo, fechas y diagnóstico.'],
                                ['Página 2', 'Tabla resumen: un área por fila.'],
                                ['Página 3', 'Estrategias: rejilla de 5 días × 10 momentos, observación y firma.'],
                            ] as [$pagina, $detalle])
                                <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-400">{{ $pagina }}</p>
                                    <p class="mt-1 text-sm leading-6 text-gray-400">{{ $detalle }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 rounded-xl border border-amber-500/25 bg-amber-500/5 p-4">
                            <p class="text-sm leading-6 text-amber-100/90">
                                <strong class="text-amber-300">Si el formato sale vacío:</strong>
                                vuelve al plan y guarda primero. El formato imprime lo que está guardado, no lo que quedó escrito sin guardar.
                            </p>
                        </div>
                    </section>

                    {{-- ═══════════════════════════════════════════════════
                         RUTA RÁPIDA
                         ═══════════════════════════════════════════════════ --}}
                    <section id="ruta-rapida" class="scroll-mt-24 rounded-2xl border border-white/10 bg-gray-900/60 p-5 sm:p-7">
                        <h2 class="text-lg font-bold text-white">Ruta rápida</h2>
                        <p class="mt-1 text-sm text-gray-400">Si ya entendiste los tres registros, usa esta lista para recordar el orden.</p>

                        <ol class="mt-4 grid gap-3 sm:grid-cols-4">
                            @foreach ([
                                ['Plan', 'Nivel, sección, fechas y diagnóstico.', 'cyan'],
                                ['Estrategias', 'Día + momento + texto. Guarda.', 'amber'],
                                ['Resúmenes', 'Un resumen por área.', 'purple'],
                                ['Imprimir', 'Icono de impresora.', 'emerald'],
                            ] as [$titulo, $detalle, $color])
                                @php
                                    $estilos = [
                                        'cyan' => 'border-cyan-500/25 bg-cyan-500/5 text-cyan-300',
                                        'amber' => 'border-amber-500/25 bg-amber-500/5 text-amber-300',
                                        'purple' => 'border-purple-500/25 bg-purple-500/5 text-purple-300',
                                        'emerald' => 'border-emerald-500/25 bg-emerald-500/5 text-emerald-300',
                                    ][$color];
                                @endphp
                                <li class="rounded-xl border p-3 {{ $estilos }}">
                                    <p class="text-[10px] font-bold uppercase tracking-widest">Paso {{ $loop->iteration }}</p>
                                    <p class="mt-1 text-sm font-bold text-white">{{ $titulo }}</p>
                                    <p class="mt-1 text-xs leading-5 text-gray-400">{{ $detalle }}</p>
                                </li>
                            @endforeach
                        </ol>
                    </section>

                    {{-- ═══════════════════════════════════════════════════
                         SI ALGO SALE MAL
                         ═══════════════════════════════════════════════════ --}}
                    <section id="problemas" class="scroll-mt-24 rounded-2xl border border-rose-500/20 bg-gray-900/60 p-5 sm:p-7">
                        <h2 class="text-lg font-bold text-white">Si algo sale mal</h2>
                        <p class="mt-1 text-sm text-gray-400">Busca tu situación. Si no está aquí, consulta con Coordinación.</p>

                        <div class="mt-4 space-y-2">
                            @foreach ([
                                ['No veo el módulo de Educación Inicial', 'Pide a Coordinación que active tu acceso al módulo. Si eres administrador, puedes entrar por la dirección directa.'],
                                ['No veo mi plan en la lista', 'Revisa los filtros de nivel, sección y área. Borra la búsqueda y vuelve a intentar.'],
                                ['Otro docente no puede ver mi plan', 'Es normal: cada docente ve sus propios planes. Coordinación tiene una vista de revisión.'],
                                ['Borré un plan sin querer', 'No se puede deshacer. Crea el plan otra vez o usa Importar de s2526 si tienes el período anterior.'],
                                ['El formato no muestra una estrategia', 'Abre la celda, revisa que el texto esté guardado y vuelve a imprimir.'],
                                ['No puedo guardar un resumen', 'Elige el área y completa Componente, Objetivo, Aprendizaje esperado e Indicadores.'],
                            ] as [$problema, $solucion])
                                <details class="group rounded-xl border border-white/10 bg-white/5">
                                    <summary class="cursor-pointer list-none px-4 py-3 text-sm font-semibold text-gray-200 flex items-center justify-between gap-3">
                                        {{ $problema }}
                                        <svg class="w-4 h-4 shrink-0 text-gray-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </summary>
                                    <p class="px-4 pb-4 text-sm leading-6 text-gray-400">{{ $solucion }}</p>
                                </details>
                            @endforeach
                        </div>
                    </section>

                    {{-- ═══════════════════════════════════════════════════
                         CIERRE
                         ═══════════════════════════════════════════════════ --}}
                    <footer class="rounded-2xl border border-white/10 bg-gray-900/60 p-5 sm:p-7">
                        <p class="text-sm leading-6 text-gray-400">
                            Ya conoces los tres registros. Recuerda: <strong class="text-white">primero el plan</strong>,
                            <strong class="text-white">después las estrategias</strong> y
                            <strong class="text-white">al final los resúmenes</strong>.
                            Si te cansas, guarda y vuelve después. Nada de lo guardado se pierde.
                        </p>
                        <div class="mt-4 flex flex-wrap gap-3">
                            <a href="{{ route('inicials.eiplanningwks.index') }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-cyan-600 hover:bg-cyan-500 px-4 py-2 text-sm font-semibold text-white transition-colors">
                                Ir a Planificación semanal
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            <a href="{{ route('inicials.use-cases') }}"
                                class="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/5 hover:bg-white/10 px-4 py-2 text-sm font-semibold text-gray-300 transition-colors">
                                Volver a casos de uso
                            </a>
                        </div>
                    </footer>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.role>
