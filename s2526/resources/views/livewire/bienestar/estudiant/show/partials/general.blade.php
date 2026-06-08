<div class="card mb-3 bd-callout bd-callout-{{$estudiant->getInscripcion()->seccion->grado->color ?? 'default'}} text-uppercase">
    <div class="card-header">
        <div class="font-weight-bold text-right"> <i class="{{$icon_menus['estudiante'] ?? ''}} text-dark" aria-hidden="true"></i> {{$estudiant->fullname ?? null}}</div>
    </div>
    <div class="row no-gutters">
        <div class="col-md-4">
            <img class="card-img-top" src="{{ (isset($estudiant->logo)) ? asset($estudiant->logo) : asset('images/avatar/user_default.png') }}" alt="Card image cap">
        </div>
        <div class="col-md-8">

        <div class="card-body">

            <p class="card-text">
                <ul class="list-group">
                    <li class="list-group-item py-0">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1 font-weight-bold">
                                <span class=" font-weight-bold">Identificador:</span>
                            </div>
                            <div  class="d-flex align-items-center"> {{$estudiant->type_ci->code}}: {{$estudiant->ci_estudiant}}</div>
                        </div>
                    </li>
                    <li class="list-group-item py-0">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1 font-weight-bold">
                                <span class=" font-weight-bold">Edad:</span> 
                            </div>
                            <div class="d-flex align-items-center">{{$estudiant->age ?? null}} años</div>
                        </div>
                    </li>
                    <li class="list-group-item py-0">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1 font-weight-bold">
                                <span class=" font-weight-bold">Inscripción:</span> 
                            </div>
                            <div class="d-flex align-items-center">{{$estudiant->fullinscripcion ?? null}}</div>
                        </div>
                    </li>
                    <li class="list-group-item py-0">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1">
                                <span class="">N. de Incidencias:</span> 
                            </div>
                            @php  $incidents = $estudiant->incidents; @endphp
                            <div class="d-flex align-items-center">{{ $incidents->count() ?? null}}</div>
                        </div>
                    </li>
                    <li class="list-group-item py-0">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1">
                                <span class="">N. de Acuerdos:</span> 
                            </div>
                            @php $incident_agreements = $estudiant->incident_agreements; @endphp
                            <div class="d-flex align-items-center">{{ $incident_agreements->count() ?? null}}</div>
                        </div>
                    </li>

                    <li class="list-group-item py-0">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1">
                                <span class="">N. de Convocatorias al representante:</span> 
                            </div>
                            @php $announcement = $estudiant->incident_agreements->where('status_announcement',true); @endphp
                            <div class="d-flex align-items-center">{{ $announcement->count() ?? null}}</div>
                        </div>
                    </li>
                    <li class="list-group-item py-0">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1">
                                <span class="">% de incidencias con agresión:</span> 
                            </div>
                            @php $index_aggression = $estudiant->index_aggression; @endphp
                            <div class="d-flex align-items-center">{{ $index_aggression ?? null}} %</div>
                        </div>
                    </li>
                </ul>
            </p>
            {{-- <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p> --}}
        </div>
        </div>
    </div>
</div>
