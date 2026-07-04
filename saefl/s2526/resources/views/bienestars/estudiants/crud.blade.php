@extends('bienestars.layouts.dashboard.app')

@section('title')
    Historial del estudiante digitales, Sección Bienestar Estudiantil - {{ Auth::user()->rol ?? '' }}
@endsection

@section('main')
    <main role="main">

        <div class="card card-primary mt-2">
            <div class="card-header alert-info">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('livewire.bienestar.estudiant.menu.index')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>
                    {{-- <u title="Listado especial con botones de acción">Listado</u> de Estudiantes formalmente inscritos --}}
                    <u title="Listado especial con botones de acción">Listado</u> de Estudiantes
                </h4>
            </div>

            <div class="card-body">

                {!! Form::open([
                    'route' => 'bienestars.estudiants.crud',
                    'method' => 'GET',
                    'class' => 'pb-2',
                    'role' => 'search',
                ]) !!}
                <div class="form-row font-weight-bold">
                    <div class="col-2">Grado</div>
                    <div class="col-2">Sección</div>
                    {{-- <div class="col-2">Estado</div> --}}
                    <div class="col-2">Formalizado</div>
                    {{-- <div class="col-2">Formalizado</div> --}}
                    <div class="col-2">I.Administrativa</div>
                    <div class="col-2">I.Académica</div>
                    <div class="col-2">&nbsp;</div>
                </div>
                <div class="form-row">
                    <div class="col-2">
                        {!! Form::select('grado_id', $list_grado, $grado_id, [
                            'class' => 'form-control form-control-sm',
                            'id' => 'grado_id',
                            'placeholder' => 'Seleccione',
                        ]) !!}
                    </div>

                    <div class="col-2">
                        {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                            'class' => 'form-control form-control-sm',
                            'id' => 'seccion_id',
                            'placeholder' => 'Seleccione',
                        ]) !!}
                    </div>

                    <div class="col-2">
                        {!! Form::select('formally', ['SI' => 'SI', 'NO' => 'NO'], $formally, [
                            'class' => 'form-control form-control-sm',
                            'id' => 'formally',
                            'placeholder' => 'Seleccione',
                        ]) !!}
                    </div>

                    <div class="col-2">
                        {!! Form::select('status_inscription_affects', ['true' => 'SI'], $status_inscription_affects, [
                            'class' => 'form-control form-control-sm',
                            'id' => 'formally',
                            'placeholder' => 'Seleccione',
                        ]) !!}
                    </div>

                    <div class="col-2">

                        {!! Form::select('seccion_status_active', ['true' => 'SI'], $seccion_status_active, [
                            'class' => 'form-control form-control-sm',
                            'id' => 'formally',
                            'placeholder' => 'Seleccione',
                        ]) !!}

                    </div>

                    <div class="col-2">
                        <div class="btn-group btn-group-sm btn-block" role="group" aria-label="Basic example">
                            <button class="btn btn-primary btn-block" type="submit">
                                <i class="fa fa-search" aria-hidden="true"></i>
                                Buscar
                            </button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}

                <hr>

                @include('bienestars.estudiants.table.crud')

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
