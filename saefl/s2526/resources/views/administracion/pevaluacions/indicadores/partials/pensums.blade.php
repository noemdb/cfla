<span class="small text-muted pl-2">POR ASIGNATURA</span>

<table class="table table-striped table-sm" id="table-data-pensums-{{ $lapso->id ?? ''}}">
    <thead class="thead-inverse">
        <tr>
            <th class="w-75">Asignatura</th>
            <th class="w-25 text-center" title="Porcentaje de notas cargadas">&#65285;</th>
        </tr>
        </thead>
        <tbody id="tdatos">
            @foreach ($pensums as $pensum)
                @php $goal = $pensum->getCountEvaluacions($lapso->id); @endphp
                @php $notas = $pensum->getCountNotas($lapso->id); @endphp
                @php $total = (!empty($goal)) ? round((100*$notas/$goal),2):0; @endphp
                @php $title = $pensum->grado->code_sm. ' '. $pensum->name; @endphp
                {{-- @php $title_class = $pensum->grado->class_text_color; @endphp --}}
                <tr data-id="{{$pensum->id}}">
                    <td class="p-0" title="{{$pensum->asignatura->name ?? ''}}">
                        @component('administracion.elements.progress.bars_xs')
                            @slot('title', $pensum->asignatura->code)
                            {{-- @slot('title_class', $title_class) --}}
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
{{-- {{ $pensums->links() }} --}}

@section('scripts')
    @parent
    <script>
        // "pagingType": simple,simple_numbers,numbers,full,full_numbers,first_last_numbers
        $(document).ready(function() {
            $('#table-data-pensums-{{ $lapso->id ?? ''}}').DataTable( {
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
