@php
    $class_N="d-none d-sm-table-cell";
    $class_representant="";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

<table width="100%" class="table table-striped table-hover table-sm p-1 small" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_representant }}">CI</th>
            <th class="{{ $class_representant }}">Representante</th>
            <th class="{{ $class_deuda }}" >Monto pagado ($)</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @forelse($paids as $paid)

        @php
            $representant = $paid['representant'];
            $estudiants = $paid['estudiants'];
            $monto_exchange = $estudiants->sum('monto_exchange');

            $total_abono_exchange = $representant['total_credito_exchange'] ;
            $total_credito_exchange = $representant['total_abono_exchange'] ;
            $saldo_a_favor_exchange = $total_credito_exchange + $total_abono_exchange;
            $deuda_total_exchange = $monto_exchange - $saldo_a_favor_exchange;
        @endphp

            <tr data-representant_id="{{$representant->id ?? ''}}" class="{{ (0) ? 'table-danger' : null}}">

                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td id="td-count" class="{{ $class_representant ?? '' }}">
                    {{ $representant->ci_representant ?? ''}}
                </td>

                <td id="td-representant-{{ $representant->id }}" class="{{ $class_representant  ?? ''}} align-top">
                    <a class="btn-link text-dark" href="{{ route('administracion.representants.index',['search'=>$representant->ci_representant]) }}">
                        <b>{{$representant->name}}</b>
                    </a>
                </td>

                <td class="{{ $class_fecha }}">
                    {{ round($monto_exchange,2) }}
                </td>

            </tr>

            @empty

            <tr> <td colspan="5" class=" small text-left pl-4 font-weight-bold">NO HAY DATOS</td> </tr>

            @endforelse

    </tbody>
</table>

{{-- @include('administracion.datatables.default') --}}
@include('administracion.datatables.exportBootstrap')
