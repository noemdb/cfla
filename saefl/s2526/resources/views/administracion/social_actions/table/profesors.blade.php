<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th>N°</th>
            <th>Nombre</th>
            <th>Servicios Ejecutados</th>
            <th>Cumplimineto</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody id="tdatos">
        @php 
            $profesors = $profesors->sortBy('name');
            
         @endphp
        @forelse ($profesors as $item)
            @php
                $community_actions = $item->community_actions;
                $hours_completed = $item->hours_completed;
            @endphp
            <tr>
                <td>{{ $loop->iteration}}</td>
                <td scope="row">{{$item->fullname}}</td>
                <td>{{ $community_actions->count() ?? null}}</td>
                <td>{{$hours_completed ?? null}}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                      <a name="" id="" class="btn btn-dark btn-sm" href="{{route('administracion.social_actions.community_actions.profesor.pdf',$item->id)}}" role="button" target="_BLANK">
                        <i class="far fa-file-pdf"></i>
                      </a>
                    </div>
                </td>
            </tr>
        @empty
            
        @endforelse
        
    </tbody>
</table>