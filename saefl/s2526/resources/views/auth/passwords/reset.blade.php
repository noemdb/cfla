@extends ('auth.layouts.login')

@section('content')
    <form class="form-signin small" method="POST" action="{{ route('password.request') }}"
        aria-label="{{ __('Reset Password') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="row justify-content-center">

            <img class="mb-4" src="{{ asset('images/brand/144/1.png') }}" alt="" width="144" height="144">

            <h3 class="form-signin-heading ">Establcer contraseña</h3>

        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <div class="form-label-group pb-2">

            <label for="email" class="col-md-4 col-form-label text-md-right">Correo Electrónico</label>

            <div class="form-control  alert-secondary text-muted text-center">
                <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                {{ $email ?? old('email') }}
            </div>

            @if ($errors->has('email'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif

        </div>

        <div class="form-label-group">
            <input type="password" id="password" name="password"
                class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="Contraseña"
                value="{{ old('password') }}" required>
            <label for="password">Contraseña</label>


            @if ($errors->has('password'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif

        </div>

        <div class="form-label-group">

            <input type="password" id="password_confirmation" name="password_confirmation"
                class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
                placeholder="Repetir Contraseña" required>
            <label for="password_confirmation">Repetir Contraseña</label>

            @if ($errors->has('password_confirmation'))
                <div class="invalid-feedback" style="width: 100%;">
                    <span class="help-block">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                </div>
            @endif

        </div>
        {!! Form::submit('Establcer', [
            'class' => 'btn-create btn btn-primary btn-block font-weight-bold',
            'id' => 'create',
        ]) !!}

        <hr>

        <div class="card  border rounded shadow-sm border border-info text-muted">
            <h4 class="card-title py-1 my-1 text-center">
                <i class="fa fa-info-circle text-info" aria-hidden="true"></i>
            </h4>
            <div class="card-body py-1 my-1 text-justify">
                Ingresa su nueva contraseña cumpliendo con los siguientes requerimientos:

                <ul>
                    <li>8 o mas caracteres</li>
                    <li>Evite usar espacios</li>
                    <li>Haga una combinación de letras y números</li>
                </ul>
            </div>
        </div>


        {{-- <div class="alert alert-success text-center" role="alert">
            <strong>Período Escolar 2020-2021</strong>
            {{ Form::hidden('pescolar_id', Session::get('pescolar_id')) }}
        </div> --}}

    </form>
@endsection
