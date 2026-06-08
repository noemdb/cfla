@extends ('auth.layouts.login')

@section('content')
    <form class="form-signin small" method="POST" action="{{ route('password.email') }}"
        aria-label="{{ __('Reset Password') }}">
        @csrf

        <div class="row justify-content-center">

            <img class="mb-4" src="{{ asset('images/brand/144/1.png') }}" alt="" width="144" height="144">

            <h3 class="form-signin-heading ">Restablecer contraseña</h3>

        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <div class="form-label-group pb-2">

            <input type="text" id="inputEmail" name="email"
                class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="Correo Electrónico"
                value="{{ old('email') }}" required>
            <label for="inputEmail">Correo Electrónico</label>

            @if ($errors->has('email'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif

        </div>

        {!! Form::submit('Enviar', [
            'class' => 'btn-create btn btn-warning btn-block font-weight-bold',
            'id' => 'create',
        ]) !!}

        <hr>

        <div class="card  border rounded shadow-sm border border-info text-muted">
            <h4 class="card-title py-1 my-1 text-center">
                <i class="fa fa-info-circle text-info" aria-hidden="true"></i>
            </h4>
            <div class="card-body py-1 my-1 text-justify">
                Ingresa el correo electrónico asociado a tu cuenta en el sistema. En caso de no tenerlo,
                puedes ir a la

                <a href="{{ url('/') }}" class=" font-weight-bold"> web principal de la institución </a>

                y solicitar ayuda en la sección de <b>Soporte Técnico.</b>
            </div>
        </div>

        {{-- <div class="alert alert-success text-center" role="alert">
            <strong>Período Escolar 2020-2021</strong>
            {{ Form::hidden('pescolar_id', Session::get('pescolar_id')) }}
        </div> --}}

    </form>
@endsection
