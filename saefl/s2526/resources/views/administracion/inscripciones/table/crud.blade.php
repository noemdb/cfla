@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-sm-table-cell";
    $class_planpago="d-none d-sm-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Nombre</th>
                <th class="{{ $class_estudiant }}">Apellido</th>
                <th class="{{ $class_ci }}">Cédula</th>
                <th class="{{ $class_planpago }}">F.Nacimiento</th>
                <th class="{{ $class_planpago }}">P.Estudio</th>
                <th class="{{ $class_planpago }}">Grado</th>
                <th class="{{ $class_planpago }}">Sección</th>
                <th class="{{ $class_planpago }}">Tipo</th>
                <th class="{{ $class_planpago }}">Escolaridad</th>
                <th class="{{ $class_planpago }}">Grupo Estable</th>
                <th class="{{ $class_planpago }}">Fecha</th>
                <th class="{{ $class_planpago }}">Ins.Admon</th>
                <th class="{{ $class_planpago }}">GS</th>
                <th class="{{ $class_planpago }}">Correo GS</th>
                <th class="{{ $class_planpago }}">Representante</th>
                <th class="{{ $class_planpago }}">Correo Representante</th>
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($inscripcions as $inscripcion)

                @php 
                    $estudiant = $inscripcion->estudiant;
                    $representant = $estudiant->representant;
                    $grado = $estudiant->grado; 
                    $pestudio = $estudiant->pestudio; 
                    $date_birth = ($estudiant->date_birth) ? $estudiant->date_birth : $estudiant->date_enrollment;
                @endphp
            

                <tr data-id="{{$inscripcion->id ?? ''}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td id="td-estudiant-{{ $estudiant->id }}" class="{{ $class_estudiant  ?? ''}}">
                        {{$estudiant->name ?? ''}}
                    </td>
                    <td id="td-estudiant-{{ $estudiant->id }}" class="{{ $class_estudiant  ?? ''}}">
                        {{$estudiant->lastname ?? ''}}
                    </td>

                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_ci ?? '' }}">

                        <a class="btn-link" href="{{ route('administracion.estudiants.index',['search'=>$estudiant->ci_estudiant]) }}">
                            {{-- {{ ($grado->id > 1) ? $estudiant->ci_estudiant : '-NO-' }} --}}
                            {{ ($estudiant->ci_estudiant) ? $estudiant->ci_estudiant : '-NO-' }}
                        </a>
                    </td>
                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
                        {{ ($date_birth) ? f_date($date_birth) : ''}}
                    </td>

                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
                        {{ ($pestudio) ? $pestudio->name: null }}
                    </td>

                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
                        {{ $estudiant->grado->name ?? ''}}
                    </td>

                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
                        {{ $estudiant->seccion->name ?? ''}}
                    </td>

                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
                        {{ $inscripcion->tinscripcion->name ?? ''}}
                    </td>

                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
                        {{ $inscripcion->escolaridad->name ?? ''}}
                    </td>

                    <td class="{{ $class_planpago ?? '' }}">
                        <span id="grupo_estable_{{ $inscripcion->id ?? ''}}">
                            {{ $inscripcion->grupo_estable->name ?? ''}}
                        </span>
                    </td>
                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
                        {{ $inscripcion->created_at->format('Y-m-d') ?? ''}}
                    </td>

                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
                        @php $administrativa = $estudiant->administrativa; @endphp
                        {{ (empty($administrativa)) ? '|NO|':'|SI|'}}
                    </td>
                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
                        {{ (empty($estudiant->gsemail)) ? '-NO-':'-SI-'}}
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
                            <a title="Editar Inscripción" class="btn btn-warning btn-sm" href="{{ route('administracion.inscripciones.edit',['id'=>$inscripcion->id]) }}" role="button">
                                <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                            </a>
                            <button title="Asignar Grupo Estable"  type="button" class="btn-ge btn btn-info btn-sm ">
                                <i class="{{ $icon_menus['grupo_estables'] }} fa-1x text-light "></i>
                            </button>
                            <a title="Eliminar inscripción"  type="button" class="btn-destroy btn btn-danger btn-sm {{ ($inscripcion->status_delete) ? null:'disabled' }}" href="#" role="button">
                                <i class="{{ $icon_menus['eliminar'] }} fa-1x"></i>
                            </a>
                        </div>

                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
    {!! Form::open(['route' => ['administracion.inscripciones.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
    {!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
    {!! Form::close() !!}
    @section('scripts')  @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

    <div id="container_modal"></div>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.exportBootstrap')

<div id="container_modal"></div>

@section('scripts')
    @parent
    <script>
        $('.btn-ge').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');  //console.log(id);
            var modal = '#modal_update_'+id;  //console.log(modal);
            var container = '#container_modal';  //console.log(container);
            var ajaxurl = '{{route("administracion.ajax.fill.modal.inscripcions.grupo_estable.update", "_id_")}}';
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
