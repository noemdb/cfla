<label for="profesor_id" class="m-0">Profesor</label>
<div class="input-group mb-3">
    {!! Form::select('profesor_id', $profesor_list, old('profesor_id'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<label for="grado_id" class="m-0">Grado</label>
<div class="input-group mb-3">
    {{ Form::hidden('maximo', '1') }}
    {!! Form::select('grado_id', $list_grado, old('grado_id'), [
        'id' => 'grado_id',
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<label for="seccion_id" class="m-0">Sección</label>
@php $list_seccion = (!empty($profesor_guia->grado_id)) ? $profesor_guia->grado->seccions->pluck('id','name'): ['A'=>'A','B'=>'B']; @endphp
<div class="input-group mb-3">
    {!! Form::select('seccion_id', $list_seccion, old('seccion_id'), [
        'class' => 'form-control',
        'id' => 'seccion_id',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<label for="lapso_id" class="m-0">Lapso</label>
<div class="input-group mb-3">
    {!! Form::select('lapso_id', $list_lapso, old('lapso_id'), [
        'class' => 'form-control',
        'id' => 'lapso_id',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="observations" class="m-0">Observación</label>
    {!! Form::text('observations', old('observations'), [
        'class' => 'form-control',
        'placeholder' => 'Observación',
        'id' => 'observations',
    ]) !!}
</div>

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            // fill select para seccion
            $("#grado_id").change(function() {
                var grado_id = $(this)
            .val(); //console.log(grado_id);console.log('gradoByseccion/'+grado_id);
                $.ajax({
                        type: "GET",
                        url: "{{ route('administracion.ajax.fill.gradoByseccion', '') }}/" + grado_id,
                        data: {
                            grado_id: grado_id
                        }
                    })
                    .done(function(data) {
                        // console.log(data);
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

            $("#seccion_id").focusin(function() {
                var grado_id = $("#grado_id")
            .val(); //console.log(grado_id);console.log('gradoByseccion/'+grado_id);
                $.ajax({
                        type: "GET",
                        url: "{{ route('administracion.ajax.fill.gradoByseccion', '') }}/" + grado_id,
                        data: {
                            grado_id: grado_id
                        }
                    })
                    .done(function(data) {
                        // console.log(data);
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

@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $('.crt_checkboxes').click(function(e) {
                var div = $(this).parents('div'); //console.log(div); //fila contentiva de la data
                var name = div.data('name'); //console.log(name);
                var checked = $(this).prop('checked');
                console.log(checked);
                $('#' + name).val(checked); //console.log($('#'.name).val());
            });
        });
    </script>
@endsection
