<div>
    <main role="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 p-1">
                    <div class="card card-primary mt-2">
                        <div class="card-header pb-0 mb-0 alert-secondary">
                            {{-- INI Menu rapido --}}
                            <div class="btn-group float-right pt-0 pb-2">
                                @include('administracion.matriculations.catchments.menus.index')
                            </div>
                            {{-- FIN Menu rapido --}}
                            <h4><u title="Listado especial con botones de acción">Listado</u> de las <span
                                    class="font-weight-bolder">manifestaciones de interés</span> registradas</h4>
                        </div>

                        <div class="card-body">
                            {{-- Filtros de búsqueda --}}
                            <div class="py-2 my-2">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Buscar por Cédula"
                                                wire:model.defer="representant_ci">
                                            <div class="input-group-append">
                                                <button class="btn btn-info" type="button" wire:click="searchByCi">
                                                    Buscar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" placeholder="Búsqueda general..."
                                            wire:model.debounce.500ms="search">
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-control" wire:model="filterStatusActive">
                                            <option value="">Todos (Estatus)</option>
                                            <option value="1">Activos</option>
                                            <option value="0">Eliminados</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-control" wire:model="filterPestudio">
                                            <option value="">— Plan de Estudio —</option>
                                            @foreach($list_pestudios as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-control" wire:model="filterGrado"
                                                {{ $filterPestudio === '' ? 'disabled' : '' }}>
                                            <option value="">
                                                {{ $filterPestudio === '' ? '— Plan... —' : '— Grado —' }}
                                            </option>
                                            @foreach($list_grados_filtered as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-2">
                                        <select class="form-control" wire:model="perPage">
                                            <option value="10">10 por pág.</option>
                                            <option value="15">15 por pág.</option>
                                            <option value="25">25 por pág.</option>
                                            <option value="50">50 por pág.</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        @if($representant_ci || $search || $filterStatusActive !== '' || $filterPestudio !== '' || $filterGrado !== '')
                                            <div class="alert alert-light py-1 mb-0 border">
                                                <small><strong>Filtros:</strong></small>
                                                @if($representant_ci) <span class="badge badge-info">CI: {{ $representant_ci }}</span> @endif
                                                @if($search) <span class="badge badge-info">Busq: {{ $search }}</span> @endif
                                                @if($filterStatusActive !== '') <span class="badge badge-secondary">{{ $filterStatusActive ? 'Activos' : 'Eliminados' }}</span> @endif
                                                @if($filterPestudio !== '') <span class="badge badge-primary">Plan: {{ $list_pestudios[$filterPestudio] ?? '' }}</span> @endif
                                                @if($filterGrado !== '') <span class="badge badge-secondary">Grado: {{ $list_grados_filtered[$filterGrado] ?? '' }}</span> @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <button class="btn btn-secondary btn-block" wire:click="clearFilters">
                                            Limpiar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Loading indicator --}}
                            <div wire:loading class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                            </div>

                            {{-- Tabla de datos --}}
                            <div wire:loading.remove>
                                @include('livewire.administracion.matriculation.partials.catchment-table')
                            </div>

                            {{-- Paginación --}}
                            <div class="d-flex justify-content-center mt-3">
                                {{ $catchments->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

@section('sweetalert')
    @parent
    <script>
        window.addEventListener('swal:confirm-catchment', event => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: event.detail.confirmButtonText || 'Sí',
                cancelButtonText: event.detail.cancelButtonText || 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed || result.value) {
                    if (event.detail.action === 'forceDelete') {
                        @this.call('confirmForceDelete');
                    } else if (event.detail.action === 'restore') {
                        @this.call('confirmRestore');
                    } else {
                        @this.call('confirmDelete');
                    }
                } else {
                    if (event.detail.action === 'forceDelete') {
                        @this.call('cancelForceDelete');
                    } else if (event.detail.action === 'restore') {
                        @this.call('cancelRestore');
                    } else {
                        @this.call('cancelDelete');
                    }
                }
            });
        });

        window.addEventListener('swal:success-catchment', event => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            });
        });

        window.addEventListener('swal:error-catchment', event => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                icon: 'error'
            });
        });
    </script>
@endsection
</div>
