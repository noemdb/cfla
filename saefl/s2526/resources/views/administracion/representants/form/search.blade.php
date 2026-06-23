<div class="card-header p-0 m-0 mb-3">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}

    <div class="row mt-0 pt-0">
        <div class="col-2">
            <div class="font-weight-bold">Grado</div>
            {!! Form::select('grado_id', $list_grado, $grado_id, [
                'class' => 'form-control form-control-sm',
                'id' => 'grado_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            <div class="font-weight-bold">Sección</div>
            {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                'class' => 'form-control form-control-sm',
                'id' => 'seccion_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>

        <div class="col-2">
            <div class="font-weight-bold">I.Administrativa</div>
            {!! Form::select('status_inscription_affects', ['true' => 'SI', 'false' => 'NO'], $status_inscription_affects, [
                'class' => 'form-control form-control-sm',
                'id' => 'formally',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            <div class="font-weight-bold">I.Académica</div>
            {!! Form::select('status_active', ['true' => 'SI', 'false' => 'NO'], $status_active, [
                'class' => 'form-control form-control-sm',
                'id' => 'formally',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>

        <div class="col-2">
            <div class="font-weight-bold">Formalizado</div>
            {!! Form::select('formally', ['SI' => 'SI'], $formally, [
                'class' => 'form-control form-control-sm',
                'id' => 'formally',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>

        <div class="col-2">
            <div class="font-weight-bold">&nbsp;</div>
            <div class="btn-group btn-block" role="group" aria-label="Basic example">
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
