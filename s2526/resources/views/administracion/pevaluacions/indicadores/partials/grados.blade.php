<span class="small text-muted pl-2">POR GRADO</span>

<table class="table table-striped table-sm" id="table-data-grados-{{ $lapso->id ?? ''}}">
    <thead class="thead-inverse">
        <tr>
            <th class="w-75">Grado</th>
            <th class="w-25 text-center" title="Porcentaje de notas cargadas">&#65285;</th>
        </tr>
        </thead>
        <tbody id="tdatos">
            @foreach ($grados as $grado)
                {{-- @php $evaluacions = $grado->getCountEvaluacions($lapso->id); @endphp --}}
                {{-- @php $notas = $grado->getCountNotas($lapso->id); @endphp --}}
                {{-- @php $total = (!empty($goal)) ? round((100*$notas/$goal),2):0; @endphp --}}

                @php
                    $goal = $grado->goal_notas_load($lapso->id);
                    $real = $grado->real_notas_load($lapso->id);
                    $indice = (!empty($goal)) ? $real/$goal : 0; $indice = ($indice>1) ? 1 : $indice ;
                    $porcentaje = round((100*$indice),1) ;
                @endphp

                <tr data-id="{{$grado->id}}">
                    {{-- {{$goal}} --}}
                    <td class="p-0">
                        @component('administracion.elements.progress.bars_xs')
                            @slot('title', $grado->name)
                            @slot('actual_ammount',$real)
                            @slot('goal_ammount',$goal)
                        @endcomponent
                    </td>
                    <td class="align-middle text-center">
                        <span class="badge badge-light" style=" font-size:0.8rem">
                            {{-- {{$goal ?? ''}} - --}}
                            {{-- {{$real ?? ''}} - --}}
                            {{$porcentaje ?? ''}} %
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
</table>

@section('scripts')
    @parent
    <script>
        // "pagingType": simple,simple_numbers,numbers,full,full_numbers,first_last_numbers
        $(document).ready(function() {
            $('#table-data-grados-{{ $lapso->id ?? ''}}').DataTable( {
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



{{--

@foreach ($grados as $grado)
    @php $goal = $grado->getCountEvaluacions($lapso->id); @endphp
    @php $notas = $grado->getCountNotas($lapso->id); @endphp
    @component('administracion.elements.progress.bars_sm')
        @slot('title', $grado->name)
        @slot('actual_ammount',$notas)
        @slot('goal_ammount',$goal)
    @endcomponent
@endforeach --}}
