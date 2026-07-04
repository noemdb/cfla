@php
    $class_N="d-none d-sm-table-cell";
    $class_plan="d-none d-md-table-cell";
    $class_descripcion="";
    $class_profesor="d-none d-md-table-cell";
    $class_asignatura="";
    $class_grado="d-none d-lg-table-cell";
    $class_lapso="d-none d-lg-table-cell";
    $class_date="d-none d-lg-table-cell text-nowrap";
    $class_action="nosort";
@endphp

{{-- Total: {{$boletins->count()}} --}}

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_plan }}">Estudiantes</th>
            <th class="{{ $class_plan }}">Evaluación</th>
            <th class="{{ $class_profesor }}">Profesor</th>
            <th class="{{ $class_asignatura }}">Asignatura</th>
            <th class="{{ $class_grado }}">Grado</th>
            <th class="{{ $class_lapso }}">Lapso</th>
            <th class="{{ $class_date }}">F.Registro</th>
            <th class="{{ $class_date }}">F.Actualización</th>
            <th class="{{ $class_lapso }}">Nota</th>
            <th class="{{ $class_action }}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($boletins as $boletin)

        @php
            $pensum = $boletin->pensum;
            $grado = $boletin->grado;
            $estudiant = ($boletin->estudiant) ? $boletin->estudiant : null;
            $evaluacion = ($boletin->evaluacion) ? $boletin->evaluacion : null;
            $pevaluacion = $boletin->pevaluacion;
            $seccion = $boletin->seccion;
            $profesor = $boletin->profesor;
            // property_exist($boletin,'estudiant')


            $pevaluacion_lapso = $pevaluacion->lapso;
            $fecha_corte = $pevaluacion_lapso->date_cutnote;
            $evaluacion_fecha = ($evaluacion) ? $evaluacion->fecha : null;
            $boletin_created_at = (isset($boletin->created_at)) ? $boletin->created_at->format('Y-m-d') : null;

            $status_load_nocorte = false ;
            $status_load_corte = false ;

            if ($evaluacion_fecha <= $fecha_corte) {
                if ($boletin_created_at <= $fecha_corte) {
                    if (isset($boletin->nota)) {
                        $status_load_corte = true ;
                    }
                }
            } else {
                $status_load_corte = true ;
                $status_load_nocorte = true ;
            }

        @endphp

        <tr data-id="{{$boletin->id}}" data-evaluacion="{{$boletin->id ?? ''}}" class=" {{ ($status_load_corte == false) ? 'table-warning' : null }} {{ ($status_load_nocorte == true) ? 'text-success font-weight-bold' : null }}">
            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
                {{-- {{$status_load_corte}} {{$boletin_created_at}} {{$fecha_corte}} --}}
            </td>
            <td class="{{ $class_plan  ?? ''}}">
                {{-- @admin <span class="small">{{ ($boletin) ? '['.$boletin->id.']' : null }}</span> @endadmin --}}
                {{ $estudiant->fullname ?? ''}}
                <br>
                {{ $estudiant->full_inscripcion ?? ''}}
            </td>
            @php $description = (empty($evaluacion->description)) ? null: $evaluacion->description; @endphp
            <td class="{{ $class_descripcion  ?? ''}}" title="{{$description ?? ''}}">
                @admin {{ ($evaluacion) ? '['.$evaluacion->id.']' : null }} @endadmin
                {{-- {{ Str::limit($description,20,'...') ?? ''}} --}}
                {{ $description ?? ''}}
            </td>
            {{-- @php $profesor = (empty($epevaluacion->profesor)) ? null:$epevaluacion->profesor->fullname; @endphp --}}
            <td class="{{ $class_descripcion  ?? ''}}" title="{{$profesor->fullname ?? ''}}">
                {{ ($profesor) ? Str::limit($profesor->fullname,20,'...') : null}}
            </td>
            <td class="{{ $class_asignatura ?? '' }}" title="{{$pevaluacion->pensum->asignatura->name ?? '' }}">
                {{ $pevaluacion->pensum->asignatura->code ?? ''}}
            </td>
            <td class="{{ $class_grado ?? '' }}">
                {{ $grado->name ?? ''}}
                {{ $seccion->name ?? ''}}
            </td>
            <td class="{{ $class_lapso ?? '' }}">
                {{ $pevaluacion->lapso->name ?? ''}}
            </td>
            <td class="{{ $class_date ?? '' }}">
                {{ ($boletin->created_at) ? $boletin->created_at->format('d-m-Y h:i A') : null }}
            </td>
            <td class="{{ $class_date ?? '' }}">
                {{ ($boletin->updated_at) ? $boletin->updated_at->format('d-m-Y h:i A') : null }}
            </td>
            <td class="{{ $class_lapso ?? '' }}">
                {{ $boletin->nota ?? ''}}
                {{ ($boletin->status_duplicate) ? '###':null }}
            </td>
            <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $boletin->id }}">
                <div class="btn-group btn-group-sm">
                    <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.boletins.edit',$boletin->id)}}" role="button">
                        <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                    </a>
                    {{-- @php $disabled = ($boletin->boletins->isNotEmpty()) ? ' disabled ': null ; @endphp --}}
                    <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm" href="#" id="btn-destroy_id_{{$boletin->id}}">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </td>

        </tr>
        @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('administracion.datatables.default') --}}
@include('administracion.datatables.exportBootstrap')


{!! Form::open(['route' => ['administracion.boletins.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts')  @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection
