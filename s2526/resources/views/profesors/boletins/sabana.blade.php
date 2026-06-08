@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-secondary">
                <div class="btn-group float-right pt-0 pb-2">
                    {{-- @include('administracion.boletins.menus.index') --}}
                </div>

                <h4>Planilla de Registro de Notas <b class="text-dark">POR LAPSO</b></h4>

            </div>

            <div class="card-body bd-callout bd-callout-{{ $grado->color ?? 'default' }} p-2 m-2">

                {!! Form::open([
                    'route' => 'administracion.boletins.sabana',
                    'method' => 'GET',
                    'class' => 'pb-3',
                    'role' => 'search',
                ]) !!}
                <div class="form-inline">
                    Grado/Sección/Lapso
                    {{-- <div class="w-25">Grado</div>
                        <div class="w-25">Sección</div>
                        <div class="w-25">Lapso</div> --}}
                </div>
                {{-- <label for="grado_id" class="m-0">Grado/Sección</label> --}}
                <div class="input-group">
                    {!! Form::select('grado_id', $list_grado, $grado_id, [
                        'class' => 'form-control',
                        'id' => 'grado_id',
                        'placeholder' => 'Seleccione',
                        'required',
                    ]) !!}
                    {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                        'class' => 'form-control',
                        'id' => 'seccion_id',
                        'placeholder' => 'Seleccione',
                        'required',
                    ]) !!}
                    {!! Form::select('lapso_id', $list_lapso, $lapso_id, [
                        'class' => 'form-control',
                        'id' => 'lapso_id',
                        'placeholder' => 'Seleccione',
                        'required',
                    ]) !!}
                    <div class="input-group-append">
                        <button class="btn btn-primary my-2 my-sm-0" type="submit">Buscar</button>
                        <button class="btn btn-success my-2 my-sm-0" type="button">
                            <i class="fas fa-file-excel" aria-hidden="true"></i>
                        </button>
                        <button id="btn_toprint" class="btn btn-danger my-2 my-sm-0" type="button">
                            <i class="fa fa-file-pdf" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                {!! Form::close() !!}

                @if ($pensums->IsNotEmpty())
                    {{-- {{$pensums ?? ''}} --}}
                    @php $estudiants = (!empty($seccion))  ? $seccion->estudiants_in:null; @endphp
                    @include('profesors.boletins.table.sabana')
                @endif

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
                $.ajax({
                        type: "GET",
                        url: "{{ route('administracion.ajax.fill.gradoByseccion', '') }}/" + grado_id,
                        data: {
                            grado_id: grado_id
                        }
                    }).done(function(data) {
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

        //btn para ir a editar los datos del EST
        $(document).ready(function() {
            $('#btn_toprint').click(function(e) {
                e.preventDefault();
                var grado_id = $('#grado_id').val(); //console.log(ci_estudiant);
                var seccion_id = $('#seccion_id').val(); //console.log(ci_estudiant);
                var lapso_id = $('#lapso_id').val(); //console.log(ci_estudiant);
                var url = '{{ route('administracion.boletins.sabana.pdf', ['_gid_', '_sid_', '_lid_']) }}';
                url = url.replace('_gid_', grado_id);
                url = url.replace('_sid_', seccion_id);
                url = url.replace('_lid_', lapso_id);
                window.open(url, '_blank');
            });
        });
    </script>
@endsection
