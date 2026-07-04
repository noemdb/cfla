@php
    $disabled = Auth::user()->isControlDir() ? null : ' disabled ';
@endphp

<div class="card-header p-0 m-0 mb-3">
    {!! Form::open([
        'route' => $route,
        'method' => 'POST',
        'class' => 'p-1 m-1',
        'role' => 'search',
        'files' => 'true',
        'enctype' => 'multipart/form-data',
    ]) !!}
    <fieldset {{ $disabled }}>
        <div class="form-row font-weight-bold">
            <div class="col-2">Grado</div>
            <div class="col-1">Sección</div>
            <div class="col-2">Lapso</div>
            <div class="col-2">Asignatura</div>
            <div class="col-3">Archivo XLS</div>
            <div class="col-2">&nbsp;</div>
        </div>
        <div class="form-row">

            <div class="col-2">
                {!! Form::select('grado_id', $list_grado, $grado_id, [
                    'class' => 'form-control',
                    'id' => 'grado_id',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
            </div>
            <div class="col-1">
                {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                    'class' => 'form-control',
                    'id' => 'seccion_id',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
            </div>
            <div class="col-2">
                {!! Form::select('lapso_id', $list_lapso, $lapso_id, [
                    'class' => 'form-control',
                    'id' => 'lapso_id',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
            </div>
            <div class="col-2">
                {!! Form::select('pensum_id', $list_pensum, $pensum_id, [
                    'class' => 'form-control',
                    'id' => 'pensum_id',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
            </div>
            <div class="col-3">
                <div class="input-group">
                    <div class="custom-file">
                        {!! Form::file('file_xls', ['class' => 'custom-file-input', 'required']) !!}
                        <label class="custom-file-label" for="inputGroupFile01">Selecciona XLS</label>
                    </div>
                </div>
            </div>

            <div class="col-2">
                <div class="btn-group btn-group btn-block">
                    <button class="btn btn-primary my-2 my-sm-0" type="submit">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>
                    <button class="btn-pdf btn btn-dark my-2 my-sm-0 " id="btn-pdf" type="button">
                        <i class="fa fa-file-pdf" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

        </div>

    </fieldset>
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

            //btn para ir a editar los datos del EST
            $(document).ready(function() {
                $('#btn-pdf').click(function(e) {
                    e.preventDefault();
                    var grado_id = $('#grado_id').val(); //console.log(ci_estudiant);
                    var seccion_id = $('#seccion_id').val(); //console.log(ci_estudiant);
                    var lapso_id = $('#lapso_id').val(); //console.log(ci_estudiant);
                    var pensum_id = $('#pensum_id').val(); //console.log(ci_estudiant);
                    var url =
                        '{{ route('administracion.boletins.sabana_simple.pdf', ['grado_id' => '_gid_', 'seccion_id' => '_sid_', 'lapso_id' => '_lid_', 'pensum_id' => '_pid_']) }}';
                    url = url.replace('_gid_', grado_id);
                    url = url.replace('_sid_', seccion_id);
                    url = url.replace('_lid_', lapso_id);
                    url = url.replace('_pid_', pensum_id);
                    window.open(url, '_blank');
                });
            });
        });
    </script>
@endsection
