@extends('admin.layouts.dashboard.app')

{{-- @section('page_heading','Listado de Usuarios') --}}

@section('main')

    @php ($user = $messege->user)

    <main taske="main" class="col-md-10 ml-sm-auto col-lg-10 px-2">

        <div class="card mt-2 bd-callout bd-callout-info">

            <div class="card-header">

                <h2>

                    Información del Mensaje

                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">

                        @include('admin.messeges.menus.show')

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

                            {{-- Partial con los tabs de usuario (perfiles, Alertas) --}}

                            @include('admin.messeges.show.messege')

                        </div>
                    </div>

                </div>

            </div>

        </div>

    </main>

@endsection