@php
    $class_N="d-none d-sm-table-cell";
    $class_ci="";
    $class_grado="";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_grado }}">Grado</th>
                <th class="{{ $class_action }}">Asignar</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($grados as $grado)
            <tr data-id="{{$grado->id}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_user  ?? ''}}">
                    <span class="{{$grado->class_text_color}}">
                        {{$grado->name ?? 'fallo'}}
                    </span>
                </td>
                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $grado->id }}">
                    <a title="Asignar Profesor Guía" class="btn {{$class_btn_1er ?? ''}} btn-sm btn-success" href="{{ route('administracion.configuraciones.profesor_guias.create',['grado_id'=>$grado->id]) }}" role="button">
                        <i class="{{ $icon_menus['carga'] }} fa-1x "></i>
                    </a>
                </td>
            </tr>
            @endforeach

        </tbody>

    </table>


@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
    <script src="{{ asset("js/models/datatable/default.js") }}"></script>
@endsection
