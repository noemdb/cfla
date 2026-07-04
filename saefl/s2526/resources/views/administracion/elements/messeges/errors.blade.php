{{-- INI Mensaje flash sobreo operaciones con base de datos --}}
@if (Session::has('db_errors'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        {!! Session::get('db_errors') !!}
    </div>
@endif
{{-- FIN Mensaje flash sobreo operaciones con base de datos --}}
