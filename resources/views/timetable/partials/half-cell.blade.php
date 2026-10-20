{{--
    ADR-TT-017 — Celda de horario en vistas de solo lectura y PDF.

    Agrupa los slots de UNA celda (período × día) por sub-franja:
      - bloque completo (half === null): se renderiza tal cual (sin cambios).
      - ½ superior (half === 0) y ½ inferior (half === 1): se apilan con un rótulo
        «½ sup.» / «½ inf.» y un separador discontinuo, sin alterar el layout de
        una celda normal.

    Variables:
      $slots      : iterable de TimetableSlot (modelo) o de celdas de preview (array).
      $lines      : callable($entry): list<array{class:string, text:string}>
                    líneas a mostrar por asignatura (asignatura + meta).
      $emptyClass : clase CSS del marcador vacío.
      $emptyText  : contenido del marcador vacío (se emite sin escapar; por defecto
                    `&nbsp;`). Ej.: '—'.
      $itemClass  : (opcional) clase que envuelve cada asignatura (p. ej. para el
                    separador entre asignaturas de una celda). Null = sin envoltura.
      $bandColor  : color de acento del rótulo de mitad (por defecto azul #0369a1).
--}}
@php
    $entries = collect($slots ?? [])->values();

    $halfOf = static fn ($entry) => data_get($entry, 'half');

    $fullEntries = $entries->filter(static fn ($entry) => $halfOf($entry) === null)->values();
    $supEntries = $entries->filter(static fn ($entry) => $halfOf($entry) !== null && (int) $halfOf($entry) === 0)->values();
    $infEntries = $entries->filter(static fn ($entry) => $halfOf($entry) !== null && (int) $halfOf($entry) === 1)->values();
    $hasHalves = $supEntries->isNotEmpty() || $infEntries->isNotEmpty();

    $emptyClass = $emptyClass ?? 'teacher';
    $emptyText = $emptyText ?? '&nbsp;';
    $itemClass = $itemClass ?? null;
    $bandColor = $bandColor ?? '#0369a1';
@endphp

@if ($entries->isEmpty())
    <span class="{{ $emptyClass }}">{!! $emptyText !!}</span>
@else
    @foreach ($fullEntries as $entry)
        @if ($itemClass)<div class="{{ $itemClass }}">@endif
        @foreach ($lines($entry) as $line)
            <div class="{{ $line['class'] ?? '' }}">{{ $line['text'] ?? '' }}</div>
        @endforeach
        @if ($itemClass)</div>@endif
    @endforeach

    @if ($hasHalves)
        <div>
            <div style="border-bottom:1px dashed #0ea5e9;padding-bottom:1px;margin-bottom:1px;">
                <span style="font-size:0.85em;font-weight:800;letter-spacing:0.3px;color:{{ $bandColor }};">½ sup.</span>
                @forelse ($supEntries as $entry)
                    @if ($itemClass)<div class="{{ $itemClass }}">@endif
                    @foreach ($lines($entry) as $line)
                        <div class="{{ $line['class'] ?? '' }}">{{ $line['text'] ?? '' }}</div>
                    @endforeach
                    @if ($itemClass)</div>@endif
                @empty
                    <div class="{{ $emptyClass }}">&nbsp;</div>
                @endforelse
            </div>
            <div style="padding-top:1px;">
                <span style="font-size:0.85em;font-weight:800;letter-spacing:0.3px;color:{{ $bandColor }};">½ inf.</span>
                @forelse ($infEntries as $entry)
                    @if ($itemClass)<div class="{{ $itemClass }}">@endif
                    @foreach ($lines($entry) as $line)
                        <div class="{{ $line['class'] ?? '' }}">{{ $line['text'] ?? '' }}</div>
                    @endforeach
                    @if ($itemClass)</div>@endif
                @empty
                    <div class="{{ $emptyClass }}">&nbsp;</div>
                @endforelse
            </div>
        </div>
    @endif
@endif

