@php $table_id = 'table-data-profesors-'.$lapso->id.'-'.$area_conocimiento->id; @endphp

<table class="table table-striped table-sm pt-4" id="{{ $table_id ?? '' }}">
    <thead class="thead-inverse">
        <tr class="small">
            <th>Profesor</th>
            <th class="text-center">Planes de Evaluación</th>
            <th class="text-center">N. Notas Cargadas</th>
            <th class="text-center table-info" title="Porcentaje de notas cargadas">IEE <div class="text-muted">% de notas
                    cargadas</div>
            </th>
            <th class="text-center table-info" title="Porcentaje de notas cargadas para el corte de notas">IEE-CN <div
                    class="text-muted">% corte de notas cargadas</div>
            </th>
            <th class="text-center table-info" title="Índice de Relativo del| Rendimiento en Evaluación (IRE)">IRE <div
                    class="text-muted">% Rendimiento en Evaluación</div>
            </th>
        </tr>
    </thead>
    <tbody id="tdatos" class="small">
        @foreach ($profesors as $profesor)
            @php
                $peducativo = $area_conocimiento->peducativo;
                $pestudio = $area_conocimiento->pestudio;
                $pevaluacions = $profesor->getPevaluacionsAreaConocimientoLapso($area_conocimiento->id, $lapso->id);
                $boletins = $profesor->getBoletinsAreaConocimientoLapso($area_conocimiento->id, $lapso->id);
                $user = $profesor->user;
            @endphp

            @if ($pevaluacions->IsNotEmpty())
                <tr data-id="{{ $profesor->id }}">

                    <td class="p-0" title="{{ $profesor->fullname ?? '' }}">
                        <span class="font-weight-bold">
                            {{ $profesor->fullname }}
                            <div class="text-muted font-weight-bold">{{ $user ? $user->username : 'N/A' }}</div>
                        </span>
                    </td>

                    <td class="align-middle text-center">
                        <span class="badge badge-light" style=" font-size:0.8rem">
                            {{ $pevaluacions->count() }}
                        </span>
                    </td>

                    <td class="align-middle text-center">
                        <span class="badge badge-light" style=" font-size:0.8rem">
                            {{ $boletins->count() }}
                        </span>
                    </td>

                    @php
                        $iee = $profesor->getProfesorIEEForPeducativo($lapso->id, $peducativo->id);
                        $indice = round(100 * $iee, 1);
                        //CCORTEZ
                        if ($indice > 99.85) {
                            $indice = 100.0;
                        }
                        $indice = $indice > 100 ? 100 : $indice;
                    @endphp
                    <td class="align-middle text-center table-info">
                        <span class="badge badge-light" style=" font-size:0.8rem">
                            {{ $indice ?? '' }} %
                        </span>
                    </td>

                    @php
                        $ieeCN = $profesor->getProfesorIEECNPeducativo($lapso->id, $peducativo->id);
                        $indice = round(100 * $ieeCN, 1);
                        $indice = $indice > 100 ? 100 : $indice;
                    @endphp
                    <td class="align-middle text-center table-info">
                        <span class="badge badge-light" style=" font-size:0.8rem">
                            {{ $indice ?? '' }} %
                        </span>
                    </td>

                    @php
                        $ire = $profesor->getProfesorIREPeducativo($peducativo->id, $lapso->id);
                        $indice = round(100 * $ire, 1);
                    @endphp
                    <td class="align-middle text-center table-info">
                        <span class="badge badge-light" style=" font-size:0.8rem">
                            {{ $indice ?? '' }} %
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
            $('#{{ $table_id ?? '' }}').DataTable({
                "pagingType": "full_numbers",
                "pageLength": 25,
                "bLengthChange": false,
                "bPaginate": false,
                "searching": false,
                "bInfo": false,
                "responsive": false,
                "columnDefs": [{
                    "targets": 'nosort',
                    "orderable": false
                }],
                "language": {
                    "url": "/vendor/datatables/lang/spanish.json"
                },
            });
            $.fn.DataTable.ext.pager.numbers_length = 5;
        });
    </script>
@endsection
