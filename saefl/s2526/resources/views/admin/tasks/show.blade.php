@extends('admin.layouts.dashboard.app')

{{-- @section('page_heading','Listado de Usuarios') --}}

@section('main')

    @php ($user = (isset($task->user)) ? $task->user: false)
    @php ($profile = (isset($user->profile)) ? $user->profile: false)
    @php ($messeges = (isset($user->messeges)) ? $user->messeges: false)
    @php ($alerts = (isset($user->alerts)) ? $user->alerts: false)

    <main taske="main" class="col-md-10 ml-sm-auto col-lg-10 px-2">

        <div class="card mt-2 bd-callout bd-callout-info">

            <div class="card-header">

                <h2>

                    Información de la Tarea

                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">

                        @include('admin.tasks.menus.show')

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

                            {{-- Partial con los tabs de usuario (perfiles, Tareas) --}}

                            @include('admin.tasks.show.tabs')

                        </div>
                    </div>

                </div>

            </div>

        </div>

    </main>

@endsection