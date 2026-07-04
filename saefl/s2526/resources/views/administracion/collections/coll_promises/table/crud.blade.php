@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['coll_political_id']="d-none d-sm-table-cell";
    $class['representant_id']="d-none d-sm-table-cell";
    $class['date']="d-none d-sm-table-cell";
    $class['exchange_ammount']="d-none d-sm-table-cell";
    $class['status']="d-none d-sm-table-cell";
    $class['description']="d-none d-md-table-cell";
    $class['observation']="d-none d-md-table-cell";
    $class['action']="d-none d-sm-table-cell";
@endphp

{{-- 'coll_political_id','representant_id','date','ammount','exchange_ammount','status','description','observation' --}}

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['coll_political_id'] ?? ''}}">{{$list_comment['coll_political_id'] ?? ''}}</th>
            <th class="{{ $class['representant_id'] ?? ''}}">{{$list_comment['representant_id'] ?? ''}}</th>
            <th class="{{ $class['date'] ?? ''}}">{{$list_comment['date'] ?? ''}}</th>
            <th class="{{ $class['exchange_ammount'] ?? ''}}">{{$list_comment['exchange_ammount'] ?? ''}}</th>
            <th class="{{ $class['status'] ?? ''}}">{{$list_comment['status'] ?? ''}}</th>
            <th class="{{ $class['description'] ?? ''}}">{{$list_comment['description'] ?? ''}}</th>
            <th class="{{ $class['observation'] ?? ''}}">{{$list_comment['observation'] ?? ''}}</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($coll_promises as $coll_promise)

    @php
        $coll_political = ($coll_promise->coll_political) ? $coll_promise->coll_political : null;
        $representant = ($coll_promise->representant) ? $coll_promise->representant : null;
    @endphp

        <tr data-id="{{$coll_promise->id}}">
            <td class="{{ $class['iteration'] ?? ''}}">N</td>
            <td class="{{ $class['coll_political_id'] ?? ''}}">{{ ($coll_political) ? $coll_political->fullname : null}}</td>
            <td class="{{ $class['representant_id'] ?? ''}}">{{ ($representant) ? $representant->name : null}}</td>
            <td class="{{ $class['date'] ?? ''}}">{{ f_date($coll_promise->date) }}</td>
            <td class="{{ $class['exchange_ammount'] ?? ''}}">{{ f_float($coll_promise->exchange_ammount) }}</td>
            <td class="{{ $class['status'] ?? ''}}">{{$coll_promise->status ?? ''}}</td>
            <td class="{{ $class['description'] ?? ''}}">{{$coll_promise->description ?? ''}}</td>
            <td class="{{ $class['observation'] ?? ''}}">{{$coll_promise->observation ?? ''}}</td>

            <td class="{{ $class_action ?? '' }}">

                <div class="btn-group btn-group-sm">

                    <div class="btn-group btn-group-sm">
                        <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.collections.coll_promises.edit',$coll_promise->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>
                        <a title="Editar" class="btn btn-dark btn-sm"  href="{{route('administracion.collections.coll_promises.pdf',$coll_promise->id)}}" role="button" target="_blank">
                            <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x"></i>
                        </a>
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm disabled" href="#" id="btn-destroy_id_{{$coll_promise->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{!! Form::open(['route' => ['administracion.collections.coll_promises.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
