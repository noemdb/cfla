@php
    $class_N="d-none d-sm-table-cell";
    $class_representant="";
    $class_fecha="d-none d-md-table-cell text-nowrap";
    $class_banco="d-none d-md-table-cell";
    $class_monto="d-none d-lg-table-cell text-nowrap";
    $class_action="nosort text-center";
@endphp

     <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default"> {{-- table-hover  --}}

        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_N }}">ID</th>
                <th class="{{ $class_representant }}">Representante</th>
                <th class="{{ $class_fecha }}" title="Fecha de Pago">F.Pago</th>
                <th class="{{ $class_fecha }}" title="Fecha en Banco">F.Banco</th>
                <th class="{{ $class_banco }}">Banco</th>
                <th class="{{ $class_monto }}">Referencia</th>
                <th class="{{ $class_monto }}">Monto (Bs)</th>
                <th class="{{ $class_monto }}" title="Monto Cambiario">M.Cambiario ($)</th>
                @if ($status_late_payment == "true" || $status_late_payment == "on") <th class="{{ $class_monto }}" title="Monto Extemporaneo Cambiario">ME.Cambiario ($)</th> @endif
                <th class="{{ $class_monto }}">Destino</th>
                <th class="{{ $class_fecha }}" title="Fecha Registro">F. Registro</th>
                <th class="{{ $class_fecha }}" title="Usuario registrador">Usuario</th>
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

        @foreach($ingresos as $ingreso)

            @php $representant = $ingreso->representant; @endphp
            @php $exchange_rate = $ingreso->exchange_rate @endphp
            @php $user = $ingreso->user @endphp

            <tr data-id="{{$ingreso->id ?? ''}}" class="table-{{(!empty($ingreso->deleted_at))? 'danger':'' }}">

                <td id="td-id" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>

                <td id="td-count" class="{{ $class_N }}">
                    {{$ingreso->id}}
                </td>

                <td id="td-representant-{{ $representant->id ?? '' }}" class="{{ $class_representant  ?? ''}}">
                    {{-- <a class="btn-link" href="{{ route('administracion.representants.index',['search'=>$representant->ci_representant]) }}"> --}}
                        <b>{{$representant->name ?? ''}}</b>
                    {{-- </a> --}}
                    ({{ $representant->ci_representant ?? ''}})
                </td>

                <td class="{{ $class_fecha ?? '' }}">
                    {{ ($ingreso->date_payment) ? $ingreso->date_payment->format('d-m-Y') : '%%%' }}
                </td>

                <td class="{{ $class_fecha ?? '' }}">
                    {{ $ingreso->date_transaction->format('d-m-Y') }}
                </td>

                <td id="td-ingreso-banco-{{ $representant->id ?? '' }}" class="{{ $class_monto ?? '' }}">
                    {{ $ingreso->banco->name ?? ''}}
                </td>

                <td id="td-ingreso-number_i_pay-{{ $ingreso->id ?? '' }}" class="font-weight-bold {{ $class_monto ?? '' }}">

                    {{ $ingreso->number_i_pay ?? ''}} {{(!empty($ingreso->deleted_at))? '[BORRADO]':'' }}

                </td>

                @php $class_exchange_ammount = ($exchange_rate) ? 'font-weight-bold  text-primary':'text-dark'; @endphp
                <td class="{{ $class_monto ?? '' }} {{ $class_exchange_ammount  ?? null }}">
                    {{ (!empty($ingreso->ingreso_ammount)) ? f_float($ingreso->ingreso_ammount):''}}
                </td>

                <td class="{{ $class_monto ?? '' }}">
                    @php
                        $symbol = ($exchange_rate) ? $exchange_rate->currency->symbol : null ;
                        $symbol_referential = ($exchange_rate) ? $exchange_rate->currency_referential->symbol : null ;
                        $ammount = ($exchange_rate) ? $exchange_rate->ammount : null;
                        $date = ($exchange_rate) ? $exchange_rate->date->format('d-m-Y') : null;
                        $title =  ($exchange_rate) ?  'Tasa de Cambio: '.$symbol.' '.f_float($ammount).' - ['.$date.']' : null;
                    @endphp
                    <span class="{{ $class_exchange_ammount  ?? null }}" title="{{ $title ?? 'No hay tasa de cambio' }}">
                        {{ f_float($ingreso->exchange_ammount)}}
                    </span>
                </td>

                @if ($status_late_payment == "true" || $status_late_payment == "on")
                    <td class="{{ $class_monto ?? '' }}">
                        {{ f_float($ingreso->exchange_ammount_late_payment)}} {{ f_float($ingreso->exchange_ammount_late_payment)}}
                    </td>
                @endif

                <td id="td-ingreso-destino-{{ $ingreso->id ?? '' }}" class="{{ $class_monto ?? '' }}">
                    {{ $ingreso->destino ?? ''}}
                </td>
                <td id="td-ingreso-created_at-{{ $representant->id  ?? ''}}" class="{{ $class_fecha ?? '' }}">
                    {{ $ingreso->created_at->format('d-m-Y') }}
                </td>

                <td id="td-ingreso-created_at-{{ $representant->id  ?? ''}}" class="{{ $class_fecha ?? '' }}">
                    {{ ($user) ? $user->username : null  }}
                </td>

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $representant->id  ?? ''}}">
                    <div class="btn-group btn-group-sm">

                        {{-- <a title="Mostrar detalles" class="btn btn-info btn-xs" href="#" data-toggle="modal" data-target="#{{$id_modal ?? 'id_modal'}}">
                            <i class="fas fa-info"></i>
                        </a> --}}
                        @admon
                        <a title="Editar Registro" class="btn btn-warning btn-sm" href="{{ route('administracion.ingresos.edit',['id'=>$ingreso->id]) }}" role="button">
                            <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                        </a>
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-xs" href="#" id="btn-destroy_id_{{$ingreso->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                        @endadmon
                    </div>
                </td>
            </tr>

        @endforeach

        </tbody>
    </table>

    {!! Form::open(['route' => ['administracion.ingresos.destroy',':INGRESO_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
    {!! Form::close() !!}
    @section('scripts')
        @parent
        <script src="{{ asset("js/models/ingresos/destroy.js") }}"></script>
    @endsection

@include('administracion.datatables.exportBootstrap')
