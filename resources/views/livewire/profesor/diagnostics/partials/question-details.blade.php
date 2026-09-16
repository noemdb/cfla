@php
    $tipoLabel = match ($question->tipo_pregunta) {
        'multiple' => 'Selección múltiple',
        'open' => 'Pregunta abierta',
        'scale' => 'Escala de valoración',
        default => (string) $question->tipo_pregunta,
    };

    $dificultadLabel = match ($question->difficulty) {
        'easy' => 'Fácil',
        'hard' => 'Difícil',
        default => 'Media',
    };

    $rows = collect([
        'Área de formación' => $question->pensum?->asignatura?->name ?? '—',
        'Diagnóstico' => $question->diagMain?->name ?? '—',
        'Tipo' => $tipoLabel,
        'Dificultad' => $dificultadLabel,
        'Ponderación' => $question->weighing ?? '—',
        'Orden' => $question->orden ?? '—',
        'Estado' => $question->activo ? 'Activa' : 'Inactiva',
        'Competencia' => $question->competency?->name,
        'Indicador' => $question->indicator?->description,
    ])->reject(fn ($value) => blank($value));
@endphp

<div class="mt-2 text-left">
    <p class="mb-3 text-[13px] font-semibold leading-snug text-secondary-800 dark:text-secondary-200">
        {{ $question->pregunta }}
    </p>

    <div class="mb-3 rounded-lg bg-secondary-100/70 p-3 dark:bg-secondary-900/40">
        @foreach ($rows as $label => $value)
            <div class="flex items-start justify-between gap-3 border-b border-secondary-200/60 py-1.5 last:border-0 dark:border-secondary-600/40">
                <span class="text-[10px] font-semibold uppercase tracking-wide text-secondary-500">{{ $label }}</span>
                <span class="max-w-[60%] text-right text-[12px] font-bold text-secondary-700 dark:text-secondary-300">{{ $value }}</span>
            </div>
        @endforeach
    </div>

    @if ($question->tipo_pregunta === 'multiple' && $question->options->isNotEmpty())
        <ul class="text-left">
            @foreach ($question->options->values() as $index => $option)
                <li class="flex items-start gap-2 py-1 text-[12px] text-secondary-700 dark:text-secondary-300">
                    <span class="font-bold">{{ chr(65 + $index) }}.</span>
                    <span class="flex-1">{{ $option->opcion }}</span>
                    @if ((int) $option->valor > 0)
                        <span class="ml-2 shrink-0 rounded-full bg-emerald-500/15 px-1.5 py-0.5 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">Correcta</span>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>
