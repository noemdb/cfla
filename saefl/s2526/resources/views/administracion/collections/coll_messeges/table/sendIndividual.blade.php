@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['user_id']="d-none d-sm-table-cell";
    $class['coll_nivel_id']="d-none d-sm-table-cell";
    $class['representant_id']="d-none d-sm-table-cell";
    $class['exchange_ammount']="d-none d-sm-table-cell";
    $class['description']="d-none d-md-table-cell";
    $class['status_id']="d-none d-sm-table-cell";
    $class['status_messege']="d-none d-sm-table-cell";
    $class['status_call']="d-none d-sm-table-cell";
    $class['action']="d-none d-sm-table-cell";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['representant_id'] ?? ''}}">Ident.</th>
            <th class="{{ $class['representant_id'] ?? ''}}">Representante</th>
            <th class="{{ $class['representant_id'] ?? ''}}">Email</th>
            <th class="{{ $class['exchange_ammount'] ?? ''}}">Deuda[$]</th>
            <th class="{{ $class['status_messege'] ?? ''}}">Notificación Email</th>
            <th class="{{ $class['status_call'] ?? ''}}">Notificación Telefónica</th>
            <th class="{{ $class['action'] ?? ''}}">Mensajes</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($representants as $representant)

    @php
        $exchange_ammount = $representant->exchange_ammount_expire_bill;
        $emails = $representant->emails;
    @endphp

        <tr data-id="{{$representant->id}}">
            <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
            <td class="{{ $class['representant_id'] ?? ''}}">{{ $representant->ci_representant }}</td>
            <td class="{{ $class['representant_id'] ?? ''}}">{{ $representant->name }}</td>
            <td class="{{ $class['representant_id'] ?? ''}}">{{ $representant->email }}</td>
            {{-- <td class="{{ $class['representant_id'] ?? ''}}">{!! ($emails->isNotEmpty()) ? $emails->implode('<br>'):'N.Emails' !!}</td> --}}
            <td class="{{ $class['exchange_ammount'] ?? ''}}" title="{{$exchange_ammount}}">{{f_number($exchange_ammount)}}</td>
            <td class="{{ $class['status_messege'] ?? ''}}">{{ ($representant->status_messege=='true') ? 'SI':'NO' }}</td>
            <td class="{{ $class['status_call'] ?? ''}}">{{ ($representant->status_call=='true') ? 'SI':'NO' }}</td>

            <td class="{{ $class_action ?? '' }}">

                <div class="btn-group btn-group-sm">

                    @forelse ($coll_messeges as $coll_messege)
                        @php $disabled = ($coll_messege->status == 'false') ? 'disabled' : null @endphp
                        <a title="Enviar notificación" class="btn btn-success btn-sm {{$disabled ?? ''}}"  href="{{route('email.collections.coll_politicals',[$representant->id,$coll_messege->id])}}" role="button">
                            <i class="{{ $icon_menus['mail'] ?? ''}} fa-1x"></i>
                            {{$loop->iteration}}
                        </a>
                        <a title="Mostrar vista previa del mensaje" class="btn-preview btn btn-info btn-sm"  href="#" role="button" data-id="{{$coll_messege->id}}">
                            <i class="{{ $icon_menus['info'] ?? ''}} fa-1x"></i>
                            {{$loop->iteration}}
                        </a>
                    @empty
                        <span>No hay mensajes</span>
                    @endforelse

                </div>

            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables default --}}
@include('administracion.datatables.exportBootstrap')


{{-- preview --}}
@include('administracion.collections.coll_politicals.form.asistent.modals.preview')

@section('scripts')
    @parent
    <script>
        $(document).ready(function(){
            $('.btn-preview').click(function (e) {
                e.preventDefault();
                var row = $(this); //fila contentiva de la data
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
