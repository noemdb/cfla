{{-- <div class="card mb-3 bd-callout bd-callout-{{$profesor->inscripcion->seccion->grado->color ?? 'default'}} text-uppercase"> --}}
    <div class="card-header">
        <div class="font-weight-bold text-right"> <i class="{{$icon_menus['profesor'] ?? ''}} text-dark" aria-hidden="true"></i> {{$profesor->fullname ?? null}}</div>
    </div>
    <div class="row no-gutters">
        <div class="col-md-4">
            <img class="card-img-top" src="{{ asset('images/avatar/user_default.png') }}" alt="Card image cap">
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
                            <div  class="d-flex align-items-center"> {{$profesor->ci_profesor}}</div>
                        </div>
                    </li>
                    <li class="list-group-item py-0">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1 font-weight-bold">
                                <span class=" font-weight-bold">Edad:</span> 
                            </div>
                            <div class="d-flex align-items-center">{{$profesor->age ?? null}} años</div>
                        </div>
                    </li>
                    <li class="list-group-item py-0">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1">
                                <span class="">N. de Incidencias:</span> 
                            </div>
                            @php  $incidents = $profesor->incidents; @endphp
                            <div class="d-flex align-items-center">{{ $incidents->count() ?? null}}</div>
                        </div>
                    </li>
                    <li class="list-group-item py-0">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1">
                                <span class="">N. de Acuerdos:</span> 
                            </div>
                            @php $incident_agreements = $profesor->incident_agreements; @endphp
                            <div class="d-flex align-items-center">{{ $incident_agreements->count() ?? null}}</div>
                        </div>
                    </li>

                    <li class="list-group-item py-0">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1">
                                <span class="">N. de Convocatorias al representante:</span> 
                            </div>
                            @php $announcement = $profesor->incident_agreements->where('status_announcement',true); @endphp
                            <div class="d-flex align-items-center">{{ $announcement->count() ?? null}}</div>
                        </div>
                    </li>
                    <li class="list-group-item py-0">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1">
                                <span class="">% de incidencias con agresión:</span> 
                            </div>
                            @php $index_aggression = $profesor->index_aggression; @endphp
                            <div class="d-flex align-items-center">{{ $index_aggression ?? null}} %</div>
                        </div>
                    </li>
                </ul>
            </p>
            {{-- <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p> --}}
        </div>
        </div>
    </div>
{{-- </div> --}}
