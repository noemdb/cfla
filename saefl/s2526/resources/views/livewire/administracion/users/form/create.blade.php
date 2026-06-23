{{-- resources/views/livewire/administracion/users/form/create.blade.php --}}
<div>
    <div class="p-1 m-1">
        {{-- Contenedor con altura máxima y scroll --}}
        <div style="max-height: 70vh; overflow-y: auto;">

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mx-2 mt-2" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form wire:submit.prevent="store" class="px-2">

                <ul class="nav nav-tabs mb-3" id="userCreateTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="user-tab" data-toggle="tab" href="#user" role="tab"
                            aria-controls="user" aria-selected="true">
                            <i class="fas fa-user mr-2"></i>Usuario
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                            aria-controls="profile" aria-selected="false">
                            <i class="fas fa-id-card mr-2"></i>Perfil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="rol-tab" data-toggle="tab" href="#rol" role="tab"
                            aria-controls="rol" aria-selected="false">
                            <i class="fas fa-user-tag mr-2"></i>Rol
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="userCreateTabContent">

                    <!-- Tab: Usuario -->
                    <div class="tab-pane fade show active" id="user" role="tabpanel" aria-labelledby="user-tab">
                        <!-- Sección: Datos de Usuario -->
                        <div class="card mb-3 border-0">
                            <div class="card-body p-0">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user.username" class="font-weight-bold m-0 small">Usuario
                                                *</label>
                                            {!! Form::text('user.username', null, [
                                                'wire:model.defer' => 'user.username',
                                                'class' => 'form-control',
                                                'placeholder' => 'Nombre de usuario',
                                            ]) !!}
                                            @error('user.username')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user.email" class="font-weight-bold m-0 small">Email *</label>
                                            {!! Form::email('user.email', null, [
                                                'wire:model.defer' => 'user.email',
                                                'class' => 'form-control',
                                                'placeholder' => 'correo@ejemplo.com',
                                            ]) !!}
                                            @error('user.email')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password" class="font-weight-bold m-0 small">Contraseña
                                                *</label>
                                            {!! Form::password('password', [
                                                'wire:model.defer' => 'password',
                                                'class' => 'form-control',
                                                'placeholder' => 'Mínimo 6 caracteres',
                                            ]) !!}
                                            @error('password')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation"
                                                class="font-weight-bold m-0 small">Confirmar Contraseña *</label>
                                            {!! Form::password('password_confirmation', [
                                                'wire:model.defer' => 'password_confirmation',
                                                'class' => 'form-control',
                                                'placeholder' => 'Repetir contraseña',
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user.is_active" class="font-weight-bold m-0 small">Estado
                                                *</label>
                                            {!! Form::select('user.is_active', $list_status, null, [
                                                'wire:model.defer' => 'user.is_active',
                                                'class' => 'form-control',
                                                'placeholder' => 'Seleccionar estado',
                                            ]) !!}
                                            @error('user.is_active')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h6 class="mb-3 font-weight-bold text-primary">
                                    <i class="fas fa-address-card mr-2"></i>Datos de Identificación
                                </h6>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="user.card_id" class="font-weight-bold m-0 small">Número de
                                                Tarjeta</label>
                                            {!! Form::text('user.card_id', null, [
                                                'wire:model.defer' => 'user.card_id',
                                                'class' => 'form-control',
                                                'placeholder' => 'Número de tarjeta',
                                            ]) !!}
                                            @error('user.card_id')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="user.work_id" class="font-weight-bold m-0 small">Ident.
                                                Trabajador BIO</label>
                                            {!! Form::number('user.work_id', null, [
                                                'wire:model.defer' => 'user.work_id',
                                                'class' => 'form-control',
                                                'placeholder' => 'ID Biométrico',
                                            ]) !!}
                                            <small class="form-text text-muted">Asociación Biométrico</small>
                                            @error('user.work_id')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="user.ident" class="font-weight-bold m-0 small">Ident.
                                                Trabajador</label>
                                            {!! Form::text('user.ident', null, [
                                                'wire:model.defer' => 'user.ident',
                                                'class' => 'form-control',
                                                'placeholder' => 'Identificación trabajador',
                                            ]) !!}
                                            @error('user.ident')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Perfil -->
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="card mb-3 border-0">
                            <div class="card-body p-0">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="profile.firstname" class="font-weight-bold m-0 small">Nombres
                                                *</label>
                                            {!! Form::text('profile.firstname', null, [
                                                'wire:model.defer' => 'profile.firstname',
                                                'class' => 'form-control',
                                                'placeholder' => 'Primer nombre',
                                            ]) !!}
                                            @error('profile.firstname')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="profile.lastname" class="font-weight-bold m-0 small">Apellidos
                                                *</label>
                                            {!! Form::text('profile.lastname', null, [
                                                'wire:model.defer' => 'profile.lastname',
                                                'class' => 'form-control',
                                                'placeholder' => 'Apellidos completos',
                                            ]) !!}
                                            @error('profile.lastname')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="profile.card_number"
                                                class="font-weight-bold m-0 small">Cédula</label>
                                            {!! Form::text('profile.card_number', null, [
                                                'wire:model.defer' => 'profile.card_number',
                                                'class' => 'form-control',
                                                'placeholder' => 'Número de cédula',
                                            ]) !!}
                                            @error('profile.card_number')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Rol -->
                    <div class="tab-pane fade" id="rol" role="tabpanel" aria-labelledby="rol-tab">
                        <div class="card mb-3 border-0">
                            <div class="card-body p-0">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rol.area" class="font-weight-bold m-0 small">Área *</label>
                                            {!! Form::select('rol.area', $list_area, null, [
                                                'wire:model.defer' => 'rol.area',
                                                'class' => 'form-control',
                                                'placeholder' => 'Seleccionar área',
                                            ]) !!}
                                            @error('rol.area')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rol.rol" class="font-weight-bold m-0 small">Rol *</label>
                                            {!! Form::select('rol.rol', $list_rol, null, [
                                                'wire:model.defer' => 'rol.rol',
                                                'class' => 'form-control',
                                                'placeholder' => 'Seleccionar rol',
                                            ]) !!}
                                            @error('rol.rol')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rol.cargo_id" class="font-weight-bold m-0 small">Cargo</label>
                                            {!! Form::select('rol.cargo_id', $list_cargos, null, [
                                                'wire:model.defer' => 'rol.cargo_id',
                                                'class' => 'form-control',
                                                'placeholder' => 'Seleccionar cargo',
                                            ]) !!}
                                            @error('rol.cargo_id')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rol.assit_schedule_id"
                                                class="font-weight-bold m-0 small">Horario</label>
                                            {!! Form::select('rol.assit_schedule_id', $list_assit_schedule, null, [
                                                'wire:model.defer' => 'rol.assit_schedule_id',
                                                'class' => 'form-control',
                                                'placeholder' => 'Seleccionar horario',
                                            ]) !!}
                                            @error('rol.assit_schedule_id')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="rol.finicial" class="font-weight-bold m-0 small">Fecha Inicial
                                                *</label>
                                            {!! Form::date('rol.finicial', null, [
                                                'wire:model.defer' => 'rol.finicial',
                                                'class' => 'form-control',
                                            ]) !!}
                                            @error('rol.finicial')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="rol.ffinal" class="font-weight-bold m-0 small">Fecha Final
                                                *</label>
                                            {!! Form::date('rol.ffinal', null, [
                                                'wire:model.defer' => 'rol.ffinal',
                                                'class' => 'form-control',
                                            ]) !!}
                                            @error('rol.ffinal')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="rol.group" class="font-weight-bold m-0 small">Grupo</label>
                                            {!! Form::select('rol.group', $list_rols_group, null, [
                                                'wire:model.defer' => 'rol.group',
                                                'class' => 'form-control',
                                                'placeholder' => 'Seleccionar grupo',
                                            ]) !!}
                                            @error('rol.group')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="form-group mt-4 mb-2">
                    <div class="btn-group btn-block" role="group">
                        <button type="button" class="btn btn-secondary" wire:click="close">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-2"></i>Crear Usuario
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
