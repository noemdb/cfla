@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_ci }}">Cédula</th>
                <th class="{{ $class_planpago }}">Grado</th>
                <th class="{{ $class_planpago }}">Sección</th>
                {{-- <th class="{{ $class_planpago }}">Tipo</th> --}}
                {{-- <th class="{{ $class_planpago }}">Escolaridad</th> --}}
                {{-- <th class="{{ $class_planpago }}">Grupo Estable</th> --}}
                <th class="{{ $class_planpago }} w-25">Estado</th>
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($preinscripcions as $preinscripcion)

                @php
                    $estudiant = $preinscripcion->estudiant;
                    $seccion = $preinscripcion->seccion;
                    $grado = $preinscripcion->grado;
                @endphp

                <tr data-id="{{$preinscripcion->id ?? ''}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td class="{{ $class_estudiant  ?? ''}}">
                        {{$estudiant->fullname ?? ''}}
                    </td>

                    <td class="{{ $class_ci ?? '' }}">
                        {{ $estudiant->ci_estudiant ?? ''}}
                    </td>
                    <td class="{{ $class_ci ?? '' }}">
                        {{ $grado->name ?? ''}}
                    </td>

                    <td class="{{ $class_ci ?? '' }}">
                        {{ $seccion->name ?? ''}}
                    </td>

                    {{-- <td class="{{ $class_ci ?? '' }}">
                        {{ $preinscripcion->tinscripcion->name ?? ''}}
                    </td> --}}

                    {{-- <td class="{{ $class_ci ?? '' }}">
                        {{ $preinscripcion->escolaridad->name ?? ''}}
                    </td> --}}

                    {{-- <td class="{{ $class_ci ?? '' }}">
                        <span id="grupo_estable_{{ $preinscripcion->id ?? ''}}">
                            {{ $preinscripcion->grupo_estable->name ?? ''}}
                        </span>
                    </td> --}}
                    <td class="{{ $class_ci ?? '' }} alert-light w-25" align="center">
                        <span class=" font-weight-bold text-primary">REGISTRADA</span>
                        @php
                            $count_requerimiento = 1;
                            $total_requerimiento = 10;
                        @endphp
                        @component('administracion.elements.progress.bars_xs')
                            {{-- @slot('title', 'Estado') --}}
                            @slot('actual_ammount',$count_requerimiento)
                            @slot('goal_ammount',$total_requerimiento)
                            @endcomponent
                        @include('representants.preinscripcions.show.info_estado')
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">

                        <div class="btn-group btn-group-sm">
                            <a title="Editar Preinscripción" class="btn btn-warning btn-sm" href="{{ route('representants.preinscripcions.edit',['search'=>$preinscripcion->id]) }}" role="button">
                                <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                            </a>
                            <a title="Eliminar inscripción"  type="button" class="btn-destroy btn btn-danger btn-sm {{ ($preinscripcion->status_delete) ? null:'disabled' }}" href="#" role="button">
                                <i class="{{ $icon_menus['eliminar'] }} fa-1x"></i>
                            </a>
                        </div>

                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
    {!! Form::open(['route' => ['representants.preinscripcions.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
    {!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
    {!! Form::close() !!}
    @section('scripts')  @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

    <div id="container_modal"></div>

{{-- partials contentivo de los scripts datatables --}}
@include('representants.datatables.default')

