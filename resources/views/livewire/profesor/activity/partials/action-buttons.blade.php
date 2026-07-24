{{-- btnGroup: Action buttons (shared between grid/table views) --}}
{{-- Requires: $item (activity), $enable_edit, $achievements (collection) --}}
<div class="flex items-center gap-1.5 shrink-0"
     x-data="{ actionsOpen: false }"
     @click.away="actionsOpen = false">

    {{-- Desktop group: all actions inline on sm+ --}}
    <div class="hidden sm:flex items-center gap-1.5">
        {{-- View Detail --}}
        <button wire:click="viewDetail({{ $item->id }})"
            title="Ver todos los detalles"
            class="inline-flex items-center justify-center min-w-[44px] min-h-[44px] w-8 h-8 rounded-lg text-xs font-bold bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
        </button>
        {{-- LMS: Wizard de Lecciones --}}
        <a href="{{ route('app.profesors.lms.lesson.wizard', ['activity_id' => $item->id]) }}"
            title="Abrir en Wizard de Lecciones"
            class="inline-flex items-center justify-center min-w-[44px] min-h-[44px] w-8 h-8 rounded-lg text-xs font-bold bg-violet-500/10 text-violet-400 hover:bg-violet-500/20 border border-violet-500/20 transition-all duration-200"
            @if(!$enable_edit)
                onclick="event.preventDefault()"
                style="pointer-events:none; opacity:0.4;"
            @endif>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </a>
        <button wire:click="setEditActivity({{ $item->id }})"
            title="Editar actividad"
            {{ $enable_edit ? '' : 'disabled' }}
            class="inline-flex items-center justify-center min-w-[44px] min-h-[44px] w-8 h-8 rounded-lg text-xs font-bold {{ $enable_edit ? 'bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20' : 'bg-gray-800/50 text-gray-600 cursor-not-allowed' }} transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
        </button>
        @php $disabled = ($achievements->count() > 0 || !$enable_edit); @endphp
        <button wire:click="delActivity({{ $item->id }})"
            title="Eliminar actividad"
            {{ $disabled ? 'disabled' : '' }}
            class="inline-flex items-center justify-center min-w-[44px] min-h-[44px] w-8 h-8 rounded-lg text-xs font-bold {{ $disabled ? 'bg-gray-800/50 text-gray-600 cursor-not-allowed' : 'bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20' }} transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </button>
    </div>

    {{-- Mobile: only "···" visible — todos los botones dentro del dropdown --}}
    <div class="relative sm:hidden">
        <button @click="actionsOpen = !actionsOpen"
                class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-slate-700/30 hover:bg-gray-200 dark:hover:bg-slate-600/50 border border-gray-200 dark:border-slate-600/30 transition-all"
                title="Más acciones">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 6a2 2 0 110-4 2 2 0 010 4z"/>
                <path d="M10 12a2 2 0 110-4 2 2 0 010 4z"/>
                <path d="M10 18a2 2 0 110-4 2 2 0 010 4z"/>
            </svg>
        </button>

        <div x-show="actionsOpen"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute right-0 z-50 mt-1 min-w-[200px] bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-xl py-1"
             @click="actionsOpen = false">

            {{-- View Detail --}}
            <button wire:click="viewDetail({{ $item->id }})"
                    class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                <svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Ver detalles
            </button>

            {{-- LMS Wizard --}}
            @if($enable_edit)
                <a href="{{ route('app.profesors.lms.lesson.wizard', ['activity_id' => $item->id]) }}"
                   class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                    <svg class="w-4 h-4 shrink-0 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Wizard de Lecciones
                </a>
            @else
                <span class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-500 cursor-not-allowed">
                    <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Wizard de Lecciones
                </span>
            @endif

            {{-- Editar --}}
            @if($enable_edit)
                <button wire:click="setEditActivity({{ $item->id }})"
                        class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                    <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar actividad
                </button>
            @else
                <span class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-500 cursor-not-allowed">
                    <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar actividad
                </span>
            @endif

            {{-- Eliminar --}}
            @php $disabled = ($achievements->count() > 0 || !$enable_edit); @endphp
            @if(!$disabled)
                <button wire:click="delActivity({{ $item->id }})"
                        wire:confirm="¿Eliminar esta actividad? Se eliminarán todos los indicadores asociados."
                        class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                    <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Eliminar actividad
                </button>
            @else
                <span class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-500 cursor-not-allowed">
                    <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Eliminar actividad
                </span>
            @endif
        </div>
    </div>
</div>
