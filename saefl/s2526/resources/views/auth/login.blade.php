@extends ('auth.layouts.login')

@section('content')
    <form class="form-signin small" method="POST" action="{{ route('login') }}">

        {{ csrf_field() }}

        <center>
            <img class="mb-4" src="{{ asset('images/brand/144/1.png') }}" alt="" width="144" height="144">
            <h3 class="form-signin-heading ">Datos de acceso</h3>
        </center>

        {{-- <label for="pescolar_id">Período Escolar</label> --}}
        <div class="form-label-group pb-2">
            {!! Form::select('pescolar_id', ['2025-2026' => '2025-2026'], old('pescolar_id'), [
                'class' => 'form-control font-weight-bold',
                'id' => 'pescolar_id',
                'placeholder' => 'Período Escolar',
                'required' => 'required',
            ]) !!}
            @if ($errors->has('pescolar_id'))
                <div class="invalid-feedback" style="width: 100%;">
                    <span class="help-block">
                        <strong>{{ $errors->first('pescolar_id') }}</strong>
                    </span>
                </div>
            @endif
        </div>

        <div class="form-label-group">
            <input type="text" id="inputUser" name="username"
                class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" placeholder="Nombre de Usuario"
                value="{{ old('username') }}" required>
            <label for="inputUser">Nombre de Usuario</label>
            @if ($errors->has('username'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('username') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-label-group">
            <input type="password" id="inputPassword" name="password"
                class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="Contraseña" required>
            <label for="inputPassword">Contraseña</label>
            @if ($errors->has('password'))
                <div class="invalid-feedback" style="width: 100%;">
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                </div>
            @endif
        </div>


        <div class="checkbox mb-0" align="right">
            <label>
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Recordarme
            </label>
        </div>

        {{-- <div class="text-center text-muted">
            <span class="text-info font-weight-bold">Período Escolar 2021-2022</span>
            {!! Form::select('pescolar_id',['2022-2023'=>'2022-2023'],old('pescolar_id'),['class'=>'form-control','id'=>'pescolar_id','placeholder'=>'Seleccione','required'=>'required']) !!}
        </div> --}}

        {!! Form::submit('Ingresar', ['class' => 'btn-create btn btn-dark btn-block', 'id' => 'create']) !!}

        {{-- <br> --}}
        <hr>


        {{-- <div class=" border px-2 rounded"> --}}
        {{-- @if (Carbon\Carbon::now() < '2021-05-04 00:00:00')
                <div class="d-flex justify-content-end">
                    <span class="badge badge-danger m-2">Nuevos</span>
                </div>
            @endif --}}
        <div class="table-light p-2 border rounded">
            <a class="btn-link" href="{{ route('password.request') }}">
                Problemas para acceder? reinicia la contraseña
            </a>
        </div>

        <hr>

        <div class="input-group">

            <div class="input-group-prepend">
                <div class="input-group-text" id="btnGroupAddon2">
                    <img class="google-icon" src="{{ asset('images/avatar/social/google.svg') }}" width="24"
                        height="24" />
                </div>
            </div>
            <a name="" id="" class="form-control btn btn-light" href="{{ route('social.auth', 'google') }}"
                role="button">
                <span class="font-weight-bold text-muted">
                    Ingresa con tu cuenta GSuite
                </span>
            </a>

            @if (Session::has('noLogin'))
                @include('auth.partials.login.errors.modal')
            @endif

        </div>
        {{-- </div> --}}


    </form>
@endsection
