<div class="card h-100">
    <div class="card-body p-0">
        <div class=" alert alert-{{ ($coll_political->status == 'true') ? 'success':'danger'}}">
            <span class="small float-right font-weight-bold rounded table-light p-1" title="Fecha de inicio y fecha de fin">
                {{ f_date($coll_political->finicial) }} al {{ f_date($coll_political->ffinal) }}
            </span>
            <h5 class=" font-weight-bold">{{$coll_political->name}}</h5>
        </div>
        <div class="card-text px-2 py-0 my-2">

            <nav class=" font-weight-bold">
                <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-detaills-{{$coll_political->id}}-tab" data-toggle="tab" href="#nav-detaills-{{$coll_political->id}}" role="tab" aria-controls="nav-detaills-{{$coll_political->id}}" aria-selected="true">Detalles Generales</a>
                    <a class="nav-item nav-link" id="nav-coll_political-nivels-{{$coll_political->id}}-tab" data-toggle="tab" href="#nav-coll_political-nivels-{{$coll_political->id}}" role="tab" aria-controls="nav-coll_political-nivels-{{$coll_political->id}}" aria-selected="false">Niveles</a>
                    {{-- <a class="nav-item nav-link" id="nav-coll_political-messeges-{{$coll_political->id}}-tab" data-toggle="tab" href="#nav-coll_political-messeges-{{$coll_political->id}}" role="tab" aria-controls="nav-coll_political-messeges-{{$coll_political->id}}" aria-selected="false">Mensajes</a> --}}
                    <a class="nav-item nav-link" id="nav-coll_political-graph-{{$coll_political->id}}-tab" data-toggle="tab" href="#nav-coll_political-graph-{{$coll_political->id}}" role="tab" aria-controls="nav-coll_political-graph-{{$coll_political->id}}" aria-selected="false">Indicador</a>
                </div>
            </nav>
            <div class="tab-content border border-top-0 " id="nav-tabContent">
                <div class="tab-pane fade show active h-100" id="nav-detaills-{{$coll_political->id}}" role="tabpanel" aria-labelledby="nav-detaills-{{$coll_political->id}}-tab">
                    @include('administracion.collections.coll_politicals.partials.details.index')
                    <div class="text-center">
                        <div class="btn-group py-2" role="group" aria-label="Basic example">
                            @php $disabled = ($coll_political->status_send_mail) ? null:'disabled'; @endphp
                            <a name="" id="" class="btn btn-primary {{$disabled ?? null}}" href="{{ route('email.collections.coll_politicals.bacth.send.mail',$coll_political->id)}}" role="button">
                                <i class="fa fa-envelope" aria-hidden="true"></i>
                                Enviar Mensajes
                            </a>
                            <a title="Editar Política de Cobranza" class="btn btn-warning"  href="{{route('administracion.collections.coll_politicals.edit',$coll_political->id)}}" role="button">
                                <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                            </a>
                            <a title="Cargar cola de tareas" class="btn btn-danger {{$disabled ?? null}}"  href="{{route('administracion.collections.coll_politicals.queuing.email.send',$coll_political->id)}}" role="button">
                                <i class="{{ $icon_menus['queuing'] ?? ''}} fa-1x"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade h-100" id="nav-coll_political-nivels-{{$coll_political->id}}" role="tabpanel" aria-labelledby="nav-coll_political-nivels-{{$coll_political->id}}-tab">
                    @php $coll_nivels = $coll_political->coll_nivels; @endphp
                    @include('administracion.collections.coll_nivels.deck.simple')
                </div>
                <div class="tab-pane fade h-100" id="nav-coll_political-graph-{{$coll_political->id}}" role="tabpanel" aria-labelledby="nav-coll_political-graph-{{$coll_political->id}}-tab">
                    @php $coll_nivels = $coll_political->coll_nivels; @endphp
                    {{-- @include('administracion.collections.coll_politicals.charts.ingresoxmonth',['index'=>$coll_political->id]) --}}
                    @include('administracion.collections.coll_politicals.charts.ingresoxdaydate',['index'=>$coll_political->id,'date'=>$coll_political->finicial])
                </div>
            </div>

        </div>
    </div>
</div>
