@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_fecha="d-none";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1 small" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_estudiant }}">Estudiante</th>
            <th class="{{ $class_fecha }}">Fecha</th>
            <th class="{{ $class_deuda }}" title="Concepto de Cobro">Concepto Cobro</th>
            <th class="{{ $class_grado }}">Pagado (Bs.)</th>
            <th class="{{ $class_grado }}" title="Crédito Generado">C.Generado (Bs.)</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach($registropagos as $registropago)

            @if ($registropago->estudiant)

                @php
                    $estudiant = $registropago->estudiant;
                @endphp

                <tr data-id="{{$registropago->id}}" data-representant_id="{{$registropago->representant->id ?? ''}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td  class="{{ $class_estudiant  ?? ''}}">
                        {{-- /home/user/code/saefl/resources/views/administracion/estudiants/partials/href.blade.php --}}
                        @include('administracion.estudiants.partials.href')
                        {{-- <a class="btn-link" href="{{ route('administracion.estudiants.index',['search'=>$estudiant->ci_estudiant]) }}">
                            <span class="{{$estudiant->getInscripcion()->seccion->grado->class_text_color ?? 'default'}}">
                                <b>{{$estudiant->fullname}}</b>
                            </span>
                        </a> --}}
                        <br>
                        ({{ $estudiant->ci_estudiant ?? ''}})
                        ({{ $estudiant->representant->ci_representant ?? ''}})

                        @admin
                            <span class=" small font-weight-bold text-muted">
                                [H{{ (!empty($estudiant->representant->estudiants)) ? count($estudiant->representant->estudiants):''}}H]
                                [RP_ID: {{ $registropago->id ?? ''}}]
                                [RPC_ID: {{ $registropago->registro_pago_combinado->id ?? 'fallo'}}]
                            </span>
                        @endadmin
                    </td>

                    <td  class="{{ $class_ci ?? '' }}">
                        {{ f_date($registropago->created_at)}}
                    </td>

                    <td id="td-planpago-estudiant-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
                        {{$registropago->cuentaxpagar->name ?? ''}}
                    </td>

                    <td class="{{ $class_grado ?? '' }}">
                        @php $pagos_ammount = (!empty($registropago->pagos)) ? $registropago->pagos->sum('pagos_ammount'):'0'; @endphp
                        {{ f_float($pagos_ammount)}}
                        {{($pagos_ammount==0) ? '[P_EMPTY]':''}}
                    </td>

                    <td class="{{ $class_grado ?? '' }}">
                        @php $ammount_creditos = (!empty($registropago->creditoafavor)) ? $registropago->creditoafavor->credito_ammount:null; @endphp
                       {{ f_float($ammount_creditos) ?? null }}
                    </td>

                </tr>

            @endif

        @endforeach

    </tbody>
</table>
