@extends('layouts.app_inicio')

@section('content')

      <main role="main" class="inner cover">

        @if (Session::has('operp_ok'))

          <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{Session::get('operp_ok')}}.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

        @endif

        <h1 class="cover-heading">

          <i class="fas fa-home"></i>
          Inicio

        </h1>

        @if (Auth::check())

            <small class="btn btn-sm btn-outline-success disabled"><b>Sesión iniciada correctamete!</b></small>

        @endif

        <p class="lead mt-4">

          <a href="#" class="btn btn-sm btn-info font-weight-bold">
            <i class="fas fa-info-circle"></i>
            Conoce más...
          </a>

        </p>

      </main>
      
@endsection
