@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="text-nowrap";
    $class_fecha="text-nowrap";
    $class_grado="d-none d-md-table-cell";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_ci }}">Cédula</th>
                <th class="{{ $class_planpago }}">F.Nacimiento</th>
                <th class="{{ $class_planpago }}">Plan de Pago</th>
                <th class="{{ $class_fecha }}" title="Fecha de Inscripción Administrativa">Fecha</th>
                <th class="{{ $class_grado }}">Grado/Sección</th>
                <th class="{{ $class_planpago }}">Correo GS</th>
                <th class="{{ $class_planpago }}">Representante</th>
                <th class="{{ $class_planpago }}">Correo Representante</th>
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($estudiants as $estudiant)
            @php
                // $enable_inscription = $estudiant->enable_inscription;
                $representant = $estudiant->representant;
                $administrativa = $estudiant->administrativa;
                $status_administrativa = empty($administrativa->id);
                $inscripcion = $inscripcion = ($estudiant->fullinscripcion) ? $estudiant->fullinscripcion:null;
                $class_text_color = ($estudiant->grado) ? $estudiant->grado->class_text_color:null;
                $date_birth = ($estudiant->date_birth) ? $estudiant->date_birth : $estudiant->date_enrollment;
            @endphp

            <tr data-estudiant_id="{{$estudiant->id ?? ''}}"  data-administrativa_id="{{$estudiant->administrativa->id ?? ''}}">

                <td class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>

                <td class="{{ $class_estudiant  ?? ''}}">
                    <a class="btn-link" href="{{ route('administracion.estudiants.index',['search'=>$estudiant->ci_estudiant]) }}">
                        <span class="{{$class_text_color ?? 'default'}}">
                            {{$estudiant->fullname}}
                        </span>
                    </a>
                </td>

                <td class="{{ $class_ci  ?? ''}}">
                    {{ $estudiant->ci_estudiant ?? ''}}
                </td>

                <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
                    {{ ($date_birth) ? f_date($date_birth) : ''}}
                </td>

                <td class="{{ $class_planpago ?? '' }}">
                    {{-- {{ $estudiant->administrativa->planpago->name ?? ''}} --}}
                    @php $planpago = $administrativa->planpago ; @endphp
                    @include('elements.badges.planpago',['planpago'=>$planpago])
                </td>

                <td class="{{ $class_fecha ?? '' }}" title="Fecha de Inscripción Administrativa: {{$estudiant->administrativa->created_at ?? ''}}">
                    {{$administrativa->created_at ?? ''}}
                </td>

                <td id="td-users-is_active-{{ $estudiant->id }}" class="{{ $class_grado ?? '' }}">
                    <span class=" text-{{ ($inscripcion) ? $class_text_color:'danger' }}">
                        {{$inscripcion ?? 'NO POSEE'}}
                    </span>
                </td>

                <td id="td-estudiant-gsemail-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
                    {{ $estudiant->gsemail ?? '' }}
                </td>

                <td id="td-estudiant-gsemail-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
                    {{ $representant->ci_representant ?? '' }} || {{ $representant->name ?? '' }}
                </td>

                <td id="td-estudiant-gsemail-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
                    {{ $representant->email ?? '' }}
                </td>

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">
                    <div class="btn-group btn-group-sm">
                        @php
                            $id_modal = 'modal_administrativa_'.$estudiant->id;
                            $administrativa = $estudiant->administrativa;
                        @endphp
                        <a title="Mostrar detalles de las inscripción administrativa" class="btn btn-info btn-xs" href="#" data-toggle="modal" data-target="#{{$id_modal ?? 'id_modal'}}">
                            <i class="fas fa-info"></i>
                        </a>
                        @component('elements.widgets.modal')
                            @slot('classH','secondary')
                            @slot('id',$id_modal)
                            @slot('title','Detalles de la Inscripcións Administrativa')
                            @slot('close',true)
                            @slot('close',true)
                            @slot('body')
                                @include('administracion.administrativas.partial.detaill')
                            @endslot
                        @endcomponent

                        @php
                            $id_modal = 'modal_estiduant_'.$estudiant->id;
                        @endphp
                        {{-- <a title="Detalles del Estudiante" class="btn btn-success btn-xs btn-action-group-{{ $estudiant->id }}" href="{{ route('administracion.estudiants.index',['search'=>$estudiant->ci_estudiant]) }}" id="btn-edituser_{{$estudiant->id}}">
                            <i class="{{ $icon_menus['estudiante'] }}"></i>
                        </a> --}}

                        <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.administrativas.edit',$estudiant->administrativa->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>

                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-xs" href="#" id="btn-destroy-estudiant_id_{{$estudiant->administrativa->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>

{{-- </div> --}}

{!! Form::open(['route' => ['administracion.administrativas.destroy',':ADMINISTRATIVA_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset("js/models/administrativas/destroy.js") }}"></script>
@endsection

@include('administracion.datatables.exportBootstrap')