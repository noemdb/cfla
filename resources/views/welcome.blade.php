<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EDUSYS - Sistema Automatizado de Gestión Escolar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<script>
    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
            '(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark')
    }
</script>

<body class="antialiased bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">

    <!-- Navigation -->
    <nav x-data="{ open: false }"
        class="fixed w-full z-50 top-0 start-0 border-b border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="#" class="flex items-center space-x-3 rtl:space-x-reverse">
                <span
                    class="self-center text-2xl font-bold whitespace-nowrap dark:text-white bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-green-600">EDUSYS</span>
            </a>
            <div class="flex md:order-2 space-x-3 md:space-x-2 rtl:space-x-reverse items-center">

                <!-- Dark Mode Toggle -->
                <button id="theme-toggle" type="button"
                    class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5 mr-2">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z"
                            fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <button type="button"
                    class="text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800 shadow-lg shadow-emerald-500/30 transition-all hover:scale-105">
                    Solicitar Demo
                </button>
                <button @click="open = !open" type="button"
                    class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                    aria-controls="navbar-sticky" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 17 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1h15M1 7h15M1 13h15" />
                    </svg>
                </button>
            </div>
            <div :class="{ 'block': open, 'hidden': !open }"
                class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
                <ul
                    class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-transparent dark:bg-gray-800 md:dark:bg-transparent dark:border-gray-700">
                    <li>
                        <a href="#"
                            class="block py-2 px-3 text-white bg-emerald-700 rounded md:bg-transparent md:text-emerald-700 md:p-0 md:dark:text-emerald-500"
                            aria-current="page">Inicio</a>
                    </li>
                    <li>
                        <a href="#features"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-emerald-700 md:p-0 md:dark:hover:text-emerald-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Características</a>
                    </li>
                    <li>
                        <a href="#modules"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-emerald-700 md:p-0 md:dark:hover:text-emerald-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Módulos</a>
                    </li>
                    <li>
                        <a href="#about"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-emerald-700 md:p-0 md:dark:hover:text-emerald-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Nosotros</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gray-50 dark:bg-gray-900 pt-32 pb-20 overflow-hidden">
        <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
            <div class="mr-auto place-self-center lg:col-span-7">
                <div
                    class="inline-flex items-center justify-between px-1 py-1 pr-4 mb-7 text-sm text-emerald-700 bg-emerald-100 rounded-full dark:bg-emerald-900/30 dark:text-emerald-300 hover:bg-emerald-200 dark:hover:bg-emerald-800 transition-colors cursor-pointer">
                    <span class="text-xs bg-emerald-600 rounded-full text-white px-3 py-1.5 mr-3">Nuevo</span> <span
                        class="text-sm font-medium">EDUSYS 2.0 ya está disponible</span>
                    <svg class="w-2.5 h-2.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 9 4-4-4-4" />
                    </svg>
                </div>
                <h1
                    class="max-w-2xl mb-4 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl dark:text-white">
                    Gestión escolar <span class="text-emerald-600 dark:text-emerald-500">inteligente</span> para
                    instituciones
                    educativas
                </h1>
                <p class="max-w-2xl mb-6 font-light text-gray-500 lg:mb-8 md:text-lg lg:text-xl dark:text-gray-400">
                    Optimiza la gestión institucional, reduce las cargas operativas y asegura el control total de los
                    procesos académicos, administrativos y financieros con EDUSYS.
                </p>
                <div class="flex flex-col space-y-4 sm:flex-row sm:justify-start sm:space-y-0 sm:space-x-4">
                    <a href="#"
                        class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-white rounded-lg bg-emerald-600 hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-300 dark:focus:ring-emerald-900 shadow-xl shadow-emerald-500/20 transition-all hover:-translate-y-1">
                        Empezar Ahora
                        <svg class="w-3.5 h-3.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M1 5h12m0 0L9 1m4 4L9 9" />
                        </svg>
                    </a>
                    <a href="#features"
                        class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-gray-900 rounded-lg border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:text-white dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-800 transition-all">
                        Conocer Más
                    </a>
                </div>

                <div class="mt-8 flex items-center gap-4 text-gray-500 dark:text-gray-400 text-sm">
                    <div class="flex -space-x-3">
                        <div class="w-8 h-8 rounded-full bg-gray-200 border-2 border-white dark:border-gray-900"></div>
                        <div class="w-8 h-8 rounded-full bg-gray-300 border-2 border-white dark:border-gray-900"></div>
                        <div class="w-8 h-8 rounded-full bg-gray-400 border-2 border-white dark:border-gray-900"></div>
                    </div>
                    <p>Más de <span class="font-bold text-gray-900 dark:text-white">5 años</span> de experiencia en
                        evolución
                        educativa.</p>
                </div>
            </div>
            <div class="hidden lg:mt-0 lg:col-span-5 lg:flex relative">
                <div
                    class="absolute inset-0 bg-gradient-to-tr from-emerald-500 to-teal-500 blur-3xl opacity-30 rounded-full animate-pulse">
                </div>
                <!-- Abstract UI Representation -->
                <div
                    class="relative w-full h-full bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 p-4 shadow-2xl rotate-3 hover:rotate-0 transition-all duration-500">
                    <div class="flex items-center gap-2 mb-4 border-b border-gray-700 pb-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-gray-700/50 h-24 rounded-lg animate-pulse"></div>
                        <div class="bg-gray-700/50 h-24 rounded-lg animate-pulse delay-75"></div>
                    </div>
                    <div class="h-4 bg-gray-700/50 rounded w-3/4 mb-2"></div>
                    <div class="h-4 bg-gray-700/50 rounded w-1/2 mb-4"></div>
                    <div class="space-y-2">
                        <div class="h-8 bg-emerald-600/20 rounded w-full flex items-center px-2">
                            <div class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></div>
                        </div>
                        <div class="h-8 bg-gray-700/20 rounded w-full"></div>
                        <div class="h-8 bg-gray-700/20 rounded w-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Problem/Solution Section -->
    <section class="bg-white dark:bg-gray-800/50 py-20" id="about">
        <div class="max-w-screen-xl px-4 mx-auto lg:px-6">
            <div class="max-w-3xl mx-auto text-center mb-16">
                <h2 class="mb-4 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white md:text-4xl">La
                    evolución de la gestión educativa</h2>
                <p class="text-gray-500 dark:text-gray-400 sm:text-lg">
                    La gestión escolar tradicional está llena de procesos manuales, desconectados y burocráticos. EDUSYS
                    centraliza y automatiza todo en una sola plataforma.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="relative">
                    <div
                        class="absolute -left-4 -top-4 w-24 h-24 bg-emerald-100 dark:bg-emerald-900/30 rounded-full -z-10">
                    </div>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div
                                class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Carga Administrativa
                                </h3>
                                <p class="text-gray-500 dark:text-gray-400">Procesos manuales y repetitivos que
                                    consumen tiempo valioso de docentes y directivos.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div
                                class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-lg bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Información Dispersa
                                </h3>
                                <p class="text-gray-500 dark:text-gray-400">Datos descentralizados que dificultan la
                                    toma de decisiones y el seguimiento estudiantil.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="max-w-lg">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">La Solución EDUSYS</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">
                        EDUSYS es una plataforma integral diseñada para automatizar, centralizar y optimizar los
                        procesos
                        académicos, administrativos y financieros. Su enfoque sistémico permite que toda la comunidad
                        educativa se concentre en lo más importante: la educación.
                    </p>
                    <ul class="space-y-3 text-gray-500 dark:text-gray-400">
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Gestión financiera y control de ingresos
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Expediente digital y gestión de incidencias
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Comunicación efectiva con representantes
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section class="py-24 bg-gray-50 dark:bg-gray-900" id="features">
        <div class="max-w-screen-xl px-4 mx-auto lg:px-6">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span
                    class="text-emerald-600 dark:text-emerald-500 font-semibold tracking-wide uppercase text-sm">Funcionalidades</span>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white md:text-4xl">Todo
                    lo que necesitas en un solo lugar</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Featured: Planificación Docente -->
                <div
                    class="p-8 bg-emerald-50 dark:bg-gray-800 rounded-2xl border-2 border-emerald-500 dark:border-emerald-500 hover:shadow-2xl transition-all group relative overflow-hidden md:col-span-2 lg:col-span-3">
                    <div
                        class="absolute top-0 right-0 bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg uppercase tracking-wider">
                        Destacado</div>
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                        <div
                            class="flex-shrink-0 w-16 h-16 bg-emerald-100 dark:bg-emerald-900/50 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Planificación Docente
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 text-lg leading-relaxed">
                                Optimiza el proceso de enseñanza con herramientas bien estructuradas para la creación de
                                planes de evaluación, seguimiento de objetivos y gestión de actividades académicas. Todo
                                integrado en un solo lugar, según la normativa educativa vigente.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Feature 1 -->
                <div
                    class="p-8 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-emerald-500/50 hover:shadow-xl transition-all group">
                    <div
                        class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/50 rounded-lg flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Gestión Administrativa</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Control de ingresos, recepción
                        y registro de pagos, y políticas de cobranza eficaces para la estabilidad financiera.</p>
                </div>

                <!-- Feature 2 -->
                <div
                    class="p-8 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-emerald-500/50 hover:shadow-xl transition-all group">
                    <div
                        class="w-12 h-12 bg-teal-100 dark:bg-teal-900/50 rounded-lg flex items-center justify-center text-teal-600 dark:text-teal-400 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Seguimiento Académico</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Gestión estructurada de planes de
                        evaluación, indicadores y criterios académicos para un control pedagógico continuo.</p>
                </div>

                <!-- Feature 3 -->
                <div
                    class="p-8 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-emerald-500/50 hover:shadow-xl transition-all group">
                    <div
                        class="w-12 h-12 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center text-green-600 dark:text-green-400 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.013 8.013 0 01-5.45-2.15l-4.25 1.06 1.06-4.25A8.01 8.01 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Comunicación Fluida</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Automatización de informes y acceso
                        seguro a la información para padres y representantes, incluyendo WhatsApp.</p>
                </div>

                <!-- Feature 4 -->
                <div
                    class="p-8 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-emerald-500/50 hover:shadow-xl transition-all group">
                    <div
                        class="w-12 h-12 bg-rose-100 dark:bg-rose-900/50 rounded-lg flex items-center justify-center text-rose-600 dark:text-rose-400 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Expediente Digital</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Historial completo del estudiante y
                        docente, centralizando logros, incidencias y documentación importante.</p>
                </div>

                <!-- Feature 5 -->
                <div
                    class="p-8 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-emerald-500/50 hover:shadow-xl transition-all group">
                    <div
                        class="w-12 h-12 bg-cyan-100 dark:bg-cyan-900/50 rounded-lg flex items-center justify-center text-cyan-600 dark:text-cyan-400 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Inteligencia Artificial</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Debates académicos generados con IA y
                        herramientas avanzadas para potenciar el aprendizaje.</p>
                </div>

                <!-- Feature 6 -->
                <div
                    class="p-8 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-emerald-500/50 hover:shadow-xl transition-all group">
                    <div
                        class="w-12 h-12 bg-orange-100 dark:bg-orange-900/50 rounded-lg flex items-center justify-center text-orange-600 dark:text-orange-400 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Seguridad y Normativa</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Cumplimiento total de regulaciones
                        educativas vigentes y protección de datos sensible.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Diagnostic Module Highlight -->
    <section class="bg-white dark:bg-gray-800 py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-emerald-900/10 dark:bg-emerald-900/20"></div>
        <div class="max-w-screen-xl px-4 mx-auto lg:px-6 relative">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="order-2 lg:order-1">
                    <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white md:text-4xl mb-6">
                        Diagnóstico Académico Inteligente
                    </h2>
                    <p class="text-lg text-gray-500 dark:text-gray-300 mb-6">
                        Evalúa, mide y potencia el conocimiento de tus estudiantes con nuestra potente herramienta de
                        diagnóstico.
                    </p>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <div
                                class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center mt-1">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Evaluación por Áreas
                                </h4>
                                <p class="text-gray-500 dark:text-gray-400">Pruebas personalizadas por materia y nivel
                                    educativo.</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div
                                class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center mt-1">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Estadísticas en Tiempo
                                    Real</h4>
                                <p class="text-gray-500 dark:text-gray-400">Visualiza el progreso y rendimiento con
                                    gráficos detallados.</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div
                                class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center mt-1">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Retroalimentación
                                    Instantánea</h4>
                                <p class="text-gray-500 dark:text-gray-400">Resultados inmediatos para mejorar el
                                    proceso de aprendizaje.</p>
                            </div>
                        </li>
                    </ul>
                    <a href="{{ route('diagnostico') }}"
                        class="inline-flex items-center text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-300 font-medium rounded-lg text-lg px-6 py-3.5 text-center dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800 transition-all hover:-translate-y-1 shadow-lg shadow-emerald-500/30">
                        Probar Diagnóstico
                        <svg class="w-5 h-5 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
                <div class="order-1 lg:order-2 flex justify-center">
                    <!-- Abstract representation of the diagnostic dashboard -->
                    <div class="relative w-full max-w-md aspect-square">
                        <!-- Decorative blobs -->
                        <div
                            class="absolute top-0 right-0 w-72 h-72 bg-emerald-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob">
                        </div>
                        <div
                            class="absolute bottom-0 left-0 w-72 h-72 bg-green-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000">
                        </div>

                        <!-- UI Card -->
                        <div
                            class="relative bg-gray-900 rounded-2xl border border-gray-700 shadow-2xl p-6 backdrop-blur-sm h-full flex flex-col justify-between transform rotate-3 hover:rotate-0 transition-transform duration-500">
                            <!-- Header -->
                            <div class="flex items-center justify-between mb-8">
                                <div class="h-4 w-1/3 bg-gray-700 rounded"></div>
                                <div class="flex space-x-2">
                                    <div class="h-3 w-3 rounded-full bg-red-500"></div>
                                    <div class="h-3 w-3 rounded-full bg-yellow-500"></div>
                                    <div class="h-3 w-3 rounded-full bg-green-500"></div>
                                </div>
                            </div>

                            <!-- Progress Rings -->
                            <div class="grid grid-cols-2 gap-4 mb-8">
                                <div
                                    class="bg-gray-800 rounded-xl p-4 border border-gray-700 flex flex-col items-center">
                                    <div class="relative w-20 h-20 mb-3">
                                        <svg class="w-full h-full transform -rotate-90">
                                            <circle cx="40" cy="40" r="36" stroke="currentColor"
                                                stroke-width="8" fill="none" class="text-gray-700" />
                                            <circle cx="40" cy="40" r="36" stroke="currentColor"
                                                stroke-width="8" fill="none" class="text-emerald-500"
                                                stroke-dasharray="160, 251.2" stroke-linecap="round" />
                                        </svg>
                                        <div
                                            class="absolute inset-0 flex items-center justify-center text-white font-bold">
                                            75%</div>
                                    </div>
                                    <div class="h-2 w-16 bg-gray-700 rounded"></div>
                                </div>
                                <div
                                    class="bg-gray-800 rounded-xl p-4 border border-gray-700 flex flex-col items-center">
                                    <div class="relative w-20 h-20 mb-3">
                                        <svg class="w-full h-full transform -rotate-90">
                                            <circle cx="40" cy="40" r="36" stroke="currentColor"
                                                stroke-width="8" fill="none" class="text-gray-700" />
                                            <circle cx="40" cy="40" r="36" stroke="currentColor"
                                                stroke-width="8" fill="none" class="text-blue-500"
                                                stroke-dasharray="210, 251.2" stroke-linecap="round" />
                                        </svg>
                                        <div
                                            class="absolute inset-0 flex items-center justify-center text-white font-bold">
                                            92%</div>
                                    </div>
                                    <div class="h-2 w-16 bg-gray-700 rounded"></div>
                                </div>
                            </div>

                            <!-- Chart lines -->
                            <div class="space-y-3">
                                <div class="h-2 bg-gray-700 rounded w-full">
                                    <div class="h-full bg-emerald-500 rounded w-[60%]"></div>
                                </div>
                                <div class="h-2 bg-gray-700 rounded w-full">
                                    <div class="h-full bg-green-500 rounded w-[85%]"></div>
                                </div>
                                <div class="h-2 bg-gray-700 rounded w-full">
                                    <div class="h-full bg-teal-500 rounded w-[45%]"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Academic Debates Section -->
    <section class="bg-gray-50 dark:bg-gray-900 py-24 relative overflow-hidden">
        <div class="max-w-screen-xl px-4 mx-auto lg:px-6 relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <!-- Visual Representation -->
                <div class="order-1 relative">
                    <div
                        class="absolute -inset-4 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl opacity-20 blur-2xl animate-pulse">
                    </div>
                    <div
                        class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                        <!-- Scoreboard Header -->
                        <div class="bg-gray-900 px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                                <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                            </div>
                            <span class="text-xs font-mono text-emerald-400">LIVE SCOREBOARD</span>
                        </div>

                        <!-- Scoreboard Content -->
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-8">
                                <div class="text-center">
                                    <div class="text-4xl font-bold text-gray-900 dark:text-white mb-1">Equipo A</div>
                                    <div class="text-emerald-600 dark:text-emerald-400 font-mono text-2xl">850 pts
                                    </div>
                                </div>
                                <div class="text-2xl font-bold text-gray-400">VS</div>
                                <div class="text-center">
                                    <div class="text-4xl font-bold text-gray-900 dark:text-white mb-1">Equipo B</div>
                                    <div class="text-blue-600 dark:text-blue-400 font-mono text-2xl">720 pts</div>
                                </div>
                            </div>

                            <!-- Timer -->
                            <div class="bg-gray-100 dark:bg-gray-700/50 rounded-lg p-4 text-center mb-6">
                                <span class="text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400">Tiempo
                                    Restante</span>
                                <div class="text-3xl font-mono font-bold text-gray-900 dark:text-white">04:59</div>
                            </div>

                            <!-- Activity Log -->
                            <div class="space-y-3">
                                <div class="flex items-center text-sm">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full mr-3"></span>
                                    <span class="text-gray-600 dark:text-gray-300">Moderador: Nueva pregunta
                                        activada</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                                    <span class="text-gray-600 dark:text-gray-300">Estudiante #42: Respuesta
                                        registrada</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="order-2">
                    <span
                        class="text-emerald-600 dark:text-emerald-500 font-bold tracking-wide uppercase text-sm mb-2 block">Aprendizaje
                        Interactivo</span>
                    <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white md:text-4xl mb-6">
                        Competiciones y Debates Educativos
                    </h2>
                    <p class="text-lg text-gray-500 dark:text-gray-300 mb-8">
                        Lleva el aprendizaje al siguiente nivel con nuestra plataforma de debates y competiciones en
                        tiempo real. Fomenta la participación activa y el pensamiento crítico.
                    </p>

                    <div class="space-y-6 mb-8">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white">Panel de Moderador</h4>
                                <p class="mt-1 text-gray-500 dark:text-gray-400 mb-2">Gestiona rondas, preguntas y
                                    tiempos con control total sobre la dinámica de la clase.</p>
                                <a href="#"
                                    class="text-sm font-semibold text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 inline-flex items-center transition-colors">
                                    Acceder al Panel
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white">Pizarra en Vivo</h4>
                                <p class="mt-1 text-gray-500 dark:text-gray-400 mb-2">Visualización de puntajes y
                                    rankings en tiempo real para mantener la emoción y el compromiso.</p>
                                <a href="#"
                                    class="text-sm font-semibold text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 inline-flex items-center transition-colors">
                                    Ver Pizarra
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- New CTA Element: Join Session Input -->
                    <div
                        class="bg-gray-100 dark:bg-gray-800 p-1 rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row shadow-sm">
                        <input type="text" placeholder="Ingresa código de competencia..."
                            class="bg-transparent border-0 focus:ring-0 text-gray-900 dark:text-white flex-1 p-3 px-4 placeholder-gray-500">
                        <button
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg px-6 py-2.5 transition-colors sm:mt-0 mt-2 shadow-md">
                            Unirse Ahora
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Modules List (Compact) -->
    <section class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 py-16" id="modules">
        <div class="max-w-screen-xl px-4 mx-auto text-center">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Ecosistema Modular Completo</h3>
            <div class="flex flex-wrap justify-center gap-3">
                <span
                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full text-sm text-gray-600 dark:text-gray-300 shadow-sm hover:border-emerald-500 hover:text-emerald-500 transition-colors cursor-default">Configuraciones
                    Generales</span>
                <span
                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full text-sm text-gray-600 dark:text-gray-300 shadow-sm hover:border-emerald-500 hover:text-emerald-500 transition-colors cursor-default">Gestión
                    de Estudiantes</span>
                <span
                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full text-sm text-gray-600 dark:text-gray-300 shadow-sm hover:border-emerald-500 hover:text-emerald-500 transition-colors cursor-default">Gestión
                    de Representantes</span>
                <span
                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full text-sm text-gray-600 dark:text-gray-300 shadow-sm hover:border-emerald-500 hover:text-emerald-500 transition-colors cursor-default">Control
                    de Asistencia</span>
                <span
                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full text-sm text-gray-600 dark:text-gray-300 shadow-sm hover:border-emerald-500 hover:text-emerald-500 transition-colors cursor-default">Inscripciones</span>
                <span
                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full text-sm text-gray-600 dark:text-gray-300 shadow-sm hover:border-emerald-500 hover:text-emerald-500 transition-colors cursor-default">Bienestar
                    Estudiantil</span>
                <span
                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full text-sm text-gray-600 dark:text-gray-300 shadow-sm hover:border-emerald-500 hover:text-emerald-500 transition-colors cursor-default">Histórico
                    de Notas</span>
                <span
                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full text-sm text-gray-600 dark:text-gray-300 shadow-sm hover:border-emerald-500 hover:text-emerald-500 transition-colors cursor-default">Promociones
                    y Títulos</span>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
        <div class="w-full max-w-screen-xl mx-auto p-4 md:py-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <a href="#" class="flex items-center mb-4 sm:mb-0 space-x-3 rtl:space-x-reverse">
                    <span
                        class="self-center text-2xl font-bold whitespace-nowrap dark:text-white bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-green-600">EDUSYS</span>
                </a>
                <ul
                    class="flex flex-wrap items-center mb-6 text-sm font-medium text-gray-500 sm:mb-0 dark:text-gray-400">
                    <li>
                        <a href="#" class="hover:underline me-4 md:me-6">Inicio</a>
                    </li>
                    <li>
                        <a href="#" class="hover:underline me-4 md:me-6">Política de Privacidad</a>
                    </li>
                    <li>
                        <a href="#" class="hover:underline me-4 md:me-6">Licencia</a>
                    </li>
                    <li>
                        <a href="#" class="hover:underline">Contacto</a>
                    </li>
                </ul>
            </div>
            <hr class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8" />
            <span class="block text-sm text-gray-500 sm:text-center dark:text-gray-400">© {{ date('Y') }} <a
                    href="#" class="hover:underline">EDUSYS™</a>. Todos los derechos reservados.</span>
        </div>
    </footer>
    <script>
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Change the icons inside the button based on previous settings
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {

            // toggle icons inside button
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // if set via local storage previously
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }

                // if NOT set via local storage previously
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }

        });
    </script>
</body>

</html>
