{{--
    Detalle de un documento de Educación Inicial en SOLO LECTURA.

    Usado por las TRES perspectivas (Evaluación, Planificación, Académico) vía
    `App\Http\Controllers\Inicial\PerspectivaInicialController@show`.

    ─────────────────────────────────────────────────────────────────────────────
    POR QUÉ UN SOLO ARCHIVO PARA 6 DOCUMENTOS
    ─────────────────────────────────────────────────────────────────────────────
    El cuerpo del documento no se reimplementa aquí: se incluye el parcial de
    detalle que ya usan las vistas del docente (F3). Son las MISMAS tablas y las
    MISMAS estrategias, así que es imposible que la revisión y el documento
    original divergan: si cambian las columnas en F3, cambian en las dos
    perspectivas.

    Lo que sí cambia aquí es el marco: sin botones de edición, con enlace al
    formato y, cuando el documento tiene campo de revisión, con la lectura de ese
    campo en lugar de un textarea.
--}}
<x-layouts.role>
    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ═══ Cabecera ═══ --}}
            <header class="space-y-3">
                <nav class="text-xs text-gray-500" aria-label="Migas de pan">
                    <ol class="flex flex-wrap items-center gap-2">
                        <li>
                            <a href="{{ route('inicials.home') }}" class="hover:text-cyan-400 transition-colors">
                                Educación Inicial
                            </a>
                        </li>
                        <li aria-hidden="true">/</li>
                        <li class="text-gray-300" aria-current="page">{{ $titulo }}</li>
                        <li aria-hidden="true">/</li>
                        <li class="text-gray-400">N° {{ $documento->id }}</li>
                    </ol>
                </nav>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 class="text-lg font-semibold text-gray-100">{{ $titulo }}</h1>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Solo lectura · registrado el {{ $documento->created_at?->format('d/m/Y H:i') ?? '—' }}
                        </p>
                    </div>

                    <a href="{{ $rutaFormato }}" target="_blank"
                        class="inline-flex items-center gap-2 rounded-lg bg-cyan-600 hover:bg-cyan-500 px-4 py-2 text-sm font-medium text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z" />
                        </svg>
                        Formato imprimible
                    </a>
                </div>
            </header>

            {{-- ═══ Campo de revisión (solo lectura) ═══ --}}
            @if ($campoRevision)
                <section @class([
                        'rounded-xl border p-4',
                        'border-amber-500/20 bg-amber-500/5' => filled($documento->{$campoRevision}),
                        'border-white/10 bg-gray-900/60' => blank($documento->{$campoRevision}),
                     ])
                    aria-labelledby="revision-titulo">
                    <h2 id="revision-titulo"
                        class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">
                        {{ $campoRevision === 'recomendacion' ? 'Recomendación de la Coordinación' : 'Observación de la Coordinación' }}
                    </h2>

                    @if (filled($documento->{$campoRevision}))
                        <p class="text-sm text-gray-200 whitespace-pre-line">{{ $documento->{$campoRevision} }}</p>
                    @else
                        <p class="text-sm italic text-gray-600">
                            Sin {{ $campoRevision === 'recomendacion' ? 'recomendación' : 'observación' }} registrada.
                        </p>
                    @endif

                    {{-- La escritura vive en la pestaña de la perspectiva
                         (componente Livewire), no aquí: esta pantalla es de
                         lectura para las tres perspectivas. --}}
                    <p class="mt-2 text-[11px] text-gray-600">
                        Para escribirla, use la pestaña «{{ $titulo }}» de la perspectiva correspondiente.
                    </p>
                </section>
            @endif

            {{-- ═══ Cuerpo del documento ═══ --}}
            <section class="rounded-xl border border-white/10 bg-gray-900/40 p-5">
                @if ($detalle)
                    @include($detalle, ['plan' => $documento])
                @else
                    {{-- El informe final no tiene parcial de detalle reutilizable
                         (su cuerpo es por estudiante): se resume su cabecera y
                         se enlaza al formato, que sí lo desarrolla entero. --}}
                    <dl class="grid gap-3 sm:grid-cols-3">
                        <div>
                            <dt class="text-xs text-gray-500">Estudiante</dt>
                            <dd class="text-sm text-gray-200">{{ $documento->expectant?->full_name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Carga académica</dt>
                            <dd class="text-sm text-gray-200">
                                {{ trim(($documento->pevaluacion?->seccion?->name ?? '—').' · '.($documento->pevaluacion?->lapso?->name ?? '—')) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Título</dt>
                            <dd class="text-sm text-gray-200">{{ $documento->title }}</dd>
                        </div>
                    </dl>

                    <p class="mt-4 text-sm text-gray-400">
                        El desarrollo completo del informe, sus expectativas por área y el texto
                        firmado se imprimen desde el formato.
                    </p>
                @endif
            </section>
        </div>
    </div>
</x-layouts.role>