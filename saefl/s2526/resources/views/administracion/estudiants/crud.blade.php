@extends('administracion.layouts.dashboard.app')

@section('title')
    - Listado de Estudiantes
@endsection

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-info">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">

                    @include('administracion.estudiants.menus.crud')

                </div>
                {{-- FIN Menu rapido --}}
                <h4>
                    {{-- <u title="Listado especial con botones de acción">Listado</u> de Estudiantes formalmente inscritos --}}
                    <u title="Listado especial con botones de acción">Listado</u> de Estudiantes
                </h4>
            </div>

            <div class="card-body">

                {!! Form::open([
                    'route' => 'administracion.estudiants.crud',
                    'method' => 'GET',
                    'class' => 'pb-2',
                    'role' => 'search',
                ]) !!}
                <div class="form-row font-weight-bold">
                    <div class="col-2">Identificador</div>
                    <div class="col-2">Grado/Sección</div>
                    <div class="col-2">Planes de pago</div>
                    <div class="col-2">Formalizado/Prosecución</div>
                    <div class="col-2">I.Administrativa/I.Académica</div>

                    <div class="col-2">&nbsp;</div>
                </div>
                <div class="form-row">
                    <div class="col-2">
                        {!! Form::text('ci_estudiant', $ci_estudiant, [
                            'class' => 'form-control form-control-sm',
                            'placeholder' => 'Buscar por Cédula',
                        ]) !!}
                    </div>
                    <div class="col-2">
                        <div class="btn-group btn-group-sm btn-block" role="group" aria-label="Basic example">
                            {!! Form::select('grado_id', $list_grado, $grado_id, [
                                'class' => 'form-control form-control-sm',
                                'id' => 'grado_id',
                                'placeholder' => 'Seleccione',
                            ]) !!}
                            {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                                'class' => 'form-control form-control-sm',
                                'id' => 'seccion_id',
                                'placeholder' => 'Seleccione',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-2">
                        {!! Form::select('planpago_id', $planpago_list, $planpago_id, [
                            'class' => 'form-control form-control-sm',
                            'id' => 'planpago_id',
                            'placeholder' => 'Seleccione',
                        ]) !!}
                    </div>
                    <div class="col-2">
                        <div class="btn-group btn-group-sm btn-block">
                            {!! Form::select('formally', ['SI' => 'SI', 'NO' => 'NO'], $formally, [
                                'class' => 'form-control form-control-sm',
                                'id' => 'formally',
                                'placeholder' => 'Seleccione',
                            ]) !!}
                            {!! Form::select('status_prosecution', [true => 'SI'], $status_prosecution, [
                                'class' => 'form-control form-control-sm',
                                'id' => 'formally',
                                'placeholder' => 'Seleccione',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="btn-group btn-group-sm btn-block">
                            {!! Form::select('status_inscription_affects', ['true' => 'SI'], $status_inscription_affects, [
                                'class' => 'form-control form-control-sm',
                                'id' => 'formally',
                                'placeholder' => 'Seleccione',
                            ]) !!}
                            {!! Form::select('seccion_status_active', ['true' => 'SI'], $seccion_status_active, [
                                'class' => 'form-control form-control-sm',
                                'id' => 'formally',
                                'placeholder' => 'Seleccione',
                            ]) !!}
                        </div>
                    </div>

                    <div class="col-2">
                        <div class="btn-group btn-group-sm btn-block" role="group" aria-label="Basic example">
                            <button class="btn btn-primary btn-block" type="submit">
                                <i class="fa fa-search" aria-hidden="true"></i>
                                Buscar
                            </button>
                            <button class="btn btn-dark btn-sm" type="button" id="btn_toprint">
                                <i class="fa fa-print" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}

                <hr>

                @include('administracion.estudiants.table.crud')

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
                        url: "{{ route('administracion.ajax.fill.gradoByseccion', '') }}/" + grado_id,
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

            $('#btn_toprint').click(function(e) {
                e.preventDefault();
                var grado_id = $('#grado_id').val(); //console.log(ci_estudiant);
                var seccion_id = $('#seccion_id').val(); //console.log(ci_estudiant);
                var dataString = '?grado_id=' + grado_id + '&seccion_id=' + seccion_id;
                var url = "{{ route('administracion.estudiants.listado.pdf') }}" + dataString;
                // window.open(url,'_self');
                window.open(url, '_blank');
            });
        });
    </script>
@endsection
