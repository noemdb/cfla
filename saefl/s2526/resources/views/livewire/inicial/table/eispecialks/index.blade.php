<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th>{{$list_comment['profesor_id']}}</th>
            <th>Inscripción</th>
            <th>{{$list_comment['finicial']}}</th>
            <th>{{$list_comment['ffinal']}}</th>
            <th>{{$list_comment['tiempo_ejecucion']}}</th>
            <th>{{$list_comment['justificacion']}}</th>
            <th>Actividades</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($eispecialks as $item)
            <tr class="{{ ($item->id == $eispecialk_id) ? 'table-secondary' : null}}">
                <td>{{ $item->profesor->fullname}}</td>
                <td>{{ $item->grado->name}} {{ $item->seccion->name}}</td>
                <td>{{ $item->finicial}}</td>
                <td>{{ $item->ffinal}}</td>
                <td>{{ $item->tiempo_ejecucion}}</td>
                <td>{{ $item->justificacion}}</td>
                <td>
                    <ul class="list-group">
                        @php $activities = $item->getOrderedActivities(); @endphp
                        @forelse ($activities as $subItem)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div class="">{{$subItem->order}}.- {{$subItem->objetivo}}</div>
                                    <div class="ml-2 text-nowrap">
                                        <i wire:click.prevent="setEditActivity({{$subItem->id}})" class="{{$icon_menus['edit'] ?? ''}} p-1 btn-light text-dark" style="cursor: pointer" aria-hidden="true"></i>
                                        <i wire:click.prevent="deleteActivity({{$subItem->id}})" class="{{$icon_menus['eliminar'] ?? ''}} p-1 btn-light text-danger" style="cursor: pointer" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </li>
                        @empty
                        <li class="list-group-item disabled">No hay datos</li>
                        @endforelse
                    </ul>
                </td>

                <td>
                    <div class="d-flex justify-content-start">

                        <a title="Editar Plan Especial" class="btn btn-warning btn-sm mr-1" href="#" wire:click.prevent="edit({{$item->id}})" role="button">
                            <i class="{{ $icon_menus['edit'] ?? '' }} fa-1x"></i>
                        </a>

                        <a title="Eliminar Plan Especial" class="btn btn-danger btn-sm mr-1" href="#" wire:click.prevent="delete({{$item->id}})" role="button">
                            <i class="{{ $icon_menus['eliminar'] ?? '' }} fa-1x"></i>
                        </a>

                        <a title="Agregar Actividades" class="btn btn-info btn-sm mr-1" href="#" wire:click.prevent="setModeCreateActivity({{$item->id}})" role="button">
                            <i class="{{ $icon_menus['nuevo'] ?? '' }} fa-1x"></i>
                        </a>
                        <a title="Formato Plan Especiall" class="btn btn-dark btn-sm mr-1" href="{{route('inicials.eispecialks.format.index',$item->id)}}" role="button" target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                        </a>

                    </div>

                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9">No hay datos</td>
            </tr>
        @endforelse
        
    </tbody>
</table>