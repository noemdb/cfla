{{-- @php $required_seccion = (!empty($required_seccion)) ? 'required':true ; @endphp
@php $required_lapso = (!empty($required_lapso)) ? 'required':true ; @endphp --}}

<div class="card-header p-0 m-0 mb-3">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}
    <div class="form-row font-weight-bold">
        <div class="col-4">Nombre/Identificador</div>
        <div class="col-3">Prosecución</div>
        {{-- <div class="col-2">N.P. Disponibles</div> --}}
        <div class="col-3">Preinscripción</div>
        <div class="col-2">&nbsp;</div>
    </div>
    <div class="form-row">
        <div class="col-4">
            {!! Form::text('search', $search, ['class' => 'form-control', 'placeholder' => 'Buscar Nombre o Identificador']) !!}
        </div>

        <div class="col-3">
            {!! Form::select('prosecucion_seccion_id', $list_prosecucion, $prosecucion_seccion_id, [
                'class' => 'form-control',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        {{-- <div class="col-2">
                {!! Form::select('status_enable',['SI'=>'SI'],$status_enable,['class' => 'form-control','placeholder' => 'Seleccione']) !!}
            </div> --}}

        <div class="col-3">
            {!! Form::select('status_preinscripcion', ['SI' => 'SI', 'NO' => 'NO'], $status_preinscripcion, [
                'class' => 'form-control',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>

        <div class="col-2">
            <div class="btn-group btn-group btn-block">
                <button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">
                    <i class="{{ $icon_menus['buscar'] ?? '' }} fa-1x"></i>
                    Buscar
                </button>
                <a id="" class="btn btn-light" href="{{ url()->current() }}" role="button"
                    title="Refrescar la página">
                    <i class="fas fa-redo" aria-hidden="true"></i>
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
@endsection
