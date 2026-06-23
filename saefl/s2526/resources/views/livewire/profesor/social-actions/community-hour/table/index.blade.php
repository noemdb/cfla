{{-- 
user_id
estudiant_id
community_action_id
date
hours
observations
--}}

<table class="table table-striped table-inverse table-sm small">    
    <thead class="thead-inverse">
        <tr>
            <th>N</th>
            <th>Título</th>
            <th>Identificador</th>
            <th>Estudiante</th>
            <th>Duración (Hrs)</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($community_hours as $item)
            @php
                $hours = 10;
                $community_action = $item->community_action;
                $estudiant = $item->estudiant;
            @endphp
            
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $community_action->title }} <br> <span class="text-muted">{{ $community_action->description }}</span></td>
                <td>{{ $estudiant->ci_estudiant }}</td>
                <td>{{ $estudiant->fullname }}</td>
                <td>{{ $item->duration }}</td>
                <td>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Small button group" wire:key="btn-group-crud-{{$item->id}}">
                        <a name="" id="" class="btn btn-danger" href="#" role="button" wire:click="removeCommunityHour({{$item->id}})">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>
                    </div> 
                </td>
            </tr>
        @empty

            <tr>
                <th colspan="5" align="center">No hay datos</th>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $community_hours->links() }}

{{-- 
status
observations
author_id --}}