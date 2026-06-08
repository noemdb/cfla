@php ($class_N="d-none d-sm-table-cell")
@php ($c_freg="")
@php ($c_freoper="d-none d-lg-table-cell")
@php ($c_tipo="")
@php ($c_numero="")
@php ($c_emisor="d-none d-md-table-cell")

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $c_freg }}">Banco</th>
            <th class="{{ $c_freoper }}">Fecha oper.</th>
            <th class="{{ $c_tipo }}">Tipo</th>
            <th class="{{ $c_numero }}">Número</th>
            <th class="{{ $c_emisor }}">Emisor de pago</th>
            <th class="{{ $c_numero }}" style=" text-align:right">Monto (Bs.)</th>
            <th class="{{ $c_numero }}" style=" text-align:right">M.Cambiario ($.)</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @php ($n=1)
    @foreach($ingresos as $ingreso)

        <tr data-id="{{$ingreso->id}}">
            <td id="td-count" class="{{ $class_N }}">
                {{$n++}}
            </td>
            <td class="{{ $c_freg  ?? ''}}">
                {{ $ingreso->banco->name ?? ''}}
            </td>
            <td class="{{ $c_freoper ?? '' }}">
                    {{$ingreso->date_transaction->format('d-m-Y')}}
            </td>
            <td class="{{ $c_tipo ?? '' }}" title="{{$ingreso->metodo_pago->name ?? ''}}">
                {{$ingreso->metodo_pago->createAcronym() ?? ''}}
            </td>
            <td class="{{ $c_numero ?? '' }}">
                {{$ingreso->number_i_pay ?? ''}}
            </td>
            <td class="{{ $c_numero ?? '' }}">
                {{$ingreso->estudiant->representant->name ?? ''}}
            </td>
            <td class="{{ $c_numero ?? '' }}" style=" text-align:right">
                {{f_float($ingreso->ingreso_ammount)}}
            </td>
            <td class="{{ $c_numero ?? '' }}" style=" text-align:right">
                {{f_float($ingreso->exchange_ammount)}}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- </div> --}}

@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
    <script src="{{ asset("js/models/datatable/default.js") }}"></script>
@endsection
