<div
    x-data
    wire:poll.5s="refreshCount"
    class="inline-flex items-center gap-1"
    title="Lecciones programadas pendientes de aprobación"
>
    @if($count > 0)
        <span class="inline-flex items-center justify-center rounded-full bg-amber-400/15 px-2 py-0.5 text-xs font-semibold text-amber-400 ring-1 ring-inset ring-amber-400/30">
            {{ $count }}
        </span>
    @endif
</div>
