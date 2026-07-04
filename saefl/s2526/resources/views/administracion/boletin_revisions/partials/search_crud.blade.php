<div class="card-header p-0 m-0 mb-3">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}

    <div class="row">
        <div class="col-12">
            <div class="form-row">

                <div class="col-5">
                    <label for="grado_id" class="font-weight-bold m-0">Grado</label>
                    {!! Form::select('grado_id', $list_grado, $grado_id, [
                        'class' => 'form-control',
                        'id' => 'grado_id',
                        'placeholder' => 'Seleccione',
                        'required',
                    ]) !!}
                </div>
                <div class="col-5">
                    <label for="seccion_id" class="font-weight-bold m-0">Sección</label>
                    {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                        'class' => 'form-control',
                        'id' => 'seccion_id',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>
                <div class="col-2">
                    <label for="observations" class="font-weight-bold m-0">&nbsp;</label>
                    <div class="btn-group btn-group btn-block text-center" style="vertical-align: middle !important">
                        <button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">Buscar</button>
                        <a id="" class="btn btn-light" href="{{ url()->current() }}" role="button"
                            title="Refrescar la página">
                            <i class="fas fa-redo" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>


    {!! Form::close() !!}
</div>

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $("#grado_id").change(function() {
                var grado_id = $(this).val();
                console.log(grado_id);
                console.log('gradoByseccion/' + grado_id);
                $.ajax({
                        type: "GET",
                        url: "{{ route('ajax.fill.gradoByseccion', '') }}/" + grado_id,
                        data: {
                            grado_id: grado_id
                        }
                    })
                    .done(function(data) {
                        console.log(data);
                        var seccion_select = '<option value="">Seleccione</option>'
                        for (var i = 0; i < data.length; i++)
                            seccion_select += '<option value="' + data[i].id + '">' + data[i].name +
                            '</option>';
                        $("#seccion_id").html(seccion_select);
                    })
                    .fail(function() {
                        console.log("error occured");
                    });

            });
        });
    </script>
@endsection
