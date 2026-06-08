@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main">

        <div class="card card-primary mt-2">


            <div class="card-header  alert-info">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.pevaluacions.menus.index')
                </div>
                <h3>
                    <i class="{{ $icon_menus['pevaluacion'] ?? '' }} text-primary" aria-hidden="true"></i>
                    Plan de Evaluación por Grado/Asignatura/Lapso
                </h3>
            </div>

            <div class="card-body bd-callout bd-callout-{{ $grado->color ?? 'default' }} p-2 m-2">

                {!! Form::open([
                    'route' => 'administracion.pevaluacions.carga',
                    'method' => 'GET',
                    'class' => 'pb-2',
                    'role' => 'search',
                ]) !!}
                <label for="grado_id" class="m-0">Grado</label>
                <div class="input-group">
                    {!! Form::select('grado_id', $list_grado, $grado->id, [
                        'class' => 'form-control',
                        'id' => 'grado_id',
                        'placeholder' => 'Seleccione',
                        'required' => 'required',
                    ]) !!}
                    {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                        'class' => 'form-control',
                        'id' => 'seccion_id',
                        'required' => 'required',
                    ]) !!}
                    <div class="input-group-append" style="z-index: 0;">
                        <button class="btn btn-primary my-2 my-sm-0" type="submit">Buscar</button>
                    </div>
                </div>
                {!! Form::close() !!}

                @php $pensums = (!empty($grado->pensums->first())) ? $grado->pensums : null; @endphp

                @if ($pensums)
                    @include('administracion.pevaluacions.table.carga')
                @else
                    <div class="alert alert-danger">
                        <b>NO HAY PENSUM REGISTRADO PARA EL GRADO SELECCIONADO [{{ $grado->name ?? '' }}]. </b>
                        Haz clic <a name="" id="" class="btn-link"
                            href="{{ route('administracion.configuraciones.pensums.index') }}" role="button">aquí</a>
                        para registrar uno.
                    </div>
                @endif

                <button class="btn btn-primary btn-block" type="submit">
                    <i class="fa fa-save" aria-hidden="true"></i>
                    Guargar
                </button>

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

            // sumit todos los formularios
            $('#all_sumit').click(function(e) {
                e.preventDefault();
                $("#number_i_pay").val("");
                $("#ci").val("");
            });

        });
    </script>
@endsection
