@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm p-1 small" id="table-data-default">
        <caption style="caption-side: top">Listado de saldos por representante - Total: {{ count($deuda_ex_arr) ?? ''}} - Montos en Divisas ($)</caption>
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant ?? '' }}">Representante</th>
                <th class="{{ $class_estudiant ?? '' }}">CI</th>
                <th class="{{ $class_estudiant ?? '' }}">Estudiante</th>
                <th class="{{ $class_grado ?? '' }}">Grado/Sección</th>
                {{-- <th class="{{ $class_deuda ?? '' }}" title="Deuda Cambiaria Bs.">DC (Bs.)</th> --}}
                <th class="{{ $class_deuda ?? '' }}" title="Deuda Cambiaria Divisas">D.C.</th>
                {{-- <th class="{{ $class_deuda ?? '' }}" title="Créditos a Favor del Estudiante">CAF EST.</th> --}}
                {{-- <th class="{{ $class_deuda ?? '' }}" title="Abonos en tránsito del Estudiante">ABN EST.</th> --}}
                {{-- <th class="{{ $class_deuda ?? '' }}" title="Saldo a Favor del Estudiante" >SAF EST.</th> --}}
                <th class="{{ $class_deuda ?? '' }}" >N.Hermanos</th>
                <th class="{{ $class_deuda ?? '' }}" >C.Morosidad</th>
            </tr>
        </thead>

        <tbody id="tdatos">

        @foreach($estudiants as $estudiant)

            @if (array_key_exists($estudiant->id, $deuda_bs_arr))

                @php
                    $representant = $estudiant->representant;
                    $brothers_count = ($estudiant->brothers_formaly) ? $estudiant->brothers_formaly->count() : 1;

                    $total_credito_exchange = ($brothers_count > 0) ? $representant->total_credito_exchange / $brothers_count : $representant->total_credito_exchange;
                    $total_abono_exchange = ($brothers_count > 0) ? $representant->total_abono_exchange / $brothers_count : $representant->total_abono_exchange;
                    //$total_abono_exchange = $representant->total_abono_exchange / $brothers_count;
                    $saldo_a_favor_exchange = $total_credito_exchange + $total_abono_exchange;

                    $ammount_expire_bill = $deuda_bs_arr[$estudiant->id];
                    $exchange_ammount_expire_bill = $deuda_ex_arr[$estudiant->id];
                    $deuda_total_exchange = $exchange_ammount_expire_bill - $saldo_a_favor_exchange;
                    $recargosMorosidad = $estudiant->cantidad_recargos_morosidad;
                @endphp

                <tr data-estudiant_id="{{$estudiant->id ?? ''}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>
                    <td id="td-count" class="{{ $class_estudiant ?? '' }}">
                        {{ $representant->ci_representant ?? ''}} {{ $representant->name ?? ''}}
                    </td>

                    <td id="td-count" class="{{ $class_estudiant ?? '' }}">
                        {{ $estudiant->ci_estudiant ?? ''}}
                    </td>

                    <td id="td-estudiant-{{ $estudiant->id }}" class="{{ $class_estudiant  ?? ''}} align-top">
                        <a class="btn-link text-dark" href="{{ route('administracion.estudiants.index',['search'=>$estudiant->ci_estudiant]) }}">
                            <b>{{$estudiant->fullname}}</b>
                        </a>
                    </td>

                    <td id="td-users-is_active-{{ $estudiant->id }}" class="{{ $class_grado ?? '' }}">
                        <span class="{{$estudiant->grado->class_text_color ?? 'default'}}">
                            {{$estudiant->grado->name ?? ''}} {{$estudiant->seccion->name ?? ''}}
                        </span>
                    </td>

                    {{-- <td id="td-ammount_expire_bill-{{ $estudiant->id }}" class="{{ $class_deuda ?? '' }} align-top">
                        <b class=" text-danger">{{ f_float($ammount_expire_bill) ?? ''}}</b>
                    </td> --}}
                    <td id="td-ammount_expire_bill-{{ $estudiant->id }}" class="{{ $class_deuda ?? '' }} align-top">
                        <b class=" text-dark">
                            {{ ($exchange_ammount_expire_bill) ? round($exchange_ammount_expire_bill,2) : null }}
                        </b>
                    </td>
                    {{-- <td class="{{ $class_deuda ?? '' }} align-top">
                        {{ ($total_credito_exchange) ? round($total_credito_exchange,2) : null }}
                    </td>
                    <td class="{{ $class_deuda ?? '' }} align-top">
                        {{ ($total_abono_exchange) ? round($total_abono_exchange,2) : null }}
                    </td>
                    <td class="{{ $class_deuda ?? '' }} align-top">
                        {{ ($saldo_a_favor_exchange) ? round($saldo_a_favor_exchange,2) : null }}
                    </td>
                    --}}

                    <td id="td-count" class="{{ $class_N }}">
                        {{$brothers_count}}
                    </td>

                    <td id="td-count" class="{{ $class_N }}">
                        {{$recargosMorosidad}}
                    </td>

                </tr>

            @endif

        @endforeach

        </tbody>
    </table>

{{-- </div> --}}

@include('administracion.datatables.exportBootstrap')

