@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-info">
                <h4>Retiros de estudiantes..</h4>
            </div>

            <div class="card-body">
                {!! Form::open([
                    'route' => 'administracion.retiros.crud',
                    'method' => 'GET',
                    'class' => 'pb-1',
                    'id' => 'form_search',
                    'role' => 'search',
                ]) !!}
                <div class="input-group">

                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="{{ $icon_menus['estudiante'] ?? '' }} fa-1x"></i>
                        </span>
                    </div>
                    {!! Form::text('search', $search, [
                        'class' => 'form-control',
                        'placeholder' => 'Identificador/Nombre',
                        'id' => 'search',
                    ]) !!}

                    <div class="input-group-append" style="z-index: 0;">
                        {!! Form::select(
                            'per_page',
                            ['10' => 10, '50' => 50, '100' => 100, '1000' => 1000, '10000' => 10000],
                            $per_page,
                            ['title' => 'Registros por página', 'class' => 'form-control', 'id' => 'per_page'],
                        ) !!}

                        <button class="btn btn-primary my-2 my-sm-0" type="submit">Buscar</button>
                        <a href="#" id="btn_reset" class="btn btn-info">
                            <i class="fas fa-undo"></i>
                        </a>
                        <a href="{{ route('administracion.registrarpagos.list.pagos.dw.excel') }}"
                            class="btn btn-success float-right" target="_blank">
                            <i class="fas fa-file-excel text-light" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="btn btn-danger float-right" target="_blank">
                            <i class="fa fa-file-pdf  text-light" aria-hidden="true"></i>
                        </a>
                    </div>

                </div>
                {!! Form::close() !!}

                @include('administracion.retiros.table.crud')

            </div>
        </div>
    </main>
    {!! Form::open([
        'route' => ['administracion.retiros.store', ':ESTUDIANT_ID'],
        'method' => 'POST',
        'id' => 'form-retirar',
        'role' => 'form',
    ]) !!}
    {!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset('js/models/retiros/retirar.js') }}"></script>
@endsection
@endsection

@section('scripts')
@parent
<script type="text/javascript">
    // script para realizar el borrado del registro
    $('.btn-confirm').click(function(e) {
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

    }); //fin del evento clic
</script>
@endsection
