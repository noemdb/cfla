@props(['stats'])

<div class="grid grid-cols-2 gap-4 md:grid-cols-4">
    <!-- Indicador 1: Visitas Totales -->
    <x-card class="bg-white hover:shadow-md transition-shadow">
        <div class="flex items-center">
            <div class="p-2 mr-3 rounded-full bg-primary-100 text-primary-600">
                <x-icon name="users" class="w-5 h-5" />
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Visitas Totales</p>
                <p class="text-xl font-semibold">{{ number_format($stats['total']) }}</p>
                <p class="text-xs text-gray-400">Periodo actual</p>
            </div>
        </div>
    </x-card>

    <!-- Indicador 2: Tasa de Crecimiento -->
    <x-card class="bg-white hover:shadow-md transition-shadow">
        <div class="flex items-center">
            <div class="p-2 mr-3 rounded-full bg-emerald-100 text-emerald-600">
                <x-icon name="trending-up" class="w-5 h-5" />
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Tasa Crecimiento</p>
                <p class="text-xl font-semibold flex items-center">
                    {{ $stats['growth_rate'] > 0 ? '+' : '' }}{{ number_format($stats['growth_rate'], 1) }}%
                    @if ($stats['growth_rate'] > 0)
                        <x-icon name="arrow-up" class="w-4 h-4 ml-1 text-emerald-600" />
                    @elseif($stats['growth_rate'] < 0)
                        <x-icon name="arrow-down" class="w-4 h-4 ml-1 text-red-600" />
                    @endif
                </p>
                <p class="text-xs text-gray-400">vs periodo anterior</p>
            </div>
        </div>
    </x-card>

    <!-- Indicador 3: Dispositivos Móviles -->
    <x-card class="bg-white hover:shadow-md transition-shadow">
        <div class="flex items-center">
            <div class="p-2 mr-3 rounded-full bg-purple-100 text-purple-600">
                <x-icon name="device-mobile" class="w-5 h-5" />
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Móviles</p>
                <p class="text-xl font-semibold">{{ number_format($stats['mobile_percentage'], 1) }}%</p>
                <p class="text-xs text-gray-400">{{ $stats['mobile'] }} de {{ $stats['total'] }}</p>
            </div>
        </div>
    </x-card>

    <!-- Indicador 4: Visitantes Recurrentes -->
    <x-card class="bg-white hover:shadow-md transition-shadow">
        <div class="flex items-center">
            <div class="p-2 mr-3 rounded-full bg-amber-100 text-amber-600">
                <x-icon name="refresh" class="w-5 h-5" />
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Recurrentes</p>
                <p class="text-xl font-semibold">{{ number_format($stats['returning_percentage'], 1) }}%</p>
                <p class="text-xs text-gray-400">{{ $stats['returning'] }} usuarios</p>
            </div>
        </div>
    </x-card>
</div>
