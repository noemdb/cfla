
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

<hr>

<div class="card text-uppercase">
    <div class="card-header mb-2">
        <div class="font-weight-bold text-right "> <i class="{{$icon_menus['evaluacion'] ?? ''}} text-dark" aria-hidden="true"></i> Evaluaciones</div>
    </div>

    <div class="py-2 px-1">
        <nav>
            <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
                @foreach($lapsos as $lapso)
                    <a class="nav-item nav-link {{($loop->iteration==$lapso_active->id) ? 'active':''}}" id="nav-header-tab-seguimiento-{{$lapso->id}}" data-toggle="tab" href="#nav-content-seguimiento-{{$lapso->id}}" role="tab" aria-controls="nav-home" aria-selected="true"><b>{{$lapso->name ?? ''}}</b></a>
                @endforeach
            </div>
        </nav>

        <div class="tab-content border border-top-0" id="nav-tabContent">
            @foreach($lapsos as $lapso)
                <div class="tab-pane fade show {{($loop->iteration==$lapso_active->id) ? 'active':''}}" id="nav-content-seguimiento-{{$lapso->id}}" role="tabpanel" aria-labelledby="nav-header-home-tab-{{$lapso->id}}">
                    @php $pevaluacions = $estudiant->getPevaluacionsLapso($lapso->id); @endphp
                    <div class="p-1">
                        @foreach($pevaluacions as $pevaluacion)
                            @php
                                $pensum = $pevaluacion->pensum;
                                $nota = $estudiant->getnota($lapso->id,$pensum->id);
                                $profesor = $pevaluacion->profesor;
                            @endphp
                            <div class="pt-4">
                                <div class="d-flex">
                                    <div class="p-2 flex-grow-1 font-weight-bold">
                                        <div>{{$pevaluacion->asignaturas_name}}</div>                                        
                                        <div class="small pl-2">Prof: {{ ($profesor) ? $profesor->fullname : null}}</div>
                                    </div>
                                    <div class=""> <span class=" font-weight-bold border rounded p-1 m-1">{{ $nota ?? ''}}</span> </div>
                                </div>
                            </div>

                            @php $evaluacions = $pevaluacion->evaluacions; @endphp
                            <ul class="list-group list-group-flush">
                                @foreach ($evaluacions as $evaluacion)
                                    @php $boletin = $evaluacion->boletins->where('estudiant_id',$estudiant->id)->first(); @endphp
                                    <li class="list-group-item py-0">
                                        <div class="px-2 small">
                                            <div class="d-flex bd-highlight">
                                              <div class="p-2 flex-grow-1">
                                                    <div class="text-muted">
                                                        {{$loop->iteration}}.- {{ strtoupper($evaluacion->description) }}
                                                        <span class="small text-muted pl-2">Fecha: {{ f_date($evaluacion->fecha) }}</span>
                                                    </div>                                                    
                                              </div>
                                              <div class="p-2">{{ $boletin->nota ?? ''}}</div>
                                            </div>                                            
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>


