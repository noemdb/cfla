@php
    $class_N="d-none d-sm-table-cell";
    $class_asignatura="";
    $class_ci="";
    $class_grado="";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-sm table-hover table-sm p-1" id="table-data-default">
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

            @php
                $asignatura = $pensum->asignatura;
                // $flag_peva = ($pensum->pevaluacions->where('lapso_id',1)->count()) ? 'disabled' : ' ';
                $class_btn_1er = ($pensum->exist_seccion($pensum->id,1,$seccion_id)) ? 'btn-primary' : 'btn-outline-primary' ;
                $class_btn_2do = ($pensum->exist_seccion($pensum->id,2,$seccion_id)) ? 'btn-success' : 'btn-outline-success' ;
                $class_btn_3er = ($pensum->exist_seccion($pensum->id,3,$seccion_id)) ? 'btn-danger' : 'btn-outline-danger' ;

                // $class_btn_2do = (empty( $pensum->pevaluacions->where('lapso_id',2)->count() )) ? 'btn-outline-success' : 'btn-success' ;
                // $class_btn_3er = (empty( $pensum->pevaluacions->where('lapso_id',3)->count() )) ? 'btn-outline-danger' : 'btn-danger' ;
            @endphp

            <tr data-asignatura="{{$asignatura->id}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td id="td-users-username-{{ $asignatura->id }}" class="{{ $class_user  ?? ''}}">
                    {{$asignatura->fullname}}
                </td>
                <td id="td-asignatura-lapso_1-{{ $asignatura->id }}" class="{{ $class_email ?? '' }}">
                    @include('administracion.pevaluacions.partials.btn_info',['lapso_id'=>1])
                </td>
                <td id="td-asignatura-lapso_2-{{ $asignatura->id }}" class="{{ $class_email ?? '' }}">
                    @include('administracion.pevaluacions.partials.btn_info',['lapso_id'=>2])
                </td>
                <td id="td-asignatura-lapso_3-{{ $asignatura->id }}" class="{{ $class_email ?? '' }}">
                    @include('administracion.pevaluacions.partials.btn_info',['lapso_id'=>3])
                </td>
                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $asignatura->id }}">
                    {{-- <div class="btn-group" role="group" aria-label="Basic example"> --}}

                        <a title="Registrar Plan de Evaluación/Evaluaciones 1er Lapso" class="btn {{$class_btn_1er ?? ''}}" href="{{ route('administracion.pevaluacions.create',['grado_id'=>$grado->id,'seccion_id'=>$seccion_id,'pensum_id'=>$pensum->id,'lapso_id'=>1]) }}" role="button">1</a>
                        <a title="Registrar Plan de Evaluación/Evaluaciones 2do Lapso" class="btn {{$class_btn_2do ?? ''}}" href="{{ route('administracion.pevaluacions.create',['grado_id'=>$grado->id,'seccion_id'=>$seccion_id,'pensum_id'=>$pensum->id,'lapso_id'=>2]) }}" role="button">2</a>
                        <a title="Registrar Plan de Evaluación/Evaluaciones 3er Lapso" class="btn {{$class_btn_3er ?? ''}} " href="{{ route('administracion.pevaluacions.create',['grado_id'=>$grado->id,'seccion_id'=>$seccion_id,'pensum_id'=>$pensum->id,'lapso_id'=>3]) }}" role="button">3</a>
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
