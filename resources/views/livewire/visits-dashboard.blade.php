<div>
    <!-- Encabezado -->
    <x-card title="Registro de Visitas" subtitle="Historial de accesos al sistema"
        class="mb-4 dark:bg-secondary-800/50 dark:border-secondary-700">
        <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4">
            <!-- Filtro por días -->
            <x-native-select wire:model.live="daysFilter" label="Periodo" :options="[
                ['value' => 1, 'name' => 'Últimas 24h'],
                ['value' => 7, 'name' => 'Últimos 7 días'],
                ['value' => 30, 'name' => 'Últimos 30 días'],
                ['value' => 90, 'name' => 'Últimos 3 meses'],
            ]" option-value="value"
                option-label="name" class="w-full sm:w-40" />

            <!-- Filtro por dispositivo -->
            <x-native-select wire:model.live="deviceType" label="Dispositivo" :options="[
                ['value' => '', 'name' => 'Todos'],
                ['value' => 'mobile', 'name' => 'Móviles'],
                ['value' => 'desktop', 'name' => 'Escritorio'],
                ['value' => 'tablet', 'name' => 'Tablets'],
            ]" option-value="value"
                option-label="name" class="w-full sm:w-40" />

            <!-- Búsqueda -->
            <x-input wire:model.live.debounce.300ms="search" label="Buscar" placeholder="URL, IP o usuario..."
                icon="magnifying-glass" class="w-full sm:w-48" />
        </div>
    </x-card>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 gap-4 mb-4 sm:grid-cols-3">
        <x-card class="hover:shadow-md transition-shadow dark:bg-secondary-800/50 dark:border-secondary-700">
            <div class="flex items-center">
                <x-icon name="users" class="w-8 h-8 mr-3 text-primary-600 dark:text-primary-400" />
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-secondary-400">Total Visitas</h3>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-secondary-100">
                        {{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-md transition-shadow dark:bg-secondary-800/50 dark:border-secondary-700">
            <div class="flex items-center">
                <x-icon name="finger-print" class="w-8 h-8 mr-3 text-emerald-600 dark:text-emerald-400" />
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-secondary-400">Visitantes Únicos</h3>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-secondary-100">
                        {{ number_format($stats['unique']) }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-md transition-shadow dark:bg-secondary-800/50 dark:border-secondary-700">
            <div class="flex items-center">
                <x-icon name="device-phone-mobile" class="w-8 h-8 mr-3 text-purple-600 dark:text-purple-400" />
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-secondary-400">Visitas Móviles</h3>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-secondary-100">
                        {{ number_format($stats['mobile']) }}</p>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Lista de visitas -->
    <x-card padding="none" class="overflow-hidden dark:bg-secondary-800/50 dark:border-secondary-700">
        <!-- Encabezados -->
        <div
            class="hidden sm:grid grid-cols-12 gap-4 p-4 font-medium text-gray-500 bg-gray-50 border-b border-gray-200 dark:bg-secondary-800 dark:text-secondary-400 dark:border-secondary-700">
            <div class="col-span-3 cursor-pointer select-none" wire:click="sortBy('created_at')">
                <div class="flex items-center">
                    <span>Fecha</span>
                    @if ($sortField === 'created_at')
                        <x-icon name="chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="w-4 h-4 ml-1" />
                    @endif
                </div>
            </div>
            <div class="col-span-4 cursor-pointer select-none" wire:click="sortBy('path')">
                <div class="flex items-center">
                    <span>Ruta</span>
                    @if ($sortField === 'path')
                        <x-icon name="chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="w-4 h-4 ml-1" />
                    @endif
                </div>
            </div>
            <div class="col-span-2">Dispositivo</div>
            <div class="col-span-3">Usuario</div>
        </div>

        <!-- Contenido -->
        <div class="divide-y divide-gray-200 dark:divide-secondary-700">
            @forelse($visits as $visit)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-secondary-800/30 transition-colors"
                    wire:key="visit-{{ $visit->id }}">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                        <!-- Fecha -->
                        <div class="sm:col-span-3 flex justify-between items-center sm:block">
                            <span
                                class="text-xs font-bold uppercase tracking-wider text-gray-400 sm:hidden">Fecha</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-secondary-200">
                                {{ $visit->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>

                        <!-- Ruta -->
                        <div class="sm:col-span-4 flex justify-between items-center sm:block">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400 sm:hidden">Ruta</span>
                            <div class="flex justify-end sm:justify-start">
                                <x-badge flat primary :label="Str::limit(Str::after($visit->path, '/'), 25)" class="max-w-[150px] sm:max-w-none" />
                            </div>
                        </div>

                        <!-- Dispositivo -->
                        <div class="sm:col-span-2 flex justify-between items-center sm:block">
                            <span
                                class="text-xs font-bold uppercase tracking-wider text-gray-400 sm:hidden">Dispositivo</span>
                            <div class="flex items-center">
                                @if ($visit->device_type === 'mobile')
                                    <x-icon name="device-phone-mobile"
                                        class="w-4 h-4 mr-2 text-purple-500 dark:text-purple-400" />
                                    <span class="text-sm text-gray-600 dark:text-secondary-400">Móvil</span>
                                @elseif($visit->device_type === 'tablet')
                                    <x-icon name="tablet" class="w-4 h-4 mr-2 text-blue-500 dark:text-blue-400" />
                                    <span class="text-sm text-gray-600 dark:text-secondary-400">Tablet</span>
                                @else
                                    <x-icon name="computer-desktop"
                                        class="w-4 h-4 mr-2 text-gray-400 dark:text-secondary-500" />
                                    <span class="text-sm text-gray-600 dark:text-secondary-400">Escritorio</span>
                                @endif
                            </div>
                        </div>

                        <!-- Usuario -->
                        <div class="sm:col-span-3 flex justify-between items-center sm:block">
                            <span
                                class="text-xs font-bold uppercase tracking-wider text-gray-400 sm:hidden">Usuario</span>
                            <div class="flex justify-end sm:justify-start text-secondary-600">
                                @if ($visit->user)
                                    <div class="flex items-center">
                                        <x-avatar xs :label="$visit->user->name" />
                                        <div class="ml-2">
                                            <div
                                                class="text-sm font-medium text-gray-900 dark:text-secondary-600 truncate max-w-[120px]">
                                                {{ $visit->user->name }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <x-badge flat slate label="Invitado" />
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <x-icon name="magnifying-glass" class="w-12 h-12 text-gray-300 dark:text-secondary-600" />
                        <span class="mt-4 text-sm font-medium text-gray-500 dark:text-secondary-400">
                            No se encontraron visitas con los filtros actuales
                        </span>
                        <x-button wire:click="resetFilters" xs label="Limpiar filtros" icon="x-circle" class="mt-6"
                            flat />
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        @if ($visits->hasPages())
            <div class="mt-12">
                {{ $visits->links() }}
            </div>
        @endif
    </x-card>

    <!-- Acciones -->
    <div class="flex justify-end mt-4 space-x-2">
        <x-button x-on:click="$dispatch('openModal', { component: 'full-visits-report' })" label="Ver reporte completo"
            icon="document-text" primary sm />
        {{-- <x-button wire:click="exportToPDF" label="Exportar PDF" icon="document-arrow-down" positive sm /> --}}
    </div>
</div>
