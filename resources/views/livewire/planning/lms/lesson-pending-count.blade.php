<div
    x-data
    wire:poll.{{ config('broadcasting.poll_interval', 5000) }}ms="refreshCount"
    class="inline-flex items-center gap-1"
    title="Lecciones programadas pendientes de aprobación"
>
    @if($count > 0)
        <a href="{{ route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']) }}"
           class="inline-flex items-center justify-center rounded-full bg-amber-400/15 px-2 py-0.5 text-xs font-semibold text-amber-400 ring-1 ring-inset ring-amber-400/30 hover:bg-amber-400/25 hover:ring-amber-400/50 transition-all duration-150"
           title="Ver lecciones programadas en el monitor">
            {{ $count }}
        </a>
    @else
        <span class="inline-flex items-center justify-center rounded-full bg-amber-400/15 px-2 py-0.5 text-xs font-semibold text-amber-400 ring-1 ring-inset ring-amber-400/30">
            0
        </span>
    @endif
</div>
