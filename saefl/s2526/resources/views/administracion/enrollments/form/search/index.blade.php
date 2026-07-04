<div class="card-header p-0 m-0 mb-3">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}
    <div class="form-row font-weight-bold">
        <div class="col-6">Grado</div>
        <div class="col-4">Sección</div>
        <div class="col-2">&nbsp;</div>
    </div>
    <div class="form-row">
        <div class="col-6">
            {!! Form::select('grado_id', $list_grado, $grado_id, [
                'class' => 'form-control',
                'id' => 'grado_id',
                'placeholder' => 'Seleccione',
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
            <div class="btn-group btn-group btn-block">
                <button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    Buscar
                </button>
                {{-- <a id="" class="btn btn-light" href="{{url()->current()}}" role="button" title="Refrescar la página">
                        <i class="fas fa-redo" aria-hidden="true"></i>
                    </a> --}}
                @if (!empty($btn_toprint))
                    <a id="btn_toprint" class="btn btn-dark {{ $seccion ? null : 'disabled' }}" href="#"
                        role="button" title="Generar PDF">
                        <i class="fa fa-file-pdf" aria-hidden="true"></i>
                    </a>
                @endif
                {{-- @if (!empty($btn_toprint_lote))
                    <a id="btn_toprint_lote"  class="btn btn-light {{ ($estudiants->isEmpty()) ? 'disabled':null }}" href="#" role="button" title="Genera PDF de todos los grados del lapso seleccionado">
                            <i class="fa fa-print" aria-hidden="true"></i>
                        </a>
                    @endif --}}
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


@section('scripts')
    @parent
    <script>
        //btn para ir a editar los datos del EST
        $(document).ready(function() {
            $('#btn_toprint').click(function(e) {
                e.preventDefault();
                var grado_id = $('#grado_id').val(); //console.log(ci_estudiant);
                var seccion_id = $('#seccion_id').val(); //console.log(ci_estudiant);
                var url = '{{ route('administracion.enrollments.pdf.formatos', ['_gid_', '_sid_']) }}';
                url = url.replace('_gid_', grado_id);
                url = url.replace('_sid_', seccion_id);
                window.open(url, '_blank');
            });
        });
    </script>
@endsection
