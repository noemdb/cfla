@extends('administracion.layouts.home.app')

@section('main')

    <main role="main" class="col-md-10 col-lg-10" style="box-shadow: inset 0 0 5rem rgba(0, 0, 0, .5);">

        @admon
            <div class=" float-right"> <livewire:administracion.exchange-rate.list-component /> </div>
        @endadmon

        <div class="vertical-center text-center">


            <div class="container w-75">
                <div class="h6 font-weight-bold m-0 p-0 text-secondary text-uppercase">Período Escolar
                    {{ Session::get('pescolar_name') }}</div>
                <img class="{{-- mb-4 --}}" src="{{ asset('images/brand/192/1.png') }}" alt="" width="192"
                    height="192">
                <div style="color:#004000" class="m-0 p-0 text-muted small font-weight-bold ">
                    SISTEMA PARA LA ADMINISTRACIÓN<BR>Y CONTROL DE PROCESOS ESCOLARES
                </div>

                <footer class="mastfoot mt-auto">
                    <div class="inner">
                        <p>
                            <span class="text-info font-weight-bold small" style="opacity:0.8">
                                Desarrollado por <a href="https://noemdb.com/">NoeMDB</a><br>
                                <img src="{{ asset('images/avatar/social/github.png') }}" alt="" width="32"
                                    height="32">
                                <img src="{{ asset('images/avatar/social/whatsapp.png') }}" alt="" width="32"
                                    height="32">
                                <br>
                        </p>
                    </div>
                </footer>

            </div>

        </div>

    </main>

@endsection

@section('title')
    - Inicio - {{ Auth::user()->rol ?? '' }}
@endsection

@section('stylesheet')
    @parent
    <style>
        .vertical-center {
            min-height: 100%;
            /* Fallback for browsers do NOT support vh unit */
            /*min-height: 100vh; /* These two lines are counted as one :-)       */

            display: flex;
            align-items: center;
        }
    </style>
@endsection
