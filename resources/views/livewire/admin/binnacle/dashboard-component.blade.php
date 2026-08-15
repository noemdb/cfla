<div class="fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-1">Métricas de Auditoría</h1>
            <p class="text-emerald-400 font-medium text-sm">Resumen operativo de la bitácora inmutable.</p>
        </div>
        <div class="flex items-center gap-1 bg-gray-900/40 border border-white/10 rounded-lg p-1">
            @foreach([7, 30, 90] as $d)
                <button type="button" wire:click="$set('days', {{ $d }})"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold transition-colors {{ (int) $this->days === $d ? 'bg-emerald-500/20 text-emerald-300' : 'text-gray-400 hover:text-white' }}">
                    {{ $d }}d
                </button>
            @endforeach
        </div>
    </div>

    <!-- Tarjetas de métricas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total acumulado</p>
            <p class="text-3xl font-extrabold text-white mt-1">{{ number_format($metrics['total']) }}</p>
        </div>
        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Hoy</p>
            <p class="text-3xl font-extrabold text-emerald-400 mt-1">{{ number_format($metrics['today']) }}</p>
        </div>
        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Últimos {{ $days }} días</p>
            <p class="text-3xl font-extrabold text-white mt-1">{{ number_format($metrics['lastDays']) }}</p>
        </div>
        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Archivadas</p>
            <p class="text-3xl font-extrabold text-gray-300 mt-1">{{ number_format($metrics['archived']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Distribución por severidad -->
        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-5">
            <h2 class="text-sm font-bold text-white mb-4">Distribución por severidad</h2>
            @forelse($bySeverity as $row)
                @php
                    $pct = $severityTotal > 0 ? round($row->total / $severityTotal * 100) : 0;
                    $bar = match ($row->event_severity) {
                        'critical' => 'bg-red-500',
                        'alert' => 'bg-orange-500',
                        'warning' => 'bg-yellow-500',
                        'debug' => 'bg-gray-500',
                        default => 'bg-emerald-500',
                    };
                @endphp
                <div class="mb-3">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-mono text-gray-300">{{ $row->event_severity }}</span>
                        <span class="text-gray-500">{{ number_format($row->total) }} ({{ $pct }}%)</span>
                    </div>
                    <div class="h-2 rounded-full bg-white/5 overflow-hidden">
                        <div class="h-full {{ $bar }} rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Sin eventos en el período.</p>
            @endforelse
        </div>

        <!-- Distribución por categoría -->
        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-5">
            <h2 class="text-sm font-bold text-white mb-4">Distribución por categoría</h2>
            @forelse($byCategory as $row)
                @php
                    $pct = $metrics['lastDays'] > 0 ? round($row->total / $metrics['lastDays'] * 100) : 0;
                @endphp
                <div class="mb-3">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="text-gray-300">{{ $row->event_category }}</span>
                        <span class="text-gray-500">{{ number_format($row->total) }} ({{ $pct }}%)</span>
                    </div>
                    <div class="h-2 rounded-full bg-white/5 overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Sin eventos en el período.</p>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Integridad de la cadena -->
        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-5">
            <h2 class="text-sm font-bold text-white mb-4">Integridad de la cadena (ADR-003)</h2>
            <div class="flex items-center gap-3 mb-4">
                @if($integrity['valid'])
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Cadena íntegra
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-xs font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        ¡Enlaces rotos!
                    </span>
                @endif
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Eslabones critical/alert</dt><dd class="text-white font-mono">{{ number_format($integrity['total']) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Enlaces rotos</dt><dd class="text-white font-mono">{{ number_format($integrity['broken_links']) }}</dd></div>
            </dl>
        </div>

        <!-- Top actores -->
        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-5 lg:col-span-2">
            <h2 class="text-sm font-bold text-white mb-4">Actores más activos ({{ $days }}d)</h2>
            @forelse($topActors as $actor)
                <div class="flex items-center justify-between py-2 border-b border-white/5 last:border-0">
                    <span class="text-sm text-gray-200 font-medium">{{ $actor->subject_identifier }}</span>
                    <span class="text-xs text-gray-500">{{ number_format($actor->total) }} eventos</span>
                </div>
            @empty
                <p class="text-sm text-gray-500">Sin actividad en el período.</p>
            @endforelse
        </div>
    </div>

    <!-- Eventos críticos recientes -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-white/5">
            <h2 class="text-sm font-bold text-white">Eventos críticos / alerta recientes</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/5 bg-gray-800/30">
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Severidad</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Evento</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Actor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCritical as $entry)
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-300 whitespace-nowrap">{{ $entry->created_at?->format('d/m/Y H:i:s') }}</td>
                            <td class="px-4 py-3"><x-binnacle.badge :value="$entry->event_severity" kind="severity" /></td>
                            <td class="px-4 py-3 text-sm text-gray-200 font-medium">{{ $entry->title }}</td>
                            <td class="px-4 py-3 text-sm text-gray-300">{{ $entry->subject_identifier ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-sm text-gray-500">Sin eventos críticos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>