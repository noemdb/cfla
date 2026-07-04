@extends('admin.layouts.dashboard.app')

{{-- @section('page_heading','Listado de Usuarios') --}}

@section('main')

    @php ($user = $rol->user)
    @isset($user->profile)
        @php ($profile = $user->profile)
    @endisset

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10 px-4">

        <div class="card mt-2 bd-callout bd-callout-info">

            <div class="card-header">

                <h2>

                    Información del Rol

                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">

                        @include('admin.rols.menus.show')

                    </div>
                    {{-- FIN Menu rapido --}}

                </h2>

            </div>



            <div class="card-body">

                <div class="container">

                    <div class="row">

                        <div class="col-sm-4">

                            <img alt="{{$user->username ?? ''}}" class="img-thumbnail img-rounded" src="{{ (isset($profile->url_img)) ? asset($profile->url_img) : asset('images/avatar/user_default.png') }}">

                        </div>

                        <div class="col-sm-8">

                            {{-- Partial con los tabs de usuario (perfiles, roles) --}}

                            @include('admin.rols.show.tabs')

                        </div>
                    </div>

                </div>

            </div>

        </div>

    </main>

@endsection