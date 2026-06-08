<div class="card-header p-0 m-0 mb-3">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}
    <div class="form-row font-weight-bold">
        <div class="col-4">Grado</div>
        <div class="col-4">Sección</div>
        <div class="col-2">Tipo de Inscripción</div>
        <div class="col-2">&nbsp;</div>
    </div>
    <div class="form-row">
        <div class="col-4">
            {!! Form::select('grado_id', $list_grado, $grado_id, [
                'class' => 'form-control',
                'id' => 'grado_id',
                'placeholder' => 'Seleccione',
                'required',
            ]) !!}
        </div>
        <div class="col-4">
            {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                'class' => 'form-control',
                'id' => 'seccion_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::select('tipo_id', $list_tinscripcion, $tipo_id, [
                'class' => 'form-control',
                'id' => 'tipo_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            <div class="btn-group btn-group btn-block">
                <button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">Buscar</button>
                <a id="" class="btn btn-light" href="{{ url()->current() }}" role="button"
                    title="Refrescar la página">
                    <i class="fas fa-redo" aria-hidden="true"></i>
                </a>
                <a id="btn_toprint_lote" class="btn btn-dark" href="#" role="button" title="Imprimir en lote">
                    <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                </a>
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

    <script>
        //btn generar PDF de todos los grados del lapso seleccionado
        //btn para ir a editar los datos del EST
        $(document).ready(function() {
            $('#btn_toprint_lote').click(function(e) {
                e.preventDefault();
                var grado_id = $('#grado_id').val(); //console.log(ci_estudiant);
                var seccion_id = $('#seccion_id').val(); //console.log(ci_estudiant);
                var lapso_id = $('#lapso_id').val(); //console.log(ci_estudiant);
                var url =
                    '{{ route('administracion.inscripciones.constancia.prosecucion.pdf.lote', ['_gid_', '_sid_']) }}';
                url = url.replace('_gid_', grado_id);
                url = url.replace('_sid_', seccion_id);
                url = url.replace('_lid_', lapso_id);
                window.open(url, '_blank');
            });
        });
    </script>
@endsection
