<div>
    {{-- The Master doesn't talk, he acts. --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="text-start col-sm-6 col-md-3">
                    <label for="start_date" class="form-label">Fecha inicial</label>
                    <input type="date" wire:model.defer="start_date" class="form-control" id="start_date">
                </div>
                <div class="text-start col-sm-6 col-md-3">
                    <label for="end_date" class="form-label">Fecha final</label>
                    <input type="date" wire:model.defer="end_date" class="form-control" id="end_date">
                </div>
                <div class="text-start col-sm-6 col-md-3">
                    <label for="area" class="form-label">Área</label>
                    <select wire:model.defer="selectedArea" class="form-select" id="area">
                        <option value="">Todas las áreas</option>
                        @foreach($areas as $area)
                            <option value="{{ $area }}">{{ $area }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-start col-sm-6 col-md-3">
                    <label for="rol" class="form-label">Rol</label>
                    <select wire:model.defer="selectedRol" class="form-select" id="rol">
                        <option value="">Todos los roles</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol }}">{{ $rol }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="text-start col-sm-6 col-md-2">
                    <label for="url" class="form-label">URL</label>
                    <input type="text" wire:model="url" class="form-control" id="url" placeholder="Filtrar por URL">
                </div>

                <div class="col-12 text-end">
                    <button wire:click="getStats" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
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

    {{-- Tarjetas de Resumen --}}
    <div class="row" wire:loading.remove wire:target="getStats">
        @include('livewire.activity-logs.partials.summary-cards')
    </div>

    {{-- Gráficos --}}
    <div class="row mt-4" wire:loading.remove wire:target="getStats">
        <div class="col-md-12">
            @include('livewire.activity-logs.partials.charts.activities-by-hour')
        </div>
        <div class="col-md-12">
            @include('livewire.activity-logs.partials.charts.activities-by-day')
        </div>
        <div class="col-md-12">
            @include('livewire.activity-logs.partials.charts.top-users')
        </div>
        <div class="col-md-12">
            @include('livewire.activity-logs.partials.charts.top-urls')
        </div>
    </div>
</div>

@section('scripts')
@parent
<script>
    document.addEventListener('livewire:load', function () {
        // El chart se inicializará automáticamente con los datos iniciales
    });

    document.addEventListener('livewire:update', function () {
        // El chart se actualizará automáticamente a través del evento topUsersDataUpdated
    });
</script>
@endsection
