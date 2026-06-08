@php
$btnId = 'btnId'.$coll_messege->id;
$modalId = 'modalIdCollMsn'.$coll_messege->id;
$container = 'containerlIdCollMsn'.$coll_messege->id;
@endphp
<a id="{{$btnId ?? 'btnId'}}" title="Mostrar vista previa del mensaje" class="btn-preview btn btn-info btn-sm"  href="#" role="button" data-id="{{$coll_messege->id}}" data-modalId="{{$modalId}}" data-container="{{$container}}">
    <i class="{{ $icon_menus['info'] ?? ''}} fa-1x"></i>
</a>
@include('administracion.collections.coll_politicals.form.asistent.modals.preview')
@section('scripts')
    @parent
    <script>
        $(document).ready(function(){
            $('#{{$btnId ?? 'btnId'}}').click(function (e) {
                e.preventDefault();
                var obj = $(this); //fila contentiva de la data
                var id = obj.data('id');
                var container = '#content_preview';
                var modalId = '#modalIdPreview';

                var ajaxurl = '{{route("administracion.collections.coll_messeges.preview.id", "_id_")}}'; ajaxurl = ajaxurl.replace('_id_', id);
                $.ajax({
                    type: "GET",
                    url: ajaxurl,
                })
                .done(function( result ) {
                    $(container).html(result);
                    $(modalId).modal('toggle');
                })
                .fail(function() {
                    console.log( "error occured" );
                });

            });
        });
    </script>
@endsection
