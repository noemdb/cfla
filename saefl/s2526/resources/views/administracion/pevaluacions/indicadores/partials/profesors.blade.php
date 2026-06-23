<span class="small text-muted pl-2">POR PROFESOR</span>

<table class="table table-striped table-sm" id="table-data-profesors-{{ $lapso->id ?? '' }}">
    <thead class="thead-inverse">
        <tr>
            <th>Profesor</th>
            <th class="w-25 text-center" title="Porcentaje de notas cargadas">&#65285;</th>
        </tr>
    </thead>
    <tbody id="tdatos">
        @foreach ($profesors as $profesor)
            @php $pevaluacions = $profesor->pevaluacions; @endphp

            @if ($pevaluacions->IsNotEmpty())
                {{-- @php $goal = $profesor->goal_notas_load($lapso->id); @endphp
                @php $real = $profesor->real_notas_load($lapso->id); @endphp
                @php $total = (!empty($goal)) ? round((100*$real/$goal),2):0; @endphp --}}

                @php
                    $goal = $profesor->goal_notas_load($lapso->id);
                    $real = $profesor->real_notas_load($lapso->id);
                    $indice = !empty($goal) ? $real / $goal : 0;
                    $porcentaje = round(100 * $indice, 1);
                    $porcentaje = $porcentaje > 100 ? 100 : $porcentaje; /*fixNMDB*/
                    //CCORTEZ
                    if ($porcentaje > 99.85) {
                        $porcentaje = 100.0;
                    }
                @endphp
                <tr data-id="{{ $profesor->id }}">
                    <td class="p-0" title="{{ $profesor->fullname ?? '' }}">
                        @component('administracion.elements.progress.bars_xs')
                            @slot('title', $profesor->sm_name)
                            @slot('actual_ammount', $real)
                            @slot('goal_ammount', $goal)
                        @endcomponent
                    </td>
                    <td class="align-middle text-center">
                        <span class="badge badge-light" style=" font-size:0.8rem">
                            {{ $porcentaje ?? '' }} %
                        </span>
                    </td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
{{-- {{ $profesors->links() }} --}}

@section('scripts')
    @parent
    <script>
        // "pagingType": simple,simple_numbers,numbers,full,full_numbers,first_last_numbers
        $(document).ready(function() {
            $('#table-data-profesors-{{ $lapso->id ?? '' }}').DataTable({
                "pagingType": "simple",
                "pageLength": 8,
                "bLengthChange": false,
                "bPaginate": true,
                "searching": false,
                "bInfo": false,
                "responsive": false,
                "columnDefs": [{
                    "targets": 'nosort',
                    "orderable": false
                }],
                "language": {
                    "url": "/vendor/datatables/lang/spanish.json"
                }
            });
            $.fn.DataTable.ext.pager.numbers_length = 5;
        });
    </script>
@endsection
