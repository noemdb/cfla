{{-- {{ Form::hidden('pensum_id', $pensum->id) }} --}}
{{ Form::hidden('grado_id', $grado->id) }}
{{ Form::hidden('seccion_id_old', $seccion->id) }}
{{ Form::hidden('lapso_id', $lapso->id) }}

<label for="grado_id" class="m-0">Grado</label>
<div class="input-group mb-3">
    {!! Form::select('grado_id', $list_grado, $grado_id, [
        'id' => 'grado_id',
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<label for="seccion_id" class="m-0">Sección</label>
<div class="input-group mb-3">
    {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
        'class' => 'form-control',
        'id' => 'seccion_id',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<label for="profesor_id" class="m-0">Asignatura</label>
<div class="input-group mb-3">
    {!! Form::select('pensum_id', $pensum_list, old('profesor_id'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<label for="profesor_id" class="m-0">Profesor</label>
<div class="input-group mb-3">
    {!! Form::select('profesor_id', $profesor_list, old('profesor_id'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<label for="grupo_estable_id" class="m-0">Grupo Estable</label>
<div class="input-group mb-3">
    {!! Form::select('grupo_estable_id', $list_grupo_estable, old('grupo_estable_id'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>

<div class="form-group">
    <label for="nota_type" class="m-0">Tipo de nota final</label>
    {!! Form::select('nota_type', $tipo_list, old('nota_type'), [
        'id' => 'nota_type',
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<div class="form-group" id="div_escala_id" style="display:none">
    <label for="escala_id" id="label_escala_id" class="m-0">Escala de Evaluación</label>
    {!! Form::select('escala_id', $escala_list, old('escala_id'), [
        'id' => 'escala_id',
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="status_baremo" class="m-0">Baremo</label>
    {!! Form::select('status_baremo', $baremo_apply_list, old('status_baremo'), [
        'id' => 'status_baremo',
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>

<div class="form-group">
    <label for="status_official" class="font-weight-bold text-secondary m-0">En documentos oficiales</label>
    <div class="form-group">
        {!! Form::select('status_official', [true => 'SI', false => 'NO'], old('status_official'), [
            'class' => 'form-control',
            'id' => 'status_official',
            'placeholder' => 'Seleccione',
            'required',
        ]) !!}
    </div>
</div>

<div class="form-group">
    <label for="status_note_report" class="font-weight-bold text-secondary m-0">En Informe de Notas</label>
    <div class="form-group">
        {!! Form::select('status_note_report', [true => 'SI', false => 'NO'], old('status_note_report'), [
            'class' => 'form-control',
            'id' => 'status_note_report',
            'placeholder' => 'Seleccione',
            'required',
        ]) !!}
    </div>
</div>

<div class="form-group">
    <label for="description" class="m-0">Descripción</label>
    {!! Form::text('description', old('description'), [
        'class' => 'form-control',
        'placeholder' => 'Descripción',
        'id' => 'Descripción',
        'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="objetivo" class="m-0">Objetivo</label>
    {!! Form::text('objetivo', old('objetivo'), [
        'class' => 'form-control',
        'placeholder' => 'Objetivo',
        'id' => 'objetivo',
    ]) !!}
</div>

<div class="form-group">
    <label for="category" class="m-0">Category</label>
    {!! Form::select('category', $list_category, old('category'), [
        'id' => 'category',
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('#nota_type').change(function(e) {
                switch ($(this).val()) {
                    case "ACUMULATIVA":
                        $("#div_escala_id").fadeOut();
                        $("#label_escala_id").html('Escala de la nota final');
                        $("#div_escala_id").fadeIn();
                        break;
                    case "PROMEDIADA":
                        $("#div_escala_id").fadeOut();
                        $("#label_escala_id").html('Escala de las Evaluaciones');
                        $("#div_escala_id").fadeIn();
                        break;
                    default:
                        $("#div_escala_id").fadeOut();
                        break;
                }
            });
        });
    </script>
@endsection

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
