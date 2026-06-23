<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th>Profesor</th>
            <th>Inscripción</th>
            <th>F.Inicial</th>
            <th>F.Final</th>
            <th>Tiempo</th>
            <th>Diagnóstico</th>
            <th>Revisión</th>
            <th>T.Resumen</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($eiprojectks as $item)
            <tr class="">
                <td>{{ $item->profesor->fullname}}</td>
                <td>{{ $item->grado->name}} {{ $item->seccion->name}}</td>
                <td>{{ $item->finicial}}</td>
                <td>{{ $item->ffinal}}</td>
                <td>{{ $item->tiempo_ejecucion}}</td>
                <td>{{ $item->diagnostico}}</td>

                <td>
                    <ul class="list-group">
                        @php $eiprojectreviews = $item->eiprojectreviews; @endphp
                        @forelse ($eiprojectreviews as $subItem)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div class="text-nowrap">{{$loop->iteration}}.- {{$subItem->eleccion_tema_nombre}}</div>
                                </div>
                            </li>
                        @empty
                        <li class="list-group-item disabled">No hay datos</li>
                        @endforelse
                    </ul>
                </td>

                <td>
                    
                    <ul class="list-group">
                        @php $eiprojectsummaries = $item->eiprojectsummaries; @endphp
                        @forelse ($eiprojectsummaries as $subItem)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div class="">{{$loop->iteration}}.- {{$subItem->objetivo}}</div>
                                </div>
                            </li>
                        @empty
                        <li class="list-group-item disabled">No hay datos</li>
                        @endforelse
                    </ul>

                </td>                

                <td>
                    <div class="d-flex justify-content-start">

                        <a title="Formato Planificación semanal" class="btn btn-dark btn-sm mr-1" href="{{route('academicos.eiprojectks.format.index',$item->id)}}"" role="button" target="_BLANK">
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