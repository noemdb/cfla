<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Error del servidor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex items-center justify-center min-h-screen p-4 bg-gray-50">
    <div class="w-full max-w-md p-8 text-center bg-white rounded-lg shadow-sm">
        <div class="mb-6">
            <h3 class="font-bold text-gray-800 text-8xl">Ha ocurrido al inusal</h3>
        </div>
        <h4 class="mb-3 text-2xl font-semibold text-gray-700">Error del servidor</h4>
        <p class="mb-8 text-gray-500">Lo sentimos, presentamos un error interno en nuestro servidor. Nuestro equipo técnico ha sido notificado. Le invitamos a que lo intente mas tarde</p>
        <a href="{{ url('/') }}" class="inline-block px-6 py-3 text-white transition-colors duration-200 bg-gray-800 rounded-md hover:bg-gray-700">
            Volver al inicio
        </a>
    </div>
</body>
</html>
