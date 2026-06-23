<span class="small text-muted pl-2">POR SECCIÓN</span>

<table class="table table-striped table-sm" id="table-data-seccions-{{ $lapso->id ?? ''}}">
    <thead class="thead-inverse">
        <tr>
            <th class="w-75">Sección</th>
            <th class="w-25 text-center" title="Porcentaje de notas cargadas">&#65285;</th>
        </tr>
        </thead>
        <tbody id="tdatos">
            @foreach ($seccions as $seccion)
                @php $goal = $seccion->getCountEvaluacions($lapso->id); @endphp
                @php $notas = $seccion->getCountNotas($lapso->id); @endphp
                @php $total = (!empty($goal)) ? round((100*$notas/$goal),2):0; @endphp
                @php $title = $seccion->grado->code_sm. ' '. $seccion->name; @endphp
                <tr data-id="{{$seccion->id}}">
                    <td class="p-0">
                        @component('administracion.elements.progress.bars_xs')
                            @slot('title', $seccion->fullnamesm)
                            @slot('actual_ammount',$notas)
                            @slot('goal_ammount',$goal)
                        @endcomponent
                    </td>
                    <td class="align-middle text-center">
                        <span class="badge badge-light" style=" font-size:0.8rem">
                            {{$total ?? ''}} %
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
</table>
{{-- {{ $seccions->links() }} --}}

@section('scripts')
    @parent
    <script>
        // "pagingType": simple,simple_numbers,numbers,full,full_numbers,first_last_numbers
        $(document).ready(function() {
            $('#table-data-seccions-{{ $lapso->id ?? ''}}').DataTable( {
                "pagingType": "simple",
                "pageLength": 8,
                "bLengthChange": false,
                "bPaginate": true,
                "searching": false,
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
        } );
    </script>
@endsection
