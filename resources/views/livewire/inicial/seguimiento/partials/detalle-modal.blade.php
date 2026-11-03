{{--
    Modal de detalle y revisión.

    · Cierra con Escape, con el botón “Cerrar” y con clic en el fondo.
    · El formulario guarda `revision_seguimiento`; en planificación semanal
      también permite editar el campo `observacion` existente.
    · Si el documento se borró mientras el modal estaba abierto, `$detalle`
      llega nulo y se informa sin revelar el id.
--}}
@php
    $servicio = app(\App\Services\Inicial\SeguimientoInicial::class);
    $esFinal = $entidad === 'eifinalks';
    $campoRevision = \App\Services\Inicial\RegistroInicial::campoRevision($entidad)
        ? 'revision_seguimiento'
        : null;

    if ($detalle && $esFinal) {
        $nivel = ($detalle->pevaluacion?->seccion?->name ?? '—').' · '.($detalle->pevaluacion?->lapso?->name ?? '—');
        $docente = $detalle->pevaluacion?->profesor?->full_name;
    } elseif ($detalle) {
        $nivel = trim(($detalle->grado?->name ?? '—').' · '.($detalle->seccion?->name ?? '—'));
        $docente = $detalle->profesor?->full_name;
    }
@endphp

<div class="fixed inset-0 z-50 flex items-center justify-center px-2 py-4 sm:px-4 lg:px-6 2xl:px-8"
    x-data
    x-on:keydown.escape.window="$wire.revisionAbierta ? $wire.cerrarRevision() : $wire.cerrarDetalle()"
    role="dialog" aria-modal="true" aria-labelledby="detalle-titulo">

    {{-- Fondo --}}
    <div class="absolute inset-0 bg-black/70"
        x-on:click="$wire.revisionAbierta ? $wire.cerrarRevision() : $wire.cerrarDetalle()"
        aria-hidden="true"></div>

    {{-- Panel --}}
    <div class="relative w-full max-w-none max-h-[90vh] overflow-y-auto rounded-xl border border-white/10 bg-gray-900 shadow-2xl">
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
                @if ($entidad === 'eiplanningwks')
                    <div class="mb-4 flex gap-1 border-b border-white/10" role="tablist" aria-label="Edición de seguimiento">
                        <button type="button" role="tab"
                            id="tab-revision"
                            aria-controls="panel-revision"
                            aria-selected="{{ $revisionPestana === 'revision' ? 'true' : 'false' }}"
                            wire:click="seleccionarPestanaRevision('revision')"
                            @class([
                                'min-h-11 border-b-2 px-4 text-sm font-medium transition-colors',
                                'border-amber-400 text-amber-300' => $revisionPestana === 'revision',
                                'border-transparent text-gray-400 hover:text-gray-200' => $revisionPestana !== 'revision',
                            ])>
                            Revisión
                        </button>
                        <button type="button" role="tab"
                            id="tab-observacion"
                            aria-controls="panel-observacion"
                            aria-selected="{{ $revisionPestana === 'observacion' ? 'true' : 'false' }}"
                            wire:click="seleccionarPestanaRevision('observacion')"
                            @class([
                                'min-h-11 border-b-2 px-4 text-sm font-medium transition-colors',
                                'border-cyan-400 text-cyan-300' => $revisionPestana === 'observacion',
                                'border-transparent text-gray-400 hover:text-gray-200' => $revisionPestana !== 'observacion',
                            ])>
                            Observación
                        </button>
                    </div>
                @endif

                @if ($revisionPestana === 'revision')
                    <section @if ($entidad === 'eiplanningwks') id="panel-revision" role="tabpanel" aria-labelledby="tab-revision" @endif>
                        <form wire:submit="guardarRevision" class="space-y-3">
                            <p class="rounded-lg border border-amber-500/20 bg-amber-500/5 px-3 py-2 text-xs leading-relaxed text-amber-200/80">
                                Registra la revisión de seguimiento en un campo independiente. La observación
                                del plan y el resto del documento permanecen intactos.
                            </p>

                            <div>
                                <label for="revision-{{ $entidad }}" class="mb-1.5 block text-xs font-medium text-gray-400">
                                    Revisión de seguimiento
                                </label>

                                <textarea id="revision-{{ $entidad }}" rows="6" wire:model="revision"
                                    placeholder="Escriba la revisión de seguimiento…"
                                    class="w-full rounded-lg border border-white/10 bg-gray-800/50 px-3 py-2 text-sm text-gray-200 outline-none focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/50"></textarea>

                                @error('revision')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror

                                <p class="mt-1 text-[11px] text-gray-400">
                                    Mínimo 5 caracteres. El texto se guarda en un campo separado de la observación.
                                </p>
                            </div>

                            <div class="flex items-center justify-end gap-2 border-t border-white/10 pt-3">
                                <button type="button" wire:click="cerrarRevision"
                                    class="min-h-11 rounded-lg px-4 py-2 text-sm text-gray-300 transition-colors hover:bg-white/5">
                                    Cancelar
                                </button>
                                <button type="submit" wire:loading.attr="disabled" wire:target="guardarRevision"
                                    class="min-h-11 rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-amber-500 disabled:opacity-50">
                                    Guardar revisión
                                </button>
                            </div>
                        </form>
                    </section>
                @elseif ($entidad === 'eiplanningwks')
                    <section id="panel-observacion" role="tabpanel" aria-labelledby="tab-observacion">
                        <form wire:submit="guardarObservacion" class="space-y-4">
                            <div class="rounded-lg border border-cyan-500/20 bg-cyan-500/5 px-4 py-3 text-sm leading-relaxed text-cyan-100/90">
                                <h3 class="font-medium text-cyan-200">Observación para el plan semanal</h3>
                                <p class="mt-1">
                                    Este campo sirve para dejar una indicación concreta sobre la planificación que
                                    debe considerar el docente. Describe el aspecto que requiere atención, el ajuste
                                    esperado y, cuando sea útil, el momento o área al que se refiere. El texto queda
                                    visible en el documento semanal y el profesor asociado recibirá una notificación
                                    al guardarlo.
                                </p>
                                <p class="mt-2 text-xs text-cyan-200/70">
                                    Se editará el campo existente <code>Eiplanningwk.observacion</code>; al guardar,
                                    el contenido actual de ese campo será reemplazado. La revisión de seguimiento
                                    permanece independiente.
                                </p>
                            </div>

                            <div>
                                <label for="observacion-{{ $entidad }}" class="mb-1.5 block text-xs font-medium text-gray-300">
                                    Observación del plan semanal
                                </label>
                                <textarea id="observacion-{{ $entidad }}" rows="7" wire:model="observacion"
                                    placeholder="Escriba la observación para el docente…"
                                    class="w-full rounded-lg border border-white/10 bg-gray-800/50 px-3 py-2 text-sm text-gray-200 outline-none focus:border-cyan-500/50 focus:ring-2 focus:ring-cyan-500/50"></textarea>

                                @error('observacion')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror

                                <p class="mt-1 text-xs text-gray-400">Mínimo 5 caracteres.</p>
                            </div>

                            <div class="flex items-center justify-end gap-2 border-t border-white/10 pt-3">
                                <button type="button" wire:click="cerrarRevision"
                                    class="min-h-11 rounded-lg px-4 py-2 text-sm text-gray-300 transition-colors hover:bg-white/5">
                                    Cancelar
                                </button>
                                <button type="submit" wire:loading.attr="disabled" wire:target="guardarObservacion"
                                    class="min-h-11 rounded-lg bg-cyan-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-cyan-500 disabled:opacity-50">
                                    Guardar observación
                                </button>
                            </div>
                        </form>
                    </section>
                @endif
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
                    @include($detalleParcial, ['plan' => $detalle, 'resumirTextos' => true])
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
                            Revisión de seguimiento
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
