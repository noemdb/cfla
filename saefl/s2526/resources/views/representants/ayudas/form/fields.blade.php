<div class="form-group">
    <label for="tipo_id" class="m-0 font-weight-bold">Tipo</label>
    {!! Form::select('tipo_id', $list_tinscripcion, old('tipo_id'), [
        'class' => 'form-control',
        'id' => 'tipo_id',
        'placeholder' => 'Seleccione',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="grado_id" class="m-0 font-weight-bold">Grado</label>
    {!! Form::select('grado_id', $list_grado, $grado_id, [
        'class' => 'form-control',
        'id' => 'grado_id',
        'placeholder' => 'Seleccione',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="seccion_id" class="m-0 font-weight-bold">Sección</label>
    {!! Form::select('seccion_id', $list_seccion, old('seccion_id'), [
        'class' => 'form-control',
        'id' => 'seccion_id',
        'placeholder' => 'Seleccione',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="escolaridad_id" class="m-0 font-weight-bold">Escolaridad</label>
    {!! Form::select('escolaridad_id', $list_escolaridad, old('escolaridad_id'), [
        'class' => 'form-control',
        'id' => 'escolaridad_id',
        'placeholder' => 'Seleccione',
        'required' => 'required',
    ]) !!}
</div>
<div class="form-group">
    <label for="programacion_id" class="m-0 font-weight-bold">Programación - Lapsos/Períodos a cursar</label>
    {!! Form::select('programacion_id', $list_programacion, old('programacion_id'), [
        'class' => 'form-control',
        'id' => 'programacion_id',
        'placeholder' => 'Seleccione',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="programacion_id" class="m-0 font-weight-bold">Grupo Estable</label>
    {!! Form::select('grupo_estable_id', $list_grupo_estables, old('grupo_estable_id'), [
        'class' => 'form-control',
        'id' => 'grupo_estable_id',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>

<div class="form-group">
    <label for="observations" class="m-0 font-weight-bold">Observaciones</label>
    {!! Form::text('observations', old('observations'), [
        'class' => 'form-control',
        'placeholder' => 'Observaciones',
        'id' => 'observations',
    ]) !!}
</div>

{{--
  @component('representants.elements.forms.input')
    @slot('name', 'observations')
    @slot('value', old('observations'))
    @slot('label', 'Observaciones')
@endcomponent
--}}

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
                        url: "{{ route('representants.ajax.fill.gradoByseccion', '') }}/" + grado_id,
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
