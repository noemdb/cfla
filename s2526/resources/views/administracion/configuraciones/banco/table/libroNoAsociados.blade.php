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
            <th class="{{ $c_freg }}">Fecha Pago</th>
            <th class="{{ $c_freoper }}">Fecha Bco.</th>
            <th class="{{ $c_tipo }}">Tipo</th>
            <th class="{{ $c_numero }}">Referencia</th>
            <th class="{{ $c_emisor }}">Emisor de pago</th>
            <th class="{{ $c_numero }}">Monto(Bs.)</th>
            <th class="{{ $c_numero }}">M.Cambiairo($)</th>
            @if ($status_late_payment == "true")
                <th class="{{ $c_numero }}" title="Monto Extemporaneo Cambiario">ME.Cambiario(Bs)</th>
                <th class="{{ $c_numero }}" title="Fecha de Registro">F.Registro</th>
            @endif
        </tr>
    </thead>

    <tbody id="tdatos">
    @php ($n=1)
    @foreach($ingresos as $ingreso)

        @php ($representant = $ingreso->representant)

        <tr data-id="{{$ingreso->id}}">
            <td id="td-count" class="{{ $class_N }}"> {{$n++}}</td>

            <td class="{{ $c_freg  ?? ''}}">

                    {{$ingreso->date_payment->format('d-m-Y')}}

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
            <td class="{{ $c_emisor ?? '' }}">
                {{ ($representant) ? $representant->name : null }}
            </td>
            <td class="{{ $c_numero ?? '' }}">

                    {{-- {{f_float($ingreso->ingreso_ammount_total)}} --}}
                    {{number_format($ingreso->ingreso_ammount_total, 2, '.', '')}}

            </td>
            <td class="{{ $c_numero ?? '' }}">

                    {{-- {{f_float($ingreso->exchange_ammount)}} --}}
                    {{number_format($ingreso->exchange_ammount, 2, '.', '')}}

            </td>
            @if ($status_late_payment == "true")
                <td class="{{ $c_numero ?? '' }}"> {{ f_float($ingreso->exchange_ammount_late_payment)}} </td>
                <td class="{{ $c_numero ?? '' }}"> {{$ingreso->created_at->format('d-m-Y')}} </td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>

{{-- </div> --}}

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('administracion.datatables.default') --}}
@include('administracion.datatables.exportBootstrap')
