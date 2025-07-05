@extends('layouts.vote')

@section('title', 'Guía del Módulo de Votación')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-green-900 to-gray-900 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header Principal -->
            <div class="text-center mb-12">
                <div class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-2xl">
                    <div class="text-6xl mb-4">📚</div>
                    <h1 class="text-4xl font-bold text-white mb-4">
                        Guía del Módulo de Votación
                    </h1>
                    <p class="text-gray-300 text-lg">
                        Manual completo para utilizar todas las funcionalidades del módulo de votación anónima
                    </p>
                </div>
            </div>

            <!-- Índice de Contenidos -->
            <div class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700 p-6 shadow-2xl mb-8">
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Índice de Contenidos
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <a href="#introduccion"
                            class="block text-green-400 hover:text-green-300 transition-colors duration-200">
                            1. Introducción al Módulo
                        </a>
                        <a href="#como-votar"
                            class="block text-green-400 hover:text-green-300 transition-colors duration-200">
                            2. Cómo Participar en una Votación
                        </a>
                        <a href="#resultados"
                            class="block text-green-400 hover:text-green-300 transition-colors duration-200">
                            3. Visualización de Resultados
                        </a>
                        <a href="#qr-participacion"
                            class="block text-green-400 hover:text-green-300 transition-colors duration-200">
                            4. Códigos QR y Participación
                        </a>
                    </div>
                    <div class="space-y-2">
                        <a href="#tiempo-real"
                            class="block text-green-400 hover:text-green-300 transition-colors duration-200">
                            5. Actualizaciones en Tiempo Real
                        </a>
                        <a href="#navegacion"
                            class="block text-green-400 hover:text-green-300 transition-colors duration-200">
                            6. Navegación del Módulo
                        </a>
                        <a href="#seguridad"
                            class="block text-green-400 hover:text-green-300 transition-colors duration-200">
                            7. Seguridad y Privacidad
                        </a>
                        <a href="#faq" class="block text-green-400 hover:text-green-300 transition-colors duration-200">
                            8. Preguntas Frecuentes
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sección 1: Introducción -->
            <div id="introduccion"
                class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-2xl mb-8">
                <h2 class="text-3xl font-bold text-white mb-6 flex items-center">
                    <span
                        class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg font-bold mr-3">1</span>
                    Introducción al Módulo
                </h2>

                <div class="space-y-6 text-gray-300">
                    <div
                        class="bg-gradient-to-r from-green-700/20 to-green-800/20 rounded-xl p-6 border border-green-700/30">
                        <h3 class="text-xl font-semibold text-white mb-3">🎯 ¿Qué es el Módulo de Votación?</h3>
                        <p class="leading-relaxed">
                            El Módulo de Votación es un módulo completo y seguro que permite realizar encuestas anónimas en
                            tiempo real.
                            Está diseñado para facilitar la participación democrática y la recolección de opiniones de
                            manera transparente y eficiente.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-700/20 rounded-xl p-6 border border-blue-700/30">
                            <h4 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Características Principales
                            </h4>
                            <ul class="space-y-2 text-sm">
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                                    Votación completamente anónima
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                                    Resultados en tiempo real
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                                    Códigos QR para participación
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                                    Dashboard de monitoreo
                                </li>
                            </ul>
                        </div>

                        <div class="bg-purple-700/20 rounded-xl p-6 border border-purple-700/30">
                            <h4 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Beneficios del Módulo
                            </h4>
                            <ul class="space-y-2 text-sm">
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-purple-400 rounded-full mr-2"></span>
                                    Fácil de usar y accesible
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-purple-400 rounded-full mr-2"></span>
                                    Seguridad y privacidad garantizada
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-purple-400 rounded-full mr-2"></span>
                                    Resultados instantáneos
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-purple-400 rounded-full mr-2"></span>
                                    Comprobante de participación
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 2: Cómo Votar -->
            <div id="como-votar"
                class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-2xl mb-8">
                <h2 class="text-3xl font-bold text-white mb-6 flex items-center">
                    <span
                        class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg font-bold mr-3">2</span>
                    Cómo Participar en una Votación
                </h2>

                <div class="space-y-8">
                    <!-- Paso 1 -->
                    <div class="flex items-start space-x-4">
                        <div
                            class="bg-green-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold flex-shrink-0">
                            1
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-white mb-3">Acceder a la Encuesta</h3>
                            <div class="bg-gray-700/50 rounded-xl p-4 mb-4">
                                <p class="text-gray-300 mb-3">
                                    Para participar en una votación, necesitas el <strong class="text-green-400">token de
                                        acceso</strong>
                                    proporcionado por el organizador de la encuesta.
                                </p>
                                <div class="bg-gray-900/50 rounded-lg p-3 font-mono text-sm text-green-400">
                                    Ejemplo: /poll/voting/abc123def456
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 2 -->
                    <div class="flex items-start space-x-4">
                        <div
                            class="bg-green-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold flex-shrink-0">
                            2
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-white mb-3">Revisar la Información</h3>
                            <div class="bg-gray-700/50 rounded-xl p-4">
                                <p class="text-gray-300 mb-3">
                                    Una vez en la página de votación, podrás ver:
                                </p>
                                <ul class="space-y-2 text-gray-300">
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Título y descripción de la encuesta
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Todas las opciones disponibles
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Tiempo restante para votar
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 3 -->
                    <div class="flex items-start space-x-4">
                        <div
                            class="bg-green-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold flex-shrink-0">
                            3
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-white mb-3">Seleccionar tu Opción</h3>
                            <div class="bg-gray-700/50 rounded-xl p-4">
                                <p class="text-gray-300 mb-3">
                                    Haz clic en la opción que mejor represente tu opinión.
                                    Las opciones se muestran como botones claramente identificados.
                                </p>
                                <div class="bg-yellow-700/20 border border-yellow-700/30 rounded-lg p-3">
                                    <p class="text-yellow-200 text-sm flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <strong>Importante:</strong> Solo puedes votar una vez por encuesta.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 4 -->
                    <div class="flex items-start space-x-4">
                        <div
                            class="bg-green-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold flex-shrink-0">
                            4
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-white mb-3">Confirmar tu Voto</h3>
                            <div class="bg-gray-700/50 rounded-xl p-4">
                                <p class="text-gray-300 mb-3">
                                    Después de seleccionar tu opción, confirma tu voto haciendo clic en el botón
                                    <strong class="text-green-400">"Confirmar Voto"</strong>.
                                </p>
                                <p class="text-gray-300">
                                    Una vez confirmado, tu voto será registrado de forma anónima y segura.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 3: Resultados -->
            <div id="resultados"
                class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-2xl mb-8">
                <h2 class="text-3xl font-bold text-white mb-6 flex items-center">
                    <span
                        class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg font-bold mr-3">3</span>
                    Visualización de Resultados
                </h2>

                <div class="space-y-6">
                    <div class="bg-gradient-to-r from-blue-700/20 to-blue-800/20 rounded-xl p-6 border border-blue-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Dashboard de Resultados
                        </h3>
                        <p class="text-gray-300 mb-4">
                            El módulo ofrece múltiples formas de visualizar los resultados de las encuestas:
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-700/50 rounded-lg p-4">
                                <h4 class="font-semibold text-white mb-2">📊 Resultados Individuales</h4>
                                <p class="text-gray-300 text-sm">
                                    Cada encuesta muestra sus resultados con gráficos de barras,
                                    porcentajes y número total de votos.
                                </p>
                            </div>
                            <div class="bg-gray-700/50 rounded-lg p-4">
                                <h4 class="font-semibold text-white mb-2">🎯 Dashboard General</h4>
                                <p class="text-gray-300 text-sm">
                                    Vista panorámica de todas las encuestas activas con
                                    estadísticas generales y monitoreo en tiempo real.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-r from-green-700/20 to-green-800/20 rounded-xl p-6 border border-green-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Información Mostrada
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center">
                                <div
                                    class="bg-green-600 text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-2 text-xl">
                                    %
                                </div>
                                <h4 class="font-semibold text-white mb-1">Porcentajes</h4>
                                <p class="text-gray-300 text-sm">Distribución porcentual de cada opción</p>
                            </div>
                            <div class="text-center">
                                <div
                                    class="bg-blue-600 text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-2 text-xl">
                                    #
                                </div>
                                <h4 class="font-semibold text-white mb-1">Votos Totales</h4>
                                <p class="text-gray-300 text-sm">Número exacto de votos por opción</p>
                            </div>
                            <div class="text-center">
                                <div
                                    class="bg-purple-600 text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-2 text-xl">
                                    📊
                                </div>
                                <h4 class="font-semibold text-white mb-1">Gráficos</h4>
                                <p class="text-gray-300 text-sm">Barras de progreso visuales</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 4: QR y Participación -->
            <div id="qr-participacion"
                class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-2xl mb-8">
                <h2 class="text-3xl font-bold text-white mb-6 flex items-center">
                    <span
                        class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg font-bold mr-3">4</span>
                    Códigos QR y Comprobante de Participación
                </h2>

                <div class="space-y-6">
                    <div
                        class="bg-gradient-to-r from-purple-700/20 to-purple-800/20 rounded-xl p-6 border border-purple-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 3a1 1 0 00-1 1v3a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1h-3zm1 2v1h1V5h-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            ¿Qué es el Código QR?
                        </h3>
                        <p class="text-gray-300 mb-4">
                            Después de votar, el módulo genera automáticamente un <strong class="text-purple-400">código
                                QR único</strong>
                            que sirve como comprobante de tu participación en la encuesta.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-700/50 rounded-lg p-4">
                                <h4 class="font-semibold text-white mb-3 flex items-center">
                                    <span class="w-2 h-2 bg-purple-400 rounded-full mr-2"></span>
                                    Características del QR
                                </h4>
                                <ul class="space-y-2 text-gray-300 text-sm">
                                    <li>• Único e irrepetible para cada voto</li>
                                    <li>• Contiene información encriptada</li>
                                    <li>• Válido permanentemente</li>
                                    <li>• Escaneable desde cualquier dispositivo</li>
                                </ul>
                            </div>
                            <div class="bg-gray-700/50 rounded-lg p-4">
                                <h4 class="font-semibold text-white mb-3 flex items-center">
                                    <span class="w-2 h-2 bg-purple-400 rounded-full mr-2"></span>
                                    Usos del Código QR
                                </h4>
                                <ul class="space-y-2 text-gray-300 text-sm">
                                    <li>• Verificar tu participación</li>
                                    <li>• Ver tu voto registrado</li>
                                    <li>• Acceder a estadísticas detalladas</li>
                                    <li>• Compartir comprobante</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-r from-green-700/20 to-green-800/20 rounded-xl p-6 border border-green-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Página de Participación
                        </h3>
                        <p class="text-gray-300 mb-4">
                            Al escanear el código QR, accederás a una página personalizada que muestra:
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                                <div class="text-2xl mb-2">🗳️</div>
                                <h4 class="font-semibold text-white mb-1">Tu Voto</h4>
                                <p class="text-gray-300 text-xs">Opción que seleccionaste</p>
                            </div>
                            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                                <div class="text-2xl mb-2">📊</div>
                                <h4 class="font-semibold text-white mb-1">Resultados</h4>
                                <p class="text-gray-300 text-xs">Estado actual de la encuesta</p>
                            </div>
                            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                                <div class="text-2xl mb-2">⏰</div>
                                <h4 class="font-semibold text-white mb-1">Timestamp</h4>
                                <p class="text-gray-300 text-xs">Fecha y hora de tu voto</p>
                            </div>
                            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                                <div class="text-2xl mb-2">🔒</div>
                                <h4 class="font-semibold text-white mb-1">Seguridad</h4>
                                <p class="text-gray-300 text-xs">Verificación de autenticidad</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 5: Tiempo Real -->
            <div id="tiempo-real"
                class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-2xl mb-8">
                <h2 class="text-3xl font-bold text-white mb-6 flex items-center">
                    <span
                        class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg font-bold mr-3">5</span>
                    Actualizaciones en Tiempo Real
                </h2>

                <div class="space-y-6">
                    <div class="bg-gradient-to-r from-red-700/20 to-red-800/20 rounded-xl p-6 border border-red-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <div class="w-3 h-3 bg-red-400 rounded-full mr-2 animate-pulse"></div>
                            Módulo de Actualización Automática
                        </h3>
                        <p class="text-gray-300 mb-4">
                            El módulo actualiza automáticamente los resultados cada <strong class="text-red-400">3
                                segundos</strong>,
                            permitiendo ver los cambios en tiempo real sin necesidad de recargar la página.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-700/50 rounded-lg p-4">
                                <h4 class="font-semibold text-white mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Auto-Refresh
                                </h4>
                                <p class="text-gray-300 text-sm">
                                    Los datos se actualizan automáticamente sin intervención del usuario.
                                </p>
                            </div>
                            <div class="bg-gray-700/50 rounded-lg p-4">
                                <h4 class="font-semibold text-white mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Indicadores Visuales
                                </h4>
                                <p class="text-gray-300 text-sm">
                                    Puntos pulsantes y timestamps muestran el estado de actualización.
                                </p>
                            </div>
                            <div class="bg-gray-700/50 rounded-lg p-4">
                                <h4 class="font-semibold text-white mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Optimización
                                </h4>
                                <p class="text-gray-300 text-sm">
                                    Solo se actualizan los datos que han cambiado, optimizando el rendimiento.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-blue-700/20 to-blue-800/20 rounded-xl p-6 border border-blue-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Beneficios del Tiempo Real
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-semibold text-white mb-2">Para Votantes:</h4>
                                <ul class="space-y-1 text-gray-300 text-sm">
                                    <li>• Ver el impacto inmediato de su voto</li>
                                    <li>• Seguir la evolución de la encuesta</li>
                                    <li>• Verificar que su voto fue registrado</li>
                                    <li>• Observar tendencias en tiempo real</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-semibold text-white mb-2">Para Organizadores:</h4>
                                <ul class="space-y-1 text-gray-300 text-sm">
                                    <li>• Monitorear la participación activa</li>
                                    <li>• Detectar patrones de votación</li>
                                    <li>• Tomar decisiones basadas en datos actuales</li>
                                    <li>• Identificar momentos de alta actividad</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 6: Navegación -->
            <div id="navegacion"
                class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-2xl mb-8">
                <h2 class="text-3xl font-bold text-white mb-6 flex items-center">
                    <span
                        class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg font-bold mr-3">6</span>
                    Navegación del Módulo
                </h2>

                <div class="space-y-6">
                    {{--
                    <div
                        class="bg-gradient-to-r from-indigo-700/20 to-indigo-800/20 rounded-xl p-6 border border-indigo-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Estructura de Navegación
                        </h3>
                        <p class="text-gray-300 mb-4">
                            El módulo está organizado de manera intuitiva para facilitar el acceso a todas las
                            funcionalidades:
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h4 class="font-semibold text-white mb-3">🏠 Páginas Principales</h4>
                                <div class="space-y-3">
                                    <div class="bg-gray-700/50 rounded-lg p-3">
                                        <div class="font-medium text-white">/voting</div>
                                        <div class="text-gray-300 text-sm">Página principal del módulo</div>
                                    </div>
                                    <div class="bg-gray-700/50 rounded-lg p-3">
                                        <div class="font-medium text-white">/voting/results</div>
                                        <div class="text-gray-300 text-sm">Dashboard de resultados</div>
                                    </div>
                                    <div class="bg-gray-700/50 rounded-lg p-3">
                                        <div class="font-medium text-white">/voting/guia</div>
                                        <div class="text-gray-300 text-sm">Esta guía completa</div>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <h4 class="font-semibold text-white mb-3">🔗 Enlaces Dinámicos</h4>
                                <div class="space-y-3">
                                    <div class="bg-gray-700/50 rounded-lg p-3">
                                        <div class="font-medium text-white">/poll/voting/{token}</div>
                                        <div class="text-gray-300 text-sm">Página de votación específica</div>
                                    </div>
                                    <div class="bg-gray-700/50 rounded-lg p-3">
                                        <div class="font-medium text-white">/poll/participation/{uuid}</div>
                                        <div class="text-gray-300 text-sm">Comprobante de participación</div>
                                    </div>
                                    <div class="bg-gray-700/50 rounded-lg p-3">
                                        <div class="font-medium text-white">/poll/voting/result/{token}</div>
                                        <div class="text-gray-300 text-sm">Resultados de encuesta individual</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    --}}

                    <div
                        class="bg-gradient-to-r from-green-700/20 to-green-800/20 rounded-xl p-6 border border-green-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Flujo de Navegación Recomendado
                        </h3>
                        <div class="flex flex-wrap items-center justify-center gap-4 mb-4">
                            <div class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                1. Inicio (/voting/index)
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <div class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                2. Votar (/poll/voting/{token})
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <div class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                3. QR + Resultados
                            </div>
                        </div>
                        <p class="text-gray-300 text-sm text-center">
                            Este flujo garantiza la mejor experiencia de usuario y aprovecha todas las funcionalidades del
                            módulo.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sección 7: Seguridad -->
            <div id="seguridad"
                class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-2xl mb-8">
                <h2 class="text-3xl font-bold text-white mb-6 flex items-center">
                    <span
                        class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg font-bold mr-3">7</span>
                    Seguridad y Privacidad
                </h2>

                <div class="space-y-6">
                    <div class="bg-gradient-to-r from-red-700/20 to-red-800/20 rounded-xl p-6 border border-red-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Medidas de Seguridad Implementadas
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="bg-gray-700/50 rounded-lg p-4">
                                    <h4 class="font-semibold text-white mb-2 flex items-center">
                                        <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                                        Anonimato Garantizado
                                    </h4>
                                    <p class="text-gray-300 text-sm">
                                        No se almacena información personal identificable.
                                        Solo se registra el voto y un identificador único anónimo.
                                    </p>
                                </div>
                                <div class="bg-gray-700/50 rounded-lg p-4">
                                    <h4 class="font-semibold text-white mb-2 flex items-center">
                                        <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                                        Prevención de Voto Múltiple
                                    </h4>
                                    <p class="text-gray-300 text-sm">
                                        Módulo de fingerprinting que previene votos duplicados
                                        sin comprometer la privacidad del usuario.
                                    </p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="bg-gray-700/50 rounded-lg p-4">
                                    <h4 class="font-semibold text-white mb-2 flex items-center">
                                        <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                                        Encriptación de Datos
                                    </h4>
                                    <p class="text-gray-300 text-sm">
                                        Toda la información se transmite de forma segura
                                        utilizando protocolos de encriptación estándar.
                                    </p>
                                </div>
                                <div class="bg-gray-700/50 rounded-lg p-4">
                                    <h4 class="font-semibold text-white mb-2 flex items-center">
                                        <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                                        Tokens Únicos
                                    </h4>
                                    <p class="text-gray-300 text-sm">
                                        Cada encuesta utiliza tokens únicos e irrepetibles
                                        que controlan el acceso de forma segura.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-r from-yellow-700/20 to-yellow-800/20 rounded-xl p-6 border border-yellow-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Recomendaciones de Privacidad
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                                <div class="text-2xl mb-2">🔒</div>
                                <h4 class="font-semibold text-white mb-2">Navegación Privada</h4>
                                <p class="text-gray-300 text-xs">
                                    Usa modo incógnito para mayor privacidad
                                </p>
                            </div>
                            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                                <div class="text-2xl mb-2">📱</div>
                                <h4 class="font-semibold text-white mb-2">Dispositivo Personal</h4>
                                <p class="text-gray-300 text-xs">
                                    Vota desde tu dispositivo personal
                                </p>
                            </div>
                            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                                <div class="text-2xl mb-2">🚫</div>
                                <h4 class="font-semibold text-white mb-2">No Compartir</h4>
                                <p class="text-gray-300 text-xs">
                                    No compartas tu código QR personal
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 8: FAQ -->
            <div id="faq"
                class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-2xl mb-8">
                <h2 class="text-3xl font-bold text-white mb-6 flex items-center">
                    <span
                        class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg font-bold mr-3">8</span>
                    Preguntas Frecuentes (FAQ)
                </h2>

                <div class="space-y-6">
                    <div class="space-y-4">
                        <div class="bg-gray-700/50 rounded-xl p-6 border border-gray-600">
                            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <span class="text-green-400 mr-2">Q:</span>
                                ¿Puedo votar más de una vez en la misma encuesta?
                            </h3>
                            <p class="text-gray-300 ml-6">
                                <span class="text-blue-400 mr-2">A:</span>
                                No, el módulo está diseñado para permitir solo un voto por persona por encuesta.
                                Utilizamos tecnología de fingerprinting para prevenir votos duplicados manteniendo el
                                anonimato.
                            </p>
                        </div>

                        <div class="bg-gray-700/50 rounded-xl p-6 border border-gray-600">
                            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <span class="text-green-400 mr-2">Q:</span>
                                ¿Mis datos personales están seguros?
                            </h3>
                            <p class="text-gray-300 ml-6">
                                <span class="text-blue-400 mr-2">A:</span>
                                Absolutamente. El módulo no recopila ni almacena información personal identificable.
                                Solo se registra tu voto de forma completamente anónima.
                            </p>
                        </div>

                        <div class="bg-gray-700/50 rounded-xl p-6 border border-gray-600">
                            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <span class="text-green-400 mr-2">Q:</span>
                                ¿Qué pasa si pierdo mi código QR?
                            </h3>
                            <p class="text-gray-300 ml-6">
                                <span class="text-blue-400 mr-2">A:</span>
                                El código QR es único e irrepetible. Si lo pierdes, no podrás recuperar el comprobante
                                específico,
                                pero tu voto sigue siendo válido y contabilizado en los resultados.
                            </p>
                        </div>

                        <div class="bg-gray-700/50 rounded-xl p-6 border border-gray-600">
                            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <span class="text-green-400 mr-2">Q:</span>
                                ¿Los resultados se actualizan automáticamente?
                            </h3>
                            <p class="text-gray-300 ml-6">
                                <span class="text-blue-400 mr-2">A:</span>
                                Sí, los resultados se actualizan automáticamente cada 3 segundos en todas las páginas de
                                resultados.
                                No necesitas recargar la página manualmente.
                            </p>
                        </div>

                        <div class="bg-gray-700/50 rounded-xl p-6 border border-gray-600">
                            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <span class="text-green-400 mr-2">Q:</span>
                                ¿Puedo cambiar mi voto después de enviarlo?
                            </h3>
                            <p class="text-gray-300 ml-6">
                                <span class="text-blue-400 mr-2">A:</span>
                                No, una vez que confirmas tu voto, este queda registrado de forma permanente y no puede ser
                                modificado.
                                Asegúrate de revisar tu selección antes de confirmar.
                            </p>
                        </div>

                        <div class="bg-gray-700/50 rounded-xl p-6 border border-gray-600">
                            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <span class="text-green-400 mr-2">Q:</span>
                                ¿Funciona en dispositivos móviles?
                            </h3>
                            <p class="text-gray-300 ml-6">
                                <span class="text-blue-400 mr-2">A:</span>
                                Sí, el módulo está completamente optimizado para dispositivos móviles, tablets y
                                computadoras.
                                La interfaz se adapta automáticamente al tamaño de tu pantalla.
                            </p>
                        </div>

                        <div class="bg-gray-700/50 rounded-xl p-6 border border-gray-600">
                            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <span class="text-green-400 mr-2">Q:</span>
                                ¿Qué navegadores son compatibles?
                            </h3>
                            <p class="text-gray-300 ml-6">
                                <span class="text-blue-400 mr-2">A:</span>
                                El módulo funciona en todos los navegadores modernos: Chrome, Firefox, Safari, Edge y
                                Opera.
                                Recomendamos mantener tu navegador actualizado para la mejor experiencia.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de navegación finales -->
            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="{{ route('voting.index') }}"
                    class="inline-flex items-center px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                    Ir a Votación
                </a>

                {{-- <a href="{{ route('voting.results') }}"
                    class="inline-flex items-center px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Ver Resultados
                </a> --}}
            </div>

            <!-- Footer informativo -->
            <div class="text-center mt-12 pt-8 border-t border-gray-700">
                <p class="text-gray-400 text-sm">
                    📚 Esta guía se actualiza constantemente para reflejar las últimas funcionalidades del módulo.
                </p>
                <p class="text-gray-500 text-xs mt-2">
                    Última actualización: {{ now()->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>
    </div>


@endsection



@section('script')
    @parent
    <script>
        // Smooth scroll para los enlaces del índice
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Highlight de la sección actual mientras se hace scroll
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('[id]');
            const navLinks = document.querySelectorAll('a[href^="#"]');

            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (scrollY >= (sectionTop - 200)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('text-green-300', 'font-semibold');
                link.classList.add('text-green-400');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.remove('text-green-400');
                    link.classList.add('text-green-300', 'font-semibold');
                }
            });
        });
    </script>
@endsection
