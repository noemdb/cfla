{{-- resources/views/livewire/director/indicator-dashboard.blade.php --}}
<div class="fade-in">

    {{-- Cabecera --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white">Panel de Dirección</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Seguimiento institucional · modo solo lectura</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            @if($lapsoActive)
                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-sky-600 dark:text-sky-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                    Lapso activo: {{ $lapsoActive->name }}
                </span>
            @endif
            <select wire:model.live="selectedLapsoId"
                class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-100">
                @foreach($lapsos ?? [] as $lapso)
                    <option value="{{ $lapso->id }}">{{ $lapso->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- KPIs globales (toda la institución) --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Peducativos</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalPeducativos }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Pensums</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalPensums }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Evaluaciones</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalPevaluacions }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Actividades</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalActivities }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Recursos</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalResources }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Profesores</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalProfesoresActivos }}</p>
        </div>
    </div>

    {{-- ═══ Flujo de Registros (global, con rango de fechas) ═══ --}}
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-4 sm:p-5 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Flujo de Registros</h3>
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-white/5 rounded-lg p-0.5">
                @php $ranges = ['7d' => '7 días', '30d' => '30 días', '3m' => '3 meses', 'all' => 'Todo']; @endphp
                @foreach($ranges as $val => $label)
                    <button wire:click="$set('registrationRange', '{{ $val }}')"
                        class="px-3 py-1.5 min-h-[36px] text-[10px] font-bold uppercase tracking-wider rounded-md transition-all duration-200 whitespace-nowrap
                               {{ $registrationRange === $val ? 'bg-sky-500/20 text-sky-600 dark:text-sky-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/5' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
            {{-- Activities Flow --}}
            <div class="bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 bg-purple-500/20 rounded-md flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Actividades</span>
                    <span class="ml-auto text-[9px] text-gray-500 dark:text-gray-500">{{ count($chartActivitiesFlow) }} día(s)</span>
                </div>
                <div wire:ignore>
                    <div id="activities-flow-chart" class="w-full" style="min-height: 200px;"></div>
                </div>
            </div>

            {{-- Lessons Flow --}}
            <div class="bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 bg-sky-500/20 rounded-md flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Lecciones</span>
                    <span class="ml-auto text-[9px] text-gray-500 dark:text-gray-500">{{ count($chartLessonsFlow) }} día(s)</span>
                </div>
                <div wire:ignore>
                    <div id="lessons-flow-chart" class="w-full" style="min-height: 200px;"></div>
                </div>
            </div>

            {{-- Diagnostics Flow --}}
            <div class="bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 bg-emerald-500/20 rounded-md flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Diagnósticos</span>
                    <span class="ml-auto text-[9px] text-gray-500 dark:text-gray-500">{{ count($chartDiagnosticsFlow) }} día(s)</span>
                </div>
                <div wire:ignore>
                    <div id="diagnostics-flow-chart" class="w-full" style="min-height: 200px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ Charts por Día (filtrados por lapso) ═══ --}}
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-4 sm:p-5 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Actividad por Día</h3>
            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Corresponde al lapso seleccionado</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3" style="grid-auto-flow: dense;">

            {{-- Actividades Registradas por Día (2×1) --}}
            <div class="col-span-1 sm:col-span-2 bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4 sm:p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Actividades Registradas por Día</h3>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ count($chartActivitiesByDay) }} día(s) con actividad</span>
                </div>
                <div wire:ignore>
                    <div id="activities-per-day-chart" class="w-full" style="min-height: 250px;"></div>
                </div>
            </div>

            {{-- Lecciones Registradas por Día (2×1) --}}
            <div class="col-span-1 sm:col-span-2 bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4 sm:p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-sky-100 dark:bg-sky-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Lecciones Registradas por Día</h3>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ count($chartLessonsByDay['categories'] ?? []) }} día(s) con actividad</span>
                </div>
                <div wire:ignore>
                    <div id="lessons-per-day-chart" class="w-full" style="min-height: 250px;"></div>
                </div>
            </div>

            {{-- Publicaciones Programadas por Día (4×1) --}}
            <div class="col-span-1 sm:col-span-2 lg:col-span-4 bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4 sm:p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-violet-100 dark:bg-violet-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Publicaciones Programadas por Día</h3>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ count($chartScheduledByDay) }} día(s) con programación</span>
                </div>
                <div wire:ignore>
                    <div id="scheduled-per-day-chart" class="w-full" style="min-height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Indicadores por Peducativo --}}
    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden dark:border-white/5 dark:bg-gray-900">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-white/5">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Indicadores por Peducativo</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Carga, actividades y profesores por unidad educativa</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-200 dark:border-white/5">
                        <th class="px-5 py-3">Peducativo</th>
                        <th class="px-5 py-3">Pensums</th>
                        <th class="px-5 py-3">Actividades</th>
                        <th class="px-5 py-3">Profesores</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peducativoIndicators as $item)
                        <tr class="border-b border-gray-100 last:border-0 dark:border-white/5">
                            <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $item->peducativo->name }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $item->pensums_count }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $item->activities_count }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $item->profesores_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-6 text-center text-sm text-gray-400 dark:text-gray-500">Sin datos para el lapso seleccionado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Light mode support: detect dark class and expose theme-aware chart colors --}}
<script>
    window.flowChartColors = (function() {
        var isDark = document.documentElement.classList.contains('dark');
        return {
            isDark: isDark,
            tooltip: { theme: isDark ? 'dark' : 'light' },
            chartBackground: isDark ? 'transparent' : '#ffffff',
            gridColor: isDark ? '#37415140' : '#e5e7eb',
            labelStyle: { colors: isDark ? '#9ca3af' : '#6b7280', fontSize: '10px', fontWeight: 600 },
        };
    })();
</script>

@script
<script>
    let activitiesFlowChart = null;

    async function initActivitiesFlowChart() {
        if (window.loadApexCharts) await window.loadApexCharts();
        if (!window.ApexCharts) return;

        const el = document.getElementById('activities-flow-chart');
        if (!el) return;

        if (activitiesFlowChart) activitiesFlowChart.destroy();

        const rawData = await $wire.get('chartActivitiesFlow') ?? [];

        activitiesFlowChart = new window.ApexCharts(el, {
            series: [{ name: 'Actividades', data: rawData }],
            chart: {
                type: 'area',
                height: 200,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, system-ui, sans-serif',
                background: window.flowChartColors.chartBackground,
            },
            colors: ['#a855f7'],
            stroke: { curve: 'smooth', width: 2 },
            markers: {
                size: 3,
                colors: ['#a855f7'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: { size: 5 },
            },
            dataLabels: { enabled: false },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.5, opacityTo: 0, stops: [0, 90, 100] },
            },
            xaxis: {
                type: 'category',
                labels: { style: window.flowChartColors.labelStyle },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: { style: window.flowChartColors.labelStyle },
                tickAmount: 4,
                forceNiceScale: true,
            },
            grid: { borderColor: window.flowChartColors.gridColor, strokeDashArray: 4 },
            tooltip: {
                theme: window.flowChartColors.tooltip.theme,
                y: { formatter: function(val) { return val + ' actividad(es)'; } },
            },
            noData: {
                text: 'Sin datos para los filtros seleccionados',
                align: 'center',
                verticalAlign: 'middle',
                style: { color: '#6b7280', fontSize: '13px' },
            },
        });

        activitiesFlowChart.render();
    }

    initActivitiesFlowChart();
    $wire.$watch('chartActivitiesFlow', () => initActivitiesFlowChart());
    $wire.$watch('registrationRange', () => { setTimeout(() => initActivitiesFlowChart(), 100); });
</script>
@endscript

@script
<script>
    let lessonsFlowChart = null;

    async function initLessonsFlowChart() {
        if (window.loadApexCharts) await window.loadApexCharts();
        if (!window.ApexCharts) return;

        const el = document.getElementById('lessons-flow-chart');
        if (!el) return;

        if (lessonsFlowChart) lessonsFlowChart.destroy();

        const rawData = await $wire.get('chartLessonsFlow') ?? [];

        lessonsFlowChart = new window.ApexCharts(el, {
            series: [{ name: 'Lecciones', data: rawData }],
            chart: {
                type: 'area',
                height: 200,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, system-ui, sans-serif',
                background: window.flowChartColors.chartBackground,
            },
            colors: ['#0ea5e9'],
            stroke: { curve: 'smooth', width: 2 },
            markers: {
                size: 3,
                colors: ['#0ea5e9'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: { size: 5 },
            },
            dataLabels: { enabled: false },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.5, opacityTo: 0, stops: [0, 90, 100] },
            },
            xaxis: {
                type: 'category',
                labels: { style: window.flowChartColors.labelStyle },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: { style: window.flowChartColors.labelStyle },
                tickAmount: 4,
                forceNiceScale: true,
            },
            grid: { borderColor: window.flowChartColors.gridColor, strokeDashArray: 4 },
            tooltip: {
                theme: window.flowChartColors.tooltip.theme,
                y: { formatter: function(val) { return val + ' lección(es)'; } },
            },
            noData: {
                text: 'Sin datos para los filtros seleccionados',
                align: 'center',
                verticalAlign: 'middle',
                style: { color: '#6b7280', fontSize: '13px' },
            },
        });

        lessonsFlowChart.render();
    }

    initLessonsFlowChart();
    $wire.$watch('chartLessonsFlow', () => initLessonsFlowChart());
    $wire.$watch('registrationRange', () => { setTimeout(() => initLessonsFlowChart(), 100); });
</script>
@endscript

@script
<script>
    let diagnosticsFlowChart = null;

    async function initDiagnosticsFlowChart() {
        if (window.loadApexCharts) await window.loadApexCharts();
        if (!window.ApexCharts) return;

        const el = document.getElementById('diagnostics-flow-chart');
        if (!el) return;

        if (diagnosticsFlowChart) diagnosticsFlowChart.destroy();

        const rawData = await $wire.get('chartDiagnosticsFlow') ?? [];

        diagnosticsFlowChart = new window.ApexCharts(el, {
            series: [{ name: 'Diagnósticos', data: rawData }],
            chart: {
                type: 'area',
                height: 200,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, system-ui, sans-serif',
                background: window.flowChartColors.chartBackground,
            },
            colors: ['#10b981'],
            stroke: { curve: 'smooth', width: 2 },
            markers: {
                size: 3,
                colors: ['#10b981'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: { size: 5 },
            },
            dataLabels: { enabled: false },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.5, opacityTo: 0, stops: [0, 90, 100] },
            },
            xaxis: {
                type: 'category',
                labels: { style: window.flowChartColors.labelStyle },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: { style: window.flowChartColors.labelStyle },
                tickAmount: 4,
                forceNiceScale: true,
            },
            grid: { borderColor: window.flowChartColors.gridColor, strokeDashArray: 4 },
            tooltip: {
                theme: window.flowChartColors.tooltip.theme,
                y: { formatter: function(val) { return val + ' diagnóstico(s)'; } },
            },
            noData: {
                text: 'Sin datos para los filtros seleccionados',
                align: 'center',
                verticalAlign: 'middle',
                style: { color: '#6b7280', fontSize: '13px' },
            },
        });

        diagnosticsFlowChart.render();
    }

    initDiagnosticsFlowChart();
    $wire.$watch('chartDiagnosticsFlow', () => initDiagnosticsFlowChart());
    $wire.$watch('registrationRange', () => { setTimeout(() => initDiagnosticsFlowChart(), 100); });
</script>
@endscript
