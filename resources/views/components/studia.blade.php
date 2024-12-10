<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prepárate para tu examen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 antialiased">

    <!-- Splash Screen -->
    <div id="splash-screen" class="fixed inset-0 flex items-center justify-center bg-blue-500 z-50">
        <img src="{{ asset('image/splash/ima.jpg') }}" alt="Preparación Física" class="w-auto">
    </div>

    <!-- Main Content -->
    <div id="app" class="min-h-screen flex flex-col">
        <!-- Dynamic Content -->
        {{ $slot }}
    </div>

</body>
</html>


@php /*  @endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyAI - Aprende Inteligentemente</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-50">
    <header class="bg-white dark:bg-gray-800 shadow-md fixed w-full z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="/studia" class="text-2xl font-bold">StudAI</a>
            <nav>
                <a href="#features" class="text-gray-700 dark:text-gray-300 px-4 hover:underline">Características</a>
                <a href="#testimonials" class="text-gray-700 dark:text-gray-300 px-4 hover:underline">Testimonios</a>
                <a href="#faq" class="text-gray-700 dark:text-gray-300 px-4 hover:underline">FAQ</a>
                {{-- 
                <button 
                    onclick="loginWithGoogle()" 
                    class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600">
                    Iniciar sesión con Google
                </button>
                --}}
            </nav>
        </div>
    </header>
    <main class="pt-20">
        {{ $slot }}
    </main>
    <footer class="bg-gray-800 text-gray-300 py-6 mt-10">
        <div class="container mx-auto text-center">
            <p>&copy; {{ date('Y') }} StudyAI. Todos los derechos reservados.</p>
        </div>
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    @livewireScripts
</body>
</html>
@php */ @endphp