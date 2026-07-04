<div class="card-header p-0 m-0 mb-3">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-2 m-1', 'role' => 'search']) !!}

    <!-- CONTENEDOR FLEX DE ANCHO COMPLETO -->
    <div class="d-flex flex-wrap w-100 align-items-end">

        <!-- CÉDULA -->
        <div class="d-flex flex-column flex-grow-1 mr-2 mb-2" style="min-width: 130px;">
            <label class="font-weight-bold small mb-1">Cédula</label>
            {!! Form::text(
                'ci_representant',
                $ci_representant ?? null,
                [
                    'class' => 'form-control form-control-sm',
                    'placeholder' => 'Buscar cédula...',
                    'autocomplete' => 'off'
                ]
            ) !!}
        </div>

        <!-- GRADO / SECCIÓN -->
        <div class="d-flex flex-column flex-grow-1 mr-2 mb-2" style="min-width: 210px;">
            <label class="font-weight-bold small mb-1">Grado / Sección</label>
            <div class="d-flex">

                {!! Form::select(
                    'grado_id',
                    $list_grado,
                    $grado_id,
                    [
                        'class' => 'form-control form-control-sm mr-1 flex-grow-1',
                        'id'    => 'grado_id',
                        'placeholder' => 'Grado'
                    ]
                ) !!}

                {!! Form::select(
                    'seccion_id',
                    $list_seccion,
                    $seccion_id,
                    [
                        'class' => 'form-control form-control-sm flex-grow-1',
                        'id'    => 'seccion_id',
                        'placeholder' => 'Sección'
                    ]
                ) !!}

            </div>
        </div>

        <!-- FORMALIZADO -->
        <div class="d-flex flex-column flex-grow-1 mr-2 mb-2" style="min-width: 140px;">
            <label class="font-weight-bold small mb-1">Formalizado</label>
            {!! Form::select(
                'formally',
                ['SI' => 'SI', 'NO' => 'NO'],
                $formally,
                [
                    'class' => 'form-control form-control-sm',
                    'id'    => 'formally',
                    'placeholder' => 'Seleccione'
                ]
            ) !!}
        </div>

        <!-- INSCRIPCIÓN ADMINISTRATIVA -->
        <div class="d-flex flex-column flex-grow-1 mr-2 mb-2" style="min-width: 170px;">
            <label class="font-weight-bold small mb-1">I. Administrativa</label>
            {!! Form::select(
                'status_inscription_affects',
                ['true' => 'SI', 'false' => 'NO'],
                $status_inscription_affects,
                [
                    'class' => 'form-control form-control-sm',
                    'id'    => 'status_inscription_affects',
                    'placeholder' => 'Seleccione'
                ]
            ) !!}
        </div>

        <!-- INSCRIPCIÓN ACADÉMICA -->
        <div class="d-flex flex-column flex-grow-1 mr-2 mb-2" style="min-width: 170px;">
            <label class="font-weight-bold small mb-1">I. Académica</label>
            {!! Form::select(
                'status_active',
                ['true' => 'SI', 'false' => 'NO'],
                $status_active,
                [
                    'class' => 'form-control form-control-sm',
                    'id'    => 'status_active',
                    'placeholder' => 'Seleccione'
                ]
            ) !!}
        </div>

        <!-- BOTÓN -->
        <div class="d-flex flex-column mb-2" style="min-width: 120px;">
            <label class="font-weight-bold small mb-1">Acción</label>
            <button class="btn btn-primary btn-sm btn-block" type="submit">
                <i class="fa fa-search"></i> Buscar
            </button>
        </div>

    </div>

    {!! Form::close() !!}
</div>

@section('scripts')
    @parent
    <script>
        $(document).ready(function(){

            $("#grado_id").change(function(){
                var grado_id = $(this).val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.fill.gradoByseccion', '') }}/" + grado_id,
                    data: { grado_id: grado_id }
                })
                .done(function(data){
                    var seccion_select = '<option value="">Seleccione</option>';
                    for (var i=0; i < data.length; i++) {
                        seccion_select += '<option value="'+data[i].id+'">'+data[i].name+'</option>';
                    }
                    $("#seccion_id").html(seccion_select);
                })
                .fail(function(){
                    console.log("error occured");
                });
            });

        });
    </script>
@endsection
