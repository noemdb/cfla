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
            <th class="{{ $class['grade'] }}">Actividades</th>
            <th class="{{ $class['representant_name'] }}">Representante</th>
            <th class="{{ $class['representant_name'] }}">Email</th>
            <th class="{{ $class['representant_name'] }}">CI</th>
            <th class="{{ $class['status_active'] }}">Procedencia</th>
            <th class="{{ $class['email'] }}">Aceptado</th>
            <th class="{{ $class['email'] }}">En espera</th>
            <th class="{{ $class['email'] }}">Notificado</th>
            <th class="{{ $class['action'] }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($catchment_interviews as $catchment_interview)

        @php
            $catchment = $catchment_interview->catchment;
            $group = ($catchment) ? $catchment->catchment_group : null;
            $activities = ($group) ? $group->activities : collect();
            $grado = ($catchment_interview) ? $catchment_interview->grado : null;
            $pestudio = ($grado) ? $grado->pestudio : null ;
            $accepted = $catchment_interview->accepted ;
            $status_notified = $catchment_interview->status_notified ;
            $status_standby = $catchment_interview->status_standby ;
        @endphp

        <tr data-id="{{$catchment_interview->id}}" class="{{ ($accepted) ? 'alert table-success font-weight-bold text-success' : null}}">
            <td id="td-count" class="{{ $class['index'] ?? null }}">
                {{$loop->iteration}}
            </td>
            <td class="{{ $class['firstname']  ?? null}}">
                {{$catchment_interview->student_full_name ?? ''}}
            </td>
            <td class="{{ $class['grade']  ?? null}}">
                {{ ($grado) ? $grado->name : null}}
                <div class="text-muted small">{{ ($pestudio) ? $pestudio->name : null}}</div>
            </td>
            <td class="{{ $class['grade']  ?? null}}">
                <div class="text-uppercase">{{ ($group) ? $group->name : null}}</div>
            </td>
            <td class="{{ $class['representant_name']  ?? null}}">
                {{$catchment_interview->full_name ?? ''}}
                <div class="small text-muted">

                    {{$catchment_interview->identification_number ?? ''}}
                    @if ($catchment) <div> @admin {{$catchment->token ?? ''}} @endadmin </div> @endif
                    <div>
                        {{$catchment_interview->email ?? null}}
                        @if ($catchment_interview->token) @admin <div> {{$catchment_interview->token ?? null}} </div> @endadmin @endif
                    </div>

                    <div>Datos de la Entrevista [Telef.]</div>
                    <ul class="ml-2 pl-2">
                        <li>{{$catchment_interview->phone_numbers}}</li>
                        <li>Tutor/Docente: {{$catchment_interview->tutor_teacher_name}} {{$catchment_interview->tutor_teacher_phone}}</li>
                        <li>Guarantor: {{$catchment_interview->person_guarantor_name_phone}}</li>
                        <li>Recommender: {{$catchment_interview->recommender_name}} {{$catchment_interview->recommender_affinity}} {{$catchment_interview->recommender_phone}}</li>
                    </ul>

                </div>
            </td>
            <td class="{{ $class['email']  ?? null}}">
                {{ $catchment_interview->email }}
            </td>
            </td>
            <td class="{{ $class['email']  ?? null}}">
                {{ $catchment_interview->identification_number }}
            </td>
            <td class="{{ $class['email']  ?? null}}">
                {{ ($catchment) ? $catchment->origin : null}}
            </td>
            <td class="{{ $class['email']  ?? null}}">
                {{ ($accepted) ? '-SI-' : '-NO-'}}
            </td>
            <td class="{{ $class['email']  ?? null}}">
                {{ ($status_standby) ? '{SI}' : '{NO}'}}
            </td>
            <td class="{{ $class['email']  ?? null}}">
                {{ ($status_notified) ? '[SI]' : '[NO]'}}
            </td>
            <td class="{{ $class['action'] ?? null }}">
                <div class="btn-group btn-group-sm">

                    <a name="" id="edit" class="btn btn-warning" href="{{route('bienestars.matriculations.interviews.edit',$catchment_interview->id)}}" role="button">
                        <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
                    </a>

                    <a name="" id="paper" class="btn btn-info" href="{{route('catchments.paper',$catchment_interview->identification_number)}}" role="button" target="_BLANK">
                        <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                    </a>

                    @php $token = $catchment_interview->token; @endphp
                    @if ($token)
                        <a name="" id="edit" class="btn btn-dark" href="{{route('catchments.accepted',$token)}}" role="button" target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                        </a>
                    @endif

                    @if($status_standby)
                        <a name="" id="standby" class="btn btn-secondary" href="{{route('catchments.standby',$catchment_interview->id)}}" role="button" target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                        </a>
                    @endif

                    @if(!$accepted)
                        <button type="button" class="btn btn-danger btn-sm"
                                onclick="confirmDeleteInterview('{{ $catchment_interview->id }}', '{{ $catchment_interview->student_full_name }}')"
                                title="Eliminar entrevista">
                            <i class="fas fa-trash"></i>
                        </button>
                    @else
                        <button type="button" class="btn btn-danger btn-sm disabled"
                                title="No se puede eliminar una entrevista aceptada" disabled>
                            <i class="fas fa-trash"></i>
                        </button>
                    @endif                    

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

@section('scripts')
    @parent
    <script src="{{ asset("js/models/destroy/secure.js") }}"></script>
    <script>
        function confirmDeleteInterview(id, name) {
            const url = '{{ route("bienestars.matriculations.interviews.destroy", "") }}/' + id;
            const rowSelector = `tr[data-id="${id}"]`;

            secureDelete(id, name, url, rowSelector, {
                title: '¿Está seguro de eliminar esta entrevista',
                text: "No podrá revertir esta acción"
            });
        }
    </script>
@endsection

@include('administracion.datatables.exportBootstrap')
