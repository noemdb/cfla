<span class="font-weight-bold">
    Seleccione estudiante y grado
</span>
<div class="px-2 rounded border">
    <div class="row small">
        <div class="col">
            <div class="form-group">
                <label for="estudiant_id" class="m-0 font-weight-bold">Estudiante</label>
                {!! Form::select('estudiant_id', $list_estudiants, old('estudiant_id'), [
                    'class' => 'form-control',
                    'id' => 'estudiant_id',
                    'placeholder' => 'Seleccione',
                    'required' => 'required',
                ]) !!}
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="grado_id" class="m-0 font-weight-bold">Grado a preinscribir</label>
                {!! Form::select('grado_id', $list_grado, old('grado_id'), [
                    'class' => 'form-control',
                    'id' => 'grado_id',
                    'placeholder' => 'Seleccione',
                    'required' => 'required',
                ]) !!}
            </div>
        </div>
    </div>
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
