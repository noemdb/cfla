@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-secondary">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.boletins.menus.carga')
                </div>

                <h4>Evaluación descriptiva <b class="text-dark">por estudiante</b></h4>

            </div>

            <div class="card-body bd-callout bd-callout-{{$grado->color ?? 'default'}} p-2 m-2">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.boletins.table.ecualitativa')

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
