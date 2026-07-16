# ApexCharts en el Proyecto

Referencia para implementar gráficos con ApexCharts en este proyecto Laravel + Livewire + Vite.

---

## Instalación

```bash
npm install apexcharts
```

Versión instalada: **5.16.0** (compatible con Node 20.x+).

## Configuración con Vite

### 1. Lazy loader (recomendado)

ApexCharts (~300 kB) se carga bajo demanda solo en las páginas que lo necesitan. El patrón lazy loader ya está configurado:

**resources/js/loaders.js** — añade al final:

```js
export async function loadApexCharts() {
    if (window._apexChartsPromise) return window._apexChartsPromise;

    window._apexChartsPromise = import('apexcharts/dist/apexcharts.esm.js').then((m) => {
        const ApexCharts = m.default || m;
        window.ApexCharts = ApexCharts;
        return ApexCharts;
    });

    return window._apexChartsPromise;
}
```

**resources/js/app.js** — expón en `window`:

```js
import { loadMermaid, loadSwiper, loadChart, loadApexCharts } from './loaders';

window.loadApexCharts = loadApexCharts;
```

### 2. Import estático (alternativa, si se usa en toda la app)

```js
import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts;
```

---

## Uso en Livewire + Alpine

### Estructura base

```blade
<div wire:key="chart-unique-key"
     x-data="{
         chart: null,
         chartData: @json($phpChartData),
         async initChart() {
             if (window.loadApexCharts) await window.loadApexCharts();
             const el = this.$refs.chartEl;
             if (!el || !window.ApexCharts) return;
             if (this.chart) { this.chart.destroy(); }
             const categories = this.chartData.map(d => d.label);   // eje X
             const values = this.chartData.map(d => d.value);        // eje Y
             this.chart = new window.ApexCharts(el, { /* options */ });
             this.chart.render();
         },
     }"
     x-init="
         initChart();
         $wire.$watch('phpChartDataProperty', () => {
             $nextTick(() => { chartData = $wire.phpChartDataProperty; initChart(); });
         });
     "
     class="...">
    <div wire:ignore>
        <div x-ref="chartEl" style="min-height: 250px;"></div>
    </div>
</div>
```

### Puntos clave

| Elemento | Propósito |
|----------|-----------|
| `wire:key` | Fuerza re-render completo en Livewire |
| `wire:ignore` | Evita que Livewire mute el DOM del chart |
| `x-ref="chartEl"` | Referencia Alpine al contenedor del SVG |
| `chartData: @json(...)` | Datos iniciales desde PHP a Alpine |
| `$wire.$watch(...)` | Reacciona cuando Livewire actualiza la propiedad |
| `initChart()` | Destruye chart anterior y crea uno nuevo |

### Formato de datos desde PHP

```php
// En el componente Livewire
public $chartData = [];

private function loadChartData()
{
    $this->chartData = Model::query()
        ->selectRaw('column as label, COUNT(*) as value')
        ->groupBy('label')
        ->orderBy('label')
        ->get()
        ->map(fn($row) => [
            'label' => $row->label,
            'value' => (int) $row->value,
        ])
        ->toArray();
}
```

---

## Configuración de gráficos

### Bar Chart / Column Chart

```js
const options = {
    chart: {
        type: 'bar',
        height: 250,
        toolbar: { show: false },
        animations: {
            enabled: true,
            dynamicAnimation: { speed: 500 },
        },
        zoom: { enabled: false },
        fontFamily: 'Inter, system-ui, sans-serif',
    },
    series: [
        { name: 'Label', data: [10, 20, 15, 30] },
    ],
    xaxis: {
        categories: ['2026-07-01', '2026-07-02', '2026-07-03'],
        labels: {
            format: 'dd MMM',         // para fechas con type: 'datetime'
            style: { colors: '#9ca3af', fontSize: '10px', fontWeight: 600 },
        },
        axisBorder: { show: false },
        axisTicks: { show: false },
    },
    yaxis: {
        labels: {
            style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 600 },
        },
        tickAmount: 5,
    },
    colors: ['#10b981'],              // emerald-500
    fill: {
        type: 'gradient',
        gradient: {
            shade: 'dark',
            type: 'vertical',
            gradientToColors: ['#34d399'],
            stops: [0, 80, 100],
            opacityFrom: 1,
            opacityTo: 0.4,
        },
    },
    plotOptions: {
        bar: {
            borderRadius: 4,
            horizontal: false,
            columnWidth: '65%',
        },
    },
    dataLabels: { enabled: false },
    grid: {
        borderColor: '#37415140',
        strokeDashArray: 4,
        xaxis: { lines: { show: false } },
    },
    tooltip: {
        theme: 'dark',
        style: { fontSize: '12px' },
        y: { formatter: (v) => v + ' unidad(es)' },
    },
    states: {
        hover: { filter: { type: 'lighten', value: 0.1 } },
    },
    noData: {
        text: 'Sin datos para los filtros seleccionados',
        style: { color: '#6b7280', fontSize: '13px' },
    },
};
```

### Line Chart

```js
const options = {
    chart: {
        type: 'line',
        height: 300,
        toolbar: { show: true },
        zoom: { enabled: true, type: 'x' },
        fontFamily: 'Inter, system-ui, sans-serif',
    },
    series: [
        { name: 'Serie 1', data: [30, 40, 45, 50] },
        { name: 'Serie 2', data: [20, 35, 40, 45] },
    ],
    xaxis: {
        categories: ['Ene', 'Feb', 'Mar', 'Abr'],
        labels: { style: { colors: '#9ca3af', fontSize: '11px' } },
    },
    yaxis: {
        labels: { style: { colors: '#9ca3af', fontSize: '11px' } },
    },
    colors: ['#10b981', '#06b6d4'],
    stroke: {
        curve: 'smooth',
        width: 3,
    },
    markers: {
        size: 5,
        hover: { size: 8 },
    },
    grid: {
        borderColor: '#37415140',
        strokeDashArray: 4,
    },
    tooltip: {
        theme: 'dark',
        shared: true,
    },
    legend: {
        position: 'top',
        labels: { colors: '#d1d5db' },
    },
};
```

### Donut / Pie Chart

```js
const options = {
    chart: {
        type: 'donut',
        height: 350,
        fontFamily: 'Inter, system-ui, sans-serif',
    },
    series: [44, 55, 41, 17],
    labels: ['Categoría A', 'Categoría B', 'Categoría C', 'Categoría D'],
    colors: ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6'],
    legend: {
        position: 'bottom',
        labels: { colors: '#d1d5db' },
    },
    dataLabels: {
        enabled: true,
        style: { colors: ['#fff'] },
    },
    tooltip: { theme: 'dark' },
    plotOptions: {
        pie: {
            donut: {
                size: '65%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Total',
                        color: '#d1d5db',
                    },
                },
            },
        },
    },
    responsive: [
        {
            breakpoint: 768,
            options: { chart: { height: 250 }, legend: { position: 'bottom' } },
        },
    ],
};
```

---

## Colores del proyecto

Referencia para reutilizar la paleta:

| Color | Tailwind | Código | Uso |
|-------|----------|--------|-----|
| Esmeralda | emerald-500 | `#10b981` | Principal (éxito, actividades) |
| Esmeralda claro | emerald-400 | `#34d399` | Gradiente |
| Cian | cyan-400 | `#22d3ee` | Acento informativo |
| Ámbar | amber-400 | `#fbbf24` | Advertencia |
| Azul | blue-400 | `#60a5fa` | Información |
| Violeta | violet-400 | `#a78bfa` | Diagnóstico |
| Gris texto | gray-400 | `#9ca3af` | Etiquetas secundarias |
| Gris sutil | gray-500 | `#6b7280` | Texto menos relevante |

---

## Dark Mode

ApexCharts tiene un theme `'dark'` para el tooltip, pero los colores de labels, grid, y fondo se configuran manualmente.

| Elemento | Dark mode | Light mode |
|----------|-----------|------------|
| Tooltip | `theme: 'dark'` | `theme: false` (auto) o `theme: 'light'` |
| Label text | `#9ca3af` (gray-400) | `#6b7280` (gray-500) |
| Grid border | `#37415140` | `#e5e7eb` (gray-200) |
| Fondo del chart | transparente (heredado) | transparente (heredado) |

Para soportar ambos modos, puedes leer la clase del `<html>`:

```js
const isDark = document.documentElement.classList.contains('dark');
const labelColor = isDark ? '#9ca3af' : '#6b7280';
const gridColor = isDark ? '#37415140' : '#e5e7eb40';
```

---

## Integración con Livewire

### Actualización automática al cambiar filtros

Cuando los filtros se vinculan con `wire:model.live`, Livewire re-renderiza el componente y envía datos frescos. El `$wire.$watch` detecta el cambio:

```blade
x-init="
    initChart();
    $wire.$watch('phpChartDataProperty', () => {
        $nextTick(() => {
            chartData = $wire.phpChartDataProperty;
            initChart();
        });
    });
"
```

### Forzar recreación con wire:key

Si el gráfico necesita recrearse completamente (ej. cambia el tipo de chart), usa `wire:key` con un sufijo dinámico:

```blade
<div wire:key="chart-{{ $chartVersion }}">
```

Incrementa `$chartVersion` en PHP cuando quieras forzar un reinicio completo del componente Alpine.

---

## Ejemplos en el proyecto

### Implementación actual: Indicadores

- **Componente:** `app/Livewire/Planning/Indicator/IndexComponent.php`
- **Propiedad:** `$chartActivitiesByDay`
- **Vista:** `resources/views/livewire/planning/indicator/index-component.blade.php`
- **Tipo:** Bar chart — actividades registradas por fecha (`finicial`)
- **Filtros:** lapso, programa educativo, plan de estudio, grado, profesor

---

## Buenas prácticas

1. **Lazy load:** usa `loadApexCharts()` para no inflar el bundle global
2. **Siempre `wire:ignore`** alrededor del contenedor del chart
3. **Destruir antes de recrear:** `if (this.chart) this.chart.destroy()`
4. **Usar `$nextTick`** después de actualizar `chartData` para esperar al DOM
5. **Min-height en el contenedor** para evitar layout shift mientras carga
6. **Tooltip siempre `theme: 'dark'`** si la app está en modo oscuro permanente
7. **Fuente:** usar `fontFamily: 'Inter, system-ui, sans-serif'` para consistencia
