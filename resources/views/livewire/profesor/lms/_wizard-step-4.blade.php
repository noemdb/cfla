                        <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-lg p-5 space-y-4">
                            <div class="flex items-center gap-2 pb-3 border-b border-gray-200 dark:border-slate-700">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold">4</span>
                                <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Preguntas de Repaso</h2>
                                <span class="text-[10px] text-gray-400 dark:text-slate-600 font-mono">Markdown</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                                Escribe preguntas de repaso en formato Markdown. Puedes usar <code class="text-emerald-400/80">##</code> títulos,
                                <code class="text-emerald-400/80">**negritas**</code>, listas, tablas y más.
                            </p>
                            <textarea wire:model="reviewQuestions"
                                      rows="10"
                                      class="w-full bg-white dark:bg-slate-950/80 border border-gray-300 dark:border-slate-700/50 rounded-lg px-4 py-2.5 text-sm text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-600 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all resize-y font-mono leading-relaxed"
                                      placeholder="## Preguntas de Repaso

1. Cuál es...?
2. Explica...

### Sección 2
Cómo...?"
                                      spellcheck="false" @disabled($isPublished)></textarea>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400 dark:text-slate-600">{{ strlen($reviewQuestions) }} caracteres</span>
                                <div class="flex items-center gap-2">
                                    <button wire:click="generateReviewQuestions"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                                   text-purple-400 bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/20 transition-all" @disabled($isPublished)>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        Generar con IA
                                    </button>
                                    @if(!empty($reviewQuestions))
                                        <button wire:click="$set('showReviewPreview', true)"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-medium text-cyan-400 bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/20 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Vista previa
                                        </button>
                                    @endif
                                    <button wire:click="$set('reviewQuestions', '')"
                                            wire:confirm="Limpiar las preguntas de repaso?"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-medium text-gray-400 dark:text-slate-500 hover:text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/20 transition-all" @disabled($isPublished)>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- ═══ MODAL: Vista Previa de Preguntas de Repaso ═══ --}}
                        @if($showReviewPreview && !empty($reviewQuestions))
                            <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="review-preview-modal">
                                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="$set('showReviewPreview', false)"></div>
                                <div class="relative min-h-screen flex items-center justify-center p-4">
                                    <div class="relative w-full max-w-3xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-2xl shadow-2xl overflow-hidden">
                                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800/90">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Vista Previa — Preguntas de Repaso</h3>
                                            </div>
                                            <button wire:click="$set('showReviewPreview', false)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700/50 text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6L18 18"/></svg>
                                            </button>
                                        </div>
                                        <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
                                            <x-lms.math-text
                                                :content="Str::markdown($reviewQuestions)"
                                                class="prose prose-sm prose-invert max-w-none
                                                       prose-headings:text-white prose-headings:font-bold
                                                       prose-h2:text-lg prose-h2:border-b prose-h2:border-slate-700 prose-h2:pb-2 prose-h2:mb-4
                                                       prose-h3:text-base prose-h3:mt-6 prose-h3:mb-2
                                                       prose-p:text-slate-300 prose-p:leading-relaxed
                                                       prose-strong:text-emerald-300
                                                       prose-ul:text-slate-300 prose-ol:text-slate-300
                                                       prose-li:marker:text-emerald-500
                                                       prose-code:text-cyan-300 prose-code:bg-slate-700/50 prose-code:px-1 prose-code:py-0.5 prose-code:rounded prose-code:text-[13px]
                                                       prose-hr:border-slate-700" />
                                        </div>
                                        <div class="flex items-center justify-end gap-3 px-6 py-3 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/60">
                                            <button wire:click="$set('showReviewPreview', false)"
                                                    class="px-4 py-2 text-xs font-medium text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-slate-700/50 hover:bg-gray-200 dark:hover:bg-slate-700 rounded-lg transition-all">
                                                Cerrar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
