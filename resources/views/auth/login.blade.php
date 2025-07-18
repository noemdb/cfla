<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <form method="POST" action="{{ route('login') }}" class="bg-white p-6 rounded shadow-md w-full max-w-sm">
        @csrf

        <h1 class="text-xl font-bold mb-4">Iniciar Sesión</h1>

        @error('username')
            <div class="bg-red-100 text-red-700 p-2 rounded mb-4">{{ $message }}</div>
        @enderror

        <div class="mb-4">
            <label class="block mb-1">Usuario</label>
            <input type="text" name="username" value="{{ old('username') }}" required autofocus class="w-full border px-3 py-2 rounded">
        </div>

        <div class="mb-4">
            <label class="block mb-1">Contraseña</label>
            <input type="password" name="password" required class="w-full border px-3 py-2 rounded">
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Entrar</button>
    </form>

</body>
</html>
