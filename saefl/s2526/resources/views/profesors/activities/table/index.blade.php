@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-sm table-hover small p-1" id="table-data-default">
        <caption style="caption-side: top-right">Listado de Áreas de Formación</caption>
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_asignatura }}">Asignatura/Grado/Sección</th>
                <th class="{{ $class_grado }}">Cant.Act</th>
                <th class="{{ $class_grado }}">Cant.Ind.</th>
                <th class="{{ $class_lapso }}">Lapso</th>
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($pevaluacions as $pevaluacion)

            @php 
            $activities = $pevaluacion->activities; 
            $achievements = $pevaluacion->achievements; 
            $pensum = $pevaluacion->pensum; 
            $grado = $pensum->grado; 
            $pestudio = $grado->pestudio;
            $grupo_estable = $pevaluacion->grupo_estable;
            @endphp

            <tr data-id="{{$pevaluacion->id}}" data-pevaluacion="{{$pevaluacion->id ?? ''}}"
            class="table-{{(empty($pevaluacion->administrativa->id)) ? 'default':'success'}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td id="td-pevaluacion-asignatura-{{ $pevaluacion->id }}" class="{{ $class_email ?? '' }}">
                    {{ $pevaluacion->pensum->asignatura->name ?? ''}}
                    <div class="font-weight-bold">
                        {{ $grado->name ?? ''}} {{ $pevaluacion->seccion->name ?? ''}}
                        <span class="text-muted">[{{$pestudio->code ?? null}}]</span>                         
                    </div>
                    @if ($grupo_estable) <div class=" text-muted small">Comp. de Formación: {{$grupo_estable->name ?? null}}</div> @endif
                </td>
                <td id="td-grado-{{ $pevaluacion->id }}" class="{{ $class_state ?? '' }}">
                    {{$activities->count()}}
                </td>
                <td id="td-grado-{{ $pevaluacion->id }}" class="{{ $class_state ?? '' }}">
                    {{$achievements->count()}}
                </td>
                <td id="td-pevaluacion-{{ $pevaluacion->id }}" class="{{ $class_state ?? '' }}">
                    {{ $pevaluacion->lapso->name ?? ''}}
                </td>

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $pevaluacion->id }}">
                    <div class="btn-group">

                        @php $class_btn = ($activities->count()) ? 'btn-info' : 'btn-outline-info' ; @endphp
                        <a title="Registrar Actividades" class="btn {{$class_btn ?? ''}}" href="{{route('profesors.activities.create',$pevaluacion->id)}}" role="button">
                            <i class="{{ $icon_menus['activities'] ?? ''}} fa-1x"></i>
                        </a>

                        <a title="Resumen del Plan de Actividades" class="btn btn-secondary" href="{{route('profesors.activities.resume',$pevaluacion->id)}}" role="button" target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x"></i>
                        </a>

                        <a title="Plan de Actividades" class="btn btn-success" href="{{route('profesors.activities.format',$pevaluacion->id)}}" role="button" target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x"></i>
                        </a>

                    </div>
                </td>

            </tr>
            @endforeach

        </tbody>

    </table>


{!! Form::open(['route' => ['profesors.pevaluacions.destroy',':PEVALUCION_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/pevaluacions/destroy.js") }}"></script> @endsection

{{-- partials contentivo de los scripts datatables --}}
@include('profesors.datatables.default')
