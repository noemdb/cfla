
<table width="100%" class="table table-sm small p-1" id="table-data-default">
    <thead>
        <tr style="border-bottom: 0.2rem solid #c5c5c5">
            <th>N°</th>
            <th>Inscripción</th>
            <th>F.Inicial - F.Final</th>
            <th>T.Resumen</th>
            {{-- <th>Estrategias</th> --}}
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($eiplanningbwks as $item)

            @php
                $profesor = $item->profesor;
                $grado = $item->grado;
                $seccion = $item->seccion;
            @endphp
        
            <tr>
                <th rowspan="2" class="">{{$loop->iteration}}</th>
                <td colspan="8" class=""><span class="font-weight-bold">Diagnóstico: </span>{{ $item->diagnostico}}</td>
            </tr>
            
            <tr style="border-bottom: 0.4rem solid #c5c5c5">                
                <td>
                    <div class=" font-weight-bold">{{ $grado->name}} {{ $seccion->name}}</div>
                    
                    <div class="text-muted font-weight-bold">{{$profesor->fullname}}</div>
                </td>
                <td class="text-nowrap">{{ f_date($item->finicial)}} - {{ f_date($item->ffinal)}}</td>
                <td>

                    <ul class="list-group">
                        @php $eiplanningbwsummaries = $item->eiplanningbwsummaries; @endphp
                        @forelse ($eiplanningbwsummaries as $subItem)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div class="" title="{{$subItem->objetivo}}">{{$loop->iteration}}.- {{$subItem->objetivo}}</div>
                                </div>
                            </li>
                        @empty
                        <li class="list-group-item disabled">No hay datos</li>
                        @endforelse
                    </ul>
                    
                </td>                

                <td>

                    <div class="btn-group-vertical">
                        <a title="Formato Planificación Quincenal" class="btn btn-dark btn-sm mr-1" href="{{route('plannings.eiplanningwks.format.index',$item->id)}}" role="button" target="_BLANK">
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