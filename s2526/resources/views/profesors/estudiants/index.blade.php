@extends('profesors.layouts.dashboard.app')

@section('title') - Listado de Representante @endsection

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">
            <div class="card-header alert-info">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right">

                    @include('profesors.representants.menus.index')

                </div>
                {{-- FIN Menu rapido --}}
                <h4>
                     <span title="Listado especial con botones de acción"><u>Listado</u></span> de Estudiantes que pertenecen a la guiatura.
                </h4>
            </div>

            <div class="card-body">

                {{-- @include('profesors.representants.partials.search',['route'=>'profesors.representants.index']) --}}

                {{-- <hr> --}}

                @include('profesors.estudiants.table.index')

            </div>
        </div>
    </main>

@endsection

{{-- @section('scripts')
    @parent
    <script>
        $(document).ready(function(){
            $('#btn_toprint').click(function (e) {
                e.preventDefault();
                var grado_id = $('#grado_id').val();	//console.log(ci_estudiant);
                var seccion_id = $('#seccion_id').val();	//console.log(ci_estudiant);
                var formally = $('#formally').val();	//console.log(ci_estudiant);
                var dataString = '?grado_id='+grado_id+'&seccion_id='+seccion_id+'&formally='+formally;
                var url = "{{ route('profesors.representants.libro.pdf') }}"+dataString;
                // window.open(url,'_self');
                window.open(url,'_blank');
          });
        });
    </script>
@endsection --}}
