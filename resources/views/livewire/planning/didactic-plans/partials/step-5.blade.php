{{-- Paso 5: resultado del lote, descargas y aprobación pendiente --}}
<div class="space-y-4">
    @php
        $approvedResults = collect($planResults)->where('status', \App\Models\app\Academy\DidacticPlan::STATUS_APPROVED);
        $draftResults = collect($planResults)->where('status', \App\Models\app\Academy\DidacticPlan::STATUS_DRAFT);
        $errorResults = collect($planResults)->filter(
            fn (array $result): bool => ! empty($result['error']) || ! empty($result['errors'])
        );
    @endphp

    @if($approved && $approvedResults->count() === count($planResults) && count($planResults) > 0)
        <div class="rounded-xl border border-emerald-200 dark:border-emerald-500/20 bg-emerald-50 dark:bg-emerald-500/10 p-4">
            <div class="flex items-start gap-3">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-600 text-white text-sm font-bold">✓</span>
                <div>
                    <p class="text-sm font-extrabold text-emerald-800 dark:text-emerald-200">Planes aprobados</p>
                    <p class="mt-1 text-xs text-emerald-700 dark:text-emerald-300">
                        Los planes de las {{ count($planResults) }} secciones quedaron aprobados por planificación y están listos para imprimir.
                    </p>
                </div>
            </div>
        </div>
    @elseif($draftResults->isNotEmpty())
        <div class="rounded-xl border border-amber-200 dark:border-amber-500/20 bg-amber-50 dark:bg-amber-500/10 p-4">
            <div class="flex items-start gap-3">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-500 text-white text-sm font-bold">i</span>
                <div>
                    <p class="text-sm font-extrabold text-amber-800 dark:text-amber-200">Borradores guardados</p>
                    <p class="mt-1 text-xs text-amber-700 dark:text-amber-300">
                        {{ $draftResults->count() }} de {{ count($planResults) }} planes quedaron como borrador. El PDF oficial solo se genera cuando planificación aprueba cada plan.
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if($errorResults->isNotEmpty())
        <div class="rounded-xl border border-red-200 dark:border-red-500/20 bg-red-50 dark:bg-red-500/10 p-4 space-y-2">
            <p class="text-sm font-extrabold text-red-800 dark:text-red-200">Validaciones pendientes para aprobar</p>
            <p class="text-xs text-red-700 dark:text-red-300">
                Los planes quedaron guardados como borrador. Completa los indicadores/evaluación o usa la justificación estándar para aprobarlos.
            </p>
            <ul class="text-xs text-red-700 dark:text-red-300 space-y-1.5">
                @foreach($errorResults as $result)
                    <li>
                        <span class="font-bold">{{ $result['label'] }}:</span>
                        {{ $result['error'] ?: 'Revisa el detalle.' }}

                        @if(! empty($result['errors']))
                            <details class="mt-1">
                                <summary class="cursor-pointer font-semibold underline decoration-red-400/60">Ver detalle ({{ count($result['errors']) }})</summary>
                                <ul class="mt-1 list-disc pl-5 space-y-0.5">
                                    @foreach($result['errors'] as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                </ul>
                            </details>
                        @endif
                    </li>
                @endforeach
            </ul>
            <div class="pt-1">
                <x-button primary sm label="Justificar faltantes y aprobar" wire:click="justifyAndRetry"
                    spinner="justifyAndRetry" />
            </div>
        </div>
    @endif

    <div class="rounded-lg border border-gray-200 dark:border-white/10 overflow-hidden">
        <div class="px-3 py-2 bg-gray-100 dark:bg-gray-800 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
            Planes del lote
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[11px]">
                <thead class="bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[9px] border-b border-gray-100 dark:border-white/5">
                    <tr>
                        <th class="px-3 py-2">Sección</th>
                        <th class="px-3 py-2 text-center">Estado</th>
                        <th class="px-3 py-2 text-center">Versión</th>
                        <th class="px-3 py-2 text-right">Documento</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @forelse($planResults as $result)
                        <tr>
                            <td class="px-3 py-2 font-semibold text-gray-800 dark:text-gray-100">
                                {{ $result['label'] }}
                                @if($result['error'])
                                    <span class="block text-[10px] font-normal text-red-600 dark:text-red-400 line-clamp-2">{{ $result['error'] }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($result['status'] === \App\Models\app\Academy\DidacticPlan::STATUS_APPROVED)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">Aprobado</span>
                                @elseif($result['status'] === \App\Models\app\Academy\DidacticPlan::STATUS_DRAFT)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">Borrador</span>
                                @else
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300">Error</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-300">
                                {{ $result['version'] ? 'v'.$result['version'] : '—' }}
                            </td>
                            <td class="px-3 py-2 text-right">
                                @if($result['status'] === \App\Models\app\Academy\DidacticPlan::STATUS_APPROVED && $result['plan_id'])
                                    <a href="{{ route('app.planning.activities.didactic-plans.pdf', $result['plan_id']) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-bold">
                                        Descargar PDF
                                    </a>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-6 text-center text-xs text-gray-500 dark:text-gray-400">
                                Aún no hay planes guardados en este lote.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <x-button flat label="Volver a revisar" wire:click="$set('step', 4)" />
        @if($errorResults->isNotEmpty())
            <x-button primary label="Justificar faltantes y aprobar" wire:click="justifyAndRetry" spinner="justifyAndRetry" />
        @elseif($hasDraftResults)
            <x-button primary label="Aprobar borradores pendientes" wire:click="approveDrafts" spinner="approveDrafts" />
        @endif
    </div>

    <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3 text-[11px] text-gray-500 dark:text-gray-400 space-y-1">
        <p><span class="font-bold text-gray-700 dark:text-gray-200">Trazabilidad:</span> cada plan conserva la instantánea de filtros y el hash del universo usado.</p>
        <p><span class="font-bold text-gray-700 dark:text-gray-200">Periodo:</span>
            {{ \Carbon\Carbon::parse($periodStart)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($periodEnd)->format('d/m/Y') }}
        </p>
        <p><span class="font-bold text-gray-700 dark:text-gray-200">Universo:</span> {{ $filteredCount }} actividades filtradas · {{ $selectedCount }} seleccionadas</p>
    </div>
</div>
