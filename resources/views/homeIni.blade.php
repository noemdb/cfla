<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    @vite('resources/css/app.css')
    @vite(['resources/js/app.js'])
    
    @livewireStyles
    
</head>

<body>

    <livewire:home />

    <!-- Encabezado -->
    @include('header.main')    

    <!-- Sección Hero -->

    <section id="hero">
        <div class="container mx-auto p-12 flex flex-col md:flex-row">

            <!-- Contenido Hero -->
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-4xl font-bold mb-4">Bienvenidos a la Escuela</h1>
                <p class="mb-6">Somos una institución educativa líder enfocada en proveer la mejor educación.</p>
                <a href="#" class="bg-blue-500 rounded-full py-3 px-6 text-white hover:bg-blue-600">Más
                    Información</a>
            </div>

            <!-- Imagen Hero -->
            <div class="flex-1">
                <img src="hero.jpg" alt="Imagen principal">
            </div>

        </div>
    </section>

    <!-- Sección Acerca de -->

    <section id="about" class="bg-gray-100 py-12">
        <div class="container mx-auto px-4">

            <h2 class="text-3xl font-semibold mb-4">Acerca de Nosotros</h2>

            <p class="mb-4">Somos una escuela con más de 50 años de experiencia...</p>

            <div class="md:flex">

                <!-- Años de experiencia -->
                <div class="flex-1 mb-8 text-center md:text-left md:mr-8">
                    <h3 class="text-3xl font-semibold mb-2">+50</h3>
                    <p class="text-gray-500">Años de experiencia</p>
                </div>

                <!-- Certificaciones -->
                <div class="flex-1 mb-8 text-center md:text-left">
                    <h3 class="text-3xl font-semibold mb-2">5+</h3>
                    <p class="text-gray-500">Certificaciones</p>
                </div>

            </div>

            <p>Contamos con profesores altamente calificados...</p>

        </div>
    </section>


    <!-- Sección Programas-->

    <section id="programs" class="py-12 bg-white">
        <div class="container mx-auto px-4">

            <h2 class="text-3xl font-semibold text-center mb-8">Nuestros Programas</h2>

            <!-- Programas -->
            <div class="flex flex-wrap -mx-4">

                <!-- Programa 1 -->
                <div class="w-full md:w-1/3 px-4 mb-8">
                    <div class="bg-white rounded overflow-hidden shadow-md p-4 h-full">
                        <h3 class="text-lg font-semibold mb-2">Programa 1</h3>
                        <p>Descripción del programa 1...</p>
                        <a href="#" class="inline-block mt-4 text-blue-500 hover:text-blue-800">Más
                            Información</a>
                    </div>
                </div>

                <!-- Programa 2 -->
                <div class="w-full md:w-1/3 px-4 mb-8">
                    <div class="bg-white rounded overflow-hidden shadow-md p-4 h-full">
                        <h3 class="text-lg font-semibold mb-2">Programa 2</h3>
                        <p>Descripción del programa 2...</p>
                        <a href="#" class="inline-block mt-4 text-blue-500 hover:text-blue-800">Más
                            Información</a>
                    </div>
                </div>

                <!-- Programa 3 -->
                <div class="w-full md:w-1/3 px-4 mb-8">
                    <div class="bg-white rounded overflow-hidden shadow-md p-4 h-full">
                        <h3 class="text-lg font-semibold mb-2">Programa 3</h3>
                        <p>Descripción del programa 3...</p>
                        <a href="#" class="inline-block mt-4 text-blue-500 hover:text-blue-800">Más
                            Información</a>
                    </div>
                </div>

            </div>

            <div class="text-center">
                <a href="#" class="bg-blue-500 text-white rounded-full py-3 px-6 hover:bg-blue-600">
                    Ver Todos los Programas
                </a>
            </div>

        </div>
    </section>

    <!-- Sección Contacto -->

    <section id="contact" class="bg-gray-100 py-12">
        <div class="container mx-auto px-4">

            <h2 class="text-3xl font-semibold text-center mb-8">Contacto</h2>

            <form>
                <div class="mb-4">
                    <label class="block font-semibold mb-2" for="name">Nombre</label>
                    <input class="border p-2 w-full" type="text" id="name">
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-2" for="email">Email</label>
                    <input class="border p-2 w-full" type="email" id="email">
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-2" for="message">Mensaje</label>
                    <textarea class="border p-2 w-full" id="message"></textarea>
                </div>

                <button class="bg-blue-500 text-white rounded-full py-3 px-6 hover:bg-blue-600">
                    Enviar
                </button>

            </form>

        </div>
    </section>

    <!-- Pie de página -->

    <footer class="bg-blue-500 text-white">
        <div class="container mx-auto px-4 py-12 flex justify-between items-center">

            <p>Copyright 2025 Escuela. Todos los derechos reservados.</p>

            <nav>
                <ul class="flex space-x-4">
                    <li><a href="#" class="hover:text-gray-200">Aviso Legal</a></li>
                    <li><a href="#" class="hover:text-gray-200">Política de Privacidad</a></li>
                </ul>
            </nav>

        </div>
    </footer>

    
    {{-- @livewireScripts --}}

    @wireUiScripts

    

</body>

</html>
