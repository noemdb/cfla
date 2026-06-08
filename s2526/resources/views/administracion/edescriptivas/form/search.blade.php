<div class="card-header p-0 m-0 mb-3">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}
    <div class="form-row font-weight-bold">
        <div class="col-4">Estudiante</div>
        <div class="col-3">Grado</div>
        <div class="col-2">Sección</div>
        <div class="col-3">&nbsp;</div>
    </div>
    <div class="form-row">
        <div class="col-4">
            {!! Form::text('search', $search, ['class' => 'form-control', 'placeholder' => 'Buscar Nombre o Cédula']) !!}
        </div>
        <div class="col-3">
            {!! Form::select('grado_id', $list_grado, $grado_id, [
                'class' => 'form-control',
                'id' => 'grado_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                'class' => 'form-control',
                'id' => 'seccion_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>

        <div class="col-3">
            <div class="btn-group btn-group btn-block">
                <button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </button>
                @if (!empty($btn_toprint_lote))
                    {!! Form::select('lapso_id', $list_lapso, $lapso_id, ['class' => 'text-muted small', 'id' => 'lapso_id']) !!}
                    <a id="btn_toprint_lote" class="btn btn-dark" href="#" role="button" title="Generar PDF">
                        <i class="fa fa-file-pdf" aria-hidden="true"></i>
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

            $('#btn_toprint_lote').click(function(e) {
                e.preventDefault();
                var grado_id = $('#grado_id').val(); //console.log(ci_estudiant);
                var seccion_id = $('#seccion_id').val(); //console.log(ci_estudiant);
                var lapso_id = $('#lapso_id').val(); //console.log(ci_estudiant);
                var url =
                    '{{ route('administracion.edescriptivas.edescriptiva.lote.pdf', ['_gid_', '_sid_', '_lid_']) }}';
                url = url.replace('_gid_', grado_id);
                url = url.replace('_sid_', seccion_id);
                url = url.replace('_lid_', lapso_id);
                window.open(url, '_blank');
            });

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
