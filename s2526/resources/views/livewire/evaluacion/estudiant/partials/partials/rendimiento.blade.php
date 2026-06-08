@php
$seccion = $estudiant->seccion;
$seccion_promedio = $seccion->getPromedio();
$posicion_seccion = $estudiant->posicion_seccion;
$pevaluacions = $estudiant->pevaluacions;
@endphp

<div class="card text-uppercase">
    <div class="card-header">
        <div class="font-weight-bold text-right "> <i class="{{$icon_menus['control_estudio'] ?? ''}} text-dark" aria-hidden="true"></i> Rendimiento Académico</div>
    </div>
    <ul class="list-group">
        <li class="list-group-item py-1 font-weight-bold"><span class=" ">PROMEDIO ACADEMICO:</span> <span class="float-right"> {{$estudiant->getPromedioFinalRound()}}</span></li>
        <li class="list-group-item py-1 font-weight-bold"><span class=" ">PROMEDIO DE LA SECCIÓN:</span> <span class="float-right"> {{$seccion_promedio ?? null}}</span></li>
        <li class="list-group-item py-1 font-weight-bold">
            <span class=" ">Posición con respecto a su sección:</span>

            <span class=" float-lg-right">
                @if ($posicion_seccion==1 || $posicion_seccion==2 || $posicion_seccion==3)
                    <img src="{{asset('images/icon/star-for-number-'.$posicion_seccion.'.png')}}" alt="star" width="30px" height="30px">
                @else
                    {{$posicion_seccion}}
                @endif
            </span>
        </li>
    </ul>
</div>