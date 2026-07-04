@if ($errors->any())
    <div class="alert alert-danger fade show shadow-sm py-1 my-1" role="alert">
        <button type="button" class="close p-1 m-1 float-right" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <div class="font-weight-bold text-danger">Errores encontrados, revise detalladamente</div>
        <hr>
        <ul class="px-1">
            @foreach ($errors->all() as $error)
                <li class="small font-weight-bold">{{ $error }}</li>
            @endforeach
        </ul>
        {{-- <button type="button" class="close p-1 m-1 float-right" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button> --}}
    </div>
@endif

{{-- INI Mensaje flash sobreo operaciones con base de datos --}}
@if (Session::has('operp_no_ok'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        {{ Session::get('operp_no_ok')}}
    </div>
@endif
{{-- FIN Mensaje flash sobreo operaciones con base de datos --}}
