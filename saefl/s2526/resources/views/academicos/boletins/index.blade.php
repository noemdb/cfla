@extends('academicos.layouts.dashboard.app')

@section('main')
    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right pt-2">
                    @include('academicos.boletins.menus.index')
                </div>

                <h3 class="mb-0 pb-0">
                    <i class="{{ $icon_menus['notas'] ?? '' }} text-secondary" aria-hidden="true"></i>
                    Registro de Notas
                </h3>

                <span class="text-muted small text-capitalize font-light">
                    {{ Auth::user()->profesor->fullname }}
                </span>

            </div>

            <div class="card-body p-1 m-1">

                {!! Form::open([
                    'route' => 'academicos.boletins.index',
                    'method' => 'GET',
                    'class' => 'pb-3',
                    'role' => 'search',
                ]) !!}
                <div class="form-inline">
                    <div class="w-50">Grado</div>
                    <div class="w-50">Sección</div>
                </div>
                {{-- <label for="grado_id" class="m-0">Grado/Sección</label> --}}
                <div class="input-group">
                    {!! Form::select('grado_id', $list_grado, $grado_id, [
                        'class' => 'form-control',
                        'id' => 'grado_id',
                        'placeholder' => 'Todos',
                    ]) !!}
                    {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                        'class' => 'form-control',
                        'id' => 'seccion_id',
                        'placeholder' => 'Todas',
                    ]) !!}
                    <div class="input-group-append" style="z-index: 0;">
                        <button class="btn btn-primary my-2 my-sm-0" type="submit">Buscar</button>
                    </div>
                </div>
                {!! Form::close() !!}

                <h6 class="pb-2"><u>Listado</u> de Asignaturas</h6>
                @include('academicos.boletins.table.index')
            </div>
        </div>
    </main>
@endsection

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
