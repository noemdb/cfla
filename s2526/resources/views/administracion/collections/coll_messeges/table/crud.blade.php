@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['user_id']="d-none d-sm-table-cell";
    $class['coll_nivel_id']="d-none d-sm-table-cell";
    $class['subject']="d-none d-sm-table-cell";
    $class['title']="d-none d-md-table-cell";
    $class['subtitle']="d-none d-sm-table-cell";
    $class['greeting']="d-none d-sm-table-cell";
    $class['consider']="d-none d-sm-table-cell";
    $class['sentence']="d-none d-sm-table-cell";
    $class['waiting']="d-none d-sm-table-cell";
    $class['footer']="d-none d-sm-table-cell";
    $class['action']="d-none d-sm-table-cell";
@endphp

{{-- 'user_id','coll_nivel_id','subject','title','subtitle','greeting','consider','sentence','waiting','footer' --}}

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['user_id'] ?? ''}}">{{$list_comment['user_id'] ?? ''}}</th>
            <th class="{{ $class['coll_nivel_id'] ?? ''}}">{{$list_comment['coll_nivel_id'] ?? ''}}</th>
            <th class="{{ $class['subject'] ?? ''}}">{{$list_comment['subject'] ?? ''}}</th>
            <th class="{{ $class['title'] ?? ''}}">{{$list_comment['title'] ?? ''}}</th>
            <th class="{{ $class['subtitle'] ?? ''}}">{{$list_comment['subtitle'] ?? ''}}</th>
            <th class="{{ $class['greeting'] ?? ''}}">{{$list_comment['greeting'] ?? ''}}</th>
            <th class="{{ $class['consider'] ?? ''}}">{{$list_comment['consider'] ?? ''}}</th>
            <th class="{{ $class['sentence'] ?? ''}}">{{$list_comment['sentence'] ?? ''}}</th>
            <th class="{{ $class['waiting'] ?? ''}}">{{$list_comment['waiting'] ?? ''}}</th>
            <th class="{{ $class['footer'] ?? ''}}">{{$list_comment['footer'] ?? ''}}</th>
            <th class="{{ $class['status'] ?? ''}}">{{$list_comment['status'] ?? ''}}</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($coll_messeges as $coll_messege)

    @php
        $user = ($coll_messege->user) ? $coll_messege->user : null;
        $coll_nivel = ($coll_messege->coll_nivel) ? $coll_messege->coll_nivel : null;
        $representant = ($coll_messege->representant) ? $coll_messege->representant : null;
        $status = ($coll_messege->status) ? $coll_messege->status : null;
    @endphp

        <tr data-id="{{$coll_messege->id}}">
            <td class="{{ $class['iteration'] ?? ''}}">N</td>
            <td class="{{ $class['user_id'] ?? ''}}">{{ ($user) ? $user->username : null}}</td>
            <td class="{{ $class['coll_nivel_id'] ?? ''}}">{{ ($coll_nivel) ? $coll_nivel->fullname : null}}</td>
            <td class="{{ $class['subject'] ?? ''}}">{{$coll_messege->subject ?? ''}}</td>
            <td class="{{ $class['title'] ?? ''}}">{{$coll_messege->title ?? ''}}</td>
            <td class="{{ $class['subtitle'] ?? ''}}">{{$coll_messege->subtitle ?? ''}}</td>
            <td class="{{ $class['greeting'] ?? ''}}">{{ $coll_messege->greeting ?? '' }}</td>
            <td class="{{ $class['consider'] ?? ''}}">{{ $coll_messege->consider ?? '' }}</td>
            <td class="{{ $class['sentence'] ?? ''}}">{{ $coll_messege->sentence ?? '' }}</td>
            <td class="{{ $class['waiting'] ?? ''}}">{{ $coll_messege->waiting ?? '' }}</td>
            <td class="{{ $class['footer'] ?? ''}}">{{ $coll_messege->footer ?? '' }}</td>
            <td class="{{ $class['status'] ?? ''}}">{{ ($coll_messege->status=='true') ? 'Activo':'Desactivo' }}</td>

            <td class="{{ $class_action ?? '' }}">

                <div class="btn-group btn-group-sm">

                    <div class="btn-group btn-group-sm">
                        <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.collections.coll_messeges.edit',$coll_messege->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>

                        <a title="Mostrar vista previa del mensaje" class="btn-preview btn btn-info btn-sm"  href="#" role="button">
                            <i class="{{ $icon_menus['info'] ?? ''}} fa-1x"></i>
                        </a>

                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm" href="#" id="btn-destroy_id_{{$coll_messege->id}}">
                            <i class="fas fa-trash"></i>
                        </a>

                    </div>

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{!! Form::open(['route' => ['administracion.collections.coll_messeges.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')



{{-- preview --}}
@include('administracion.collections.coll_politicals.form.asistent.modals.preview')

@section('scripts')
    @parent
    <script>
        $(document).ready(function(){
            $('.btn-preview').click(function (e) {

                var row = $(this).parents('tr'); //fila contentiva de la data
                var id = row.data('id');
                var container = '#content_preview';
                var ajaxurl = '{{route("administracion.collections.coll_messeges.preview.id", "_id_")}}'; ajaxurl = ajaxurl.replace('_id_', id);
                $.ajax({
                    type: "GET",
                    url: ajaxurl,
                })
                .done(function( result ) {
                    $(container).html(result);
                    $('#modalIdPreview').modal('toggle');
                })
                .fail(function() {
                    console.log( "error occured" );
                });

            });
        });
    </script>
@endsection
