<div class="card h-100">
    <div class="card-header p-0 pl-1 alert-secondary">
        <i class="{{$icon_menus['pago']}}" aria-hidden="true"></i>
        <b>Estudiantes Deudores</b> por Grado
        <span class=" text-muted small">Error 3%</span>
    </div>
    <div class="card-body p-1">
        <table class="table table-striped table-sm" id="table-data-bill_grados">
            <thead class="thead-inverse">
                <tr>
                    <th class="w-75">Conceptos</th>
                    <th class="w-25 text-center" title="Porcentaje de Ingresos">&#65285;</th>
                </tr>
            </thead>
            <tbody id="tdatos">
                @foreach ($cuentaxpagars as $cuentaxpagar)
                    @php
                        $grado = $cuentaxpagar->deudores_g_by_grado;
                        $goal = $grado->count_estudiants->count();
                        $real = $grado->count;
                        $total = (!empty($goal)) ? round((100*$real/$goal),2):0;
                    @endphp
                    <tr data-id="{{$grado->id}}">
                        <td class="p-0">
                            @php $title = $cuentaxpagar->name . ' - ' . $grado->name @endphp
                            @component('administracion.elements.progress.bars_xs_inv')
                                @slot('title', $title)
                                @slot('actual_ammount',$real)
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
    </div>
</div>

@section('scripts')
    @parent
    <script>
        // "pagingType": simple,simple_numbers,numbers,full,full_numbers,first_last_numbers
        $(document).ready(function() {
            $('#table-data-bill_grados').DataTable( {
                "pagingType": "simple",
                "pageLength": 4,
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
