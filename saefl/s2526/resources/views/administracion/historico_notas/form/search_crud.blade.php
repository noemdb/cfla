{!! Form::open(['route' => $route, 'method' => 'GET', 'class' => '', 'role' => 'search']) !!}
{{-- <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="name"> --}}


<div class="form-row">
    <div class="col-3">
        {!! Form::text('search', $search, [
            'class' => 'form-control form-control-sm',
            'placeholder' => 'Nombre o Cédula',
        ]) !!}
    </div>
    <div class="col-3">
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
            'placeholder' => 'Sección',
        ]) !!}
    </div>
    <div class="col-2">
        {!! Form::select('status', ['all' => 'Todos', 'active' => 'Activos', 'deleted' => 'Inactivos'], $status, [
            'class' => 'form-control form-control-sm',
        ]) !!}
    </div>
    <div class="col-2">
        <button class="btn btn-info my-2 my-sm-0 btn-sm w-100" type="submit">Buscar</button>
    </div>
</div>
{!! Form::close() !!}


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
