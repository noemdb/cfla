@php
    $class['index']="";
    $class['firstname']="d-none d-lg-table-cell";
    $class['grade']="d-none d-lg-table-cell";
    $class['representant_name']="d-none d-lg-table-cell";
    $class['representant_ci']="dd-none d-lg-table-cell";
    $class['email']="d-none d-lg-table-cell";
    $class['status_active']="d-none d-lg-table-cell";
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
            {{-- <th class="{{ $class['email'] }}">C.Electrónico</th> --}}
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
                {{-- <div class="">
                    @forelse ($activities as $activity)
                        <div>{{$loop->iteration ?? null}}.- {{ $activity->name ?? null}}: {{ $activity->date_time->format('d-m-Y H:i') ?? null}}</div>
                    @empty
                        <div>No hay actividades</div>
                    @endforelse
                </div> --}}
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
                    {{-- <div>{{$catchment_interview->representant_phone ?? null}}</div>                 --}}

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
                    
                    <a name="" id="edit" class="btn btn-warning" href="{{route('administracion.matriculations.interviews.edit',$catchment_interview->id)}}" role="button">
                        <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
                    </a>

                    <a name="" id="paper" class="btn btn-danger" href="{{route('catchments.paper',$catchment_interview->identification_number)}}" role="button" target="_BLANK">
                        <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                    </a>

                    @php $token = $catchment_interview->token; @endphp
                    @if ($token)                        
                        <a name="" id="edit" class="btn btn-dark" href="{{route('catchments.accepted',$token)}}" role="button" target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                        </a>                        
                    @endif   

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.exportBootstrap')
