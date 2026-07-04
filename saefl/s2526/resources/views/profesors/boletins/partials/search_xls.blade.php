<div class="card-header p-0 m-0 mb-3">
    {!! Form::open([
        'route' => $route,
        'method' => 'POST',
        'class' => 'p-1 m-1',
        'role' => 'search',
        'files' => 'true',
        'enctype' => 'multipart/form-data',
    ]) !!}
    <div class="form-row font-weight-bold">
        <div class="col-2">Grado</div>
        <div class="col-2">Sección</div>
        <div class="col-2">Lapso</div>
        <div class="col-2">Asignatura</div>
        <div class="col-3">Archivo XLS</div>
        <div class="col-1">&nbsp;</div>
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
        <div class="col-2">
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

        <div class="col-1">
            <div class="btn-group btn-group btn-block">
                <button class="btn btn-primary my-2 my-sm-0" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
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
                var grado_id = $(this)
            .val(); //console.log(grado_id); console.log('gradoByseccion/'+grado_id);
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

        // $(document).ready(function(){
        //      $("#grado_id").change(function(){
        //         var grado_id = $(this).val();console.log(grado_id);console.log('gradoBypensum/'+grado_id);
        //         $.ajax({
        //             type: "GET",
        //             url: "{{ route('administracion.ajax.fill.gradoBypensum', '') }}/"+grado_id,
        //             data: { grado_id: grado_id }
        //         })
        //         .done(function( data ) {
        //             console.log(data);
        //             var seccion_select = '<option value="">Seleccione</option>'
        //             for (var i=0; i<data.length;i++)
        //                 seccion_select+='<option value="'+data[i].id+'">'+data[i].name+'</option>';
        //             $("#pensum_id").html(seccion_select);
        //         })
        //         .fail(function() {
        //             console.log( "error occured" );
        //         });

        //     });
        // });
    </script>
@endsection
