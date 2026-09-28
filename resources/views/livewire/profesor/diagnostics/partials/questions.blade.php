{{-- Questions Tab --}}
<div class="space-y-4">
    {{-- Search & Filter Bar --}}
    <div class="pb-4 border-b border-white/5 space-y-3">
        {{-- Primera línea: buscador a todo lo ancho --}}
        <div class="relative w-full">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar preguntas..."
                class="w-full bg-gray-800/50 border border-white/10 rounded-lg pl-9 pr-3 py-1.5 text-xs text-gray-300 placeholder-gray-600 focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/20 transition-all duration-200">
            @if($search)
                <button wire:click="$set('search', '')" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            @endif
        </div>

        {{-- Segunda línea: resto de filtros --}}
        <div class="flex flex-wrap items-center gap-3">
        <select wire:model.live="filterType"
            class="bg-gray-800/50 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-gray-300 focus:border-purple-500/50 transition-all duration-200">
            <option value="">Todos los tipos</option>
            <option value="multiple">Múltiple</option>
            <option value="open">Abierta</option>
            <option value="scale">Escala</option>
        </select>

        <button wire:click="resetFilters"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200"
            title="Limpiar filtros">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Limpiar
        </button>

        <button wire:click="openQuestionModal"
            class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-bold bg-purple-500/10 text-purple-400 hover:bg-purple-500/20 border border-purple-500/20 transition-all duration-200 ml-auto">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            + Nueva Pregunta
        </button>

        {{-- Ayuda: formato compatible de notación matemática/química (KaTeX + mhchem) --}}
        <div x-data="{ mathHelpOpen: false }" class="contents">
            <button type="button" @click="mathHelpOpen = true"
                title="Ver formato compatible de notación matemática y química"
                aria-label="Ayuda de notación matemática"
                aria-haspopup="dialog"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-gray-800/50 text-gray-400 hover:text-white border border-white/10 hover:border-white/20 transition-all duration-200 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M12 18h.01M12 3c1.256 0 2.47.202 3.612.586a9.044 9.044 0 012.907 1.895 8.997 8.997 0 011.896 2.908A8.95 8.95 0 0121 12a8.95 8.95 0 01-.585 3.611 8.997 8.997 0 01-1.896 2.908 9.044 9.044 0 01-2.907 1.895A8.98 8.98 0 0112 21a8.98 8.98 0 01-3.612-.586 9.044 9.044 0 01-2.907-1.895 8.997 8.997 0 01-1.896-2.908A8.95 8.95 0 013 12a8.95 8.95 0 01.585-3.611 8.997 8.997 0 011.896-2.908 9.044 9.044 0 012.907-1.895A8.98 8.98 0 0112 3z"></path>
                </svg>
            </button>

            <div x-cloak x-show="mathHelpOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="math-help-title">
                <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="mathHelpOpen = false"></div>
                <div x-show="mathHelpOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    @keydown.escape.window="mathHelpOpen = false"
                    class="relative bg-gray-900 border border-white/10 rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
                    <div class="sticky top-0 bg-gray-900/95 backdrop-blur-sm border-b border-white/5 px-6 py-3 flex items-center justify-between z-10">
                        <div>
                            <h3 id="math-help-title" class="text-sm font-bold text-white">Notación matemática y química</h3>
                            <p class="text-[11px] text-gray-500 mt-0.5">Formato compatible (KaTeX + mhchem)</p>
                        </div>
                        <button type="button" @click="mathHelpOpen = false" aria-label="Cerrar ayuda"
                            class="min-w-[44px] min-h-[44px] w-7 h-7 rounded-lg bg-gray-800/50 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-5 space-y-5 text-xs leading-relaxed">
                        <div>
                            <h4 class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">1 · Delimitadores (obligatorios)</h4>
                            <p class="text-gray-500 mb-2">Solo el texto <span class="text-gray-300">dentro</span> de estos delimitadores se convierte en fórmula:</p>
                            <ul class="space-y-1.5">
                                <li class="flex items-start gap-2"><code class="shrink-0 px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">\( ... \)</code><span class="text-gray-400">matemática en línea, dentro de una frase</span></li>
                                <li class="flex items-start gap-2"><code class="shrink-0 px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">$$ ... $$</code><span class="text-gray-400">fórmula destacada en bloque</span></li>
                                <li class="flex items-start gap-2"><code class="shrink-0 px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">\[ ... \]</code><span class="text-gray-400">alternativa de bloque</span></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">2 · Matemáticas (núcleo KaTeX)</h4>
                            <ul class="space-y-1.5 text-gray-400">
                                <li><code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">\frac&#123;a&#125;&#123;b&#125;</code>, <code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">\sqrt&#123;x&#125;</code>, potencias <code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">x^&#123;2&#125;</code>, subíndices <code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">x_&#123;n&#125;</code></li>
                                <li>Griegas <code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">\pi \alpha \beta \theta \Delta</code> · operadores <code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">\pm \times \cdot \sum \int</code></li>
                                <li>Funciones <code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">\sin \cos \tan \log \ln \lim</code> · matrices <code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">\begin&#123;pmatrix&#125;…\end&#123;pmatrix&#125;</code></li>
                                <li class="pt-1"><span class="text-gray-500">Ejemplo:</span> <code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-emerald-300 font-mono">$$f(x) = \frac&#123;x^2 - 1&#125;&#123;x + 1&#125;$$</code></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">3 · Química (extensión mhchem)</h4>
                            <ul class="space-y-1.5 text-gray-400">
                                <li>Ecuaciones con <code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">\ce&#123;…&#125;</code> — <span class="text-gray-500">también funciona sin delimitadores en estas celdas:</span> <code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-emerald-300 font-mono">\ce&#123;N2 + 3H2 &lt;=&gt; 2NH3&#125;</code></li>
                                <li>Flechas <code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">-&gt;</code>, <code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">&lt;=&gt;</code>, condición <code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">-&gt;[\Delta]</code> · iones <code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">Ba^2+</code> · precipitado <code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">v</code> · gas <code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">^</code></li>
                                <li>Unidades con <code class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-cyan-300 font-mono">\pu&#123;123 kJ/mol&#125;</code></li>
                            </ul>
                        </div>

                        <div class="rounded-lg bg-amber-500/5 border border-amber-500/20 px-4 py-3">
                            <h4 class="text-[11px] font-bold uppercase tracking-widest text-amber-400 mb-1.5">A tener en cuenta</h4>
                            <ul class="list-disc list-inside space-y-1 text-gray-400">
                                <li>El botón <span class="text-emerald-400 font-medium">Etiquetar Not. Mat.</span> detecta y convierte las expresiones automáticamente con IA.</li>
                                <li>El <span class="text-gray-300">$ simple no es delimitador aquí</span> (para no confundirlo con precios).</li>
                                <li>Por seguridad están bloqueadas <code class="px-1 py-px rounded bg-white/5 border border-white/10 text-gray-300 font-mono">\htmlData \htmlClass \htmlStyle</code>.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    {{-- Results Summary --}}
    <div class="flex items-center justify-between">
        <p class="text-[11px] text-gray-500">
            Mostrando <span class="text-gray-400 font-medium">{{ $questions->firstItem() ?? 0 }}</span>–
            <span class="text-gray-400 font-medium">{{ $questions->lastItem() ?? 0 }}</span>
            de <span class="text-gray-400 font-medium">{{ $questions->total() }}</span> preguntas
        </p>
    </div>

    {{-- Questions Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="py-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Pregunta</th>
                    <th class="py-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Tipo</th>
                    <th class="py-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Área</th>
                    <th class="py-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Dificultad</th>
                    <th class="py-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</th>
                    <th class="py-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $question)
                    <tr class="border-b border-white/5 hover:bg-white/[0.02] transition-colors">
                        <td class="py-2 px-4">
                            <x-diag.math-cell :content="$question->pregunta" uid="q-{{ $question->id }}" title="{{ $question->pregunta }}" class="text-xs text-gray-300 max-w-[300px] leading-snug" />
                            <span class="text-[10px] text-gray-600">{{ $question->created_at->format('d/m/Y') }}</span>
                        </td>
                        <td class="py-2 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold {{ $question->tipo_pregunta === 'multiple' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : ($question->tipo_pregunta === 'open' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-green-500/10 text-green-400 border border-green-500/20') }}">
                                {{ $question->tipo_pregunta === 'multiple' ? 'Múltiple' : ($question->tipo_pregunta === 'open' ? 'Abierta' : 'Escala') }}
                            </span>
                        </td>
                        <td class="py-2 px-4">
                            <span class="text-xs text-gray-400">{{ $question->asignatura_name ?? '—' }}</span>
                        </td>
                        <td class="py-2 px-4">
                            <span class="text-xs {{ $question->difficulty === 'easy' ? 'text-emerald-400' : ($question->difficulty === 'medium' ? 'text-amber-400' : 'text-red-400') }}">
                                {{ $question->difficulty === 'easy' ? 'Fácil' : ($question->difficulty === 'medium' ? 'Media' : 'Difícil') }}
                            </span>
                        </td>
                        <td class="py-2 px-4">
                            @if($question->activo)
                                <span class="inline-flex items-center gap-1 text-xs text-emerald-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="py-2 px-4 text-right">
                            <div class="inline-flex items-center rounded-lg overflow-hidden border border-white/10 divide-x divide-white/10" role="group" aria-label="Acciones de pregunta">
                                <button wire:click="openQuestionModal({{ $question->id }})"
                                    title="Editar pregunta"
                                    class="inline-flex items-center justify-center min-w-[44px] min-h-[44px] w-11 h-11 text-xs font-bold bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button wire:click="showQuestionDetails({{ $question->id }})"
                                    title="Ver detalle de la pregunta"
                                    class="inline-flex items-center justify-center min-w-[44px] min-h-[44px] w-11 h-11 text-xs font-bold bg-sky-500/10 text-sky-400 hover:bg-sky-500/20 transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <button wire:click="confirmDeleteQuestion({{ $question->id }})"
                                    title="Eliminar pregunta"
                                    class="inline-flex items-center justify-center min-w-[44px] min-h-[44px] w-11 h-11 text-xs font-bold bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center">
                            <svg class="w-12 h-12 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            <p class="text-sm font-medium text-gray-400">
                                @if($search || $filterType || $filterSubject)
                                    No se encontraron preguntas
                                @else
                                    No hay preguntas registradas
                                @endif
                            </p>
                            @if($search || $filterType || $filterSubject)
                                <button wire:click="resetFilters" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                                    Limpiar filtros
                                </button>
                            @else
                                <button wire:click="openQuestionModal" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-500/10 text-purple-400 hover:bg-purple-500/20 border border-purple-500/20 transition-all duration-200">
                                    + Crear primera pregunta
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($questions->hasPages())
        <div class="mt-4 pt-4 border-t border-white/5">
            {{ $questions->links('vendor.livewire.custom-tailwind') }}
        </div>
    @endif

    <!-- ===== DIALOG: Confirmar Eliminación (WireUI x-dialog) ===== -->
    <x-dialog id="question-delete" title="Eliminar pregunta" width="md" blur="lg">
        <div class="text-left">
            <div class="flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-500/10 text-red-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="text-sm text-gray-700 dark:text-slate-200">
                        ¿Eliminar esta pregunta?
                        @if($confirmDeleteQuestionText)
                            <span class="mt-1 block font-medium text-gray-500 dark:text-slate-400 line-clamp-3">"{{ \Illuminate\Support\Str::limit(strip_tags($confirmDeleteQuestionText), 180) }}"</span>
                        @endif
                    </p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">
                        Esta acción no se puede deshacer. Solo se puede eliminar si no tiene respuestas asociadas.
                    </p>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-3 border-t border-white/10 pt-4">
                <button type="button" x-on:click="close(); $wire.cancelDeleteQuestion()"
                    class="px-4 py-2 rounded-lg text-sm font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 transition-all duration-200">
                    Cancelar
                </button>
                <button type="button" wire:click="deleteQuestion"
                    x-on:click="close()"
                    wire:loading.attr="disabled" wire:target="deleteQuestion"
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold text-white bg-red-600 hover:bg-red-500 transition-all duration-200 disabled:opacity-60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span wire:loading.remove wire:target="deleteQuestion">Sí, eliminar</span>
                    <span wire:loading wire:target="deleteQuestion">Eliminando...</span>
                </button>
            </div>
        </div>
    </x-dialog>
</div>
