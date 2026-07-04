<div class="card-header p-0 m-0 mb-3">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}
    <div class="form-row font-weight-bold">
        <div class="col-6">Consulta</div>
        <div class="col-2">Grado</div>
        <div class="col-2">Sección</div>
        <div class="col-2">&nbsp;</div>
    </div>
    <div class="form-row">
        <div class="col-6">
            {!! Form::select('poll_main_id', $list_poll_main, $poll_main_id, [
                'class' => 'form-control form-control-sm',
                'id' => 'poll_main_id',
                'placeholder' => 'Seleccione',
                'required',
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::select('grado_id', $list_grado, $grado_id, [
                'class' => 'form-control form-control-sm',
                'id' => 'grado_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                'class' => 'form-control form-control-sm',
                'id' => 'seccion_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            <div class="btn-group btn-group-sm btn-block" role="group" aria-label="Basic example">
                <button class="btn btn-primary btn-block" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    Buscar
                </button>
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
