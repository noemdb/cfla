@extends('admin.layouts.dashboard.app')

{{-- @section('page_heading','Listado de Usuarios') --}}

@section('main')

    @php ($user = $setting->user)
    @isset($user->profile)
        @php ($profile = $user->profile)
    @endisset
    @isset($user->tasks)
        @php ($tasks = $user->tasks)
    @endisset
    @isset($user->alerts)
        @php ($alerts = $user->alerts)
    @endisset

    <main taske="main" class="col-md-10 ml-sm-auto col-lg-10 px-2">

        <div class="card mt-2 bd-callout bd-callout-info">

            <div class="card-header">

                <h2>

                    Información del Setting

                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">

                        @include('admin.settings.menus.show')

                    </div>
                    {{-- FIN Menu rapido --}}

                </h2>

            </div>



            <div class="card-body p-1">

                <div class="container p-1">

                    <div class="row">

                        {{-- 
                        <div class="col-sm-4">

                            <img alt="{{$user->username ?? ''}}" class="img-thumbnail img-rounded" src="{{ (isset($profile->url_img)) ? asset($profile->url_img) : asset('images/avatar/user_default.png') }}">

                        </div> 
                        --}}

                        <div class="col-sm-12">

                            {{-- Partial con los tabs de usuario (perfiles, Settings) --}}

                            @include('admin.settings.show.tabs')

                        </div>
                    </div>

                </div>

            </div>

        </div>

    </main>

@endsection