<div class="fade-in">
    <div class="mb-6 sm:mb-8">
        <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Mis suplencias</h1>
        <p class="text-emerald-600 dark:text-emerald-400 font-medium text-sm">Clases que coordinación te asignó para cubrir</p>
    </div>

    @if ($message)
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-sm font-medium">{{ $message }}</div>
    @endif

    @if ($assignments->isEmpty())
        <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-8 text-center">
            <p class="text-sm text-gray-400">No tenés suplencias asignadas.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($assignments as $assignment)
                <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $assignment->slot?->lesson?->pevaluacion?->pensum?->asignatura?->name ?? 'Clase' }}
                                · {{ $assignment->slot?->lesson?->pevaluacion?->seccion?->name ?? '' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $assignment->slot?->period?->period_label }}
                                @if ($assignment->absence?->profesor)
                                    · cubre a {{ $assignment->absence->profesor->lastname }}, {{ $assignment->absence->profesor->name }}
                                @endif
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($assignment->status === 'pending')
                                <button wire:click="confirmAssignment({{ $assignment->id }})"
                                    class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-500 transition-colors">
                                    Confirmar
                                </button>
                                <button wire:click="declineAssignment({{ $assignment->id }})"
                                    class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-500 text-xs font-bold hover:bg-red-500/20 transition-colors">
                                    Rechazar
                                </button>
                            @else
                                <span class="text-xs px-2 py-1 rounded font-bold {{ $assignment->status === 'confirmed' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500' }}">
                                    {{ ucfirst($assignment->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>