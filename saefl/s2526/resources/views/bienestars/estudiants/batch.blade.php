@extends('bienestars.layouts.dashboard.app')

@section('title')
    - Listado de Estudiantes
@endsection

@section('main')
    <main role="main">

        <div class="card card-primary mt-2">
            <div class="card-header alert-info">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">

                    @include('bienestars.student_records.menu.batch')
                    {{-- /home/nuser/code/s2223/resources/views/bienestars/student_records/menu/batch.blade.php --}}

                </div>
                {{-- FIN Menu rapido --}}
                <h4>
                    {{-- <u title="Listado especial con botones de acción">Listado</u> de Estudiantes formalmente inscritos --}}
                    <u title="Listado especial con botones de acción">Listado</u> de Estudiantes
                </h4>
            </div>

            <div class="card-body">

                {!! Form::open([
                    'route' => 'bienestars.student_records.batch',
                    'method' => 'get',
                    'class' => 'pb-2',
                    'role' => 'search',
                ]) !!}
                <div class="form-row font-weight-bold">
                    <div class="col-4">Grado</div>
                    <div class="col-4">Sección</div>
                    <div class="col-2">Estado</div>
                    <div class="col-4">&nbsp;</div>
                </div>
                <div class="form-row">
                    <div class="col-4">
                        {!! Form::select('grado_id', $list_grado, $grado_id, [
                            'class' => 'form-control form-control-sm',
                            'id' => 'grado_id',
                            'placeholder' => 'Seleccione',
                        ]) !!}
                    </div>
                    <div class="col-4">
                        {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                            'class' => 'form-control form-control-sm',
                            'id' => 'seccion_id',
                            'placeholder' => 'Seleccione',
                        ]) !!}
                    </div>
                    <div class="col-4">
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

                @include('bienestars.student_records.table.batch')
                {{-- /home/nuser/code/s2223/resources/views/bienestars/student_records/table/batch.blade.php --}}

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
                var grado_id = $('#grado_id').val();
                var seccion_id = $('#seccion_id').val();
                var dataString = '?grado_id=' + grado_id + '&seccion_id=' + seccion_id;
                var url = "{{ route('bienestars.student_records.pdf.batch') }}" + dataString;
                window.open(url, '_blank');
            });
        });
    </script>
@endsection
