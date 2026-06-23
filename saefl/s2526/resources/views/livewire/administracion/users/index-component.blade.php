<div>
    <div class="card-header alert-dark mt-2">
        <h3 class="mb-0 pb-0">
            <div class="float-right">
                <small wire:loading.delay.shortest class="text-muted small px-2">
                    Procesando...
                </small>
            </div>
            <i class="{{ $icon_menus['incidents'] ?? '' }} text-dark" aria-hidden="true"></i>
            <span class="font-weight-bold">Usuarios</span>
        </h3>
    </div>

    <div class="card-body p-1 m-1">
        @if (Session::has('operp_ok'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {!! Session::get('operp_ok') !!}
            </div>
        @endif

        <div class="container-fluid px-0">
            <div class="row">
                <div class="col-12">
                    <div class="border rounded">
                        <h5 class="p-2 m-2 font-weight-bold">Listado de usuarios registrados</h5>
                        @include('livewire.administracion.users.table.index')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Perfil -->
    @if($modeProfile)
    <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header alert-info py-3">
                    <h5 class="modal-title font-weight-bolder text-dark m-0">
                        <i class="fas fa-user-circle mr-2"></i>Perfil del Usuario
                    </h5>
                    <button type="button" class="close" wire:click="close()">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="p-3 border-bottom bg-light">
                                    <div class="text-right font-weight-bold text-sm">
                                        <div class="h6 mb-1">{{ $user->username ?? null }}</div>
                                        <div class="text-muted">{{ $user->fullname ?? null }}</div>
                                        <small class="text-info">{{ ($user->profile) ? 'CI: '.$user->profile->card_number : null }}</small>
                                    </div>
                                </div>
                                @include('livewire.administracion.users.table.profile.index')
                                
                                <!-- Formulario de edición de perfil dentro del mismo modal -->
                                @includeWhen($modeProfileEdit,'livewire.administracion.users.form.profile.edit')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal para Roles -->
    @if($modeRol)
    <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header alert-info py-3">
                    <h5 class="modal-title font-weight-bolder text-dark m-0">
                        <i class="fas fa-user-tag mr-2"></i>Roles del Usuario
                    </h5>
                    <button type="button" class="close" wire:click="close()">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="p-3 border-bottom bg-light">
                                    <div class="text-right font-weight-bold text-sm">
                                        <span class="mb-1">{{ $user->username ?? null }}|</span>
                                        <span class="text-muted">{{ $user->fullname ?? null }}|</span>
                                        <small class="text-info">{{ ($user->profile) ? 'CI: '.$user->profile->card_number : null }}</small>
                                    </div>
                                </div>
                                
                                <!-- Listado de roles -->
                                <div class="p-3">
                                    @include('livewire.administracion.users.table.rol.index')
                                </div>
                                
                                <!-- Formulario de edición de rol debajo de la tabla -->
                                @includeWhen($modeRolEdit,'livewire.administracion.users.form.rol.edit')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal para Editar Usuario -->
    @if($modeEdit)
    <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header alert-warning py-3">
                    <h5 class="modal-title font-weight-bolder text-dark m-0">
                        <i class="fas fa-edit mr-2"></i>Editar Usuario
                    </h5>
                    <button type="button" class="close" wire:click="close()">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="p-3 border-bottom bg-light">
                                    <div class="text-right font-weight-bold text-sm">
                                        <span class="mb-1">{{ $user->username ?? null }}</span>
                                        <span class="text-muted">{{ $user->fullname ?? null }}</span>
                                        <small class="text-info">{{ ($user->profile) ? 'CI: '.$user->profile->card_number : null }}</small>
                                    </div>
                                </div>
                                @include('livewire.administracion.users.form.fields')
                                <div class="btn-group btn-block btn-group-sm mb-2" role="group" aria-label="Basic example">
                                    {!! Form::button('Guardar',['class' => 'form-control btn pt-1 mt-1 btn-primary','wire:click'=>"save()"]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- En resources/views/livewire/administracion/users/index-component.blade.php --}}
    {{-- Reemplazar el modal de creación --}}

    <!-- Modal para Crear Usuario -->
    @if($modeCreate)
    <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header alert-success py-3">
                    <h5 class="modal-title font-weight-bolder text-dark m-0">
                        <i class="fas fa-plus-circle mr-2"></i>Crear Nuevo Usuario
                    </h5>
                    <button type="button" class="close" wire:click="close()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    @include('livewire.administracion.users.form.create')
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal para Ver Usuario -->
    @if($modeView)
    <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header alert-primary py-3">
                    <h5 class="modal-title font-weight-bolder text-dark m-0">
                        <i class="fas fa-eye mr-2"></i>Detalles del Usuario
                    </h5>
                    <button type="button" class="close" wire:click="close()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Funcionalidad de visualización de detalles en desarrollo.
                    </div>
                    <!-- Contenido para ver usuario -->
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal para Filtros -->
    @if($modeFilter)
    <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header alert-secondary py-3">
                    <h5 class="modal-title font-weight-bolder text-dark m-0">
                        <i class="fas fa-filter mr-2"></i>Filtros de Búsqueda
                    </h5>
                    <button type="button" class="close" wire:click="close()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Funcionalidad de filtros avanzados en desarrollo.
                    </div>
                    <!-- Contenido para filtros -->
                </div>
            </div>
        </div>
    </div>
    @endif
</div>