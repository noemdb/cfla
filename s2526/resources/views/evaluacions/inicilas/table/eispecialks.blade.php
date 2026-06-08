<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th>Profesor</th>
            <th>Inscripción</th>
            <th>F.Inicial</th>
            <th>F.Final</th>
            <th>Tiempo</th>
            <th>Justificación</th>
            <th>Observación</th>
            <th>T.Actividades</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($eispecialks as $item)
            <tr class="">
                <td>{{ $item->profesor->fullname}}</td>
                <td>{{ $item->grado->name}} {{ $item->seccion->name}}</td>
                <td class=" text-nowrap">{{ f_date($item->finicial)}}</td>
                <td class=" text-nowrap">{{ f_date($item->ffinal)}}</td>
                <td>{{ $item->tiempo_ejecucion}} (Sem.)</td>
                <td>{{ $item->justificacion}}</td>
                <td>{{ $item->observacion}}</td>

                <td>
                    <ul class="list-group">
                        @php $activities = $item->activities; @endphp
                        @forelse ($activities as $subItem)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div class="text-nowrap">{{$loop->iteration}}.- {{$subItem->aprendizaje_esperado}} <div>{{$subItem->indicadores}}</div></div>
                                </div>
                            </li>
                        @empty
                        <li class="list-group-item disabled">No hay datos</li>
                        @endforelse
                    </ul>
                </td>

                <td>
                    <div class="d-flex justify-content-start">

                        <a title="Formato Plan Especial" class="btn btn-dark btn-sm mr-1" href="{{route('evaluacions.eispecialks.format.index',$item->id)}}" role="button" target="_BLANK">
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