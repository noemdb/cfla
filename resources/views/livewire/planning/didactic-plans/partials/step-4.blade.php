{{-- Paso 4: revisión, justificación y aprobación por sección --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-3">
        <div class="rounded-lg border border-gray-200 dark:border-white/10 overflow-hidden">
            <div class="px-3 py-2 bg-gray-100 dark:bg-gray-800 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                Resumen del lote
            </div>
            <div class="p-3 grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-xs">
                <div><span class="font-bold text-gray-500 dark:text-gray-400">Eje:</span> {{ $axis }}</div>
                <div><span class="font-bold text-gray-500 dark:text-gray-400">Proyecto:</span> {{ $projectTitle ?: '—' }}</div>
                <div><span class="font-bold text-gray-500 dark:text-gray-400">Periodo:</span>
                    {{ \Carbon\Carbon::parse($periodStart)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($periodEnd)->format('d/m/Y') }}
                </div>
                <div><span class="font-bold text-gray-500 dark:text-gray-400">Secciones:</span> {{ count($seccionIds) }}</div>
                <div><span class="font-bold text-gray-500 dark:text-gray-400">Sesiones:</span> {{ count($sessions) }}</div>
                <div><span class="font-bold text-gray-500 dark:text-gray-400">Actividades usadas:</span> {{ $selectedCount ?: count(array_filter(array_column($sessions, 'source_activity_id'))) }}</div>
                <div class="md:col-span-2"><span class="font-bold text-gray-500 dark:text-gray-400">Objetivo:</span> {{ $objective ?: '—' }}</div>
            </div>
        </div>

        <div class="space-y-3">
            @foreach($sectionReviews as $review)
                <div class="rounded-lg border border-gray-200 dark:border-white/10 overflow-hidden">
                    <div class="flex flex-wrap items-center justify-between gap-2 px-3 py-2 bg-gray-100 dark:bg-gray-800">
                        <p class="text-[11px] font-bold text-gray-700 dark:text-gray-200">{{ $review['label'] }}</p>
                        <div class="flex items-center gap-1.5">
                            <x-badge flat slate :label="$review['sessions_count'].' sesiones'" />
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $review['all_ok'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' }}">
                                {{ $review['all_ok'] ? 'Listo' : 'Revisar' }}
                            </span>
                        </div>
                    </div>
                    <div class="p-3">
                        @foreach(array_values(array_filter($sessions, fn ($row) => (int) ($row['seccion_id'] ?? 0) === $review['seccion_id'])) as $session)
                            <div class="py-1.5 border-b border-gray-100 dark:border-white/5 last:border-0">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-xs font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $session['title'] ?: 'Sesión sin título' }}
                                    </p>
                                    <span class="text-[10px] font-semibold text-gray-500 dark:text-gray-400">
                                        {{ ! empty($session['date']) ? \Carbon\Carbon::parse($session['date'])->format('d/m/Y') : 'Sin fecha' }}
                                    </span>
                                </div>
                                <div class="mt-1 flex flex-wrap gap-1 text-[9px] font-bold">
                                    <span class="px-1.5 py-0.5 rounded {{ trim((string) ($session['start'] ?? '')) !== '' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-300' }}">Inicio</span>
                                    <span class="px-1.5 py-0.5 rounded {{ trim((string) ($session['development'] ?? '')) !== '' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-300' }}">Desarrollo</span>
                                    <span class="px-1.5 py-0.5 rounded {{ trim((string) ($session['close'] ?? '')) !== '' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-300' }}">Cierre</span>
                                    <span class="px-1.5 py-0.5 rounded {{ trim((string) ($session['evaluation_description'] ?? '')) !== '' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-300' }}">Evaluación</span>
                                    <span class="px-1.5 py-0.5 rounded {{ count($session['indicators'] ?? []) > 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' }}">{{ count($session['indicators'] ?? []) }} indicadores</span>
                                    <span class="px-1.5 py-0.5 rounded {{ trim((string) ($session['ods_text'] ?? '')) !== '' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' }}">{{ $session['ods_text'] ?: 'Sin ODS' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="space-y-3">
        @foreach($sectionReviews as $review)
            <div class="rounded-lg border border-gray-200 dark:border-white/10">
                <div class="px-3 py-2 bg-gray-100 dark:bg-gray-800 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                    Checklist · {{ $review['label'] }}
                </div>
                <ul class="p-3 space-y-2">
                    @foreach($review['checklist'] as $item)
                        <li class="flex items-start gap-2 text-[11px]">
                            <span class="mt-0.5 inline-flex items-center justify-center w-4 h-4 rounded-full text-white text-[9px] font-bold {{ $item['ok'] ? 'bg-emerald-500' : 'bg-red-500' }}">
                                {{ $item['ok'] ? '✓' : '!' }}
                            </span>
                            <span>
                                <span class="font-bold {{ $item['ok'] ? 'text-gray-700 dark:text-gray-200' : 'text-red-600 dark:text-red-400' }}">{{ $item['label'] }}</span>
                                @unless($item['ok'])
                                    <span class="block text-gray-500 dark:text-gray-400">{{ $item['help'] }}</span>
                                @endunless
                            </span>
                        </li>
                    @endforeach
                </ul>
                @if($review['all_ok'])
                    <p class="px-3 pb-3 text-[11px] font-bold text-emerald-600 dark:text-emerald-400">Todo listo para aprobar.</p>
                @else
                    <p class="px-3 pb-3 text-[11px] text-gray-500 dark:text-gray-400">
                        Si falta algún elemento, justifícalo en las notas del plan para poder aprobar.
                    </p>
                @endif
            </div>
        @endforeach

        <x-textarea label="Notas del plan (justificaciones)" wire:model.live="notes" rows="3"
            placeholder="Justifica ausencias o decisiones pedagógicas. Se aplica a todos los planes del lote." />
        @if(collect($sectionReviews)->contains(fn (array $review): bool => ! $review['all_ok']))
            <div class="rounded-lg border border-amber-200 dark:border-amber-500/20 bg-amber-50 dark:bg-amber-500/10 px-3 py-2">
                <p class="text-[11px] text-amber-800 dark:text-amber-200">
                    Hay elementos pendientes. Puedes completarlos en el Paso 3 o justificar su ausencia.
                </p>
                <div class="mt-1.5">
                    <x-button flat sm label="Justificar faltantes en las notas" wire:click="justifyMissingItems" />
                </div>
            </div>
        @endif
        <x-textarea label="Notas para el PDF" wire:model.live="pdfNotes" rows="2"
            placeholder="Texto adicional que aparecerá en los documentos impresos." />
    </div>
</div>
