<div class="first-of-type" id="step-preview">

    <div class="alert alert-secondary rounded flex-center px-4" style="min-height: 25rem;">

        <div class="alert alert-light p-4 shadow ">
            <div class="px-4 flex-center">
                <div>

                    <div class="container-fluid">
                        <div class="row">
                            {{-- <div class="col-3 flex-center">
                                <i class="fa fa-check fa-10x text-success" aria-hidden="true"></i>
                            </div> --}}
                            <div class="col-12 flex-center">

                                {!! Form::button('Previsualizar mensaje', ['class' => 'btn btn-info btn-block btn-preview', 'id' => 'create']) !!}



                            </div>
                        </div>

                        @include('administracion.collections.coll_politicals.form.asistent.modals.preview')

                    </div>

                </div>

            </div>
        </div>

    </div>
    <div class="step_form py-2 px-1 font-weight-bold">
        <input type="button" class="clic-step btn btn-light w-25 p-2" value="Anterior"
            data-step-show="step-coll_messeges-footer" data-step-hide="step-preview" data-direction="down" />
        <input type="button" class="clic-step btn btn-info w-75 p-2 float-right"
            value="Siguinte &#10148"data-step-show="step-finish" data-step-hide="step-preview" data-direction="up" />
    </div>
</div>


@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            //ini del evento clic
            $('.btn-preview').click(function(e) {
                e.preventDefault();
                var container = '#content_preview';
                console.log(container);
                var form = $('#form-collPolitical-create');
                console.log(form); //console.log(form.attr('action'));
                var data = form.serialize();
                console.log(data);
                var url = "{{ route('administracion.collections.coll_politicals.preview') }}";
                console.log(url);
                $.post(url, data, function(result) {
                    $(container).html(result);
                    $('#modalIdPreview').modal('toggle');
                }).fail(function(result) {
                    var error_msg = '';
                    $.each(result.responseJSON.errors, function(index, valor) {
                        error_msg += valor + ', ';
                    });
                    Swal.fire({
                        title: 'ERROR',
                        type: 'error',
                        text: error_msg
                    });
                });
            });
            //fin del evento clic
        });
    </script>
@endsection
