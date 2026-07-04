@php
    $class_N = 'd-none d-sm-table-cell';
    $class_user = '';
    $class_ci = 'd-none d-md-table-cell';
    $class_planpago = 'd-none d-lg-table-cell text-nowrap';
    $class_deuda = 'd-none d-lg-table-cell text-nowrap';
    $class_grado = 'd-none d-lg-table-cell';
    $class_fecha = 'text-nowrap';
    $class_action = '';
@endphp

<div class="px-2">
    <div class="form-row">
        <div class="col-md-7 col-12 mb-2">
            {!! Form::label('search', 'Buscar Usuario', ['class' => 'm-0 font-weight-bold text-muted']) !!}
            <div class="input-group">
                {!! Form::text('search', $search, [
                    'class' => 'form-control',
                    'wire:model.defer' => 'search',
                    'placeholder' => 'Buscar por nombre, apellido o usuario',
                    'id' => 'currentInput',
                ]) !!}
                <div class="input-group-append">
                    <button wire:click="render()" class="btn btn-outline-secondary" type="button" id="button-addon2">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-6 mb-2">
            {!! Form::label('is_active', 'Estado', ['class' => 'm-0 font-weight-bold text-muted']) !!}
            {!! Form::select(
                'is_active',
                ['' => 'Todos', 'enable' => 'Activos', 'disable' => 'Inactivos'],
                $is_active ?? '',
                [
                    'class' => 'form-control',
                    'wire:model.defer' => 'is_active',
                    'wire:change' => 'applyFilters()',
                ],
            ) !!}
        </div>

        <div class="col-md-2 col-6 mb-2 d-flex align-items-end">
            <button wire:click="create()" class="btn btn-success btn-block" type="button">
                <i class="fas fa-plus mr-1"></i>Nuevo
            </button>
        </div>

        <div class="col-md-1 col-6 mb-2 d-flex align-items-end">
            <button wire:click="resetFilters()" class="btn btn-outline-secondary btn-block" type="button">
                <i class="fas fa-undo-alt mr-1"></i>
            </button>
        </div>
    </div>

    <hr>

    @php $displaModeIndex = (!$modeIndex) ? 'd-none' : null ; @endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default"
        wire:key="table-data-users">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_user }}">Usuario</th>
                <th class="{{ $class_user }}">Email</th>
                <th class="{{ $class_user }}">Rol</th>
                <th class="{{ $class_user }} text-center">Estado</th>
                <th class="{{ $class_action }} text-left">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach ($users as $user)
                @php
                    $is_active = $user->is_active == 'enable' ? true : false;
                    $status_class = $is_active ? 'success' : 'secondary';
                    $status_text = $is_active ? 'Activo' : 'Inactivo';
                @endphp
                <tr data-user_id="{{ $user->id ?? '' }}" data-id="{{ $user->id ?? '' }}"
                    class="{{ $user->id == $user_id ? 'table-secondary' : null }}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{ $loop->iteration }}
                    </td>

                    <td class="{{ $class_user ?? '' }}">
                        <div class="font-weight-bold">{{ $user->username ?? null }}</div>
                        <div class="text-muted small">{{ $user->fullname ?? null }}</div>
                    </td>

                    <td class="{{ $class_user ?? '' }}">
                        <div>{{ $user->email ?? null }}</div>
                    </td>

                    <td class="{{ $class_user ?? '' }}">
                        @if ($user->IsRepresentant())
                            <span class="badge badge-info mr-1">Representante</span>
                        @endif
                        @if ($user->IsProfesor())
                            <span class="badge badge-primary mr-1">Profesor</span>
                        @endif
                        @if ($user->IsEstudiant())
                            <span class="badge badge-success mr-1">Estudiante</span>
                        @endif
                        @if ($user->work_id)
                            <span class="badge badge-warning mr-1">Trabajador</span>
                        @endif
                        @if ($user->is_diagnostic)
                            <span class="badge badge-dark mr-1">Diagnostico</span>
                        @endif
                    </td>

                    <td class="{{ $class_user ?? '' }} text-center">
                        <span class="badge badge-{{ $status_class }}">
                            {{ $status_text }}
                        </span>
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $user->id }}">

                        <div class="btn-group" role="group" aria-label="Acciones usuario">
                            <a title="Editar datos del usuario" class="btn btn-warning btn-sm" href="#"
                                wire:click="edit({{ $user->id }})" role="button">
                                <i class="{{ $icon_menus['editar'] ?? 'fas fa-edit' }} fa-1x"></i>
                            </a>

                            <a title="Perfil" class="btn btn-info btn-sm" href="#"
                                wire:click="setModeProfile({{ $user->id }})" role="button">
                                <i class="{{ $icon_menus['profile'] ?? 'fas fa-user' }} fa-1x"></i>
                            </a>

                            <a title="Roles" class="btn btn-success btn-sm" href="#"
                                wire:click="setModeRol({{ $user->id }})" role="button">
                                <i class="{{ $icon_menus['rol'] ?? 'fas fa-user-tag' }} fa-1x"></i>
                            </a>
                        </div>

                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    {{ $users->links() }}

</div>

@section('scripts')
    @parent
    <script>
        document.addEventListener('livewire:load', function() {
            // Limpiar filtros cuando se cierre el modal de filtros
            Livewire.on('filtersReset', function() {
                // Resetear los valores de los filtros
                const searchInput = document.getElementById('currentInput');
                if (searchInput) {
                    searchInput.value = '';
                }
            });
        });
    </script>
@endsection
