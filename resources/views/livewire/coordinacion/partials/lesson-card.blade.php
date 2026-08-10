@php $pev = $lesson->pevaluacion; @endphp
<div class="bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-xl p-4 sm:p-5 transition-all duration-200 hover:border-emerald-500/30">
    <div class="flex items-start justify-between gap-4">
        <div class="flex-1 min-w-0">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">{{ $lesson->topic }}</h3>
            <div class="flex flex-wrap gap-2 mt-2 text-xs text-gray-500 dark:text-gray-400">
                <span class="px-2 py-0.5 bg-purple-100 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 rounded-full font-medium">
                    {{ $pev?->pensum?->asignatura?->name ?? '—' }}
                </span>
                <span class="inline-flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    {{ $pev?->profesor?->lastname ?? '' }}, {{ $pev?->profesor?->name ?? '—' }}
                </span>
                <span class="inline-flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    {{ $pev?->seccion?->grado?->name ?? '' }} · Sección {{ $pev?->seccion?->name ?? '—' }}
                </span>
                <span class="inline-flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    {{ $pev?->lapso?->name ?? '—' }}
                </span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button wire:click="previewLesson({{ $lesson->id }})"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs bg-white dark:bg-gray-700 hover:bg-emerald-100 dark:hover:bg-emerald-500/10 text-gray-600 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-lg border border-gray-200 dark:border-transparent hover:border-emerald-300 dark:hover:border-emerald-500/20 transition-colors font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Vista Estudiante
                </button>
                <button wire:click="openActivityReview({{ $lesson->id }})"
                    class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg transition-all border
                        {{ $lesson->status
                            ? 'text-emerald-500 bg-emerald-500/10 hover:bg-emerald-500/20 border-emerald-500/20 hover:border-emerald-500/40'
                            : 'text-amber-500 bg-amber-500/10 hover:bg-amber-500/20 border-amber-500/20 hover:border-amber-500/40' }}"
                    title="{{ $lesson->status ? 'Actividad aprobada · ver/comentar' : 'Actividad en revisión: revisar y aprobar' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                @if($lesson->lmsPublication && $lesson->lmsPublication->status === 'SCHEDULED')
                    <button wire:click="confirmPublish({{ $lesson->id }})"
                        class="px-2.5 py-1.5 rounded-lg text-white bg-emerald-600 hover:bg-emerald-500 shadow-sm border border-emerald-400/40 transition-all text-xs font-bold flex items-center gap-1"
                        title="Publicar ahora">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Publication Info --}}
    @if($lesson->lmsPublication)
        @php $pubStatus = $lesson->lmsPublication->status; @endphp
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-white/5 flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold border
                {{ match($pubStatus) {
                    'PUBLISHED' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
                    'SCHEDULED' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20',
                    'ARCHIVED' => 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-white/10',
                    default => 'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-white/10',
                } }}">
                <span class="w-1.5 h-1.5 rounded-full {{ match($pubStatus) {
                    'PUBLISHED' => 'bg-emerald-500',
                    'SCHEDULED' => 'bg-amber-500',
                    default => 'bg-gray-400 dark:bg-gray-500',
                } }}"></span>
                {{ \App\Services\Lms\LmsPublicationStatus::label($pubStatus) }}
            </span>
            @if($pubStatus === 'PUBLISHED' && $lesson->lmsPublication->published_at)
                <span class="inline-flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Publicada el {{ $lesson->lmsPublication->published_at->format('d/m/Y') }}
                </span>
            @elseif($pubStatus === 'SCHEDULED' && $lesson->lmsPublication->publish_at)
                <span class="inline-flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Programada para {{ $lesson->lmsPublication->publish_at->format('d/m/Y H:i') }}
                </span>
            @endif
            @if($lesson->lmsSections && $lesson->lmsSections->count() > 0)
                <span class="inline-flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    {{ $lesson->lmsSections->count() }} sección(es)
                </span>
            @endif
        </div>
    @elseif($lesson->lmsSections && $lesson->lmsSections->count() > 0)
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-white/5 flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold border bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-white/10">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-gray-500"></span>
                Borrador
            </span>
            <span class="inline-flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                {{ $lesson->lmsSections->count() }} sección(es)
            </span>
        </div>
    @endif
</div>