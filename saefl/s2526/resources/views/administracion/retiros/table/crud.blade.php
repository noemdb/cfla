@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="";
    $class_grado="";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_ci }}">Grado/Sección</th>
                <th class="{{ $class_ci }}">Plan de Pago</th>
                <th class="{{ $class_ci }}">Fecha</th>
                <th class="{{ $class_ci }}">Usuario</th>
                {{-- <th class="{{ $class_action }}">Acciones</th> --}}
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($retiros as $retiro)
            @php
                $estudiant = $retiro->estudiant;
            @endphp

            <tr data-estudiant="{{$estudiant->id}}" data-estudiant="{{$estudiant->id ?? ''}}" class="table-{{(empty($estudiant->administrativa->id)) ? 'default':'danger'}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td id="td-users-username-{{ $estudiant->id }}" class="{{ $class_user  ?? ''}}">
                    {{$estudiant->fullname}}
                    {{ $estudiant->ci_estudiant ?? ''}}
                </td>
                <td id="td-estudiant-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_ci ?? '' }}">
                    {{ $estudiant->getInscripcion()->seccion->grado->name ?? '' }}
                    {{ $estudiant->getInscripcion()->seccion->name ?? '' }}
                </td>
                <td id="td-estudiant-administrativa-{{ $estudiant->id }}" class="{{ $class_ci ?? '' }}">
                    {{ $estudiant->administrativa->planpago->name ?? '' }}
                </td>
                <td id="td-estudiant-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_ci ?? '' }}">
                    {{ f_date($retiro->created_at) ?? ''}}
                </td>
                <td id="td-estudiant-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_ci ?? '' }}">
                    {{ $retiro->user->username ?? '' }}
                </td>
            </tr>
            @endforeach

        </tbody>

    </table>

{{-- </div> --}}

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
