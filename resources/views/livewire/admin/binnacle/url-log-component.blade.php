<div class="fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-1">Log de URLs visitadas</h1>
            <p class="text-sky-400 font-medium text-sm">Rutas accedidas por los usuarios, extraídas de la bitácora (event_type = access).</p>
        </div>
        <div class="text-sm text-gray-400">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg">
                <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                </svg>
                {{ number_format($summary['visits']) }} visita(s) · {{ number_format($summary['urls']) }} URL(s)
            </span>
        </div>
    </div>

    <!-- Buscador -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-4 mb-4">
        <div class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[14rem]">
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Buscar URL</label>
                <input type="search" wire:model.live.debounce.300ms="search"
                       placeholder="Ej: /admin, /app/planning, login…"
                       class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:border-sky-500 focus:outline-none">
            </div>
            @if($range !== '7d' || $search)
                <div>
                    <button type="button" wire:click="clearFilters"
                            class="px-3 py-2 rounded-lg text-sm font-medium bg-white/5 border border-white/10 text-gray-300 hover:bg-white/10 transition-colors">
                        Limpiar filtros
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-4">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-500">Visitas</p>
            <p class="text-2xl font-extrabold text-sky-400">{{ number_format($summary['visits']) }}</p>
        </div>
        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-4">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-500">URLs distintas</p>
            <p class="text-2xl font-extrabold text-emerald-400">{{ number_format($summary['urls']) }}</p>
        </div>
        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-4">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-500">Usuarios</p>
            <p class="text-2xl font-extrabold text-amber-400">{{ number_format($summary['users']) }}</p>
        </div>
    </div>

    <!-- Chart -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-4 mb-4"
         @if($live) wire:poll.5000ms @endif>
        <div class="flex flex-col gap-3 mb-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-sky-500/10 border border-sky-500/30 flex items-center justify-center text-sky-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Top {{ \App\Livewire\Admin\Binnacle\UrlLogComponent::CHART_LIMIT }} URLs más visitadas</h3>
                    <p class="text-xs text-gray-500">Según el rango seleccionado</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- macOS segmented control (NSSegmentedControl): botones unidos, uno activo. --}}
                @php
                    $rangeShort = ['24h' => '24h', '7d' => '7d', '30d' => '30d', '3m' => '3m', 'all' => 'Todo'];
                @endphp
                <div role="radiogroup" aria-label="Rango de fechas del chart"
                     class="inline-flex overflow-hidden rounded-lg border border-white/10 bg-gray-800/60 p-0.5 shadow-inner">
                    @foreach(\App\Livewire\Admin\Binnacle\UrlLogComponent::RANGES as $value => $label)
                        @php $isActive = $range === $value; @endphp
                        <button type="button"
                                role="radio"
                                aria-checked="{{ $isActive ? 'true' : 'false' }}"
                                title="{{ $label }}"
                                wire:click="$set('range', '{{ $value }}')"
                                class="min-w-[3rem] whitespace-nowrap rounded-[7px] px-3 py-1.5 text-xs font-bold transition-all duration-150
                                {{ $isActive
                                    ? 'bg-white text-gray-900 shadow-sm dark:bg-white/90 dark:text-gray-900'
                                    : 'text-gray-400 hover:text-gray-200' }}">
                            {{ $rangeShort[$value] ?? $label }}
                        </button>
                    @endforeach
                </div>

                {{-- Toggle datos en vivo --}}
                <button type="button"
                        wire:click="$toggle('live')"
                        aria-pressed="{{ $live ? 'true' : 'false' }}"
                        title="{{ $live ? 'Datos en vivo activados (actualización automática)' : 'Datos en vivo pausados' }}"
                        class="inline-flex items-center gap-2 rounded-lg border px-2.5 py-1.5 text-xs font-bold transition-colors
                        {{ $live
                            ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20'
                            : 'border-white/10 bg-white/5 text-gray-400 hover:bg-white/10' }}">
                    <span class="relative flex h-3.5 w-6 items-center rounded-full transition-colors {{ $live ? 'bg-emerald-500/60' : 'bg-gray-600' }}">
                        <span class="absolute h-2.5 w-2.5 rounded-full bg-white transition-all {{ $live ? 'left-[13px]' : 'left-0.5' }}"></span>
                    </span>
                    {{ $live ? 'En vivo' : 'Pausado' }}
                </button>
            </div>
        </div>
        <div wire:ignore>
            <div id="url-log-chart" class="w-full" style="min-height: 360px;"></div>
        </div>
    </div>

    <!-- Listing -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/5 bg-gray-800/30">
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">URL</th>
                        <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-widest text-gray-400">Visitas</th>
                        <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-widest text-gray-400">Usuarios</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Métodos</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Última visita</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($urls as $url)
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-200 font-mono break-all">{{ $url->path }}</td>
                            <td class="px-4 py-3 text-sm text-sky-300 font-bold text-right">{{ number_format($url->visits) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-300 text-right">{{ number_format($url->users) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($url->methods as $method)
                                        <span class="px-2 py-0.5 rounded-full bg-white/5 border border-white/10 text-[10px] font-bold text-gray-400 font-mono">{{ $method }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-400 whitespace-nowrap">
                                {{ $url->last_visit ? \Carbon\Carbon::parse($url->last_visit)->format('d/m/Y H:i:s') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <p class="text-gray-400 text-sm">Sin visitas registradas en el rango seleccionado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination-wrapper :paginator="$urls" />
    </div>

    @script
    <script>
        let urlLogChart = null;

        async function initUrlLogChart() {
            if (window.loadApexCharts) await window.loadApexCharts();
            if (!window.ApexCharts) return;

            const el = document.getElementById('url-log-chart');
            if (!el) return;

            if (urlLogChart) urlLogChart.destroy();

            const rawData = await $wire.get('chartData') ?? [];
            const categories = rawData.map((row) => row.x);
            const values = rawData.map((row) => row.y);

            urlLogChart = new window.ApexCharts(el, {
                series: [{ name: 'Visitas', data: values }],
                chart: {
                    type: 'bar',
                    height: Math.max(260, categories.length * 26),
                    toolbar: { show: false },
                    fontFamily: 'Inter, system-ui, sans-serif',
                    animations: { enabled: true, easing: 'easeinout', speed: 400 },
                },
                plotOptions: {
                    bar: { horizontal: true, borderRadius: 4, barHeight: '70%' },
                },
                colors: ['#0ea5e9'],
                dataLabels: {
                    enabled: true,
                    style: { colors: ['#e2e8f0'], fontSize: '10px', fontWeight: 600 },
                    background: { enabled: false },
                },
                xaxis: {
                    categories: categories,
                    labels: { style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 600 } },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: {
                    labels: { style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 600 } },
                },
                grid: { borderColor: '#37415140', strokeDashArray: 4 },
                tooltip: {
                    theme: 'dark',
                    y: { formatter: (val) => val + ' visita(s)' },
                },
                noData: {
                    text: 'Sin visitas en el rango seleccionado',
                    align: 'center',
                    verticalAlign: 'middle',
                    style: { color: '#6b7280', fontSize: '13px' },
                },
            });

            urlLogChart.render();
        }

        initUrlLogChart();
        $wire.$watch('chartData', () => initUrlLogChart());
        $wire.$watch('range', () => setTimeout(() => initUrlLogChart(), 100));
    </script>
    @endscript
</div>
