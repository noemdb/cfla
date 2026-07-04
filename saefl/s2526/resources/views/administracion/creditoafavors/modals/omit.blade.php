<!-- Button trigger modal -->
<button type="button" class="btn btn-outline-secondary rounded" data-toggle="modal" data-target="#modelId">
    <i class="fa fa-check-square" aria-hidden="true"></i>
</button>

<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Omisión de <b class=" font-weight-bold">Créditos a Favor</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <button type="button" class="btn-omit btn btn-primary btn-block" value="update" data-id="{{$id ?? null}}">
                    <i class="far fa-save"></i>
                    Omitir
                </button>
            </div>
        </div>
    </div>
</div>


@section('scripts')
    @parent
    <script>
        $('.btn-omit').click(function (e) {
        e.preventDefault();
        var id = $(this).data('id'); console.log(id);
        var checkId = '#checkId'+id; console.log(checkId);
        var ajaxurl = '{{route("administracion.creditoafavors.set.ajax.omit", "_id_")}}';
        ajaxurl = ajaxurl.replace('_id_', id);
        $.ajax({
            url: ajaxurl,
            type: "GET",
            success: function(data){
                location.reload(true);
                // $("#modelId .close").click();
                // $(checkId).fadeOut();
                // $( "div" ).remove( checkId );
            }
        });
    });
    </script>
@endsection
