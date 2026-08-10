@props([
    'activity' => null,
    'showPublish' => true,
])

@php
    $lmsPub = $activity?->lmsPublication;
@endphp

@if($activity && $lmsPub)
    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-white/5">
        <div class="flex items-center justify-between mb-2">
            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Lección (LMS)
            </span>
            <div class="flex items-center gap-3">
                <button type="button" wire:click="previewLesson({{ $activity->id }})"
                    class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300 transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Ver lección
                </button>
                @if($showPublish && $lmsPub->status === 'SCHEDULED')
                    <button type="button" wire:click="confirmPublish({{ $activity->id }})"
                        class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Publicar
                    </button>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if($lmsPub->status === 'PUBLISHED')
                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-200 dark:border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Publicada
                </span>
            @elseif($lmsPub->status === 'SCHEDULED')
                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-md border border-amber-200 dark:border-amber-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    Programada
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 text-[10px] font-bold rounded-md border border-gray-200 dark:border-white/5">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                    Borrador
                </span>
            @endif
            @if($activity->lmsSections && $activity->lmsSections->count() > 0)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-sky-100 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 text-[10px] font-bold rounded-md border border-sky-200 dark:border-sky-500/20">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    {{ $activity->lmsSections->count() }} {{ $activity->lmsSections->count() === 1 ? 'sección' : 'secciones' }}
                </span>
            @endif
            @if($lmsPub->publish_at)
                <span class="text-[10px] text-gray-500 dark:text-gray-500">
                    Desde {{ \Carbon\Carbon::parse($lmsPub->publish_at)->format('d/m/Y') }}
                </span>
            @endif
            @if($lmsPub->unpublish_at)
                <span class="text-[10px] text-gray-500 dark:text-gray-500">
                    Hasta {{ \Carbon\Carbon::parse($lmsPub->unpublish_at)->format('d/m/Y') }}
                </span>
            @endif
        </div>
    </div>
@endif
