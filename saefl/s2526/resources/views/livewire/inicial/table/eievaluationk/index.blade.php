<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th>{{$list_comment['profesor_id']}}</th>
            <th>Inscripción</th>
            <th>{{$list_comment['tiempo_ejecucion']}}</th>
            <th>{{$list_comment['observaciones']}}</th>
            <th>Evaluaciones</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($eievaluationks as $item)
            @php
                $profesor = $item->profesor;
                $grado = $item->grado;
                $seccion = $item->seccion;
                $lapso = $item->lapso;
            @endphp
            <tr class="{{ ($item->id == $eievaluationk_id) ? 'table-secondary' : null}}">
                <td>{{ $profesor->fullname}}</td>
                <td>{{ $grado->name}} {{ $seccion->name}} {{ $lapso->name}}</td>
                <td class="text-nowrap">{{ $item->finicial->format('d-m-Y') }} / {{ $item->ffinal->format('d-m-Y') }}</td>
                <td>{{ $item->observaciones}}</td>
                <td>                    
                    <ul class="list-group">
                        @php $eievaluationps = $item->eievaluationps->sortBy('fecha'); @endphp
                        @forelse ($eievaluationps as $subItem)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div class=" text-wrap">{{$loop->iteration}}.- {{$subItem->aprendizaje_alcanzado}} <span class="small border-bottom font-weight-bold">{{f_date($subItem->fecha ?? null)}}</span></div>
                                    <div class="ml-2 text-nowrap">
                                        <i wire:click.prevent="setEditPosition({{$subItem->id}})" class="{{$icon_menus['edit'] ?? ''}} p-1 btn-light text-dark" style="cursor: pointer" aria-hidden="true"></i>
                                        <i wire:click.prevent="deletePosition({{$subItem->id}})" class="{{$icon_menus['eliminar'] ?? ''}} p-1 btn-light text-danger" style="cursor: pointer" aria-hidden="true"></i>
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

                        <a title="Editar plan de evaluación" class="btn btn-warning btn-sm mr-1" href="#" wire:click.prevent="edit({{$item->id}})" role="button">
                            <i class="{{ $icon_menus['edit'] ?? '' }} fa-1x"></i>
                        </a>

                        <a title="Eliminar Plan de evaluación" class="btn btn-danger btn-sm mr-1" href="#" wire:click.prevent="delete({{$item->id}})" role="button">
                            <i class="{{ $icon_menus['eliminar'] ?? '' }} fa-1x"></i>
                        </a>

                        <a title="Agregar Evaluaciones" class="btn btn-info btn-sm mr-1" href="#" wire:click.prevent="setModePosition({{$item->id}})" role="button">
                            <i class="{{ $icon_menus['nuevo'] ?? '' }} fa-1x"></i>
                        </a>
                        <a title="Formato Planificación semanal" class="btn btn-dark btn-sm mr-1" href="{{route('inicials.eievaluationks.format.index',$item->id)}}" role="button" target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                        </a>

                    </div>

                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">No hay datos</td>
            </tr>
        @endforelse
        
    </tbody>
</table>