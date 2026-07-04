@php ($class_N="d-none d-sm-table-cell")
@php ($c_freg="")
@php ($c_freoper="d-none d-lg-table-cell")
@php ($c_tipo="")
@php ($c_numero="")
@php ($c_emisor="d-none d-md-table-cell")

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default" cellspacing="0" cellpadding="0" style="font-size: 0.6rem !important">
    <thead>
        <tr style="font-size: 0.6rem !important">
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $c_freg }}">Fecha Pago</th>
            <th class="{{ $c_freoper }}">Fecha Bco.</th>
            <th class="{{ $c_tipo }}">Tipo</th>
            <th class="{{ $c_numero }}">Referencia</th>
            <th class="{{ $c_emisor }}" style="width: 10rem !important;overflow-wrap: break-word !important;white-space: normal !important;">Emisor de pago</th>
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

        <tr data-id="{{$ingreso->id}}">
            <td id="td-count" class="{{ $class_N }}">
                <span style="font-size: 0.6rem !important">
                    {{$n++}}
                </span>
            </td>

            <td class="{{ $c_freg  ?? ''}}">
                <span style="font-size: 0.6rem !important">
                    {{$ingreso->date_payment->format('d-m-Y')}}
                </span>
            </td>
            <td class="{{ $c_freoper ?? '' }}">
                <span style="font-size: 0.6rem !important">
                    {{$ingreso->date_transaction->format('d-m-Y')}}
                </span>
            </td>
            <td class="{{ $c_tipo ?? '' }}" title="{{$ingreso->metodo_pago->name ?? ''}}">
                {{$ingreso->metodo_pago->createAcronym() ?? ''}}
            </td>
            <td class="{{ $c_numero ?? '' }}">
                <span style="font-size: 0.6rem !important">
                    {{$ingreso->number_i_pay ?? ''}}
                </span>
            </td>
            <td class="{{ $c_emisor ?? '' }}" style="width: 10rem !important;overflow-wrap: break-word !important;white-space: normal !important;">
                {{$ingreso->estudiant->representant->name ?? ''}}
            </td>
            <td class="{{ $c_numero ?? '' }}">
                <span style="font-size: 0.6rem !important">
                    {{f_float($ingreso->ingreso_ammount_total)}}
                </span>
            </td>
            <td class="{{ $c_numero ?? '' }}">
                <span style="font-size: 0.6rem !important">
                    {{f_float($ingreso->exchange_ammount)}}
                </span>
            </td>
            @if ($status_late_payment == "true")
                <td class="{{ $c_numero ?? '' }}"> <span style="font-size: 0.6rem !important">{{ f_float($ingreso->exchange_ammount_late_payment)}} </span></td>
                <td class="{{ $c_numero ?? '' }}"> <span style="font-size: 0.6rem !important">{{$ingreso->created_at->format('d-m-Y')}} </span></td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>

{{-- </div> --}}

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('administracion.datatables.default') --}}
{{-- @include('administracion.datatables.exportBootstrap') --}}
