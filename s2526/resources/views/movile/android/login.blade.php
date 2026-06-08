

<form class="form-signin small px-1" method="POST" action="{{ route('movile.android.login') }}">
    {{ csrf_field() }}
    {{ Form::hidden('redirect', 'movile/android/welcome') }}

    <h1 class="h3 mb-3 fw-normal">
        <div class="fw-bold">Inicia tu sesión</div>
        <div class="small fw-light text-muted">con tus datos de acceso</div>
    </h1>

    <div class="form-floating">
        <input type="text" name="username" class="form-control {{ $errors->has('username') ? ' is-invalid' : '' }}"
            id="floatingInput" placeholder="Nombre de Usuario">
        <label for="floatingInput">Nombre de Usuario</label>
    </div>
    @if ($errors->has('username'))
        <span class="text-danger fw-bold small"> <strong>{{ $errors->first('username') }}</strong> </span>
    @endif
    <div class="form-floating">
        <input type="password" name="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}"
            id="floatingPassword" placeholder="Contraseña">
        <label for="floatingPassword">Contraseña</label>
    </div>
    @if ($errors->has('password'))
        <span class="text-danger fw-bold small"> <strong>{{ $errors->first('password') }}</strong> </span>
    @endif

    @if (session('status'))
        <div class="alert alert-danger small fw-bolder">
            {{ session('status') }}
        </div>
    @endif

    <div class="checkbox mb-3">
        <label> <input type="checkbox" value="remember-me"> Recuerdame </label>
    </div>

    {!! Form::submit('Ingresar', ['class' => 'btn btn-dark w-100 py-2', 'id' => 'create']) !!}

    <div class="mt-2">
        <a class="btn btn-light btn-sm" href="{{ route('movile.android.password.reset') }}">
            Problemas para acceder? reinicia la contraseña
        </a>
    </div>

    <p class="mt-5 mb-3 text-muted">© 2019–{{ date('Y') }}</p>

</form>

@section('stylesheets')
    @parent
    <link href="{{ asset('css/floating-labels.css') }}" rel="stylesheet">

    <style type="text/css">
        .form-signin {
            max-width: 330px;
            padding: 15px;
        }

        .form-signin .form-floating:focus-within {
            z-index: 2;
        }

        .form-signin input[type="text"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
@endsection
