<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white shadow px-8 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <span class="text-xl font-bold text-gray-800">Admin Panel</span>
            <a href="{{ route('admin.voting.dashboard') }}" class="text-gray-600 hover:text-blue-600">Mod. Votaciones</a>
            {{-- <a href="{{ route('admin.voting.results') }}" class="text-gray-600 hover:text-blue-600">Resultados</a> --}}
        </div>

        <div class="flex items-center space-x-4">
            <span class="text-gray-700">Hola, {{ Auth::user()->username }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Cerrar
                    sesión</button>
            </form>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="p-8">
        <h1 class="text-3xl font-bold mb-4">Bienvenido al Dashboard</h1>
        <p class="text-gray-700">Aquí puedes gestionar las funcionalidades de administración.</p>
    </main>

</body>

</html>
