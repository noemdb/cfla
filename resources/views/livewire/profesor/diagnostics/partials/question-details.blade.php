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
    <x-diag.math-cell :content="$question->pregunta" uid="qd-{{ $question->id }}" class="mb-3 text-[13px] font-semibold leading-snug text-secondary-800 dark:text-secondary-200" />

    <div class="mb-3 rounded-lg bg-secondary-100/70 p-3 dark:bg-secondary-900/40">
        @foreach ($rows as $label => $value)
            <div class="flex items-start justify-between gap-3 border-b border-secondary-200/60 py-1.5 last:border-0 dark:border-secondary-600/40">
                <span class="text-[10px] font-semibold uppercase tracking-wide text-secondary-500">{{ $label }}</span>
                <span class="max-w-[60%] text-right text-[12px] font-bold text-secondary-700 dark:text-secondary-300">{{ $value }}</span>
            </div>
        @endforeach
    </div>

    @if ($question->tipo_pregunta === 'multiple' && $question->options->isNotEmpty())
        <p class="mb-2 text-[10px] font-bold uppercase tracking-widest text-secondary-500">Opciones ({{ $question->options->count() }})</p>
        <ul class="space-y-2 text-left">
            @foreach ($question->options->values() as $index => $option)
                @php($isCorrect = (int) $option->valor > 0)
                <li class="flex items-center gap-3 rounded-lg border px-3 py-2 text-[12px] transition-colors {{ $isCorrect
                        ? 'border-emerald-500/50 bg-emerald-500/10 shadow-[0_0_0_1px_rgba(16,185,129,0.25)]'
                        : 'border-secondary-200/60 bg-white/40 dark:border-white/5 dark:bg-white/[0.02]' }}">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[11px] font-extrabold {{ $isCorrect
                            ? 'bg-emerald-500 text-white'
                            : 'bg-secondary-200 text-secondary-600 dark:bg-white/10 dark:text-secondary-300' }}">{{ chr(65 + $index) }}</span>
                    <x-diag.math-cell as="span" :content="$option->opcion" uid="qd-{{ $question->id }}-o-{{ $index }}" class="flex-1 {{ $isCorrect ? 'font-semibold text-secondary-800 dark:text-secondary-100' : 'text-secondary-600 dark:text-secondary-300' }}" />
                    @if ($isCorrect)
                        <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-emerald-500 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-white">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            Correcta
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>
