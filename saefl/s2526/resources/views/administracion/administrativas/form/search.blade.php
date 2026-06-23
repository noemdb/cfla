{{-- @php $required_seccion = (!empty($required_seccion)) ? 'required':true ; @endphp
@php $required_lapso = (!empty($required_lapso)) ? 'required':true ; @endphp --}}

<div class="card-header p-0 m-0 mb-3 d-flex">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}
    <div class="container-fluid">

        <div class="row font-weight-bold">
            <div class="col-2">Nombre/Identificador</div>
            <div class="col-3">Plan de Pago/Afect.Inscripción</div>
            <div class="col-3">Grado/Sección/S. Activa/Desactiva</div>
            <div class="col-2">Preinscripción/Formalizado</div>
            <div class="col-2">&nbsp;</div>
        </div>
        <div class="row">
            <div class="col-2">
                {!! Form::text('search', $search, ['class' => 'form-control', 'placeholder' => 'Buscar Nombre o Identificador']) !!}
            </div>

            <div class="col-3">
                <div class="d-flex">
                    {!! Form::select('planpago_id', $list_planpago, $planpago_id, [
                        'class' => 'form-control',
                        'id' => 'planpago_id',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                    {!! Form::select('status_inscription_affects', ['true' => 'SI', 'false' => 'NO'], $status_inscription_affects, [
                        'class' => 'form-control',
                        'id' => 'status_inscription_affects',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>
            </div>

            <div class="col-3">
                <div class="d-flex">
                    {!! Form::select('grado_id', $list_grado, $grado_id, [
                        'class' => 'form-control',
                        'id' => 'grado_id',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                    {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                        'class' => 'form-control',
                        'id' => 'seccion_id',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                    {!! Form::select('status_active', ['true' => 'SI', 'false' => 'NO'], $status_active, [
                        'class' => 'form-control',
                        'id' => 'status_active',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>
            </div>

            <div class="col-2">
                <div class="d-flex">
                    {!! Form::select('status_preinscripcion', ['SI' => 'SI', 'NO' => 'NO'], $status_preinscripcion, [
                        'class' => 'form-control',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                    {!! Form::select('formally', ['SI' => 'SI', 'NO' => 'NO'], $formally, [
                        'class' => 'form-control',
                        'id' => 'formally',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>
            </div>

            <div class="col-2">
                <div class="btn-group btn-group btn-block w-full">
                    <button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">
                        {{-- <i class="fa fa-search" aria-hidden="true"></i> --}}
                        Buscar
                    </button>
                    <a id="" class="btn btn-light" href="{{ url()->current() }}" role="button"
                        title="Refrescar la página">
                        <i class="fas fa-redo" aria-hidden="true"></i>
                    </a>
                </div>
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
