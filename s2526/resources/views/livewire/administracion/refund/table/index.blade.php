@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['registro_pago_combinado_id']="d-none d-sm-table-cell";
    $class['credito_a_favor_id']= ($modeIndex) ? "d-none d-lg-table-cell" : "d-none";
    $class['method_pay_id']= ($modeIndex) ? "d-none d-md-table-cell" : "d-none";
    $class['banco_id']="d-none d-md-table-cell";
    $class['representant_id']= ($modeIndex) ? "d-none d-md-table-cell" : "d-none";
    $class['number_i_pay']= ($modeIndex) ? "d-none d-md-table-cell" : "d-none";
    $class['date_transaction']="d-none d-md-table-cell";
    // $class['ammount']="d-none d-md-table-cell";
    $class['ammount']= ($modeIndex) ? "d-none d-md-table-cell" : "d-none";
    $class['ammount_exchange']="d-none d-md-table-cell";
    // $class['observations']="d-none d-md-table-cell";
    $class['observations']= ($modeIndex) ? "d-none d-md-table-cell" : "d-none";
    $class['action']="d-none d-sm-table-cell";
    $table_id = 'table-data-default';
@endphp

{{-- `registro_pago_combinado_id`, `credito_a_favor_id`, `method_pay_id`, `banco_id`, `representant_id`, `number_i_pay`, `date_transaction`, `ammount`, `ammount_exchange`, `observations`, --}}

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id}}">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['registro_pago_combinado_id'] ?? ''}}">{{$list_comment['registro_pago_combinado_id'] ?? ''}}</th>
            <th class="{{ $class['credito_a_favor_id'] ?? ''}}">{{$list_comment['credito_a_favor_id'] ?? ''}}</th>
            <th class="{{ $class['method_pay_id'] ?? ''}}">{{$list_comment['method_pay_id'] ?? ''}}</th>
            <th class="{{ $class['banco_id'] ?? ''}}">{{$list_comment['banco_id'] ?? ''}}</th>
            <th class="{{ $class['representant_id'] ?? ''}}">{{$list_comment['representant_id'] ?? ''}}</th>
            <th class="{{ $class['number_i_pay'] ?? ''}}">{{$list_comment['number_i_pay'] ?? ''}}</th>
            <th class="{{ $class['date_transaction'] ?? ''}}">{{$list_comment['date_transaction'] ?? ''}}</th>
            <th class="{{ $class['ammount'] ?? ''}}">{{$list_comment['ammount'] ?? ''}}</th>
            <th class="{{ $class['ammount_exchange'] ?? ''}}">{{$list_comment['ammount_exchange'] ?? ''}}</th>
            <th class="{{ $class['observations'] ?? ''}}">{{$list_comment['observations'] ?? ''}}</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos-{{$table_id}}">

        @forelse($refunds as $refund)

            @php $modal_id = "refund-info-".$refund->id; @endphp

            <tr data-id="{{$refund->id}}" class="{{($refund->id == $refund_id) ? 'bg-secondary font-weight-bold text-light' : null}}">
                <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                <td class="{{ $class['registro_pago_combinado_id'] ?? ''}}">
                    <a href="#" class="btn btn-info btn-sm p-1 m-1" wire:click="show({{$refund->registro_pago_combinado_id}})">
                        {{$refund->registro_pago_combinado_id ?? ''}}
                    </a>
                    {{-- @include('livewire.administracion.refund.modals.info') --}}
                </td>
                <td class="{{ $class['credito_a_favor_id'] ?? ''}}">{{$refund->credito_a_favor_id ?? ''}}</td>
                <td class="{{ $class['method_pay_id'] ?? ''}}">{{$refund->metodo_pago->name ?? ''}}</td>
                <td class="{{ $class['banco_id'] ?? ''}}">{{$refund->banco->name ?? ''}}</td>
                <td class="{{ $class['representant_id'] ?? ''}}">{{$refund->representant->name ?? ''}}<br>{{$refund->representant->ci_representant ?? ''}}</td>
                <td class="{{ $class['number_i_pay'] ?? ''}}">{{$refund->number_i_pay ?? ''}}</td>
                <td class="{{ $class['date_transaction'] ?? ''}}">{{$refund->date_transaction->format('d-m-Y') ?? ''}}</td>
                <td class="{{ $class['ammount'] ?? ''}}">Bs {{f_float($refund->ammount) ?? ''}}</td>
                <td class="{{ $class['ammount_exchange'] ?? ''}}">$ {{f_float($refund->ammount_exchange) ?? ''}}</td>
                <td class="{{ $class['observations'] ?? ''}}">{{$refund->observations ?? ''}}</td>
                <td class="{{ $class['action'] ?? ''}}">
                <div class="btn-group" role="group" aria-label="">
                    <button type="button" class="btn btn-info btn-sm" wire:click="details({{$refund->id}})">
                        <i class="{{ $icon_menus['info'] ?? ''}} fa-1x"></i>
                    </button>
                </div>
                </td>
            </tr>
            
        @empty

            <tr>
                <td colspan="8" align="center">
                    <strong>No hay datos</strong>            
                </td>
            </tr>
            
        @endforelse

    </tbody>

</table>

@include('administracion.datatables.exportBootstrap')
