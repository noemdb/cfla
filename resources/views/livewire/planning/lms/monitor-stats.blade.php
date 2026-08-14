<div wire:poll.{{ config('broadcasting.poll_interval', 5000) }}ms="refreshStats" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
    <div class="bg-white dark:bg-slate-800/40 border border-gray-200 dark:border-slate-700/50 rounded-lg p-3">
        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $total }}</p>
        <p class="text-xs text-gray-500 dark:text-slate-400">Total lecciones</p>
    </div>
    <div class="bg-emerald-50/50 dark:bg-emerald-500/5 border border-emerald-200 dark:border-emerald-500/20 rounded-lg p-3">
        <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ $published }}</p>
        <p class="text-xs text-emerald-600/70 dark:text-emerald-400/70">Publicadas</p>
    </div>
    <div class="bg-amber-50/50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/20 rounded-lg p-3">
        <p class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ $scheduled }}</p>
        <p class="text-xs text-amber-600/70 dark:text-amber-400/70">Programadas</p>
    </div>
    <div class="bg-gray-100/50 dark:bg-slate-500/5 border border-slate-200 dark:border-slate-500/20 rounded-lg p-3">
        <p class="text-lg font-bold text-gray-500 dark:text-slate-400">{{ $draft }}</p>
        <p class="text-xs text-gray-500/70 dark:text-slate-400/70">Borradores</p>
    </div>
    <div class="bg-red-50/50 dark:bg-red-500/5 border border-red-200 dark:border-red-500/20 rounded-lg p-3">
        <p class="text-lg font-bold text-red-600 dark:text-red-400">{{ $archived }}</p>
        <p class="text-xs text-red-400/70">Archivadas</p>
    </div>
    <div class="bg-blue-50/50 dark:bg-blue-500/5 border border-blue-200 dark:border-blue-500/20 rounded-lg p-3">
        <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $withContent }}</p>
        <p class="text-xs text-blue-400/70">Con contenido</p>
    </div>
    <div class="bg-purple-50/50 dark:bg-purple-500/5 border border-purple-500/20 rounded-lg p-3">
        <p class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ $totalActivities }}</p>
        <p class="text-xs text-purple-400/70">Total actividades</p>
    </div>
</div>
