{{--
    Modal de detalle y revisión.

    · Cierra con Escape, con el botón “Cerrar” y con clic en el fondo.
    · El formulario de revisión es la ÚNICA escritura: guarda `observacion`
      (o `recomendacion` en evaluación) con la misma regla `min:5` que la
      Coordinación. No crea ni edita el resto del documento.
    · Si el documento se borró mientras el modal estaba abierto, `$detalle`
      llega nulo y se informa sin revelar el id.
--}}
@php
    $servicio = app(\App\Services\Inicial\SeguimientoInicial::class);
    $esFinal = $entidad === 'eifinalks';
    $campoRevision = \App\Services\Inicial\RegistroInicial::campoRevision($entidad);

    if ($detalle && $esFinal) {
        $nivel = ($detalle->pevaluacion?->seccion?->name ?? '—').' · '.($detalle->pevaluacion?->lapso?->name ?? '—');
        $docente = $detalle->pevaluacion?->profesor?->full_name;
    } elseif ($detalle) {
        $nivel = trim(($detalle->grado?->name ?? '—').' · '.($detalle->seccion?->name ?? '—'));
        $docente = $detalle->profesor?->full_name;
    }
@endphp

<div class="fixed inset-0 z-50 flex items-center justify-center p-4"
    x-data
    x-on:keydown.escape.window="$wire.revisionAbierta ? $wire.cerrarRevision() : $wire.cerrarDetalle()"
    role="dialog" aria-modal="true" aria-labelledby="detalle-titulo">

    {{-- Fondo --}}
    <div class="absolute inset-0 bg-black/70"
        x-on:click="$wire.revisionAbierta ? $wire.cerrarRevision() : $wire.cerrarDetalle()"
        aria-hidden="true"></div>

    {{-- Panel --}}
    <div class="relative w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-xl border border-white/10 bg-gray-900 shadow-2xl">
        <header class="sticky top-0 z-10 flex items-start justify-between gap-3 border-b border-white/10 bg-gray-900/95 backdrop-blur px-5 py-4">
            <div class="min-w-0">
                <h2 id="detalle-titulo" class="text-sm font-semibold text-gray-100">
                    {{ \App\Services\Inicial\RegistroInicial::titulo($entidad) }}
                    @if ($detalle)
                        <span class="text-gray-500 font-normal">N° {{ $detalle->id }}</span>
                    @endif
                </h2>

                @if ($detalle)
                    <p class="mt-0.5 text-xs text-gray-500 truncate">
                        {{ $nivel }}
                        <span class="mx-1" aria-hidden="true">·</span>
                        {{ $docente ?? '—' }}
                        @if ($esFinal)
                            <span class="mx-1" aria-hidden="true">·</span>
                            {{ $detalle->expectant?->full_name ?? '—' }}
                        @else
                            <span class="mx-1" aria-hidden="true">·</span>
                            {{ $detalle->finicial?->format('d/m/Y') ?? '—' }}
                            →
                            {{ $detalle->ffinal?->format('d/m/Y') ?? '—' }}
                        @endif
                    </p>
                @endif
            </div>

            <div class="flex shrink-0 items-center gap-2">
                @if ($detalle && $campoRevision && ! $revisionAbierta)
                    <button type="button" wire:click="abrirRevision"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-amber-500/20 bg-amber-500/10 px-3 py-1.5 text-xs font-medium text-amber-300 transition-colors hover:bg-amber-500/20">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ filled($detalle->{$campoRevision}) ? 'Editar revisión' : 'Registrar revisión' }}
                    </button>
                @endif

                <button type="button"
                    x-on:click="$wire.revisionAbierta ? $wire.cerrarRevision() : $wire.cerrarDetalle()"
                    class="shrink-0 rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-white/10 hover:text-white"
                    aria-label="Cerrar detalle">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </header>

        <div class="px-5 py-4">
            @if (! $detalle)
                <p class="text-sm text-gray-400">El documento ya no está disponible.</p>
            @elseif ($revisionAbierta)
                @php
                    $rotulo = \App\Services\Inicial\RegistroInicial::rotuloRevision($entidad);
                    $minuscula = mb_strtolower($rotulo);
                @endphp

                <form wire:submit="guardarRevision" class="space-y-3">
                    <p class="rounded-lg border border-amber-500/20 bg-amber-500/5 px-3 py-2 text-xs leading-relaxed text-amber-200/80">
                        Vas a registrar la {{ $minuscula }} de la Coordinación. Solo se modifica ese
                        campo; el documento no se crea ni se edita.
                    </p>

                    <div>
                        <label for="revision-{{ $entidad }}" class="mb-1.5 block text-xs font-medium text-gray-400">
                            {{ $rotulo }} de la Coordinación de Evaluación
                        </label>

                        <textarea id="revision-{{ $entidad }}" rows="6" wire:model="revision"
                            placeholder="Escriba aquí la {{ $minuscula }}…"
                            class="w-full rounded-lg border border-white/10 bg-gray-800/50 px-3 py-2 text-sm text-gray-200 outline-none focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/50"></textarea>

                        @error('revision')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror

                        <p class="mt-1 text-[11px] text-gray-600">
                            Mínimo 5 caracteres. El resto del documento lo mantiene la docente.
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-2 border-t border-white/10 pt-3">
                        <button type="button" wire:click="cerrarRevision"
                            class="rounded-lg px-4 py-2 text-sm text-gray-300 transition-colors hover:bg-white/5">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-amber-500">
                            Guardar
                        </button>
                    </div>
                </form>
            @else
                {{-- Estado y revisión --}}
                <div class="mb-4 flex flex-wrap items-center gap-2">
                    @php $estado = $servicio->estado($entidad, $detalle); @endphp

                    <span class="inline-flex items-center rounded-md border px-2 py-0.5 text-[10px] font-medium
                        {{ [
                            'emerald' => 'border-emerald-500/20 bg-emerald-500/10 text-emerald-300',
                            'amber' => 'border-amber-500/20 bg-amber-500/10 text-amber-300',
                            'cyan' => 'border-cyan-500/20 bg-cyan-500/10 text-cyan-300',
                            'gray' => 'border-white/10 bg-white/5 text-gray-400',
                        ][$estado['variante']] ?? 'border-white/10 bg-white/5 text-gray-400' }}"
                        title="{{ $estado['detalle'] }}">
                        {{ $estado['etiqueta'] }}
                    </span>

                    @if ($campoRevision)
                        <span @class([
                                'inline-flex items-center rounded-md border px-2 py-0.5 text-[10px] font-medium',
                                'border-emerald-500/20 bg-emerald-500/10 text-emerald-300' => filled($detalle->{$campoRevision}),
                                'border-amber-500/20 bg-amber-500/10 text-amber-300' => blank($detalle->{$campoRevision}),
                            ])>
                            {{ filled($detalle->{$campoRevision}) ? 'Revisado' : 'Sin revisión' }}
                        </span>
                    @endif

                    <span class="text-[11px] text-gray-600">Documento de solo lectura</span>
                </div>

                {{-- Cuerpo: parcial reutilizado del docente --}}
                @if ($detalleParcial)
                    @include($detalleParcial, ['plan' => $detalle])
                @else
                    <dl class="grid gap-3 sm:grid-cols-3">
                        <div>
                            <dt class="text-xs text-gray-500">Estudiante</dt>
                            <dd class="text-sm text-gray-200">{{ $detalle->expectant?->full_name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Carga académica</dt>
                            <dd class="text-sm text-gray-200">{{ $nivel }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Título</dt>
                            <dd class="text-sm text-gray-200">{{ $detalle->title }}</dd>
                        </div>
                    </dl>

                    <p class="mt-4 text-sm text-gray-400">
                        El desarrollo completo del informe y sus expectativas por área se
                        imprimen desde el formato.
                    </p>
                @endif

                @if ($campoRevision)
                    <div class="mt-4 rounded-lg border border-white/10 bg-gray-800/40 p-3">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-gray-500 mb-1">
                            {{ \App\Services\Inicial\RegistroInicial::rotuloRevision($entidad) }} de la Coordinación
                        </p>
                        @if (filled($detalle->{$campoRevision}))
                            <p class="text-sm text-gray-200 whitespace-pre-line">{{ $detalle->{$campoRevision} }}</p>
                        @else
                            <p class="text-sm italic text-gray-600">Sin registro.</p>
                        @endif
                    </div>
                @endif
            @endif
        </div>

        @if (! $revisionAbierta)
            <footer class="flex flex-wrap items-center justify-end gap-2 border-t border-white/10 px-5 py-3">
                <button type="button" wire:click="cerrarDetalle"
                    class="rounded-lg border border-white/10 bg-white/5 hover:bg-white/10 px-3 py-2 text-sm font-medium text-gray-300 transition-colors">
                    Cerrar
                </button>

                @if ($detalle)
                    <a href="{{ route('inicials.seguimiento.'.$entidad.'.format', $detalle->id) }}" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/5 hover:bg-white/10 px-3 py-2 text-sm font-medium text-gray-200 transition-colors">
                        Formato imprimible
                    </a>

                    <a href="{{ route('inicials.seguimiento.'.$entidad.'.show', $detalle->id) }}" wire:navigate
                        class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-600 hover:bg-cyan-500 px-3 py-2 text-sm font-medium text-white transition-colors">
                        Abrir ficha completa
                    </a>
                @endif
            </footer>
        @endif
    </div>
</div>
