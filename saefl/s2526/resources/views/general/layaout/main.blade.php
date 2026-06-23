<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="Access-Control-Allow-Origin" content="*">

    <title>SAEFL - @yield('title') </title>

    <script>
        /**
         * Script to initialize theme and prevent flashing (FOUC)
         */
        (() => {
            'use strict'
            const getStoredTheme = () => localStorage.getItem('theme')
            const getPreferredTheme = () => {
                const storedTheme = getStoredTheme()
                if (storedTheme) {
                    return storedTheme
                }
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
            }

            const setTheme = theme => {
                if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.setAttribute('data-bs-theme', 'dark')
                } else {
                    document.documentElement.setAttribute('data-bs-theme', theme)
                }
            }

            setTheme(getPreferredTheme())
        })()
    </script>

    <!-- Styles -->
    <link href="{{ asset('vendor/bootstrap/5.3.0/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/5.3.0/icon/bootstrap-icons.css') }}" rel="stylesheet">


    <link href="{{ asset('vendor/fontawesome/5.2.0/css/all.css') }}" rel="stylesheet">

    <!-- stylesheet for page -->
    @yield('stylesheet')

    <!-- Scripts -->

    <!-- livewireStyles -->
    @livewireStyles

    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        [data-bs-theme="dark"] {
            --glass-bg: rgba(0, 0, 0, 0.2);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .overlay-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            z-index: 998;
            pointer-events: none;
        }

        .overlay {
            position: relative;
            z-index: 999;
        }

        #theme-toggle {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1050;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--glass-border);
            background-color: var(--bs-body-bg);
        }

        #theme-toggle:hover {
            transform: scale(1.1) rotate(15deg);
        }

        .navbar-bottom {
            position: fixed;
            bottom: 0;
            width: 100%;
            z-index: 1030;
            backdrop-filter: blur(15px);
            background-color: rgba(var(--bs-body-bg-rgb), 0.8) !important;
            border-top: 1px solid var(--glass-border);
            padding-bottom: env(safe-area-inset-bottom);
        }

        .rounded-4 {
            border-radius: 1rem !important;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-pulse {
            animation: pulse 2s infinite;
        }

        .font-monospace-digital {
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: -2px;
        }
    </style>

</head>

<body>

    <!-- content for page -->
    @yield('main')

    <!-- footer for page -->
    @yield('footer')

    <!-- Floating Theme Toggle -->
    <button id="theme-toggle" class="btn" onclick="toggleTheme()" aria-label="Cambiar tema">
        <i class="bi bi-sun-fill d-none-dark"></i>
        <i class="bi bi-moon-stars-fill d-none-light"></i>
    </button>

    <!-- Scripts -->
    <script src="{{ asset('vendor/bootstrap/5.3.0/js/bootstrap.js') }}"></script>

    <script>
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateToggleIcon(newTheme);
        }

        function updateToggleIcon(theme) {
            const btn = document.getElementById('theme-toggle');
            if (theme === 'dark') {
                btn.innerHTML = '<i class="bi bi-sun-fill text-warning"></i>';
            } else {
                btn.innerHTML = '<i class="bi bi-moon-stars-fill text-success"></i>';
            }
        }

        // Initialize icon
        document.addEventListener('DOMContentLoaded', () => {
            updateToggleIcon(document.documentElement.getAttribute('data-bs-theme'));
        });
    </script>

    <script src="{{ asset('vendor/sweetalert/11.4.8/js/sweetalert2.all.min.js') }}"></script>
    @yield('sweetalert')

    <!-- scripts for page -->
    @yield('scripts')

    <!-- livewireScripts -->
    @livewireScripts
    @yield('livewires')

</body>

</html>
