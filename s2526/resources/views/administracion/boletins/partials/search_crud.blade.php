<div class="card-header p-0 m-0 mb-3">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}

    <div class="row">
        <div class="col-12">
            <div class="form-row py-1">
                <div class="col-7">
                    <label for="estudiant_id" class="font-weight-bold m-0">Estudiante</label>
                    <div class="input-group">
                        <div class="input-group-append" style="z-index: 0;">
                            {!! Form::text('help_estudiant', $help_estudiant, [
                                'class' => 'form-control small',
                                'placeholder' => 'CI o nombre',
                                'id' => 'help_estudiant',
                            ]) !!}
                        </div>
                        {!! Form::select('estudiant_id', $list_estudiants, $estudiant_id, [
                            'class' => 'form-control',
                            'id' => 'estudiant_id',
                            'placeholder' => 'Seleccione',
                        ]) !!}
                    </div>
                </div>
                <div class="col-3">
                    <label for="grado_id" class="font-weight-bold m-0">Grado</label>
                    {!! Form::select('grado_id', $list_grado, $grado_id, [
                        'class' => 'form-control',
                        'id' => 'grado_id',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>
                <div class="col-2">
                    <label for="seccion_id" class="font-weight-bold m-0">Sección</label>
                    {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                        'class' => 'form-control',
                        'id' => 'seccion_id',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>
            </div>

            <div class="form-row py-1">
                <div class="col-5">
                    <label for="pensums_id" class="font-weight-bold m-0">Asignatura</label>
                    {!! Form::select('pensums_id', $list_pensum, $pensums_id, [
                        'class' => 'form-control',
                        'id' => 'pensums_id',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>
                <div class="col-5">
                    <label for="profesor_id" class="font-weight-bold m-0">Profesor</label>
                    {!! Form::select('profesor_id', $list_profesor, $profesor_id, [
                        'class' => 'form-control',
                        'id' => 'profesor_id',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>
                <div class="col-2">
                    <label for="lapso_id" class="font-weight-bold m-0">Lapso</label>
                    {!! Form::select('lapso_id', $list_lapso, $lapso_id, [
                        'class' => 'form-control',
                        'id' => 'lapso_id',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>
            </div>

            <div class="form-row py-1">
                <div class="col-2">
                    <label for="finicial" class="font-weight-bold m-0">Fecha Inicial</label>
                    {!! Form::date('finicial', $finicial, ['class' => 'form-control']) !!}
                </div>
                <div class="col-2">
                    <label for="ffinal" class="font-weight-bold m-0">Fecha Final</label>
                    {!! Form::date('ffinal', $ffinal, ['class' => 'form-control']) !!}
                </div>
                <div class="col-2">
                    <label for="enable_academic_index" class="font-weight-bold m-0">A.Indice</label>
                    {!! Form::select('enable_academic_index', ['SI' => 'SI', 'NO' => 'NO'], $enable_academic_index, [
                        'class' => 'form-control',
                        'id' => 'enable_academic_index',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>
                <div class="col-2">
                    <label for="observations" class="font-weight-bold m-0">Buscar</label>
                    <div class="btn-group btn-group-sm btn-block text-center" style="vertical-align: middle !important">
                        <button class="btn btn-primary my-2 my-sm-0 btn-block btn-lg" type="submit">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                        {{-- <a id="" class="btn btn-light" href="{{url()->current()}}" role="button" title="Refrescar la página">
                                <i class="fas fa-redo" aria-hidden="true"></i>
                            </a> --}}
                    </div>
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

            // fill select para pensum
            $("#grado_id").change(function() {
                var grado_id = $(this).val();
                console.log(grado_id);
                console.log('gradoBypensum/' + grado_id);
                $.ajax({
                        type: "GET",
                        url: "{{ route('administracion.ajax.fill.gradoBypensum', '') }}/" + grado_id,
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
                        $("#pensums_id").html(seccion_select);
                    })
                    .fail(function() {
                        console.log("error occured");
                    });

            });
        });

        $(function() {
            $('#estudiant_id').filterByText($('#help_estudiant'), true);
        });

        jQuery.fn.filterByText = function(textbox, selectSingleMatch) {
            return this.each(function() {
                var select = this;
                var options = [];
                $(select).find('option').each(function() {
                    options.push({
                        value: $(this).val(),
                        text: $(this).text()
                    });
                });
                $(select).data('options', options);
                $(textbox).bind('change keyup', function() {
                    var options = $(select).empty().scrollTop(0).data('options');
                    var search = $.trim($(this).val());
                    var regex = new RegExp(search, 'gi');

                    $.each(options, function(i) {
                        var option = options[i];
                        if (option.text.match(regex) !== null) {
                            $(select).append(
                                $('<option>').text(option.text).val(option.value)
                            );
                        }
                    });
                    if (selectSingleMatch === true &&
                        $(select).children().length === 1) {
                        $(select).children().get(0).selected = true;
                    }
                });
            });
        };
    </script>
@endsection
