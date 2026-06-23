@php $required_grado = (!empty($required_grado)) ? 'required':true ; @endphp
@php $required_pensum = (!empty($required_pensum)) ? 'required':true ; @endphp
@php $required_seccion = (!empty($required_seccion)) ? 'required':true ; @endphp
@php $required_lapso = (!empty($required_lapso)) ? 'required':true ; @endphp

<div class="card-header p-0 m-0 mb-3">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}
    <div class="form-row font-weight-bold">
        <div class="col-2">Grado</div>
        <div class="col-2">Sección</div>
        <div class="col-4">Asignatura</div>
        <div class="col-2">Lapso</div>
        <div class="col-2">&nbsp;</div>
    </div>
    <div class="form-row">

        <div class="col-2">
            {!! Form::select('grado_id', $list_grado, $grado_id, [
                'class' => 'form-control',
                'id' => 'grado_id',
                'placeholder' => 'Seleccione',
                $required_grado,
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                'class' => 'form-control',
                'id' => 'seccion_id',
                'placeholder' => 'Seleccione',
                $required_pensum,
            ]) !!}
        </div>
        <div class="col-4">
            {!! Form::select('pensum_id', $list_pensum ?? [], $pensum_id, [
                'class' => 'form-control',
                'id' => 'pensum_id',
                'placeholder' => 'Seleccione',
                $required_seccion,
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::select('lapso_id', $list_lapso, $lapso_id, [
                'class' => 'form-control',
                'id' => 'lapso_id',
                'placeholder' => 'Seleccione',
                $required_lapso,
            ]) !!}
        </div>

        <div class="col-2">
            <div class="btn-group btn-group btn-block">
                <button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    Buscar
                </button>
                <a id="" class="btn btn-light" href="{{ url()->current() }}" role="button"
                    title="Refrescar la página">
                    <i class="fas fa-redo" aria-hidden="true"></i>
                </a>
                @if (!empty($btn_toprint))
                    <a id="btn_toprint" class="btn btn-dark" href="#" role="button" title="Generar PDF">
                        <i class="fa fa-file-pdf" aria-hidden="true"></i>
                    </a>
                @endif
                @if (!empty($btn_toprint_lote))
                    <a id="btn_toprint_lote" class="btn btn-light" href="#" role="button"
                        title="Genera PDF de todos los grados del lapso seleccionado">
                        <i class="fa fa-print" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        </div>

    </div>
    {!! Form::close() !!}
</div>

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            // fill select para seccion
            $("#grado_id").change(function() {
                var grado_id = $(this).val();
                console.log(grado_id);
                console.log('gradoByseccion/' + grado_id);
                $.ajax({
                        type: "GET",
                        url: "{{ route('administracion.ajax.fill.gradoByseccion', '') }}/" + grado_id,
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

            // fill select para pensum
            $("#grado_id").change(function() {
                var grado_id = $(this).val();
                console.log(grado_id);
                console.log('gradoBypensum/' + grado_id);
                $.ajax({
                        type: "GET",
                        url: "{{ route('administracion.ajax.fill.gradoBypensum', '') }}/" + grado_id,
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
                        $("#pensum_id").html(seccion_select);
                    })
                    .fail(function() {
                        console.log("error occured");
                    });

            });
        });
    </script>
@endsection
