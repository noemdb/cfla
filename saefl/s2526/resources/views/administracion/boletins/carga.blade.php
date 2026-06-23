@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-secondary">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.boletins.menus.carga')
                </div>

                <h4>Carga de Notas <b class="text-dark">POR ESTUDIANTE</b></h4>
                @if (! empty($pevaluacion) )
                    @php
                        $pevaluacion_id = $pevaluacion->id;
                        $pensum = $pevaluacion->pensum;
                        $seccion = $pevaluacion->seccion;
                        $lapso = $pevaluacion->lapso;
                        $grado = $pevaluacion->pensum->grado;
                        $estudiants = $seccion->estudiants_in;
                    @endphp
                    <span>||{{$grado->name ?? ''}} {{$seccion->name ?? ''}}| {{$lapso->name ?? ''}}| Asignatura: {{$pensum->asignatura->name ?? ''}}||</span>
                @endif

            </div>

            <div class="card-body bd-callout bd-callout-{{$grado->color ?? 'default'}} p-2 m-2">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                @php $pensum_id = (!empty($pevaluacion)) ? $pevaluacion->pensum->id : null  @endphp

                @if ($pensum_id)

                    @if (!empty($pevaluacion))
                        @include('administracion.boletins.table.carga')
                    @else
                        <div class="alert alert-danger text-center">
                            <b>NO HAY PLAN DE EVALUACIONES REGISTRADOS.</b>
                            Haz Clic
                            <a class="btn-link"
                                href="{{ route('administracion.pevaluacions.create',['grado_id'=>$grado_id,'seccion_id'=>$seccion_id,'pensum_id'=>$pensum_id,'lapso_id'=>$lapso_id]) }}"
                                role="button">
                                aquí
                            </a>
                            para registrar uno.
                        </div>
                    @endif
                @else
                    <div class="alert alert-danger text-center">
                        <b>NO HAY PENSUM REGISTRADO PARA EL GRADO SELECCIONADO.</b>
                        Haz Clic
                        <a class="btn-link"
                            href="{{ route('administracion.configuraciones.pensums.index',['grado_id'=>$grado_id]) }}" role="button">
                            aquí
                        </a>
                        para registrar uno.
                    </div>
                @endif

            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function(){
        $("#grado_id").change(function(){
            var grado_id = $(this).val();console.log(grado_id);console.log('gradoByseccion/'+grado_id);
            $.ajax({
            type: "GET",
            url: "{{ route('administracion.ajax.fill.gradoByseccion','') }}/"+grado_id,
            data: { grado_id: grado_id }
            })
            .done(function( data ) {
            console.log(data);
            var seccion_select = '<option value="">Seleccione</option>'
            for (var i=0; i<data.length;i++)
            seccion_select+='<option value="'+data[i].id+'">'+data[i].name+'</option>';
            $("#seccion_id").html(seccion_select);
            })
            .fail(function() {
                console.log( "error occured" );
            });

        });
        });
    </script>
@endsection
