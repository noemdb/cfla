@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="nosort";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="data-tables-custom">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_profesor }}">Estudiante</th>
            {{-- <th class="{{ $class_profesor }}">Grado</th> --}}

            @foreach ($pensums as $pensum)
                @php $asignatura = $pensum->asignatura; @endphp
                <th class="{{ $class_asignatura ?? '' }} text-center">{{$asignatura->code_sm ?? ''}}</th>
            @endforeach

            <th class="{{ $class_action }} text-center">&nbsp;</th>

        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach($estudiants as $estudiant)
            @php $historico_nota = $estudiant->historico_nota; @endphp
            <tr data-id="{{$estudiant->id}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_profesor  ?? ''}}">
                    {{ $estudiant->fullname ?? ''}} <br>
                    <small class="text-muted">{{ $estudiant->ci_estudiant ?? ''}}</small>
                    {{-- <small class="text-muted">{{ $estudiant->full_inscripcion ?? ''}}</small> --}}
                </td>
                {{-- <td class="{{ $class_profesor  ?? ''}}">
                    {{ $estudiant->full_inscripcion ?? ''}}
                </td> --}}
                @foreach ($pensums as $pensum)

                    @php $asignatura = $pensum->asignatura; @endphp
                    @php $hnota = (isset($historico_nota)) ? $historico_nota->getHNota($pensum->id) : null; @endphp
                    @php
                        $nota_valor = ( $estudiant->getNotaRevisionStatus($pensum->id) ) ? $estudiant->getNotaFinalRevision($pensum->id,0) : $estudiant->getNotaFinal($pensum->id,0) ;
                    @endphp

                    @if ($asignatura->enable_academic_index == "true" && is_numeric($nota_valor))
                        @php $hnota_valor = ($hnota) ? $hnota->valor : null ; @endphp
                    @else
                        @php $hnota_valor = ($hnota) ? $hnota->literal : null ; @endphp
                    @endif
                    <td class="{{ $class_asignatura ?? '' }} {{ ($hnota_valor<>$nota_valor) ? 'alert-danger':null }} ">
                        <div class="btn-group" role="group" aria-label="Basic example">
                        <span title="Histórico de Notas" class="badge badge-primary p-2 ">{!! $hnota_valor ?? '&nbsp;&nbsp;' !!}</span>&nbsp;
                        <span title="Nota del período escolar actual" class="badge badge-info p-2 ">{!! $nota_valor ?? '&nbsp;&nbsp;' !!}</span>
                    </div>
                    </td>
                @endforeach
                <td class="{{ $class_action ?? '' }}  text-right">
                    <div class="btn-group btn-group-sm">
                        @php $route = ($historico_nota) ? route('administracion.historico_notas.certificacion.pdf',$historico_nota->id) : "#"; @endphp
                        @php $disabled = ($historico_nota) ? null:'disabled' @endphp
                        <a title="Imprimir" class="btn-print btn btn-dark btn-sm {{ $disabled ?? ''}}" data-url="{{$route ?? ''}}" href="{{$route ?? ''}}" target="_blank" role="button" >
                        <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x"></i>
                    </a>
                    </div>
                </td>

            </tr>

        @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.custom')

@section('scripts')
    @parent
    <script>
        // "pagingType": simple,simple_numbers,numbers,full,full_numbers,first_last_numbers
        $(document).ready(function() {
            $('#data-tables-custom').DataTable( {
                "pagingType": "full_numbers",
                "pageLength": 10,
                "bLengthChange": false,
                "bPaginate": false,
                "searching": true,
                "bInfo" : false,
                "responsive": false,
                "columnDefs": [ {
                    "targets": 'nosort',
                    "orderable": false
                } ],
                "language": {
                    "url": "/vendor/datatables/lang/spanish.json"
                }
            } );
            $.fn.DataTable.ext.pager.numbers_length = 5;
        } );
    </script>
@endsection
