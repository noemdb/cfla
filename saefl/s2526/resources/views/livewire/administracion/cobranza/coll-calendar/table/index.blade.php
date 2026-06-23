<table class="table table-striped">
    <thead>
        <tr>
            <th wire:click="sortBy('id')">ID</th>
            <th wire:click="sortBy('name')">Nombre</th>
            <th wire:click="sortBy('description')">Descripción</th>
            <th wire:click="sortBy('date')">Fecha</th>
            <th wire:click="sortBy('time')">Hora</th>
            <th wire:click="sortBy('time')">Medio</th>
            <th wire:click="sortBy('status_active')">Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($calendars as $calendar)
        <tr>
            <td>{{ $calendar->id }}</td>
            <td>{{ $calendar->name }}</td>
            <td>{{ $calendar->description }}</td>
            <td>{{ $calendar->date }}</td>
            <td>{{ $calendar->time }}</td>
            <td>
                @if ($calendar->status_email)
                    <div class="form-control small d-inline">@include('svg.mail',['svg_class'=>'text-danger'])</div>
                @endif
                @if ($calendar->status_whatsapp)
                    <div class="form-control small d-inline">@include('svg.whatsapp',['svg_class'=>'text-success'])</div>
                @endif
            </td>
            <td>{{ $calendar->status_active ? 'Activo' : 'Inactivo' }}</td>
            <td>
                <div class="btn-group btn-group-sm" role="group" aria-label="">
                    <button type="button" class="btn btn-warning" wire:click="edit({{ $calendar->id }})">
                        <i class="fa fa-edit" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="delete({{ $calendar->id }})">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                </div>                
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $calendars->links() }}