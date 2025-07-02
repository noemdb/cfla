<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guía de Confirmación de Prosecución</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-gray-900 via-green-900 to-slate-900 min-h-screen">
    <!-- Header -->
    <header class="bg-gray-900/80 backdrop-blur-sm border-b border-green-700 sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('image/brand/512.png') }}" alt="Logo" class="w-12 h-12 rounded-lg">
                    <div>
                        <h1 class="text-xl font-bold text-white">Guía de Prosecución</h1>
                        <p class="text-green-200 text-sm">Asistente para de confirmación de la prosecución Estudiantil</p>
                    </div>
                </div>
                <a href="{{ route('prosecucion') }}"
                   class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-500 hover:to-green-600 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200 transform hover:scale-105">
                    Ir al asistente
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-16 px-4">
        <div class="container mx-auto max-w-4xl text-center">
            <div class="mb-8">
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-2xl">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                    Guía de Confirmación de Prosecución
                </h1>
                <p class="text-xl text-green-200 mb-8">
                    Aprende a confirmar la continuidad de tus estudiantes en 3 simples pasos
                </p>
                <div class="inline-flex items-center bg-green-800/30 border border-green-600 rounded-full px-6 py-3 text-green-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Tiempo estimado: 5 minutos
                </div>
            </div>
        </div>
    </section>

    <!-- Pasos del Proceso -->
    <section class="py-16 px-4">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-white mb-4">Proceso Paso a Paso</h2>
                <p class="text-green-200 text-lg">Sigue estos pasos para completar la confirmación exitosamente</p>
            </div>

            <!-- Paso 1 -->
            <div class="mb-16">
                <div class="bg-gray-800 rounded-2xl shadow-2xl border border-green-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-green-700 p-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mr-4">
                                <span class="text-2xl font-bold text-green-600">1</span>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">Identificación del Representante</h3>
                                <p class="text-green-100">Ingresa tu cédula de identidad para acceder al sistema</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="grid md:grid-cols-2 gap-8 items-center">
                            <div>
                                <h4 class="text-xl font-semibold text-white mb-4">¿Qué necesitas hacer?</h4>
                                <ul class="space-y-3 text-gray-300">
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Ingresa tu número de cédula completo
                                    </li>
                                    
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Haz clic en "Buscar Representante"
                                    </li>
                                </ul>

                                <div class="mt-6 p-4 bg-yellow-900/20 border border-yellow-600 rounded-lg">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-yellow-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-yellow-200 font-medium">Importante</p>
                                            <p class="text-yellow-100 text-sm">Asegúrate de que tu cédula esté registrada en el sistema como representante</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-900 rounded-xl p-6 border border-green-600">
                                <h5 class="text-lg font-semibold text-white mb-4">Ejemplo de entrada:</h5>
                                <div class="space-y-3">
                                    <div class="bg-gray-800 rounded-lg p-4 border-2 border-green-500">
                                        <label class="block text-green-200 text-sm mb-2">Cédula de Identidad</label>
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-4 0V5a2 2 0 014 0v1"></path>
                                            </svg>
                                            <span class="text-white font-mono">12345678</span>
                                        </div>
                                    </div>
                                    <button class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-3 rounded-lg font-medium">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        Buscar Representante
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paso 2 -->
            <div class="mb-16">
                <div class="bg-gray-800 rounded-2xl shadow-2xl border border-green-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mr-4">
                                <span class="text-2xl font-bold text-blue-600">2</span>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">Selección de Estudiantes</h3>
                                <p class="text-blue-100">Marca los estudiantes que continuarán en la institución</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="grid md:grid-cols-2 gap-8 items-start">
                            <div>
                                <h4 class="text-xl font-semibold text-white mb-4">¿Cómo seleccionar?</h4>
                                <ul class="space-y-3 text-gray-300 mb-6">
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Revisa la lista de tus estudiantes
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Marca el checkbox de cada estudiante que continuará
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Verifica la información de grado y sección
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Haz clic en "Confirmar Prosecución"
                                    </li>
                                </ul>

                                <div class="space-y-4">
                                    <div class="p-4 bg-green-900/20 border border-green-600 rounded-lg">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-green-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-green-200 font-medium">Estudiantes ya confirmados</p>
                                                <p class="text-green-100 text-sm">Los estudiantes previamente confirmados aparecerán marcados y no podrás desmarcarlos</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-4 bg-blue-900/20 border border-blue-600 rounded-lg">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-blue-200 font-medium">Información mostrada</p>
                                                <p class="text-blue-100 text-sm">Verás el nombre, cédula, grado, sección y edad de cada estudiante</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-900 rounded-xl p-6 border border-blue-600">
                                <h5 class="text-lg font-semibold text-white mb-4">Ejemplo de selección:</h5>
                                <div class="space-y-3">
                                    <!-- Estudiante 1 -->
                                    <div class="border-2 border-green-600 rounded-xl p-4 bg-green-900/20">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" checked disabled class="w-5 h-5 text-green-600 rounded mr-4">
                                            <div class="flex-1">
                                                <h6 class="text-white font-semibold flex items-center">
                                                    García María
                                                    <span class="ml-2 px-2 py-1 bg-green-800 text-green-200 text-xs rounded-full">Confirmado</span>
                                                </h6>
                                                <p class="text-gray-300 text-sm">CI: V-98765432</p>
                                                <p class="text-green-300 text-sm">🎓 5to Grado A</p>
                                            </div>
                                            <span class="text-gray-400 text-sm">11 años</span>
                                        </label>
                                    </div>

                                    <!-- Estudiante 2 -->
                                    <div class="border-2 border-gray-600 rounded-xl p-4 hover:border-green-600 transition-colors">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" class="w-5 h-5 text-green-600 rounded mr-4">
                                            <div class="flex-1">
                                                <h6 class="text-white font-semibold">López Carlos</h6>
                                                <p class="text-gray-300 text-sm">CI: V-87654321</p>
                                                <p class="text-green-300 text-sm">🎓 3er Grado B</p>
                                            </div>
                                            <span class="text-gray-400 text-sm">9 años</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="mt-4 flex justify-between">
                                    <button class="px-4 py-2 bg-gray-600 text-white rounded-lg">Volver</button>
                                    <button class="px-6 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg">
                                        Confirmar Prosecución
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paso 3 -->
            <div class="mb-16">
                <div class="bg-gray-800 rounded-2xl shadow-2xl border border-green-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mr-4">
                                <span class="text-2xl font-bold text-purple-600">3</span>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">Confirmación y Descarga</h3>
                                <p class="text-purple-100">Obtén tu planilla oficial de confirmación</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="grid md:grid-cols-2 gap-8 items-start">
                            <div>
                                <h4 class="text-xl font-semibold text-white mb-4">¿Qué obtienes?</h4>
                                <ul class="space-y-3 text-gray-300 mb-6">
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Planilla oficial en formato PDF
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                        </svg>
                                        Código QR para verificación
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Confirmación oficial registrada
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a2 2 0 100-4 2 2 0 000 4zm6 0a2 2 0 100-4 2 2 0 000 4z"></path>
                                        </svg>
                                        Documento con validez legal
                                    </li>
                                </ul>

                                <div class="p-4 bg-purple-900/20 border border-purple-600 rounded-lg">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-purple-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-purple-200 font-medium">Importante</p>
                                            <p class="text-purple-100 text-sm">Guarda el documento PDF y el código QR para futuras referencias</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-900 rounded-xl p-6 border border-purple-600">
                                <h5 class="text-lg font-semibold text-white mb-4">Pantalla de confirmación:</h5>

                                <!-- Simulación de pantalla final -->
                                <div class="bg-gray-800 rounded-lg p-4 border border-green-600">
                                    <div class="text-center mb-4">
                                        <div class="w-16 h-16 mx-auto mb-3 bg-green-600 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <h6 class="text-white font-bold">¡Prosecución Confirmada!</h6>
                                        <p class="text-gray-300 text-sm">Registro exitoso</p>
                                    </div>

                                    <div class="bg-green-900/20 rounded-lg p-3 mb-4">
                                        <h6 class="text-white text-sm font-medium mb-2">Estudiantes confirmados:</h6>
                                        <div class="space-y-1 text-xs">
                                            <div class="flex justify-between text-gray-300">
                                                <span>García María</span>
                                                <span>5to Grado A</span>
                                            </div>
                                            <div class="flex justify-between text-gray-300">
                                                <span>López Carlos</span>
                                                <span>3er Grado B</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center mb-4">
                                        <div class="w-20 h-20 mx-auto bg-white rounded-lg flex items-center justify-center mb-2">
                                            <div class="w-16 h-16 bg-gray-800 rounded grid grid-cols-4 gap-0.5 p-1">
                                                <div class="bg-gray-600 rounded-sm"></div>
                                                <div class="bg-gray-300 rounded-sm"></div>
                                                <div class="bg-gray-600 rounded-sm"></div>
                                                <div class="bg-gray-300 rounded-sm"></div>
                                                <div class="bg-gray-300 rounded-sm"></div>
                                                <div class="bg-gray-600 rounded-sm"></div>
                                                <div class="bg-gray-300 rounded-sm"></div>
                                                <div class="bg-gray-600 rounded-sm"></div>
                                                <div class="bg-gray-600 rounded-sm"></div>
                                                <div class="bg-gray-300 rounded-sm"></div>
                                                <div class="bg-gray-600 rounded-sm"></div>
                                                <div class="bg-gray-300 rounded-sm"></div>
                                                <div class="bg-gray-300 rounded-sm"></div>
                                                <div class="bg-gray-600 rounded-sm"></div>
                                                <div class="bg-gray-300 rounded-sm"></div>
                                                <div class="bg-gray-600 rounded-sm"></div>
                                            </div>
                                        </div>
                                        <p class="text-gray-400 text-xs">Código QR</p>
                                    </div>

                                    <div class="space-y-2">
                                        <button class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-2 rounded-lg text-sm font-medium">
                                            📄 Descargar Planilla
                                        </button>
                                        <button class="w-full bg-gray-600 text-white py-2 rounded-lg text-sm">
                                            🔄 Nueva Consulta
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 px-4 bg-gray-800/50">
        <div class="container mx-auto max-w-4xl">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-white mb-4">Preguntas Frecuentes</h2>
                <p class="text-green-200">Resuelve tus dudas sobre el proceso de confirmación</p>
            </div>

            <div class="space-y-6">
                <!-- FAQ 1 -->
                <div class="bg-gray-800 rounded-xl border border-green-700 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-white mb-2 flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            ¿Qué hago si mi cédula no aparece en el sistema?
                        </h3>
                        <p class="text-gray-300">
                            Si tu cédula no es reconocida, verifica que esté registrada como representante en la institución.
                            Contacta a la dirección académica para actualizar tus datos.
                        </p>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-gray-800 rounded-xl border border-green-700 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-white mb-2 flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            ¿Puedo cambiar mi selección después de confirmar?
                        </h3>
                        <p class="text-gray-300">
                            Una vez confirmada la prosecución de un estudiante, no podrás desmarcarla. Sin embargo,
                            puedes agregar estudiantes adicionales en futuras consultas.
                        </p>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-gray-800 rounded-xl border border-green-700 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-white mb-2 flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            ¿Es obligatorio descargar la planilla?
                        </h3>
                        <p class="text-gray-300">
                            Es recomendable descargar y guardar la planilla como comprobante oficial de tu confirmación.
                            Este documento puede ser requerido para trámites futuros.
                        </p>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="bg-gray-800 rounded-xl border border-green-700 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-white mb-2 flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            ¿Hasta cuándo puedo confirmar la prosecución?
                        </h3>
                        <p class="text-gray-300">
                            El período de confirmación está establecido por la institución. Consulta las fechas límite
                            con la administración académica para no perder la oportunidad.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 px-4">
        <div class="container mx-auto max-w-4xl text-center">
            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl p-8 shadow-2xl">
                <h2 class="text-3xl font-bold text-white mb-4">¿Listo para confirmar?</h2>
                <p class="text-green-100 text-lg mb-8">
                    Sigue los pasos de esta guía y completa la confirmación de prosecución de tus estudiantes
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('prosecucion') }}"
                       class="bg-white text-green-600 px-8 py-3 rounded-lg font-bold text-lg hover:bg-gray-100 transition-colors duration-200 transform hover:scale-105">
                        Iniciar Proceso
                    </a>
                    <button onclick="window.print()"
                            class="bg-green-800 text-white px-8 py-3 rounded-lg font-medium text-lg hover:bg-green-900 transition-colors duration-200">
                        📄 Imprimir Guía
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 border-t border-green-700 py-8 px-4">
        <div class="container mx-auto max-w-4xl text-center">
            <div class="flex items-center justify-center mb-4">
                <img src="{{ asset('image/brand/512.png') }}" alt="Logo" class="w-8 h-8 rounded mr-3">
                <span class="text-white font-semibold">{{ config('app.name') }}</span>
            </div>
            <p class="text-gray-400 text-sm">
                Sistema Automatizado para la Gestión Escolar SAEFL - Confirmación de Prosecución Estudiantil
            </p>
            <p class="text-gray-500 text-xs mt-2">
                © {{ date('Y') }} Todos los derechos reservados
            </p>
        </div>
    </footer>
</body>
</html>
