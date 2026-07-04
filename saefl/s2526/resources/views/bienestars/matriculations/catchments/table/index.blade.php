@php
    $class['index']="";
    $class['firstname']="";
    $class['grade']="";
    $class['representant_name']="";
    $class['representant_ci']="";
    $class['email']="";
    $class['status_active']="";
    $class['action']="";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['index'] }}">N</th>
            <th class="{{ $class['firstname'] }}">Estudiante</th>
            <th class="{{ $class['grade'] }}">Grado/Año</th>
            {{-- <th class="{{ $class['grade'] }}">Actividades</th> --}}
            <th class="{{ $class['grade'] }}">F.Cita</th>
            <th class="{{ $class['representant_name'] }}">Rrepresentante</th>
            <th class="{{ $class['representant_name'] }}">Datos</th>
            <th class="{{ $class['status_active'] }}">Procedencia</th>
            <th class="{{ $class['status_active'] }}">Estado</th>
            <th class="{{ $class['status_active'] }}">Entrevista</th>
            <th class="{{ $class['status_active'] }}">Aceptado</th>
            <th class="{{ $class['status_active'] }}">F.Registro</th>
            <th class="{{ $class['action'] }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($catchments as $catchment)

        @php
            $group = $catchment->catchment_group;
            $status_accepted = $catchment->status_accepted;
            $activities = $catchment->activities;
            $grado = $catchment->grado;
            $pestudio = ($grado) ? $grado->pestudio : null ;
            $catchment_interview = $catchment->catchmentInterviews->sortByDesc('created_at')->first();
            $interview_id = ($catchment_interview) ? $catchment_interview->id : null ;
        @endphp

        <tr data-id="{{$catchment->id}}" class="{{($status_accepted) ? 'table-success' : null}}">
            <td id="td-count" class="{{ $class['index'] ?? null }}">
                {{$loop->iteration}}
            </td>
            <td class="{{ $class['firstname']  ?? null}}">
                {{$catchment->fullname ?? ''}}
            </td>
            <td class="{{ $class['grade']  ?? null}}">
                {{ ($grado) ? $grado->name : null}}
                <div class="text-muted small">{{ ($pestudio) ? $pestudio->name : null}}</div>
            </td>
            <td class="{{ $class['grade']  ?? null}}">
                {{f_date($catchment->day_appointment) ?? ''}}
            </td>

            <td class="{{ $class['representant_name']  ?? null}}">
                {{$catchment->fullname_representant ?? ''}}
                <div class="text-muted">
                    <div>{{$catchment->token ?? ''}}</div>
                    <div>CI: {{$catchment->representant_ci ?? null}}</div>
                    <div>Email: {{$catchment->email ?? null}}</div>

                </div>
            </td>

            <td class="{{ $class['representant_name']  ?? null}}">
                Telf.:{{$catchment->representant_phone ?? null}}
                @php  @endphp
                @if ($catchment_interview)
                    <div>Datos de la Entrevista [Telef.]</div>
                    <ul class="ml-2 pl-2">
                        <li>{{$catchment_interview->phone_numbers}}</li>
                        <li>Tutor/Docente: {{$catchment_interview->tutor_teacher_name}} {{$catchment_interview->tutor_teacher_phone}}</li>
                        <li>Guarantor: {{$catchment_interview->person_guarantor_name_phone}}</li>
                        <li>Recommender: {{$catchment_interview->recommender_name}} {{$catchment_interview->recommender_affinity}} {{$catchment_interview->recommender_phone}}</li>
                    </ul>
                @endif
            </td>

            <td class="{{ $class['email']  ?? null}}">
                {{$catchment->origin ?? null}}
            </td>
            <td class="{{ $class['status_active']  ?? null}}">
                {{ ($catchment->status_active) ? 'Activo' : 'Desactivo'}}
            </td>

            <td class="{{ $class['status_active']  ?? null}}">
                {{ ($catchment->catchmentInterviews->count()) ? '[SI]' : '[NO]'}}
            </td>
            </td>

            <td class="{{ $class['status_active']  ?? null}}">
                {{ ($status_accepted) ? '[-Aceptado-]' : '-En Espera-'}}
            </td>
            <td class="{{ $class['email']  ?? null}}">
                {{$catchment->created_at ?? null}}
            </td>

            <td class="{{ $class['action'] ?? null }}">
                <div class="btn-group btn-group-sm">

                    {{-- <button type="button" class="btn btn-info">
                        <i class="fa fa-info" aria-hidden="true"></i>
                    </button> --}}

                    @php $disabled = (! $catchment_interview ) ? 'disabled' : null ; @endphp

                    @php $url = env('APP_URL')."/general/catchments/paper/id/".$interview_id; @endphp
                    <a name="" id="" class="btn btn-dark btn-sm {{$disabled}}" {{$disabled}} href="{{ $url ?? null }}" role="button" target="_BLANK" title="Ver planilla de la entrevista">
                        <i class="{{ $icon_menus['pdf'] ?? '' }}"></i>
                    </a>

                    {{-- @if(!$catchment_interview)
                        <button type="button" class="btn btn-danger btn-sm"
                                onclick="confirmDelete('{{ $catchment->id }}', '{{ $catchment->fullname }}')"
                                title="Eliminar registro">
                            <i class="fas fa-trash"></i>
                        </button>
                    @else
                        <button type="button" class="btn btn-danger btn-sm disabled"
                                title="Eliminar registro" disabled>
                            <i class="fas fa-trash"></i>
                        </button>
                    @endif --}}

                    @php $status_delete = (! $catchment_interview) ? true : false ; @endphp
                    <fieldset {{ ( $status_delete ) ? null:"disabled=disabled" }} >
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{ ( $status_delete ) ? null:"disabled" }}" href="#" id="btn-destroy_{{$catchment->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </fieldset>

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>


{!! Form::open(['route' => ['bienestars.matriculations.catchments.destroy',':CATCHMENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':CATCHMENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/destroy/default.js") }}"></script> @endsection


{{-- {!! Form::open(['route' => ['bienestars.matriculations.catchments.destroy',':CATCHMENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':CATCHMENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}

@section('scripts')
    @parent
    <script src="{{ asset("js/models/destroy/secure.js") }}"></script>
    <script>
        function confirmDelete(id, name) {
            const url = '{{ route("bienestars.matriculations.catchments.destroy", "") }}/' + id;
            const rowSelector = `tr[data-id="${id}"]`;

            secureDelete(id, name, url, rowSelector, {
                title: '¿Está seguro de eliminar este registro',
                text: "No podrá revertir esta acción"
            });
        }
    </script>
@endsection --}}

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.exportBootstrap')
