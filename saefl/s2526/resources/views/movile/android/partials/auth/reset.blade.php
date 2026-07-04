<h1 class="h3 mb-3 fw-normal">
    {{-- <div class="fw-bold">Solicita el reinicio de tus credenciales</div> --}}
    <div class="small fw-light text-muted">Solicita el reinicio de tus credenciales.</div>
</h1>

<form class="form-signin small px-1" method="POST" action="{{ route('password.email') }}"
    aria-label="{{ __('Reset Password') }}">
    @csrf

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <div class="form-label-group pb-2 text-start">

        <input type="text" id="inputEmail" name="email"
            class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="Correo Electrónico"
            value="{{ old('email') }}" required>
        <label class="fw-light text-muted ms-1" for="inputEmail">Correo Electrónico</label>

        @if ($errors->has('email'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif

    </div>

    {!! Form::submit('Enviar', ['class' => 'btn btn-warning font-weight-bold w-100', 'id' => 'create']) !!}

    <hr>

    <div class="card  border rounded shadow-sm border border-info text-muted">
        <h4 class="card-title py-1 my-1 text-center">
            <i class="fa fa-info-circle text-info" aria-hidden="true"></i>
        </h4>
        <div class="card-body py-1 my-1 text-justify">
            Ingresa el correo electrónico asociado a tu cuenta en el sistema.
        </div>
    </div>

</form>
