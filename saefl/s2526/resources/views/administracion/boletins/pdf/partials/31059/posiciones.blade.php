@php
    $seccion_promedio_lapso = $seccion->getPromedioLapso($lapso->id);
    // $promedio_lapso = $estudiant->getNotaFinalLapso($lapso->id,2);
    $promedio_lapso = $estudiant->getNotaFinalLapso($lapso->id,4,true,false);
    $posicion_seccion_lapso = $estudiant->getPosicionSeccionLapso($lapso->id);

    $seccion_promedio = $seccion->getPromedio();
    $estudiant_promedio = $estudiant->getPromedioFinalLapsoId($lapso_id,2);
    // $posicion_seccion = $estudiant->posicion_seccion;
    $posicion_seccion = $estudiant->getPosicionSeccionLapso($lapso_id);

@endphp

<hr>

<div style="text-align:left; white-space:wrap; padding-top:0.4rem;font-size:0.6rem !important">
    Posición en base al rendimiento Estudiantil. Valoración sin redondeo ni puntos de ajuste. Considerado para el 
reconocimiento honorífico institucional.
</div>

<table class="table table-sm small" style="padding-top:0.4rem;font-size:0.6rem !important">
    <thead class="thead-inverse">
        <tr><th colspan="3">Rendimiento Académico</th></tr>
    </thead>
    <thead>
        <tr><th>&nbsp;</th><th align="right">Momento</th><th align="right">Definitiva</th></tr>
    </thead>
    <tbody style="font-size: 0.7rem">
        <tr>
            <td>Promedio de la sección: </td>
            <th align="right">{{$seccion_promedio_lapso}}</th>
            <th align="right">{{$seccion_promedio}}</th>
        </tr>
        <tr>
            <td>Promedio Académico (PA) del estudiante: </td>
            <th align="right">{{$promedio_lapso}}</th>
            <th align="right">{{$estudiant_promedio}}</th>
        </tr>
        <tr class="{{ ($posicion_seccion_lapso==1 || $posicion_seccion_lapso==2 || $posicion_seccion_lapso==3) ? 'tr_strong':null}}" style="{{ ($posicion_seccion_lapso==1 || $posicion_seccion_lapso==2 || $posicion_seccion_lapso==3) ? 'background-color: #ccc':null}}">
            <td>Lugar ocupado dentro de la sección de acuerdo al promedio: </td>
            <th align="right">
                @if ($posicion_seccion_lapso==1 || $posicion_seccion_lapso==2 || $posicion_seccion_lapso==3)
                    <img src="{{asset('images/icon/star-for-number-'.$posicion_seccion_lapso.'.png')}}" alt="star" width="20px" height="20px">
                @else
                    <span>{{$posicion_seccion_lapso}}</span>
                @endif
            </th>
            <th align="right">
                @if ($posicion_seccion==1 || $posicion_seccion==2 || $posicion_seccion==3)
                    <img src="{{asset('images/icon/star-for-number-'.$posicion_seccion.'.png')}}" alt="star" width="20px" height="20px">
                @else
                    <span>{{$posicion_seccion}}</span>
                @endif
            </th>
        </tr>
        {{-- <tr>
            <td>Según tu promedio, en tu grado estas en la posición: </td>
            <th align="right">{{$estudiant->getPosicionGradoLapso($lapso->id)}}</th>
        </tr> --}}
    </tbody>
</table>
