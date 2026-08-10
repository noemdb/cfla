{{-- Modales compartidos de lecciones LMS: preview + publicación.
     Requiere que el componente Livewire use InteractsWithLmsLessons.
     Las variables se pasan como props desde el blade que lo incluye. --}}

@props([
    'showLessonPreview' => false,
    'previewData' => [],
    'closeMethod' => 'closeLessonPreview',
    'showPublishModal' => false,
    'publishActivityTitle' => '',
    'publishPublishAt' => null,
])

{{-- ===== MODAL: Vista Previa de Lección (student-preview) ===== --}}
@if($showLessonPreview && $previewData)
    <x-lms.student-preview
        :preview="$previewData"
        :close-method="$closeMethod"
        wireKey="lms-lesson-preview" />
@endif

{{-- ===== MODAL: Publicar lección programada ===== --}}
@if($showPublishModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" wire:key="publish-confirm">
        <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700/50 flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Publicar lección
                </h3>
                <button wire:click="cancelPublish" class="p-2 min-w-[44px] min-h-[44px] rounded-lg text-gray-400 dark:text-slate-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <p class="text-sm text-gray-600 dark:text-slate-300">
                    ¿Publicar la lección <strong class="text-gray-900 dark:text-white">{{ $publishActivityTitle }}</strong>?
                </p>
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-2">
                    Al publicarla pasará de <strong>Programada</strong> a <strong>Publicada</strong> y será visible para los estudiantes en su aula virtual.
                </p>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Fecha de publicación</label>
                    <input type="datetime-local" wire:model="publishPublishAt"
                           class="w-full bg-gray-50 dark:bg-slate-900/50 border border-gray-200 dark:border-slate-600 text-gray-800 dark:text-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                    @error('publishPublishAt') <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                    <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-1">
                        Se conserva la fecha programada. Vacío: visible de inmediato. Con fecha futura: queda en vista previa (1ª sección) hasta esa fecha.
                    </p>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 dark:bg-slate-900/50 border-t border-gray-200 dark:border-slate-700/50 flex items-center justify-end gap-2">
                <button wire:click="cancelPublish"
                        class="px-4 py-1.5 rounded-lg text-sm font-medium text-gray-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 hover:bg-gray-100 dark:hover:bg-slate-700 transition-all">
                    Cancelar
                </button>
                <button wire:click="doPublish"
                        class="px-4 py-1.5 rounded-lg text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-sm border border-emerald-400/40 transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Publicar
                </button>
            </div>
        </div>
    </div>
@endif
