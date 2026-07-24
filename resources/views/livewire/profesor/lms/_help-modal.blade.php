	    {{-- ═══════════ MODAL DE AYUDA DEL WIZARD ═══════════ --}}
	    @if($showHelpModal)
	        <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="help-modal">
	            <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" wire:click="$set('showHelpModal', false)"></div>
	            <div class="relative min-h-screen flex items-center justify-center p-4">
	                <div class="relative w-full max-w-6xl max-h-[95vh] bg-gray-900 border border-slate-700/60 rounded-2xl shadow-2xl overflow-hidden"
	                     x-data="helpWizardData()">

	                    {{-- ─── Header ─── --}}
	                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700/50 bg-slate-900/50">
	                        <div class="flex items-center gap-3">
	                            <div class="w-9 h-9 rounded-lg bg-blue-500/15 flex items-center justify-center">
	                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
	                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
	                                </svg>
	                            </div>
	                            <h2 class="text-lg font-bold text-white tracking-tight">Ayuda del Wizard de Lecciones</h2>
	                        </div>
	                        <button wire:click="$set('showHelpModal', false)"
	                                class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:text-white hover:bg-slate-800 transition-colors">
	                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
	                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
	                            </svg>
	                        </button>
	                    </div>

	                    {{-- ─── Body: Sidebar + Content ─── --}}
	                    <div class="flex" style="min-height:400px; max-height:65vh;">
	                        {{-- Sidebar --}}
	                        <div class="border-r border-slate-700/30 flex flex-col shrink-0 transition-all duration-300 overflow-hidden"
	                             :class="sidebarOpen ? 'w-44' : 'w-12'">
	                            <button @click="sidebarOpen = !sidebarOpen"
	                                    class="flex items-center justify-center h-9 border-b border-slate-700/30 text-slate-500 hover:text-white transition-colors">
	                                <svg class="w-4 h-4 transition-transform duration-300" :class="sidebarOpen ? 'rotate-180' : ''"
	                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
	                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
	                                </svg>
	                            </button>
	                            <template x-for="(tab, i) in tabs" :key="i">
	                                <button @click="activeTab = i"
	                                        :class="activeTab === i ? 'bg-blue-500/10 text-blue-300 border-l-2 border-blue-500' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-2 border-transparent'"
	                                        class="flex items-center gap-3 px-3 py-2.5 text-xs font-medium transition-all whitespace-nowrap"
	                                        :title="sidebarOpen ? '' : tab.short">
	                                    <span class="shrink-0 w-5 h-5 flex items-center justify-center" x-html="tab.icon"></span>
	                                    <span x-show="sidebarOpen" x-text="tab.label" class="truncate text-[11px]"></span>
	                                </button>
	                            </template>
	                        </div>

	                        {{-- Content --}}
	                        <div class="flex-1 overflow-y-auto p-5 space-y-4">

		                            {{-- TAB: Visión General --}}
		                            <div x-show="activeTab === 0" x-cloak class="space-y-3">
		                                <h3 class="text-sm font-bold text-white">📋 Visión General del Wizard</h3>
		                                <p class="text-xs text-slate-400 leading-relaxed">
		                                    El wizard te guía en <strong class="text-white">4 pasos</strong> para crear una lección educativa completa.
		                                    Puedes avanzar y retroceder libremente; los cambios se guardan en cada paso.
		                                </p>

		                                {{-- Paso 1 --}}
		                                <div class="rounded-lg border border-slate-700/40 overflow-hidden" x-data="{ open: true }">
		                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-slate-800/40 hover:bg-slate-800/70 transition-colors text-left">
		                                        <div class="flex items-center gap-2">
		                                            <span class="w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400 text-[10px] font-bold shrink-0">1</span>
		                                            <span class="text-xs font-semibold text-white">Título y Descripción</span>
		                                        </div>
		                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                    </button>
		                                    <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                        <p class="text-[11px] text-slate-300 leading-relaxed">
		                                            <strong class="text-white">¿Qué hago aquí?</strong> Define el tema central de tu lección:
		                                            un título llamativo, una descripción que contextualice, el nivel educativo al que va dirigida
		                                            y los objetivos de aprendizaje.
		                                        </p>
		                                        <div class="bg-slate-800/60 rounded-lg p-2.5 border border-slate-700/30">
		                                            <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold mb-1">🎯 Ejemplo de uso</p>
		                                            <p class="text-[11px] text-slate-300 leading-relaxed">
		                                                <strong class="text-white">Tema:</strong> "Sistema Solar" · <strong class="text-white">Nivel:</strong> 5to grado<br>
		                                                <strong class="text-white">Objetivo:</strong> "Identificar los planetas del sistema solar y sus características principales"
		                                            </p>
		                                        </div>
		                                        <div class="flex flex-wrap gap-1.5">
		                                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20">💡 La IA puede generar título + descripción + objetivos con un clic</span>
		                                        </div>
		                                    </div>
		                                </div>

		                                {{-- Paso 2 --}}
		                                <div class="rounded-lg border border-slate-700/40 overflow-hidden" x-data="{ open: false }">
		                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-slate-800/40 hover:bg-slate-800/70 transition-colors text-left">
		                                        <div class="flex items-center gap-2">
		                                            <span class="w-6 h-6 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-400 text-[10px] font-bold shrink-0">2</span>
		                                            <span class="text-xs font-semibold text-white">Diapositivas (Secciones)</span>
		                                        </div>
		                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                    </button>
		                                    <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                        <p class="text-[11px] text-slate-300 leading-relaxed">
		                                            <strong class="text-white">¿Qué hago aquí?</strong> Cada <strong>sección</strong> es una diapositiva.
		                                            Agrégalas con título y contenido, luego usa la IA para generar texto, imágenes,
		                                            ilustraciones o diagramas para cada una.
		                                        </p>
		                                        <div class="bg-slate-800/60 rounded-lg p-2.5 border border-slate-700/30">
		                                            <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold mb-1">📖 Estructura sugerida</p>
		                                            <p class="text-[11px] text-slate-300 leading-relaxed">
		                                                <strong class="text-white">Sección 1:</strong> Introducción al tema<br>
		                                                <strong class="text-white">Sección 2:</strong> Desarrollo con conceptos clave<br>
		                                                <strong class="text-white">Sección 3:</strong> Ejemplos y aplicaciones<br>
		                                                <strong class="text-white">Sección 4:</strong> Resumen y conclusiones
		                                            </p>
		                                        </div>
		                                        <p class="text-[10px] text-slate-500">💡 Puedes reordenar, ocultar o eliminar secciones libremente</p>
		                                    </div>
		                                </div>

		                                {{-- Paso 3 --}}
		                                <div class="rounded-lg border border-slate-700/40 overflow-hidden" x-data="{ open: false }">
		                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-slate-800/40 hover:bg-slate-800/70 transition-colors text-left">
		                                        <div class="flex items-center gap-2">
		                                            <span class="w-6 h-6 rounded-full bg-amber-500/20 flex items-center justify-center text-amber-400 text-[10px] font-bold shrink-0">3</span>
		                                            <span class="text-xs font-semibold text-white">Recursos y Enlaces</span>
		                                        </div>
		                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                    </button>
		                                    <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                        <p class="text-[11px] text-slate-300 leading-relaxed">
		                                            <strong class="text-white">¿Qué hago aquí?</strong> Adjunta materiales complementarios:
		                                            archivos PDF con lecturas, imágenes de referencia, videos explicativos,
		                                            o enlaces a sitios web relacionados con el tema.
		                                        </p>
		                                        <div class="bg-slate-800/60 rounded-lg p-2.5 border border-slate-700/30">
		                                            <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold mb-1">📎 Ejemplos de recursos</p>
		                                            <ul class="text-[11px] text-slate-300 space-y-1 list-disc list-inside">
		                                                <li>PDF con la lectura complementaria del tema</li>
		                                                <li>Video de YouTube embedded como material de apoyo</li>
		                                                <li>Enlace a simulador interactivo externo</li>
		                                                <li>Infografía en imagen PNG</li>
		                                            </ul>
		                                        </div>
		                                    </div>
		                                </div>

		                                {{-- Paso 4 --}}
		                                <div class="rounded-lg border border-slate-700/40 overflow-hidden" x-data="{ open: false }">
		                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-slate-800/40 hover:bg-slate-800/70 transition-colors text-left">
		                                        <div class="flex items-center gap-2">
		                                            <span class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-[10px] font-bold shrink-0">4</span>
		                                            <span class="text-xs font-semibold text-white">Publicación</span>
		                                        </div>
		                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                    </button>
		                                    <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                        <p class="text-[11px] text-slate-300 leading-relaxed">
		                                            <strong class="text-white">¿Qué hago aquí?</strong> Revisa el contenido completo y decide
		                                            el destino de tu lección.
		                                        </p>
		                                        <div class="grid grid-cols-2 gap-2">
		                                            <div class="bg-purple-500/10 rounded-lg p-2 border border-purple-500/20">
		                                                <p class="text-[10px] font-semibold text-purple-300">📅 Programar</p>
		                                                <p class="text-[10px] text-slate-400 mt-0.5">Elige fecha futura. La lección se publicará automáticamente.</p>
		                                            </div>
		                                            <div class="bg-emerald-500/10 rounded-lg p-2 border border-emerald-500/20">
		                                                <p class="text-[10px] font-semibold text-emerald-300">🚀 Publicar ahora</p>
		                                                <p class="text-[10px] text-slate-400 mt-0.5">Disponible para estudiantes de inmediato.</p>
		                                            </div>
		                                        </div>
		                                        <p class="text-[10px] text-red-400">⚠️ Una vez publicada, la lección no se puede editar</p>
		                                    </div>
		                                </div>
		                            </div>

		                            {{-- TAB: Herramientas IA --}}
		                            <div x-show="activeTab === 1" x-cloak class="space-y-3">
		                                <h3 class="text-sm font-bold text-white">🤖 Herramientas de Inteligencia Artificial</h3>
		                                <p class="text-xs text-slate-400 leading-relaxed">
		                                    La IA está integrada en cada sección para ayudarte a crear contenido de calidad
		                                    en segundos. Cada herramienta tiene un propósito específico.
		                                </p>

		                                {{-- Generar texto --}}
		                                <div class="rounded-lg border border-slate-700/40 overflow-hidden" x-data="{ open: true }">
		                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-slate-800/40 hover:bg-slate-800/70 transition-colors text-left">
		                                        <div class="flex items-center gap-2">
		                                            <div class="w-6 h-6 rounded bg-emerald-500/15 flex items-center justify-center text-emerald-400 shrink-0"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></div>
		                                            <span class="text-xs font-semibold text-white">✏️ Generar texto</span>
		                                        </div>
		                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                    </button>
		                                    <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                        <p class="text-[11px] text-slate-300 leading-relaxed">
		                                            <strong class="text-white">¿Para qué sirve?</strong> Redacta automáticamente el contenido
		                                            textual de una sección a partir de su título. Ideal cuando tienes clara la estructura
		                                            pero necesitas ayuda con la redacción.
		                                        </p>
		                                        <div class="bg-slate-800/60 rounded-lg p-2.5 border border-slate-700/30">
		                                            <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold mb-1">🔍 Caso de uso</p>
		                                            <p class="text-[11px] text-slate-300 leading-relaxed">
		                                                Tienes una sección llamada <strong class="text-white">"La fotosíntesis"</strong> pero no sabes
		                                                cómo redactar el contenido. La IA genera párrafos explicativos con ejemplos
		                                                adaptados al nivel educativo que elegiste.
		                                            </p>
		                                        </div>
		                                        <span class="inline-block text-[10px] px-2 py-0.5 rounded bg-slate-800 text-slate-400 font-mono border border-slate-700">Botón: <span class="text-emerald-400">✨ Generar texto</span></span>
		                                    </div>
		                                </div>

		                                {{-- Generar imagen --}}
		                                <div class="rounded-lg border border-slate-700/40 overflow-hidden" x-data="{ open: false }">
		                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-slate-800/40 hover:bg-slate-800/70 transition-colors text-left">
		                                        <div class="flex items-center gap-2">
		                                            <div class="w-6 h-6 rounded bg-violet-500/15 flex items-center justify-center text-violet-400 shrink-0"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
		                                            <span class="text-xs font-semibold text-white">🖼️ Generar imagen</span>
		                                        </div>
		                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                    </button>
		                                    <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                        <p class="text-[11px] text-slate-300 leading-relaxed">
		                                            <strong class="text-white">¿Para qué sirve?</strong> Crea una imagen original que
		                                            represente visualmente el contenido de la sección. Ideal para secciones donde
		                                            una imagen vale más que mil palabras.
		                                        </p>
		                                        <div class="bg-slate-800/60 rounded-lg p-2.5 border border-slate-700/30">
		                                            <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold mb-1">🔍 Caso de uso</p>
		                                            <p class="text-[11px] text-slate-300 leading-relaxed">
		                                                Sección sobre <strong class="text-white">"Ecosistemas acuáticos"</strong> → la IA genera
		                                                una imagen de un ecosistema marino con arrecifes, peces y vegetación acuática
		                                                para complementar la explicación.
		                                            </p>
		                                        </div>
		                                        <span class="inline-block text-[10px] px-2 py-0.5 rounded bg-slate-800 text-slate-400 font-mono border border-slate-700">Botón: <span class="text-violet-400">✨ Generar imagen</span></span>
		                                    </div>
		                                </div>

		                                {{-- Generar ilustración --}}
		                                <div class="rounded-lg border border-slate-700/40 overflow-hidden" x-data="{ open: false }">
		                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-slate-800/40 hover:bg-slate-800/70 transition-colors text-left">
		                                        <div class="flex items-center gap-2">
		                                            <div class="w-6 h-6 rounded bg-rose-500/15 flex items-center justify-center text-rose-400 shrink-0"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg></div>
		                                            <span class="text-xs font-semibold text-white">🎨 Generar ilustración decorativa</span>
		                                        </div>
		                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                    </button>
		                                    <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                        <p class="text-[11px] text-slate-300 leading-relaxed">
		                                            <strong class="text-white">¿Para qué sirve?</strong> Similar a la imagen pero con un estilo
		                                            más artístico e ilustrativo. Ideal para portadas de sección, transiciones o
		                                            elementos decorativos que embellezcan la lección.
		                                        </p>
		                                        <div class="bg-slate-800/60 rounded-lg p-2.5 border border-slate-700/30">
		                                            <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold mb-1">🔍 Caso de uso</p>
		                                            <p class="text-[11px] text-slate-300 leading-relaxed">
		                                                Quieres una <strong class="text-white">ilustración de portada</strong> para tu lección
		                                                "El ciclo del agua". La IA genera un dibujo estilizado con el ciclo completo:
		                                                evaporación, condensación, precipitación — ideal como imagen principal.
		                                            </p>
		                                        </div>
		                                        <span class="inline-block text-[10px] px-2 py-0.5 rounded bg-slate-800 text-slate-400 font-mono border border-slate-700">Botón: <span class="text-rose-400">✨ Generar ilustración</span></span>
		                                    </div>
		                                </div>

		                                {{-- Generar diagrama --}}
		                                <div class="rounded-lg border border-slate-700/40 overflow-hidden" x-data="{ open: false }">
		                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-slate-800/40 hover:bg-slate-800/70 transition-colors text-left">
		                                        <div class="flex items-center gap-2">
		                                            <div class="w-6 h-6 rounded bg-cyan-500/15 flex items-center justify-center text-cyan-400 shrink-0"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg></div>
		                                            <span class="text-xs font-semibold text-white">📊 Generar diagrama</span>
		                                        </div>
		                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                    </button>
		                                    <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                        <p class="text-[11px] text-slate-300 leading-relaxed">
		                                            <strong class="text-white">¿Para qué sirve?</strong> Genera diagramas conceptuales
		                                            usando <strong class="text-white">Mermaid.js</strong>: diagramas de flujo,
		                                            mapas conceptuales, líneas de tiempo, organigramas y más. Puedes refinar
		                                            el resultado con instrucciones adicionales.
		                                        </p>
		                                        <div class="bg-slate-800/60 rounded-lg p-2.5 border border-slate-700/30">
		                                            <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold mb-1">🔍 Caso de uso</p>
		                                            <p class="text-[11px] text-slate-300 leading-relaxed">
		                                                Lección sobre <strong class="text-white">"La cadena alimenticia"</strong> → la IA genera
		                                                un diagrama de flujo mostrando productores → consumidores primarios → secundarios →
		                                                descomponedores, con ejemplos de cada nivel.
		                                            </p>
		                                        </div>
		                                        <div class="flex flex-wrap gap-1.5">
		                                            <span class="text-[10px] px-2 py-0.5 rounded bg-slate-800 text-slate-400 font-mono border border-slate-700">Botón: <span class="text-cyan-400">✨ Generar diagrama</span></span>
		                                            <span class="text-[10px] px-2 py-0.5 rounded bg-slate-800 text-slate-400">🖱️ Zoom + pantalla completa</span>
		                                        </div>
		                                    </div>
		                                </div>

		                                {{-- Generar todo --}}
		                                <div class="rounded-lg border border-slate-700/40 overflow-hidden" x-data="{ open: false }">
		                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-slate-800/40 hover:bg-slate-800/70 transition-colors text-left">
		                                        <div class="flex items-center gap-2">
		                                            <div class="w-6 h-6 rounded bg-yellow-500/15 flex items-center justify-center text-yellow-400 shrink-0"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div>
		                                            <span class="text-xs font-semibold text-white">⚡ Generar lección completa</span>
		                                        </div>
		                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                    </button>
		                                    <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                        <p class="text-[11px] text-slate-300 leading-relaxed">
		                                            <strong class="text-white">¿Para qué sirve?</strong> Disponible en el <strong class="text-white">Paso 1</strong>.
		                                            La IA genera automáticamente el título, la descripción, los objetivos de aprendizaje
		                                            y todas las diapositivas estructuradas a partir de un tema o palabra clave.
		                                        </p>
		                                        <div class="bg-slate-800/60 rounded-lg p-2.5 border border-slate-700/30">
		                                            <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold mb-1">🔍 Caso de uso</p>
		                                            <p class="text-[11px] text-slate-300 leading-relaxed">
		                                                Escribes <strong class="text-white">"Fracciones"</strong> y la IA genera:
		                                                título, descripción, objetivos y 4-6 diapositivas con contenido,
		                                                ejemplos y ejercicios prácticos listos para editar.
		                                            </p>
		                                        </div>
		                                        <span class="inline-block text-[10px] px-2 py-0.5 rounded bg-slate-800 text-slate-400 italic border border-slate-700">📌 Solo disponible en el Paso 1 del wizard</span>
		                                    </div>
		                                </div>
		                            </div>

		                            {{-- TAB: Estados --}}
		                            <div x-show="activeTab === 2" x-cloak class="space-y-3">
		                                <h3 class="text-sm font-bold text-white">📊 Estados de la Lección</h3>
		                                <p class="text-xs text-slate-400 leading-relaxed">
		                                    Cada lección pasa por diferentes estados. El estado actual determina lo que puedes
		                                    hacer con ella y quién puede verla.
		                                </p>

		                                {{-- Flujo de estados --}}
		                                <div class="bg-slate-800/40 rounded-lg p-3 border border-slate-700/30">
		                                    <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold mb-2">🔄 Flujo de transición</p>
		                                    <div class="flex items-center justify-between text-[10px]">
		                                        <span class="px-2 py-1 rounded bg-amber-500/20 text-amber-300 font-medium">Borrador</span>
		                                        <span class="text-slate-600">→</span>
		                                        <span class="px-2 py-1 rounded bg-purple-500/20 text-purple-300 font-medium">Programada</span>
		                                        <span class="text-slate-600">→</span>
		                                        <span class="px-2 py-1 rounded bg-emerald-500/20 text-emerald-300 font-medium">Publicada</span>
		                                    </div>
		                                    <p class="text-[10px] text-slate-500 mt-2 text-center">También puedes ir directo de Borrador → Publicada (solo planificadores)</p>
		                                </div>

		                                <div class="space-y-2">
		                                    {{-- Borrador --}}
		                                    <div class="rounded-lg border border-amber-500/20 overflow-hidden" x-data="{ open: true }">
		                                        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-amber-500/5 hover:bg-amber-500/10 transition-colors text-left">
		                                            <div class="flex items-center gap-2">
		                                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0"></span>
		                                                <span class="text-xs font-semibold text-white">Borrador <span class="text-amber-400 font-normal">(DRAFT)</span></span>
		                                            </div>
		                                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                        </button>
		                                        <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                            <p class="text-[11px] text-slate-300 leading-relaxed">
		                                                <strong class="text-white">Estado inicial</strong> al crear una lección.
		                                                Solo tú y los administradores pueden verla. Todos los controles de edición
		                                                están habilitados.
		                                            </p>
		                                            <div class="bg-slate-800/60 rounded p-2">
		                                                <p class="text-[10px] text-slate-400"><span class="text-amber-300">✅</span> Edición completa · <span class="text-slate-500">👁️ No visible para estudiantes</span></p>
		                                            </div>
		                                        </div>
		                                    </div>

		                                    {{-- Programada --}}
		                                    <div class="rounded-lg border border-purple-500/20 overflow-hidden" x-data="{ open: false }">
		                                        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-purple-500/5 hover:bg-purple-500/10 transition-colors text-left">
		                                            <div class="flex items-center gap-2">
		                                                <span class="w-2.5 h-2.5 rounded-full bg-purple-400 shrink-0"></span>
		                                                <span class="text-xs font-semibold text-white">Programada <span class="text-purple-400 font-normal">(SCHEDULED)</span></span>
		                                            </div>
		                                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                        </button>
		                                        <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                            <p class="text-[11px] text-slate-300 leading-relaxed">
		                                                Tiene una <strong class="text-white">fecha de publicación</strong> establecida.
		                                                Se publicará automáticamente en esa fecha. Puedes seguir editando
		                                                hasta el momento de la publicación.
		                                            </p>
		                                            <div class="grid grid-cols-2 gap-2">
		                                                <div class="bg-purple-500/10 rounded p-2 border border-purple-500/15">
		                                                    <p class="text-[10px] text-purple-300 font-semibold">✅ Edición habilitada</p>
		                                                    <p class="text-[10px] text-slate-400">Puedes modificar contenido</p>
		                                                </div>
		                                                <div class="bg-purple-500/10 rounded p-2 border border-purple-500/15">
		                                                    <p class="text-[10px] text-purple-300 font-semibold">📅 Auto-publicación</p>
		                                                    <p class="text-[10px] text-slate-400">Se publica en la fecha indicada</p>
		                                                </div>
		                                            </div>
		                                        </div>
		                                    </div>

		                                    {{-- Publicada --}}
		                                    <div class="rounded-lg border border-emerald-500/20 overflow-hidden" x-data="{ open: false }">
		                                        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-emerald-500/5 hover:bg-emerald-500/10 transition-colors text-left">
		                                            <div class="flex items-center gap-2">
		                                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shrink-0"></span>
		                                                <span class="text-xs font-semibold text-white">Publicada <span class="text-emerald-400 font-normal">(PUBLISHED)</span></span>
		                                            </div>
		                                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                        </button>
		                                        <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                            <p class="text-[11px] text-slate-300 leading-relaxed">
		                                                Visible para los estudiantes según la configuración del período académico.
		                                                <strong class="text-red-400">No se puede editar el contenido ni las secciones.</strong>
		                                                Para modificar una lección publicada, primero debe archivarse.
		                                            </p>
		                                            <div class="bg-red-500/10 rounded p-2 border border-red-500/20">
		                                                <p class="text-[10px] text-red-300"><span class="font-semibold">🔴 Importante:</span> Verifica siempre el contenido con la vista estudiante antes de publicar.</p>
		                                            </div>
		                                        </div>
		                                    </div>
		                                </div>
		                            </div>

		                            {{-- TAB: Secciones --}}
		                            <div x-show="activeTab === 3" x-cloak class="space-y-3">
		                                <h3 class="text-sm font-bold text-white">📐 Gestión de Secciones</h3>
		                                <p class="text-xs text-slate-400 leading-relaxed">
		                                    Las secciones son las diapositivas que componen la lección. Aprende a organizarlas
		                                    como un profesional.
		                                </p>

		                                <div class="rounded-lg border border-slate-700/40 overflow-hidden" x-data="{ open: true }">
		                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-slate-800/40 hover:bg-slate-800/70 transition-colors text-left">
		                                        <span class="text-xs font-semibold text-white flex items-center gap-2"><svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> ➕ Agregar secciones</span>
		                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                    </button>
		                                    <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                        <p class="text-[11px] text-slate-300 leading-relaxed">
		                                            Cada sección nueva se agrega al final. Asígnale un <strong class="text-white">título descriptivo</strong>
		                                            y luego genera o escribe el contenido. Las secciones vacías no se muestran
		                                            a los estudiantes.
		                                        </p>
		                                        <div class="bg-slate-800/60 rounded p-2 border border-slate-700/30">
		                                            <p class="text-[10px] text-slate-400">📌 <strong class="text-white">Tip:</strong> Usa títulos claros como "Introducción", "Desarrollo", "Ejemplos", "Actividad"</p>
		                                        </div>
		                                    </div>
		                                </div>

		                                <div class="rounded-lg border border-slate-700/40 overflow-hidden" x-data="{ open: false }">
		                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-slate-800/40 hover:bg-slate-800/70 transition-colors text-left">
		                                        <span class="text-xs font-semibold text-white flex items-center gap-2"><svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg> ↕️ Reordenar diapositivas</span>
		                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                    </button>
		                                    <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                        <p class="text-[11px] text-slate-300 leading-relaxed">
		                                            Usa los botones <strong class="text-white">▲</strong> y <strong class="text-white">▼</strong>
		                                            junto a cada sección para cambiar su orden. La diapositiva en la posición 1
		                                            es la primera que verán los estudiantes.
		                                        </p>
		                                        <div class="bg-slate-800/60 rounded p-2 border border-slate-700/30">
		                                            <p class="text-[10px] text-slate-400">🎯 <strong class="text-white">Ejemplo:</strong> Mueve la sección "Actividad práctica" al final para que los estudiantes primero lean la teoría</p>
		                                        </div>
		                                    </div>
		                                </div>

		                                <div class="rounded-lg border border-slate-700/40 overflow-hidden" x-data="{ open: false }">
		                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-slate-800/40 hover:bg-slate-800/70 transition-colors text-left">
		                                        <span class="text-xs font-semibold text-white flex items-center gap-2"><svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg> 👁️ Visibilidad individual</span>
		                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                    </button>
		                                    <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                        <p class="text-[11px] text-slate-300 leading-relaxed">
		                                            El toggle <strong class="text-white">👁️ ojo</strong> permite ocultar una sección
		                                            sin eliminarla. Útil cuando quieres <strong class="text-white">desactivar temporalmente</strong>
		                                            contenido que aún no está listo.
		                                        </p>
		                                        <div class="bg-slate-800/60 rounded p-2 border border-slate-700/30">
		                                            <p class="text-[10px] text-slate-400">💡 Las secciones ocultas se conservan en el editor pero no aparecen en la vista estudiante</p>
		                                        </div>
		                                    </div>
		                                </div>

		                                <div class="rounded-lg border border-slate-700/40 overflow-hidden" x-data="{ open: false }">
		                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-slate-800/40 hover:bg-slate-800/70 transition-colors text-left">
		                                        <span class="text-xs font-semibold text-white flex items-center gap-2"><svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> 🗑️ Eliminar secciones</span>
		                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                    </button>
		                                    <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                        <p class="text-[11px] text-slate-300 leading-relaxed">
		                                            Elimina secciones permanentemente. También puedes usar <strong class="text-white">"Reiniciar secciones"</strong>
		                                            para limpiar todas las diapositivas y empezar de cero.
		                                        </p>
		                                        <div class="bg-red-500/10 rounded p-2 border border-red-500/20">
		                                            <p class="text-[10px] text-red-300">⚠️ La eliminación es permanente. No hay papelera de reciclaje.</p>
		                                        </div>
		                                    </div>
		                                </div>
		                            </div>

		                            {{-- TAB: Consejos --}}
		                            <div x-show="activeTab === 4" x-cloak class="space-y-3">
		                                <h3 class="text-sm font-bold text-white">💡 Consejos y Buenas Prácticas</h3>
		                                <p class="text-xs text-slate-400 leading-relaxed">
		                                    Recomendaciones para crear lecciones efectivas y aprovechar al máximo el wizard.
		                                </p>

		                                <div class="rounded-lg border border-slate-700/40 overflow-hidden" x-data="{ open: true }">
		                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-slate-800/40 hover:bg-slate-800/70 transition-colors text-left">
		                                        <span class="text-xs font-semibold text-white flex items-center gap-2">📝 Flujo de trabajo recomendado</span>
		                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                    </button>
		                                    <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                        <ol class="text-[11px] text-slate-300 space-y-1.5 list-decimal list-inside">
		                                            <li><strong class="text-white">Empieza con IA</strong> en el Paso 1: genera toda la lección con un tema</li>
		                                            <li><strong class="text-white">Revisa y ajusta</strong> el contenido generado en cada sección</li>
		                                            <li><strong class="text-white">Agrega recursos visuales</strong>: imágenes, ilustraciones o diagramas</li>
		                                            <li><strong class="text-white">Adjunta materiales</strong> complementarios en el Paso 3</li>
		                                            <li><strong class="text-white">Previsualiza</strong> con 👁️ Vista estudiante</li>
		                                            <li><strong class="text-white">Guarda frecuentemente</strong> con el botón 💾</li>
		                                            <li><strong class="text-white">Publica o programa</strong> cuando estés satisfecho</li>
		                                        </ol>
		                                    </div>
		                                </div>

		                                <div class="rounded-lg border border-slate-700/40 overflow-hidden" x-data="{ open: false }">
		                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-slate-800/40 hover:bg-slate-800/70 transition-colors text-left">
		                                        <span class="text-xs font-semibold text-white flex items-center gap-2">🎯 Cómo estructurar una lección</span>
		                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                    </button>
		                                    <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                        <div class="bg-slate-800/60 rounded p-2.5 border border-slate-700/30">
		                                            <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold mb-1.5">📖 Ejemplo: "La Revolución Industrial"</p>
		                                            <ul class="text-[11px] text-slate-300 space-y-1">
		                                                <li><strong class="text-white">Sección 1:</strong> ¿Qué fue la Revolución Industrial? (texto + imagen)</li>
		                                                <li><strong class="text-white">Sección 2:</strong> Inventos clave (diagrama de línea de tiempo)</li>
		                                                <li><strong class="text-white">Sección 3:</strong> Impacto social (texto + ilustración)</li>
		                                                <li><strong class="text-white">Sección 4:</strong> Actividad: mapa conceptual (solo instructivo)</li>
		                                                <li><strong class="text-white">Recursos:</strong> PDF con lectura complementaria + video</li>
		                                            </ul>
		                                        </div>
		                                    </div>
		                                </div>

		                                <div class="rounded-lg border border-slate-700/40 overflow-hidden" x-data="{ open: false }">
		                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-slate-800/40 hover:bg-slate-800/70 transition-colors text-left">
		                                        <span class="text-xs font-semibold text-white flex items-center gap-2">⚠️ Errores comunes</span>
		                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                    </button>
		                                    <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                        <ul class="text-[11px] text-slate-300 space-y-1.5">
		                                            <li class="flex items-start gap-2"><span class="text-red-400 shrink-0">✗</span><span><strong class="text-white">Publicar sin previsualizar</strong> — errores de formato o contenido incompleto</span></li>
		                                            <li class="flex items-start gap-2"><span class="text-red-400 shrink-0">✗</span><span><strong class="text-white">No guardar cambios frecuentemente</strong> — podrías perder el progreso</span></li>
		                                            <li class="flex items-start gap-2"><span class="text-red-400 shrink-0">✗</span><span><strong class="text-white">Secciones sin título claro</strong> — confusión al navegar</span></li>
		                                            <li class="flex items-start gap-2"><span class="text-red-400 shrink-0">✗</span><span><strong class="text-white">No ajustar el contenido generado por IA</strong> — revisa siempre antes de publicar</span></li>
		                                        </ul>
		                                    </div>
		                                </div>

		                                <div class="rounded-lg border border-slate-700/40 overflow-hidden" x-data="{ open: false }">
		                                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-slate-800/40 hover:bg-slate-800/70 transition-colors text-left">
		                                        <span class="text-xs font-semibold text-white flex items-center gap-2">🔑 Roles: ¿quién puede hacer qué?</span>
		                                        <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
		                                    </button>
		                                    <div x-show="open" x-collapse class="px-3 py-2.5 space-y-2">
		                                        <div class="grid grid-cols-2 gap-2">
		                                            <div class="bg-slate-800/60 rounded p-2 border border-slate-700/30">
		                                                <p class="text-[10px] font-semibold text-white">👨‍🏫 Profesor</p>
		                                                <ul class="text-[10px] text-slate-400 mt-1 space-y-0.5">
		                                                    <li>✅ Crear y editar lecciones</li>
		                                                    <li>✅ Programar fecha de publicación</li>
		                                                    <li>❌ No puede publicar directamente</li>
		                                                </ul>
		                                            </div>
		                                            <div class="bg-slate-800/60 rounded p-2 border border-slate-700/30">
		                                                <p class="text-[10px] font-semibold text-white">📋 Planificador</p>
		                                                <ul class="text-[10px] text-slate-400 mt-1 space-y-0.5">
		                                                    <li>✅ Crear y editar lecciones</li>
		                                                    <li>✅ Publicar directamente</li>
		                                                    <li>✅ Programar fechas</li>
		                                                </ul>
		                                            </div>
		                                        </div>
		                                    </div>
		                                </div>
		                            </div>

	                        </div>
	                    </div>

	                    {{-- ─── Footer: referencia al doc ─── --}}
	                    <div class="px-6 py-3 border-t border-slate-700/50 bg-slate-900/30">
	                        <p class="text-[11px] text-slate-500 flex items-center gap-2">
	                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
	                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
	                            </svg>
	                            {{-- Documentación completa en <code class="text-slate-400 bg-slate-800 px-1.5 py-0.5 rounded text-[10px] font-mono">blueprint/lesson/docWizardLesson.md</code> --}}
	                        </p>
	                    </div>
	                </div>
	            </div>
	        </div>
	    @endif
