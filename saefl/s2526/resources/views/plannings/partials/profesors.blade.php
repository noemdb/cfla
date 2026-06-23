@php $table_id = 'table-data-profesors-'.$lapso->id.'-'.$pestudio->id; @endphp

<table class="table table-striped table-sm pt-4" id="{{ $table_id ?? ''}}" >
    <thead class="thead-inverse">
        <tr class="small">
            <th>Profesor</th>
            <th class="text-left">N. Actividades</th>
            <th class="text-left">Planes de Evaluación</th>
            <th class="text-left">N. Notas Cargadas</th>
            <th class="text-left" title="Porcentaje de notas cargadas">IEE</th>
            <th class="text-left" title="Porcentaje de notas cargadas para el corte de notas">IEE-CN</th>
            <th class="text-left" title="Índice de Relativo del| Rendimiento en Evaluación (IRE)">IRE</th>
        </tr>
    </thead>
    <tbody id="tdatos" class="small">
        @php $ieePROM = $pestudio->getProfesorsIEEsPROM($lapso->id) @endphp
        @foreach ($profesors as $profesor)
            @php
                $pevaluacions = $profesor->getPevaluacionsPestudioLapso($pestudio->id,$lapso->id);
                $activities = $profesor->getActivitiesPestudioLapso($pestudio->id,$lapso->id);
                $boletins = $profesor->getBoletinsPestudioLapso($pestudio->id,$lapso->id);
            @endphp

            @if ($pevaluacions->IsNotEmpty())

                <tr data-id="{{$profesor->id}}">

                    <td class="p-0" title="{{$profesor->fullname ?? ''}}">
                        <span class="font-weight-bold">
                            {{$profesor->fullname}}
                        </span>
                    </td>

                    <td class="align-middle text-left pl-2">
                        @php 
                            $count = $activities->count() ?? null;
                            $aprove = $activities->where('status',true) ?? null;
                            $real = $aprove->count() ?? null;
                            $indice = ($count) ? 100 *  $real / $count: null;
                            $indice = round($indice,1);
                            @endphp
                        <span class="badge badge-light" style=" font-size:0.8rem">                            
                            {{$count}} <small>[{{$indice}} %]</small>
                        </span>
                    </td>

                    <td class="align-middle text-left pl-2">
                        <span class="badge badge-light" style=" font-size:0.8rem">
                        {{$pevaluacions->count()}}
                        </span>
                    </td>

                    <td class="align-middle text-left pl-2">
                        <span class="badge badge-light" style=" font-size:0.8rem">
                        {{$boletins->count()}}
                        </span>
                    </td>

                    @php
                        $iee = $profesor->getProfesorIEE($lapso->id,$pestudio->id);
                        $indice = round((100*$iee),1) ;
                        $indice = ($indice>100) ? 100 : $indice ;
                    @endphp
                    <td class="align-middle text-left pl-2">
                        <span class="badge badge-light" style=" font-size:0.8rem">
                            {{$indice ?? ''}} %
                        </span>
                    </td>

                    @php
                        $ieeCN = $profesor->getProfesorIEECN($lapso->id,$pestudio->id);
                        $indice = round((100*$ieeCN),1) ;
                        $indice = ($indice>100) ? 100 : $indice ;
                    @endphp
                    <td class="align-middle text-left pl-2">
                        <span class="badge badge-light" style=" font-size:0.8rem">
                            {{-- {{$ieeCN ?? ''}} --}}
                            {{$indice ?? ''}} %
                        </span>
                    </td>

                    @php
                        $ire = $profesor->getProfesorIRE($pestudio->id,$lapso->id);
                        $indice = round((100*$ire),1) ;
                    @endphp
                    <td class="align-middle text-left pl-2">
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

