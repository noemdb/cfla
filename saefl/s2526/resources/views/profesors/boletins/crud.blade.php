@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-info">
                <h3>
                    {{-- Asignar plan de pago a los Estudiantes para el período escolar actual<br> --}}
                    Retiros de estudiantes
                    {{-- <small class="small text-dark float-right">
                        <strong><span id="user_estudiant">{{$estudiants->count()}}</span> Estudiantes</strong>
                    </small> --}}

                </h3>
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('admin.elements.messeges.oper_ok')

                    @include('administracion.retiros.table.crud')

            </div>
        </div>
    </main>
    {!! Form::open(['route' => ['administracion.retiros.store',':ESTUDIANT_ID'], 'method' => 'POST', 'id'=>'form-retirar', 'role'=>'form']) !!}
    {!! Form::close() !!}
    @section('scripts')
        @parent
        <script src="{{ asset("js/models/retiros/retirar.js") }}"></script>
    @endsection
@endsection

@section('scripts')
    @parent
    <script  type="text/javascript">

        // script para realizar el borrado del registro
        $('.btn-confirm').click(function (e) {
            e.preventDefault();

            //console.log('llego');

            // r = confirm("Estas seguro de realizar esta acción?");
            Swal.fire({
                title: 'Estas seguro de realizar esta acción?',
                text: "No podrás revertir esta acción",
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: 'No, cancelar!',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, Estoy seguro!'
            }).then((result) => {
                if (result.value) {
                    // Swal.fire(
                    // 'Deleted!',
                    // 'Your file has been deleted.',
                    // 'success'
                    // )
                    $(this).closest('form').submit();
                }
            })

        });//fin del evento clic
    </script>
@endsection
