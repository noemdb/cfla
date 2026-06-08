@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['firstname']="d-none d-sm-table-cell";
    $class['lastname']="d-none d-sm-table-cell";
    $class['number_id']="d-none d-sm-table-cell";
    $class['ident']="d-none d-md-table-cell";
    $class['position']="d-none d-md-table-cell";
    $class['pestudios']="d-none d-md-table-cell";
    $class['worker_order']="d-none d-md-table-cell";
    $class['action']="d-none d-sm-table-cell";
    $table_id = 'table-data-default';
@endphp

<div class="alert alert-info mb-3">
    <h5 class="font-weight-bold mb-0">
        <i class="fas fa-list-alt mr-2"></i>
        Listado del personal con rol y cargo actual y activo
    </h5>
    <small class="mb-0">Mostrando el personal con sus cargos vigentes y horarios activos</small>
</div>

{{-- Controles de Búsqueda y Paginación --}}
<div class="row mb-3">
    <div class="col-md-6">
        <div class="input-group">
            <input type="text" wire:model.debounce.300ms="search" class="form-control" placeholder="Buscar por nombre, cédula, identificación...">
            <div class="input-group-append">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-6 text-right">
        <div class="form-inline justify-content-end">
            <label for="perPage" class="mr-2">Mostrar:</label>
            <select wire:model="perPage" class="form-control form-control-sm">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>
</div>

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id}}">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['firstname'] ?? ''}}">{{$list_comment_user['firstname'] ?? ''}}</th>
            <th class="{{ $class['lastname'] ?? ''}}">{{$list_comment_user['lastname'] ?? ''}}</th>
            <th class="{{ $class['number_id'] ?? ''}}">{{$list_comment_user['number_id'] ?? ''}}</th>
            <th class="{{ $class['ident'] ?? ''}}">{{$list_comment_user['ident'] ?? ''}}</th>
            <th class="{{ $class['position'] ?? ''}}">Cargo Actual</th>
            <th class="{{ $class['worker_order'] ?? ''}}">{{$list_comment_user['worker_order'] ?? ''}}</th>
            <th class="{{ $class['pestudios'] ?? ''}}">{{$list_comment_user['pestudios'] ?? ''}}</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($workers as $user)
        @php
            $pestudios = $user->pestudios;
            $currentPosition = $user->position;
        @endphp

        <tr data-id="{{$user->id}}" class="{{($user->id == $user_id) ? 'bg-secondary font-weight-bold text-light' : null}}">
            <td class="{{ $class['iteration'] ?? ''}}">
                {{ ($workers->currentPage() - 1) * $workers->perPage() + $loop->iteration }}
            </td>
            <td class="{{ $class['fullname'] ?? ''}}">{{$user->firstname ?? ''}}</td>
            <td class="{{ $class['fullname'] ?? ''}}">{{$user->lastname ?? ''}}</td>
            <td class="{{ $class['number_id'] ?? ''}}">{{$user->number_id ?? ''}}</td>
            <td class="{{ $class['ident'] ?? ''}}">{{$user->ident ?? ''}}</td>
            <td class="{{ $class['position'] ?? ''}}">
                {{ $currentPosition ? $currentPosition->name : 'Sin cargo vigente' }}
            </td>
            <td class="{{ $class['worker_order'] ?? ''}}">{{$user->worker_order ?? ''}}</td>
            <td class="{{ $class['pestudios'] ?? ''}}">
                @foreach ($user->pestudios as $pestudio)
                    <span class=" mx-1">{{$pestudio->name}}</span>
                @endforeach
            </td>
            <td class="{{ $class_action ?? '' }}">
                <div class="btn-group btn-group-sm">
                    <button wire:click="editWorker({{$user->id}})" class="btn btn-warning btn-sm">
                        <i class="{{ $icon_menus['editar'] ?? 'fas fa-edit'}} fa-1x"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{-- Paginación --}}
@if($workers->hasPages())
<div class="row mt-3">
    <div class="col-md-6">
        <p class="text-muted small">
            Mostrando {{ $workers->firstItem() }} a {{ $workers->lastItem() }} de {{ $workers->total() }} registros
        </p>
    </div>
    <div class="col-md-6">
        <div class="d-flex justify-content-end">
            {{ $workers->links() }}
        </div>
    </div>
</div>
@endif