@extends('administracion.layouts.dashboard.app')

@section('title') - Listado de Representante @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-info">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right">

                    @include('administracion.representants.menus.index')

                </div>
                {{-- FIN Menu rapido --}}
                <h4>
                     <span title="Listado especial con botones de acción"><u>Listado</u></span> de Representantes
                </h4>
            </div>

            <div class="card-body">

                @include('administracion.representants.form.search',['route'=>'administracion.representants.crud','btn_pdf'=>'true'])

                <hr>

                @include('administracion.representants.table.crud')

            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function(){
            $('#btn_toprint').click(function (e) {
                e.preventDefault();
                var grado_id = $('#grado_id').val();	//console.log(ci_estudiant);
                var seccion_id = $('#seccion_id').val();	//console.log(ci_estudiant);
                var formally = $('#formally').val();	//console.log(ci_estudiant);
                var dataString = '?grado_id='+grado_id+'&seccion_id='+seccion_id+'&formally='+formally;
                var url = "{{ route('administracion.representants.libro.pdf') }}"+dataString;
                // window.open(url,'_self');
                window.open(url,'_blank');
          });
        });
    </script>
@endsection
