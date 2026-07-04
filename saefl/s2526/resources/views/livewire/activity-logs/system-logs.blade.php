<div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="text-start col-sm-6 col-md-3">
                    <label for="selected_date" class="form-label">Fecha</label>
                    <input type="date" wire:model.defer="selected_date" class="form-control" id="selected_date">
                </div>
                <div class="text-start col-sm-6 col-md-3">
                    <label for="level" class="form-label">Nivel</label>
                    <select wire:model.defer="level" class="form-select" id="level">
                        <option>Seleccione</option>
                        @foreach($levels as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-start col-sm-6 col-md-3">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" wire:model.defer="search" class="form-control" id="search" placeholder="Buscar en mensajes...">
                </div>
                <div class="text-end col-sm-6 col-md-3 d-flex justify-content-end align-items-end">
                    <div class="d-flex justify-content-end">
                        <button wire:click="getStats" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading Spinner --}}
    <div wire:loading wire:target="getStats">
        <div class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.5); z-index: 9999;">
            <div class="d-flex flex-column align-items-center bg-white p-4 rounded shadow">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <div class="mt-2 text-primary fw-bold">Procesando datos...</div>
            </div>
        </div>
    </div>

    <div class="card" wire:loading.remove wire:target="getStats">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-2 cursor-pointer" wire:click="sortBy('date')" style="cursor: pointer;">
                                Fecha
                                @if($sortField === 'date')
                                    @if($sortDirection === 'asc')
                                        <i class="fas fa-sort-up ms-1"></i>
                                    @else
                                        <i class="fas fa-sort-down ms-1"></i>
                                    @endif
                                @endif
                            </th>
                            <th class="px-3 py-2 cursor-pointer" wire:click="sortBy('level')" style="cursor: pointer;">
                                Nivel
                                @if($sortField === 'level')
                                    @if($sortDirection === 'asc')
                                        <i class="fas fa-sort-up ms-1"></i>
                                    @else
                                        <i class="fas fa-sort-down ms-1"></i>
                                    @endif
                                @endif
                            </th>
                            <th class="px-3 py-2 cursor-pointer" wire:click="sortBy('channel')" style="cursor: pointer;">
                                Canal
                                @if($sortField === 'channel')
                                    @if($sortDirection === 'asc')
                                        <i class="fas fa-sort-up ms-1"></i>
                                    @else
                                        <i class="fas fa-sort-down ms-1"></i>
                                    @endif
                                @endif
                            </th>
                            <th class="px-3 py-2">Mensaje</th>
                            <th class="px-3 py-2 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="px-3 py-2">
                                    {{ \Carbon\Carbon::parse($log['date'])->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-3 py-2">
                                    <span class="badge bg-{{ $this->getLogLevelClass($log['level']) }}">
                                        {{ strtoupper($log['level']) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    {{ $log['channel'] }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ Str::limit($log['message'], 100) }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <button wire:click="showDetails('{{ $log['id'] }}')" class="btn btn-sm btn-link text-primary">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    No se encontraron registros
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    @if($showDetails && $selectedLog)
        @include('livewire.activity-logs.partials.log-details')
    @endif
</div>
