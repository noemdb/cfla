@php
    $class_N="d-none d-sm-table-cell";
    $class_representant="text-nowrap";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_contacto="d-none d-lg-table-cell";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr style="padding-left:2px;padding-right:2px;">
                <th class="{{ $class_N }}">N</th>
                <th style="padding-left:2px;padding-right:2px;" class="{{ $class_ci }}" class="Identificador">Ident.</th>
                <th style="padding-left:2px;padding-right:2px;" class="{{ $class_representant }}">Representante</th>
                <th style="padding-left:2px;padding-right:2px;" class="{{ $class_representant }}">Estudiantes</th>
                {{-- <th style="padding-left:2px;padding-right:2px;" class="{{ $class_planpago }}">Insc.Académica</th> --}}
                {{-- <th style="padding-left:2px;padding-right:2px;" class="{{ $class_planpago }}">Insc.Admin.</th> --}}
                <th class="{{ $class_action }}">Email</th>                
                <th style="padding-left:2px;padding-right:2px;" class="{{ $class_contacto }}">Teléfono(s)</th>
                <th style="padding-left:2px;padding-right:2px;" class="{{ $class_contacto }}">WhatsApp</th>                
                
                @admin
                <th class="{{ $class_action }}">N. Usuario</th>
                <th class="{{ $class_action }}">GSEmail</th>
                <th class="{{ $class_action }}">Contraseña</th>
                @endadmin
                {{-- <th class="{{ $class_action }}">Activo</th> --}}
                <th class="{{ $class_action }}">Formalizado</th>
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">
            @foreach($representants as $representant)
                @php
                    $ci_estudiant = '';
                    $fullname = '';
                    $inscripcion = '';
                    $administrativa = '';
                    $status_active = ($representant->status_active=='true') ? true:false;
                    $active = $representant->active;
                    $enable = $representant->enable;
                    $estudiants = $representant->estudiants;
                    $fullInscripcion = null;
                    $status_whatsapp_verify = $representant->status_whatsapp_verify ?? false;
                @endphp

                <tr data-id="{{$representant->id ?? ''}}" data-representant_id="{{$representant->id ?? ''}}" class="{{($active) ? '':'table-danger'}}">
                    <td id="td-count" class="{{ $class_N ?? '' }}">
                        {{$loop->iteration}}
                    </td>
                    <td id="td-estudiant" class="{{ $class_ci ?? '' }} text-nowrap small">
                        <b> {{ $representant->ci_representant ?? ''}}</b>
                        @if ($representant->status_adviders == 'true')
                            <div><small>DELEGADO</small></div>
                        @endif
                    </td>
                    <td class="small">
                        @include('administracion.representants.partials.href')
                    </td>

                    <td>
                        @foreach ($estudiants as $estudiant)
                            @php $inscripcion = (!empty($estudiant->getInscripcion()->id)) ? $estudiant->getInscripcion()->fullname:null; @endphp
                            @php $fullInscripcion = ($inscripcion) ? $inscripcion : $fullInscripcion; @endphp
                            @php $grado = $estudiant->grado; $est_grado_id = ($grado) ? $grado->id : null ; @endphp
                            @if (isset($grado_id))
                                @if ($grado_id == $est_grado_id)
                                <div class="small pl-2">-. {{$estudiant->short_name ?? ''}} {{$inscripcion}}</div>
                                @endif
                            @else
                                <div class="small pl-2">-. {{$estudiant->short_name ?? ''}} {{$inscripcion}}</div>
                            @endif                            
                        @endforeach
                    </td>

                    {{-- <td style="white-space: nowrap !important">
                        <span class="small">
                            {{$fullInscripcion ?? null}}
                        </span>
                    </td> --}}                    

                    <td style="white-space: wrap !important" >
                        @if (validate_email($representant->email))
                            <span class="text-primary"> {{ $representant->email ?? null}} </span>
                        @else
                            <span class="text-danger font-weight-bold">NOT VALID</span>
                            <div class="text-muted">{{ $representant->email ?? null}}</div>
                        @endif
                    </td> 
                    
                    <td class="text-wrap">
                        <span class="small">{{ $representant->fullphone ?? ''}}<br>{{ $representant->phone_old ?? ''}}</span>
                    </td>
                    
                    <td style="white-space: wrap !important" >
                        <span class="text-success font-weight-bold"> {{ $representant->whatsapp ?? null}} </span>
                        <div class="text-right small font-weight-bold text-{{$status_whatsapp_verify ? 'success' : 'danger' }}">{{ $status_whatsapp_verify ? 'Verificado' : '-NO-Verificado-' }}</div>
                    </td>

                    @admin
                        <td style="white-space: wrap !important">
                            @php $user = ($representant->user) ? $representant->user:null ; @endphp
                            {{ ($user) ? $user->username : null }}
                        </td>
                        
                        <td style="white-space: wrap !important">
                            {{ $representant->gsemail ?? ''}}
                        </td>
                        <td style="white-space: wrap !important">
                            @php if ($user) $password = ($user->status_update) ? '###':$user->username ; @endphp
                            {{ $password ?? ''}}
                        </td>
                    @endadmin

                    <td style="white-space: wrap !important">
                        {{ ($active) ? 'SI':'NO'}}
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">

                        <div class="btn-group btn-group-sm">

                            <a title="Resumen" class="btn-card btn btn-info bnt-sm" href="#">
                                <i class="{{ $icon_menus['profile'] }} fa-1x"></i>
                            </a>
                            <a title="Editar datos del estudiante" class="btn btn-warning bnt-sm" href="{{ route('administracion.representants.edit',['id'=>$representant->id]) }}" role="button">
                                <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
                            </a>

                            @admin
                            <a title="Eliminar" class="btn-destroy btn btn-danger bnt-sm" href="#" id="btn-destroy_id_{{$estudiant->id}}">
                                <i class="fas fa-trash"></i>
                            </a>
                            @endadmin

                        </div>

                    </td>

                </tr>

            @endforeach

        </tbody>
    </table>

{!! Form::open(['route' => ['administracion.representants.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts')  @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('administracion.datatables.default') --}}
@include('administracion.datatables.particulars.representans.exportBootstrap')
{{-- /home/nuser/code/s2021/resources/views/administracion/datatables/particulars/representans/exportBootstrap.blade.php --}}


@section('scripts')
    @parent
    <script>
        $('.btn-card').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');  //console.log(id);
            var modal = '#modal_card';  //console.log(modal);
            var container = '#container_modal';  //console.log(container);
            var ajaxurl = '{{route("administracion.ajax.fill.modal.estudiant_card", "_id_")}}';
            ajaxurl = ajaxurl.replace('_id_', id);
            $.ajax({
                url: ajaxurl,
                type: "GET",
                success: function(data){
                    $(container).html(data);
                    $(modal).modal('toggle');
                }
            });
        });
    </script>
@endsection
