@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['bmain_id']="d-none d-sm-table-cell";
    $class['key']="d-none d-sm-table-cell";
    $class['text']="d-none d-sm-table-cell";
    $class['description']="d-none d-md-table-cell";
    $class['created_at']="d-none d-sm-table-cell";
    $class['rule_id']="d-none d-sm-table-cell";
@endphp

{{-- 'bmain_id','key','text','description' --}}

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['bmain_id'] ?? ''}}">{{$list_comment['bmain_id'] ?? ''}}</th>
            <th class="{{ $class['key'] ?? ''}}">{{$list_comment['key'] ?? ''}}</th>
            <th class="{{ $class['description'] ?? ''}}">{{$list_comment['description'] ?? ''}}</th>
            <th class="{{ $class['text'] ?? ''}}">{{$list_comment['text'] ?? ''}}</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($boptions as $boption)
        @php
            $bmain = $boption->bmain;
        @endphp
        <tr data-id="{{$boption->id}}" class="{{ ($boption->status_active=='true') ? 'table-success' : null }}">
            <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
            <td class="{{ $class['bmain_id'] ?? ''}}">{{$bmain->name ?? ''}}</td>
            <td class="{{ $class['key'] ?? ''}}">{{$boption->key ?? ''}}</td>
            <td class="{{ $class['description'] ?? ''}}">{{$boption->description ?? ''}}</td>
            <td class="{{ $class['text'] ?? ''}}">{{$boption->text ?? ''}}</td>

            <td class="{{ $class_action ?? '' }}">

                <div class="btn-group btn-group-sm">

                    <div class="btn-group btn-group-sm">
                        <a header_key="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.autoresponders.boptions.edit',$boption->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>

                        <a header_key="Eliminar" class="btn-destroy btn btn-danger btn-sm" href="#" id="btn-destroy_id_{{$boption->id}}">
                            <i class="fas fa-trash"></i>
                        </a>

                    </div>

                </div>
            </td>

        </tr>
    @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.exportBootstrap')

{!! Form::open(['route' => ['administracion.autoresponders.boptions.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

