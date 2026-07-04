@php
    $class_N="d-none d-sm-table-cell";
    $name="";
    $dir_address="d-none d-lg-table-cell";
    $institution="d-none d-md-table-cell";
    $grado="d-none d-lg-table-cell";
    $name_representant="";
    $email_representant="";
    $ci_representant="";
    $user_id="";
    $created_at="d-none d-lg-table-cell";
    $class_action="";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N ?? null }}">N</th>
            <th class="{{ $name ?? null }}">{{$list_comment['name'] ?? null}}</th>
            <th class="{{ $dir_address ?? null }}">{{$list_comment['dir_address'] ?? null}}</th>
            <th class="{{ $institution ?? null }}">{{$list_comment['institution']}}</th>
            <th class="{{ $grado ?? null }}">{{$list_comment['grado'] ?? null}}</th>
            <th class="{{ $name_representant ?? null }}">{{$list_comment['name_representant'] ?? null}}</th>
            <th class="{{ $ci_representant ?? null }}">{{$list_comment['ci_representant'] ?? null}}</th>
            <th class="{{ $email_representant ?? null }}">{{$list_comment['email_representant'] ?? null}}</th>
            <th class="{{ $user_id ?? null }}">{{$list_comment['user_id'] ?? null}}</th>
            <th class="{{ $created_at ?? null }}">{{$list_comment['created_at'] ?? null}}</th>
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($censuses as $census)

        <tr data-id="{{$census->id}}">
            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>
            <td class="{{ $name  ?? ''}}">
                {{$census->lastname ?? ''}} {{$census->name ?? ''}} || {{$census->ci_estudiant ?? ''}}
            </td>
            <td class="{{ $dir_address  ?? ''}}">
                {{$census->dir_address ?? ''}}
            </td>
            <td class="{{ $institution  ?? ''}}">
                {{$census->institution ?? ''}}
            </td>
            <td class="{{ $grado  ?? ''}}">
                {{$census->grado->name ?? ''}}
            </td>
            <td class="{{ $name_representant  ?? ''}}">
                <div>{{$census->name_representant ?? ''}}</div>
            </td>
            <td class="{{ $ci_representant  ?? ''}}">
                <div>{{$census->ci_representant ?? ''}}</div>
            </td>
            <td class="{{ $name_representant  ?? ''}}">
                <div>{{$census->email_representant ?? ''}}</div>
            </td>
            <td class="{{ $user_id  ?? ''}}">
                {{$census->user->username ?? ''}}
            </td>
            <td class="{{ $created_at  ?? ''}}">
                {{$census->created_at->format('d-m-Y h:i A') ?? ''}}
                [{{$census->created_at->dayOfWeek ?? ''}}]
            </td>
            <td class="{{ $class_action ?? '' }}">
                <div class="btn-group btn-group-sm">
                    <a title="Editar" class="btn btn-warning btn-sm" href="{{route('administracion.configuraciones.lapsos.census.edit',$census->id)}}" role="button">
                        <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                    </a>

                    @php
                        $disabled = ($now > $lapso->date_end_census) ? 'disabled' : null;
                    @endphp
                    <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{$disabled ?? ''}}" href="#" id="btn-destroy_id_{{$census->id}}">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{!! Form::open(['route' => ['administracion.configuraciones.lapsos.census.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.exportBootstrap')
