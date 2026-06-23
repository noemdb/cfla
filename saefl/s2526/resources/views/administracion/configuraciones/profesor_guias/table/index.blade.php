@php
    $class_N="d-none d-sm-table-cell";
    $class_asignatura="";
    $class_ci="";
    $class_grado="";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_asignatura }}">Asignatura</th>
                <th class="{{ $class_ci }}">1er Lapso</th>
                <th class="{{ $class_ci }}">2do Lapso</th>
                <th class="{{ $class_ci }}">3er Lapso</th>
                <th class="{{ $class_action }}">Asignar</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($pensums as $pensum)

            @php $asignatura = $pensum->asignatura; @endphp

            <tr data-asignatura="{{$asignatura->id}}" data-asignatura="{{$asignatura->id ?? ''}}" class="table-{{(empty($asignatura->administrativa->id)) ? 'default':'success'}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td id="td-users-username-{{ $asignatura->id }}" class="{{ $class_user  ?? ''}}">
                    {{$asignatura->fullname}}
                </td>
                <td id="td-asignatura-lapso_1-{{ $asignatura->id }}" class="{{ $class_email ?? '' }}">
                    @include('administracion.profesor_guias.partials.btn_info',['lapso_id'=>1])
                </td>
                <td id="td-asignatura-lapso_2-{{ $asignatura->id }}" class="{{ $class_email ?? '' }}">
                    @include('administracion.profesor_guias.partials.btn_info',['lapso_id'=>2])
                </td>
                <td id="td-asignatura-lapso_3-{{ $asignatura->id }}" class="{{ $class_email ?? '' }}">
                    @include('administracion.profesor_guias.partials.btn_info',['lapso_id'=>3])
                </td>
                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $asignatura->id }}">
                    {{-- <div class="btn-group" role="group" aria-label="Basic example"> --}}
                        <a title="Asignar Plan de Evaluación 1er Lapso" class="btn btn-primary btn-sm" href="{{ route('administracion.profesor_guias.create',['grado_id'=>$grado->id,'pensum_id'=>$pensum->id,'lapso_id'=>1]) }}" role="button">
                            1
                        </a>
                        <a title="Asignar Plan de Evaluación 2do Lapso" class="btn btn-success btn-sm" href="{{ route('administracion.profesor_guias.create',['grado_id'=>$grado->id,'pensum_id'=>$pensum->id,'lapso_id'=>2]) }}" role="button">
                            2
                            {{-- <i class="fas fa-list"></i> --}}
                        </a>
                        <a title="Asignar Plan de Evaluación 3er Lapso" class="btn btn-danger btn-sm" href="{{ route('administracion.profesor_guias.create',['grado_id'=>$grado->id,'pensum_id'=>$pensum->id,'lapso_id'=>3]) }}" role="button">
                            3
                        </a>
                    {{-- </div> --}}
                </td>
            </tr>
            @endforeach

        </tbody>

    </table>

{{-- </div> --}}

{{-- @section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
    <script src="{{ asset("js/models/datatable/default.js") }}"></script>
@endsection --}}
