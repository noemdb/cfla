@extends('academicos.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header alert-success">
                <div class="btn-group float-right pt-2">
                    {{-- @include('academicos.boletins.menus.index') --}}
                </div>

                <h3 class="mb-0 pb-0">
                    <i class="{{$icon_menus['lessons'] ?? ''}} text-secondary" aria-hidden="true"></i>
                    Áreas de Conocimiento - Lecciones
                </h3>

                {{-- <span class="text-muted small text-capitalize font-light">
                    {{ Auth::user()->profesor->fullname}}
                </span> --}}

            </div>

            <div class="card-body p-1 m-1">

                <h6 class="pb-2"><u>Listado</u> de Lecciones registradas</h6>
                @include('academicos.lessons.table.index')

            </div>
        </div>
    </main>

@endsection
