@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_fecha="d-none text-nowrap";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_N }} text-center">Idents.</th>
            <th class="{{ $class_estudiant }}">Estudiante</th>
            <th class="{{ $class_deuda }}" title="Concepto de Cobro">Concepto Cobro</th>
            <th class="{{ $class_grado }}">Pagado $</th>
            {{-- <th class="{{ $class_grado }}" title="Crédito Generado">C.Generado $</th> --}}
            <th class="{{ $class_grado }}" title="Estado del Pago">Estado</th>
            <th class="{{ $class_grado }}" title="Estado de Anulable">Anulable</th>
            {{-- <th class="{{ $class_grado }}" title="Pago Combinado">P.C.</th> --}}
            <th class="{{ $class_fecha }}">F.Registro</th>
            <th class="{{ $class_fecha }}">Usuario</th>
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach($registropagos as $registropago)

            @if ($registropago->estudiant)

                @php
                    $estudiant = $registropago->estudiant;
                    $pago = $registropago->pago;
                    $registro_pago_combinado = ($registropago) ? $registropago->registro_pago_combinado : null;

                    // Determine status
                    $status = 'active';
                    $statusClass = 'status-active';
                    $statusText = 'Activo';
                    $statusIcon = 'fa-check-circle';

                    if ($registropago->cancelled_at && $registropago->approval_date) {
                        $status = 'cancelled';
                        $statusClass = 'status-cancelled';
                        $statusText = 'Anulado';
                        $statusIcon = 'fa-times-circle';
                    } elseif ($registropago->cancelled_at && !$registropago->approval_date) {
                        $status = 'pending_approval';
                        $statusClass = 'status-pending';
                        $statusText = 'Pendiente Aprobación';
                        $statusIcon = 'fa-clock';
                    } elseif ($registropago->status_prepayment === 'true') {
                        $statusClass = 'status-prepayment';
                        $statusText = 'Pago Adelantado';
                        $statusIcon = 'fa-exclamation-circle';
                    }
                @endphp

                <tr class="{{ ($registropago->status_unexpired) ? 'table-prepayment':'' }}"
                    data-id="{{$registropago->id}}"
                    data-representant_id="{{$registropago->representant->id ?? ''}}"
                    data-status="{{ $status }}"
                    title="{{ ($registropago->status_unexpired) ? 'Registro Pago Adelantado':'' }}">

                    <td class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td class="{{ $class_N }}">
                        @if ($registropago)
                            <div class="font-weight-bold text-nowrap">
                                <span class="badge badge-primary">P{{ $registropago->id }}</span>
                                <span class="badge badge-warning">C{{ ($registro_pago_combinado) ? $registro_pago_combinado->id : '' }}</span>
                                <span class="badge badge-info">F{{ ($registro_pago_combinado) ? $registro_pago_combinado->correlative : '' }}</span>
                            </div>
                        @endif
                    </td>

                    <td class="{{ $class_estudiant }}">
                        <a class="btn-link" href="{{ route('administracion.estudiants.index',['search'=>$estudiant->ci_estudiant]) }}">
                            <span class="{{$estudiant->getInscripcion()->seccion->grado->class_text_color ?? 'default'}}">
                                <b>{{$estudiant->fullname}}</b>
                            </span>
                        </a>
                        <br>
                        <small class="text-muted">
                            Est: {{ $estudiant->ci_estudiant ?? ''}} | Rep: {{ $estudiant->representant->ci_representant ?? ''}}
                        </small>

                        @admin
                            <span class="small font-weight-bold text-muted">
                                [H{{ (!empty($estudiant->representant->estudiants)) ? count($estudiant->representant->estudiants):''}}H]
                                [RP_ID: {{ $registropago->id ?? ''}}]
                                [RPC_ID: {{ $registropago->registro_pago_combinado->id ?? 'fallo'}}]
                            </span>
                        @endadmin
                    </td>

                    <td class="{{ $class_planpago }}">
                        {{$registropago->cuentaxpagar->name ?? ''}}
                    </td>

                    <td class="{{ $class_grado }}">
                        @php $exchange_ammount = (!empty($pago->exchange_ammount)) ? $pago->exchange_ammount:null; @endphp
                        <span class="font-weight-bold" title="{{$exchange_ammount ?? ''}}">
                            {{ ($exchange_ammount) ? '$'.number_format($exchange_ammount,2) : '$0.00' }}
                        </span>
                    </td>

                    <td class="{{ $class_grado }}">
                        <span class="status-badge {{ $statusClass }}">
                            <i class="fas {{ $statusIcon }} mr-1"></i>
                            {{ $statusText }}
                        </span>
                    </td>

                    <td class="{{ $class_grado }}">
                        {{ ($registropago->cancellable) ? '-SI-' : '-NO-' }}
                    </td>

                    <td class="{{ $class_ci }}">
                        {{ ($registropago->created_at) ? $registropago->created_at->format('d-m-Y') : null }}
                    </td>

                    <td class="{{ $class_ci }}">
                        {{ $registropago->user->username ?? '' }}
                    </td>

                    <td class="{{ $class_action }}" id="btn-action-{{ $estudiant->id }}">
                        <div class="btn-group btn-group-sm">

                            <button title="Mostrar detalles del registro de pago"
                                    class="btn-modal-details btn btn-info btn-sm"
                                    data-id="{{ $registropago->id }}">
                                <i class="fas fa-info"></i>
                            </button>

                            {{--
                            @if($registropago->cancellable && !$registropago->cancelled_at)
                                <button title="Anular pago"
                                        class="btn-cancel-payment btn btn-danger btn-sm"
                                        data-id="{{ $registropago->id }}">
                                    <i class="fa fa-times"></i>
                                </button>
                            @endif
                            --}}

                            {{-- @if($registropago->cancelled_at && !$registropago->approval_date) --}}
                                <button title="Aprobar anulación"
                                        class="btn-approve-cancel btn btn-success btn-sm"
                                        data-id="{{ $registropago->id }}">
                                    <i class="fa fa-check"></i>
                                </button>
                            {{-- @endif --}}

                            @admin
                            <a title="Destruir Registro de Pago" class="btn btn-danger btn-sm" href="{{ route('administracion.registropagos.forceDelete',['id'=>$registropago->id]) }}" role="button">
                                <i class="fa fa-window-close" aria-hidden="true"></i>
                            </a>
                            @endadmin

                        </div>
                    </td>
                </tr>

            @endif

        @endforeach

    </tbody>
</table>

<div id="container_modal"></div>

{{-- Include the payment management scripts --}}
@include('administracion.registropagos.scripts.payment-management')

{{-- Include datatables --}}
@include('administracion.datatables.exportBootstrap')
