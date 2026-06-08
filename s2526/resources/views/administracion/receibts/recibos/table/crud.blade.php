@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['representant_id']="d-none d-sm-table-cell";
    $class['cashs']="d-none d-sm-table-cell";
    $class['quota']="d-none d-sm-table-cell";
    $class['action']="d-none d-sm-table-cell";
@endphp

{{-- 'coll_political_id','representant_id','date','ammount','exchange_ammount','status','description','observation' --}}

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['representant_id'] ?? ''}}">{{$list_comment['representant_id'] ?? ''}}</th>
            <th class="{{ $class['cashs'] ?? ''}}">Seriales</th>
            <th class="{{ $class['quota'] ?? ''}}">Cuotas</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($recibos as $recibo)

    @php
        $representant = $recibo->representant;
        $cash_serials = $recibo->cash_serials;
        $pago_quotas = $recibo->pago_quotas;
    @endphp

        <tr data-id="{{$recibo->id}}">
            <td class="{{ $class['iteration'] ?? ''}}">N</td>
            <td class="{{ $class['representant_id'] ?? ''}}">{{ ($representant) ? $representant->name : null}}</td>
            <td class="{{ $class['cashs'] ?? ''}}">{{ $cash_serials }}</td>
            <td class="{{ $class['quota'] ?? ''}}">{{ $pago_quotas }}</td>

            <td class="{{ $class_action ?? '' }}">

                <div class="btn-group btn-group-sm">

                    <a title="Generar PDF" class="btn btn-dark btn-sm"  href="{{route('administracion.receibts.recibo.pdf',$recibo->id)}}" role="button" target="_blank">
                        <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x"></i>
                    </a>
                    
                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{{-- {!! Form::open(['route' => ['administracion.collections.recibos.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection --}}

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
