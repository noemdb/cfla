@php
    $class['index']="";
    $class['date']="d-none d-sm-table-cell";
    $class['ammount']="d-none d-lg-table-cell";
    $class['currency_id']="d-none d-md-table-cell";
    $class['currency_referential_id']="d-none d-md-table-cell";
    $class['source']="d-none d-lg-table-cell";
    $class['name']="d-none d-lg-table-cell";
    $class['observations']="d-none d-lg-table-cell";
    $class['status_official']="d-none d-lg-table-cell";
    $class['action']="nosort";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class['index'] ?? ''}}">N</th>
                <th class="{{ $class['date'] ?? ''}}">{{$list_comment['date'] ?? ''}}</th>
                <th class="{{ $class['ammount'] ?? ''}}">{{$list_comment['ammount'] ?? ''}}</th>
                <th class="{{ $class['currency_id'] ?? ''}}">{{$list_comment['currency_id'] ?? ''}}</th>
                <th class="{{ $class['currency_referential_id'] ?? ''}}">{{$list_comment['currency_referential_id'] ?? ''}}</th>
                <th class="{{ $class['source'] ?? ''}}">{{$list_comment['source'] ?? ''}}</th>
                {{-- <th class="{{ $class['name'] ?? ''}}">{{$list_comment['name'] ?? ''}}</th> --}}
                {{-- <th class="{{ $class['observations'] ?? ''}}">{{$list_comment['observations'] ?? ''}}</th> --}}
                {{-- <th class="{{ $class['status_official'] ?? ''}}">{{$list_comment['status_official'] ?? ''}}</th> --}}
                {{-- <th class="{{ $class['action'] ?? '' }}">Acciones</th> --}}
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($exchange_rates as $exchange_rate)

            <tr data-id="{{$exchange_rate->id}}">
                <td id="td-count" class="{{ $class['index'] ?? ''}}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class['date'] ?? ''}}">
                    {{ $exchange_rate->date->format('d-m-Y')}}
                </td>
                <td class="{{ $class['ammount'] ?? ''}}">
                    {{f_float($exchange_rate->ammount) }}
                </td>
                <td class="{{ $class['currency_id'] ?? ''}}">
                    {{ ($exchange_rate->currency) ? $exchange_rate->currency->name : null  }}
                </td>
                <td class="{{ $class['currency_referential_id'] ?? ''}}">
                    {{ ($exchange_rate->currency_referential) ? $exchange_rate->currency_referential->name : null  }}
                </td>
                <td class="{{ $class['source'] ?? ''}}">
                    {{ $exchange_rate->source ?? '' }}
                </td>
                {{-- <td class="{{ $class['name'] ?? ''}}">
                    {{ $exchange_rate->name ?? '' }}
                </td> --}}
                {{-- <td class="{{ $class['observations'] ?? ''}}">
                    {{ $exchange_rate->observations ?? '' }}
                </td> --}}
                {{-- <td class="{{ $class['status_official'] ?? ''}}">
                    {{ ($exchange_rate->status_official = "true") ? 'SI' : 'NO' }}
                </td> --}}
                {{-- <td class="{{ $class_action ?? '' }}">
                    <div class="btn-group btn-group-sm">
                        <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.configuraciones.exchange_rates.edit',$exchange_rate->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>
                        @php $disabled = ($exchange_rate->status_delete) ? null : ' disabled ' ; @endphp
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{ $disabled ?? '' }}" href="#" id="btn-destroy_id_{{$exchange_rate->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td> --}}
            </tr>
        @endforeach

        </tbody>

    </table>

{!! Form::open(['route' => ['administracion.configuraciones.exchange_rates.destroy',':PLAN_PAGO_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':PLAN_PAGO_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
