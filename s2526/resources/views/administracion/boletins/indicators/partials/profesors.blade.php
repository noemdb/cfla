@php $table_id = 'table-data-profesors-'.$lapso->id.'-'.$pestudio->id; @endphp

<table class="table table-striped table-sm" id="{{ $table_id ?? ''}}" >
    <thead class="thead-inverse">
        <tr class="small">
            <th>Profesor</th>
            <th class="text-center">Planes de Evaluación</th>
            <th class="text-center">N. Notas Cargadas</th>
            <th class="text-center" title="Porcentaje de notas cargadas">IEE</th>
            <th class="text-center" title="Porcentaje de notas cargadas para el corte de notas">IEE-CN</th>
            <th class="text-center" title="Índice de Rendimiento en Evaluación (IRE)">IRE</th>
        </tr>
    </thead>
    <tbody id="tdatos" class="small">
        @php $ieePROM = $pestudio->getProfesorsIEEsPROM($lapso->id) @endphp
        @foreach ($profesors as $profesor)
            @php
                $pevaluacions = $profesor->getPevaluacionsPestudioLapso($pestudio->id,$lapso->id);
                $boletins = $profesor->getBoletinsPestudioLapso($pestudio->id,$lapso->id);
            @endphp

            @if ($pevaluacions->IsNotEmpty())

                <tr data-id="{{$profesor->id}}">

                    <td class="p-0" title="{{$profesor->fullname ?? ''}}">
                        <span class="font-weight-bold">
                            {{$profesor->fullname}}
                        </span>
                    </td>

                    <td class="align-middle text-center">
                        <span class="badge badge-light" style=" font-size:0.8rem">
                        {{$pevaluacions->count()}}
                        </span>
                    </td>

                    <td class="align-middle text-center">
                        <span class="badge badge-light" style=" font-size:0.8rem">
                        {{$boletins->count()}}
                        </span>
                    </td>

                    @php
                        $iee = $profesor->getProfesorIEE($lapso->id,$pestudio->id);
                        $indice = round((100*$iee),1) ;
                        $indice = ($indice>100) ? 100 : $indice ;
                    @endphp
                    <td class="align-middle text-center">
                        <span class="badge badge-light" style=" font-size:0.8rem">
                            {{$indice ?? ''}} %
                        </span>
                        @admin <span class="font-weight-light">{{round((100*$iee),2)}}</span> @endadmin
                    </td>

                    @php
                        $ieeCN = $profesor->getProfesorIEECN($lapso->id,$pestudio->id);
                        $indice = round((100*$ieeCN),1) ;
                        $indice = ($indice>100) ? 100 : $indice ;
                    @endphp
                    <td class="align-middle text-center">
                        <span class="badge badge-light" style=" font-size:0.8rem">
                            {{$indice ?? ''}} %
                        </span>
                    </td>

                    @php
                        $ire = $profesor->getProfesorIRE($pestudio->id,$lapso->id);
                        $indice = round((100*$ire),1) ;
                    @endphp
                    <td class="align-middle text-center">
                        <span class="badge badge-light" style=" font-size:0.8rem">
                            {{$indice ?? ''}} %
                        </span>
                    </td>
                </tr>
            @endif

        @endforeach
    </tbody>
</table>


@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('#{{ $table_id ?? ''}}').DataTable( {
                "pagingType": "full_numbers",
                "pageLength": 25,
                "bLengthChange": false,
                "bPaginate": false,
                "searching": false,
                "bInfo" : false,
                "responsive": false,
                "columnDefs": [ {
                    "targets": 'nosort',
                    "orderable": false
                } ],
                "language": {
                    "url": "/vendor/datatables/lang/spanish.json"
                },
            } );
            $.fn.DataTable.ext.pager.numbers_length = 5;
        } );
    </script>
@endsection


{{-- // "pagingType": simple,simple_numbers,numbers,full,full_numbers,first_last_numbers --}}
