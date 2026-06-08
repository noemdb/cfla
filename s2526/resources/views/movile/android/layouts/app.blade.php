<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    {{-- <meta name="viewport" content="width=device-width, initial-scale=1"> --}}
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">


    <title>{{ config('app.name', 'Laravel') }}</title>
    
    {{-- PWA --}}
    <link rel="manifest" href="{{ asset('pwa/manifest.json') }}">
    {{-- <link rel="stylesheet" href="{{ asset('pwa/styles.css') }}"> --}}

    {{-- Styles --}}
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/bootstrap/5.3.0/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/5.3.0/icon/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fontawesome/5.2.0/css/all.css') }}" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{ asset('vendor/bootstrap/5.3.0/css/sidebars.css') }}" rel="stylesheet">

    {{-- Custom Styles --}}
    @yield('stylesheets')

    {{-- Livewire Styles --}}
    @livewireStyles

    <style>
        @media (max-width: 520px ) {
            .div-contenido {
                width: 100%;
            }
        }
        .backdrop-active {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        /* Estilos para fade in */
        .fade-in {
            opacity: 1;
            transition: opacity 0.5s ease-in;
        }

        /* Estilos para fade out */
        .fade-out {
            opacity: 0;
            transition: opacity 0.5s ease-out;
        }
    </style>


</head>
<body class="w-100 d-block" style="background-color: rgba(0, 0, 0, 0.5)">

    {{-- @yield('splash') --}}

    <main id="main-content" >

        <div class="container-fluid p-0 m-0">
            <div>
                <div class="mx-auto border rounded shadow-sm vh-100 d-flex flex-column bg-white  py-0 my-0">

                    @include('movile.android.layouts.partials.header')

                    <div id="content" class="bg-white shadow-sm p-1">
                        <div class="">
                            @yield('content')
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </main>

    {{-- Core Scripts --}}
    <script src="{{ asset('vendor/alpine/3.11.1/cdn.min.js') }}" defer></script>
    <script src="{{ asset('vendor/bootstrap/5.3.0/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/apexcharts/4.7.0/apexcharts.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert/11.26.3/sweetalert2.all.min.js') }}"></script>
    {{-- /home/nuser/code/s2526/public/vendor/sweetalert/11.26.3/sweetalert2.all.min.js --}}

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- Custom Scripts --}}
    @yield('livewires')
    @yield('sweetalert')
    @yield('scripts')

    {{-- PWA Service Worker --}}
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register("{{ asset('/pwa/sw.js') }}").then(reg => {
                reg.onupdatefound = () => {
                    const installingWorker = reg.installing;
                    installingWorker.onstatechange = () => {
                        if (installingWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            const updateBanner = document.createElement('div');
                            updateBanner.innerHTML = `
                                <p>Hay una nueva versión disponible.</p>
                                <button onclick="location.reload();">Actualizar</button>
                            `;
                            document.body.appendChild(updateBanner);
                        }
                    };
                };
            });
        }
    </script>

    {{-- Navigation Toggle Script --}}
    <script>
        const toggler = document.querySelector('.navbar-toggler');
        const backdrop = document.querySelector('#main-content');
        const content = document.querySelector('#content');

        toggler.addEventListener('click', () => {
            backdrop.classList.toggle('backdrop-active');
            content.classList.toggle('d-none');
        });
    </script>

    @yield('customeScripts')

</body>

</html>
