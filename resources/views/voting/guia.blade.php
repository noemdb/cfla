@extends('layouts.vote')

@section('title', 'Guía del Asistente de Participación')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-green-900 to-gray-900 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header Principal -->
            <div class="text-center mb-12">
                <div class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-2xl">
                    <div class="text-6xl mb-4">🤖</div>
                    <h1 class="text-4xl font-bold text-white mb-4">
                        Guía del Asistente de Participación
                    </h1>
                    <p class="text-gray-300 text-lg">
                        Manual completo para utilizar el Asistente Inteligente de Votación
                    </p>
                    <div
                        class="mt-6 inline-flex items-center px-4 py-2 bg-green-600/20 border border-green-600/30 rounded-lg">
                        <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-green-300 text-sm font-medium">Método único y oficial de participación</span>
                    </div>
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
                        <a href="#que-es-asistente"
                            class="block text-green-400 hover:text-green-300 transition-colors duration-200">
                            1. ¿Qué es el Asistente de Participación?
                        </a>
                        <a href="#como-acceder"
                            class="block text-green-400 hover:text-green-300 transition-colors duration-200">
                            2. Cómo Acceder al Asistente
                        </a>
                        <a href="#proceso-participacion"
                            class="block text-green-400 hover:text-green-300 transition-colors duration-200">
                            3. Proceso de Participación Paso a Paso
                        </a>
                        <a href="#navegacion-asistente"
                            class="block text-green-400 hover:text-green-300 transition-colors duration-200">
                            4. Navegación del Asistente
                        </a>
                    </div>
                    <div class="space-y-2">
                        <a href="#confirmacion-voto"
                            class="block text-green-400 hover:text-green-300 transition-colors duration-200">
                            5. Confirmación y Alertas de Voto
                        </a>
                        <a href="#comprobantes-qr"
                            class="block text-green-400 hover:text-green-300 transition-colors duration-200">
                            6. Comprobantes QR y Participación
                        </a>
                        <a href="#seguridad-privacidad"
                            class="block text-green-400 hover:text-green-300 transition-colors duration-200">
                            7. Seguridad y Privacidad
                        </a>
                        <a href="#faq" class="block text-green-400 hover:text-green-300 transition-colors duration-200">
                            8. Preguntas Frecuentes
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sección 1: ¿Qué es el Asistente? -->
            <div id="que-es-asistente"
                class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-2xl mb-8">
                <h2 class="text-3xl font-bold text-white mb-6 flex items-center">
                    <span
                        class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg font-bold mr-3">1</span>
                    ¿Qué es el Asistente de Participación?
                </h2>
                <div class="space-y-6 text-gray-300">
                    <div
                        class="bg-gradient-to-r from-green-700/20 to-green-800/20 rounded-xl p-6 border border-green-700/30">
                        <h3 class="text-xl font-semibold text-white mb-3">🤖 Asistente Inteligente de Votación</h3>
                        <p class="leading-relaxed">
                            El Asistente de Participación es un sistema inteligente que te guía paso a paso a través de
                            todas las encuestas activas disponibles.
                            Es el <strong class="text-green-400">único método oficial</strong> para participar en las
                            votaciones, diseñado para garantizar
                            una experiencia completa, segura y eficiente.
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
                                    Navegación automática entre encuestas
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                                    Progreso visual en tiempo real
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                                    Identificación segura del dispositivo
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                                    Alertas de confirmación inteligentes
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                                    Generación automática de comprobantes QR
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
                                Ventajas del Asistente
                            </h4>
                            <ul class="space-y-2 text-sm">
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-purple-400 rounded-full mr-2"></span>
                                    No necesitas tokens individuales
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-purple-400 rounded-full mr-2"></span>
                                    Acceso a todas las encuestas activas
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-purple-400 rounded-full mr-2"></span>
                                    Prevención automática de votos duplicados
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-purple-400 rounded-full mr-2"></span>
                                    Resumen completo de participaciones
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-purple-400 rounded-full mr-2"></span>
                                    Experiencia optimizada y fluida
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-yellow-700/20 border border-yellow-700/30 rounded-xl p-6">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 mr-3 text-yellow-400 flex-shrink-0 mt-1" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h4 class="text-lg font-semibold text-yellow-300 mb-2">Importante: Método Único de
                                    Participación</h4>
                                <p class="text-yellow-200 text-sm">
                                    El Asistente de Participación es el <strong>único método oficial</strong> para votar en
                                    las encuestas.
                                    No se utilizan tokens individuales ni otros métodos de acceso. Todo el proceso se
                                    realiza a través de esta interfaz unificada.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 2: Cómo Acceder -->
            <div id="como-acceder"
                class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-2xl mb-8">
                <h2 class="text-3xl font-bold text-white mb-6 flex items-center">
                    <span
                        class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg font-bold mr-3">2</span>
                    Cómo Acceder al Asistente
                </h2>
                <div class="space-y-6">
                    <div class="bg-gradient-to-r from-blue-700/20 to-blue-800/20 rounded-xl p-6 border border-blue-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            URL de Acceso Directo
                        </h3>
                        <div class="bg-gray-900/50 rounded-lg p-4 mb-4">
                            <div class="font-mono text-green-400 text-lg text-center">
                                /voting/asistent
                            </div>
                        </div>
                        <p class="text-gray-300 text-center">
                            Esta es la única URL que necesitas para acceder a todas las encuestas activas
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-700/50 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Requisitos del Sistema
                            </h4>
                            <ul class="space-y-2 text-gray-300 text-sm">
                                <li>• Navegador web moderno (Chrome, Firefox, Safari, Edge)</li>
                                <li>• JavaScript habilitado</li>
                                <li>• Conexión a internet estable</li>
                                <li>• Dispositivo con capacidad WebRTC (recomendado)</li>
                            </ul>
                        </div>

                        <div class="bg-gray-700/50 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z">
                                    </path>
                                </svg>
                                Compatibilidad de Dispositivos
                            </h4>
                            <ul class="space-y-2 text-gray-300 text-sm">
                                <li>• Computadoras de escritorio y portátiles</li>
                                <li>• Tablets (iPad, Android)</li>
                                <li>• Smartphones (iOS, Android)</li>
                                <li>• Interfaz responsive y adaptativa</li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-green-700/20 border border-green-700/30 rounded-xl p-6">
                        <h4 class="text-lg font-semibold text-white mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Acceso Rápido
                        </h4>
                        <div class="text-center">
                            <a href="{{ route('voting.asistent') }}"
                                class="inline-flex items-center px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Acceder al Asistente de Participación
                            </a>
                            <p class="text-gray-400 text-sm mt-2">Haz clic para comenzar tu participación ahora</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 3: Proceso de Participación -->
            <div id="proceso-participacion"
                class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-2xl mb-8">
                <h2 class="text-3xl font-bold text-white mb-6 flex items-center">
                    <span
                        class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg font-bold mr-3">3</span>
                    Proceso de Participación Paso a Paso
                </h2>
                <div class="space-y-8">

                    <!-- Paso 1: Inicialización -->
                    <div class="flex items-start space-x-4">
                        <div
                            class="bg-green-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold flex-shrink-0">
                            1</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-white mb-3">Inicialización del Sistema</h3>
                            <div class="bg-gray-700/50 rounded-xl p-4 mb-4">
                                <p class="text-gray-300 mb-3">
                                    Al acceder al asistente, el sistema automáticamente:
                                </p>
                                <ul class="space-y-2 text-gray-300 text-sm">
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Genera una identificación única y segura de tu dispositivo
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Detecta tu dirección IP privada para mayor seguridad
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Carga todas las encuestas activas disponibles
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Verifica si ya has participado en alguna encuesta
                                    </li>
                                </ul>
                                <div class="bg-yellow-700/20 border border-yellow-700/30 rounded-lg p-3 mt-4">
                                    <p class="text-yellow-200 text-sm flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <strong>Nota:</strong> Este proceso puede tomar unos segundos mientras se configura
                                        la identificación segura.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 2: Navegación por Encuestas -->
                    <div class="flex items-start space-x-4">
                        <div
                            class="bg-green-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold flex-shrink-0">
                            2</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-white mb-3">Navegación por las Encuestas</h3>
                            <div class="bg-gray-700/50 rounded-xl p-4">
                                <p class="text-gray-300 mb-4">
                                    El asistente te presenta las encuestas una por una, mostrando:
                                </p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-gray-600/50 rounded-lg p-3">
                                        <h4 class="font-semibold text-white mb-2">📊 Información de la Encuesta</h4>
                                        <ul class="text-gray-300 text-sm space-y-1">
                                            <li>• Título y descripción</li>
                                            <li>• Número de opciones disponibles</li>
                                            <li>• Posición actual (ej: 2 de 5)</li>
                                        </ul>
                                    </div>
                                    <div class="bg-gray-600/50 rounded-lg p-3">
                                        <h4 class="font-semibold text-white mb-2">📈 Progreso Visual</h4>
                                        <ul class="text-gray-300 text-sm space-y-1">
                                            <li>• Barra de progreso animada</li>
                                            <li>• Porcentaje completado</li>
                                            <li>• Encuestas restantes</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 3: Selección y Votación -->
                    <div class="flex items-start space-x-4">
                        <div
                            class="bg-green-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold flex-shrink-0">
                            3</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-white mb-3">Selección y Confirmación de Voto</h3>
                            <div class="bg-gray-700/50 rounded-xl p-4">
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="font-semibold text-white mb-2">🎯 Proceso de Selección</h4>
                                        <ol class="text-gray-300 text-sm space-y-2">
                                            <li class="flex items-start">
                                                <span
                                                    class="bg-blue-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mr-2 mt-0.5">1</span>
                                                Revisa todas las opciones disponibles
                                            </li>
                                            <li class="flex items-start">
                                                <span
                                                    class="bg-blue-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mr-2 mt-0.5">2</span>
                                                Haz clic en tu opción preferida (se resaltará en verde)
                                            </li>
                                            <li class="flex items-start">
                                                <span
                                                    class="bg-blue-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mr-2 mt-0.5">3</span>
                                                Aparecerá el botón "Confirmar Voto"
                                            </li>
                                            <li class="flex items-start">
                                                <span
                                                    class="bg-blue-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mr-2 mt-0.5">4</span>
                                                Haz clic en "Confirmar Voto" para registrar tu participación
                                            </li>
                                        </ol>
                                    </div>
                                    <div class="bg-red-700/20 border border-red-700/30 rounded-lg p-3">
                                        <p class="text-red-200 text-sm flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            <strong>Importante:</strong> Una vez confirmado, tu voto no puede ser
                                            modificado.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 4: Navegación entre Encuestas -->
                    <div class="flex items-start space-x-4">
                        <div
                            class="bg-green-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold flex-shrink-0">
                            4</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-white mb-3">Navegación entre Encuestas</h3>
                            <div class="bg-gray-700/50 rounded-xl p-4">
                                <p class="text-gray-300 mb-4">
                                    El asistente ofrece múltiples opciones de navegación:
                                </p>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-gray-600/50 rounded-lg p-3 text-center">
                                        <div class="text-2xl mb-2">⬅️</div>
                                        <h4 class="font-semibold text-white mb-1">Anterior</h4>
                                        <p class="text-gray-300 text-xs">Volver a la encuesta previa</p>
                                    </div>
                                    <div class="bg-gray-600/50 rounded-lg p-3 text-center">
                                        <div class="text-2xl mb-2">⏭️</div>
                                        <h4 class="font-semibold text-white mb-1">Omitir</h4>
                                        <p class="text-gray-300 text-xs">Saltar encuesta sin votar</p>
                                    </div>
                                    <div class="bg-gray-600/50 rounded-lg p-3 text-center">
                                        <div class="text-2xl mb-2">➡️</div>
                                        <h4 class="font-semibold text-white mb-1">Siguiente</h4>
                                        <p class="text-gray-300 text-xs">Avanzar a la próxima encuesta</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 4: Navegación del Asistente -->
            <div id="navegacion-asistente"
                class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-2xl mb-8">
                <h2 class="text-3xl font-bold text-white mb-6 flex items-center">
                    <span
                        class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg font-bold mr-3">4</span>
                    Navegación del Asistente
                </h2>
                <div class="space-y-6">
                    <div
                        class="bg-gradient-to-r from-indigo-700/20 to-indigo-800/20 rounded-xl p-6 border border-indigo-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Interfaz del Asistente
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h4 class="font-semibold text-white mb-3">📊 Elementos de la Interfaz</h4>
                                <div class="space-y-3">
                                    <div class="bg-gray-700/50 rounded-lg p-3">
                                        <div class="font-medium text-white">Barra de Progreso</div>
                                        <div class="text-gray-300 text-sm">Muestra el avance general y encuestas restantes
                                        </div>
                                    </div>
                                    <div class="bg-gray-700/50 rounded-lg p-3">
                                        <div class="font-medium text-white">Información de Encuesta</div>
                                        <div class="text-gray-300 text-sm">Título, descripción y número de opciones</div>
                                    </div>
                                    <div class="bg-gray-700/50 rounded-lg p-3">
                                        <div class="font-medium text-white">Opciones de Voto</div>
                                        <div class="text-gray-300 text-sm">Botones interactivos para seleccionar</div>
                                    </div>
                                    <div class="bg-gray-700/50 rounded-lg p-3">
                                        <div class="font-medium text-white">Controles de Navegación</div>
                                        <div class="text-gray-300 text-sm">Anterior, Omitir, Siguiente/Finalizar</div>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <h4 class="font-semibold text-white mb-3">🎨 Estados Visuales</h4>
                                <div class="space-y-3">
                                    <div class="bg-gray-700/50 rounded-lg p-3">
                                        <div class="font-medium text-white flex items-center">
                                            <span class="w-3 h-3 bg-yellow-400 rounded-full mr-2"></span>
                                            Cargando
                                        </div>
                                        <div class="text-gray-300 text-sm">Sistema preparando identificación</div>
                                    </div>
                                    <div class="bg-gray-700/50 rounded-lg p-3">
                                        <div class="font-medium text-white flex items-center">
                                            <span class="w-3 h-3 bg-blue-400 rounded-full mr-2"></span>
                                            Listo para Votar
                                        </div>
                                        <div class="text-gray-300 text-sm">Encuesta disponible para participación</div>
                                    </div>
                                    <div class="bg-gray-700/50 rounded-lg p-3">
                                        <div class="font-medium text-white flex items-center">
                                            <span class="w-3 h-3 bg-green-400 rounded-full mr-2"></span>
                                            Ya Participaste
                                        </div>
                                        <div class="text-gray-300 text-sm">Voto registrado previamente</div>
                                    </div>
                                    <div class="bg-gray-700/50 rounded-lg p-3">
                                        <div class="font-medium text-white flex items-center">
                                            <span class="w-3 h-3 bg-purple-400 rounded-full mr-2"></span>
                                            Completado
                                        </div>
                                        <div class="text-gray-300 text-sm">Todas las encuestas revisadas</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-r from-green-700/20 to-green-800/20 rounded-xl p-6 border border-green-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Flujo de Navegación Completo
                        </h3>
                        <div class="flex flex-wrap items-center justify-center gap-4 mb-4">
                            <div class="bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                1. Inicialización
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <div class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                2. Encuesta por Encuesta
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <div class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                3. Confirmación de Votos
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <div class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                4. Resumen Final
                            </div>
                        </div>
                        <p class="text-gray-300 text-sm text-center">
                            El asistente te guía automáticamente a través de todo el proceso, garantizando que no te pierdas
                            ninguna encuesta activa.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sección 5: Confirmación y Alertas -->
            <div id="confirmacion-voto"
                class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-2xl mb-8">
                <h2 class="text-3xl font-bold text-white mb-6 flex items-center">
                    <span
                        class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg font-bold mr-3">5</span>
                    Confirmación y Alertas de Voto
                </h2>
                <div class="space-y-6">
                    <div
                        class="bg-gradient-to-r from-green-700/20 to-green-800/20 rounded-xl p-6 border border-green-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Alert Inteligente de Confirmación
                        </h3>
                        <p class="text-gray-300 mb-4">
                            Después de confirmar cada voto, el asistente muestra un alert inteligente con información
                            detallada sobre tu progreso y las encuestas restantes.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-700/50 rounded-lg p-4">
                                <h4 class="font-semibold text-white mb-3 flex items-center">
                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                    Información Mostrada
                                </h4>
                                <ul class="space-y-2 text-gray-300 text-sm">
                                    <li>• Confirmación del voto registrado</li>
                                    <li>• Opción seleccionada destacada</li>
                                    <li>• Progreso visual actualizado</li>
                                    <li>• Número de encuestas restantes</li>
                                    <li>• Porcentaje de completitud</li>
                                    <li>• Estadísticas de participación</li>
                                </ul>
                            </div>
                            <div class="bg-gray-700/50 rounded-lg p-4">
                                <h4 class="font-semibold text-white mb-3 flex items-center">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                                    Opciones de Acción
                                </h4>
                                <ul class="space-y-2 text-gray-300 text-sm">
                                    <li>• "Continuar a la Siguiente" - Avanza automáticamente</li>
                                    <li>• "Quedarse Aquí" - Permanece en la encuesta actual</li>
                                    <li>• "Ver Resumen Final" - Si es la última encuesta</li>
                                    <li>• Cierre automático opcional</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-blue-700/20 to-blue-800/20 rounded-xl p-6 border border-blue-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Mensajes Contextuales
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-700/50 rounded-lg p-4">
                                <h4 class="font-semibold text-white mb-2">📊 Encuestas Restantes</h4>
                                <div class="bg-blue-900/30 border border-blue-700 rounded p-2 text-blue-300 text-sm">
                                    "Quedan <strong>3 encuestas</strong> más por participar."
                                </div>
                            </div>
                            <div class="bg-gray-700/50 rounded-lg p-4">
                                <h4 class="font-semibold text-white mb-2">🎉 Última Encuesta</h4>
                                <div class="bg-purple-900/30 border border-purple-700 rounded p-2 text-purple-300 text-sm">
                                    "¡Felicidades! Has completado todas las encuestas."
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-r from-purple-700/20 to-purple-800/20 rounded-xl p-6 border border-purple-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9z">
                                </path>
                            </svg>
                            Estadísticas en Tiempo Real
                        </h3>
                        <p class="text-gray-300 mb-4">
                            El alert muestra estadísticas actualizadas de tu progreso:
                        </p>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center bg-gray-700/50 rounded-lg p-3">
                                <div class="text-2xl font-bold text-green-400">5</div>
                                <div class="text-xs text-gray-400">Completadas</div>
                            </div>
                            <div class="text-center bg-gray-700/50 rounded-lg p-3">
                                <div class="text-2xl font-bold text-blue-400">2</div>
                                <div class="text-xs text-gray-400">Restantes</div>
                            </div>
                            <div class="text-center bg-gray-700/50 rounded-lg p-3">
                                <div class="text-2xl font-bold text-purple-400">7</div>
                                <div class="text-xs text-gray-400">Total</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 6: Comprobantes QR -->
            <div id="comprobantes-qr"
                class="bg-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-2xl mb-8">
                <h2 class="text-3xl font-bold text-white mb-6 flex items-center">
                    <span
                        class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg font-bold mr-3">6</span>
                    Comprobantes QR y Participación
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
                            Generación Automática de QR
                        </h3>
                        <p class="text-gray-300 mb-4">
                            El asistente genera automáticamente un código QR único para cada voto que registres. Estos
                            códigos sirven como comprobantes permanentes de tu participación.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-700/50 rounded-lg p-4">
                                <h4 class="font-semibold text-white mb-3 flex items-center">
                                    <span class="w-2 h-2 bg-purple-400 rounded-full mr-2"></span>
                                    Características del QR
                                </h4>
                                <ul class="space-y-2 text-gray-300 text-sm">
                                    <li>• Único e irrepetible para cada voto</li>
                                    <li>• Contiene UUID de participación</li>
                                    <li>• Enlaza a página de verificación</li>
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
                                    <li>• Ver detalles de tu voto</li>
                                    <li>• Acceder a estadísticas</li>
                                    <li>• Compartir comprobante</li>
                                    <li>• Auditoría de transparencia</li>
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
                            Resumen Final con QR
                        </h3>
                        <p class="text-gray-300 mb-4">
                            Al completar todas las encuestas, el asistente muestra un resumen completo con todos tus códigos
                            QR:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                                <div class="text-2xl mb-2">📊</div>
                                <h4 class="font-semibold text-white mb-1">Estadísticas</h4>
                                <p class="text-gray-300 text-xs">Total de participaciones</p>
                            </div>
                            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                                <div class="text-2xl mb-2">🗳️</div>
                                <h4 class="font-semibold text-white mb-1">Votos</h4>
                                <p class="text-gray-300 text-xs">Opciones seleccionadas</p>
                            </div>
                            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                                <div class="text-2xl mb-2">📱</div>
                                <h4 class="font-semibold text-white mb-1">QR Codes</h4>
                                <p class="text-gray-300 text-xs">Comprobantes generados</p>
                            </div>
                            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                                <div class="text-2xl mb-2">🔍</div>
                                <h4 class="font-semibold text-white mb-1">Detalles</h4>
                                <p class="text-gray-300 text-xs">Modal de participación</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-blue-700/20 to-blue-800/20 rounded-xl p-6 border border-blue-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            Modal de Detalles de Participación
                        </h3>
                        <p class="text-gray-300 mb-4">
                            Desde el resumen final, puedes hacer clic en "Ver detalles de participación" para abrir un modal
                            completo con:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-semibold text-white mb-2">📋 Información Detallada:</h4>
                                <ul class="space-y-1 text-gray-300 text-sm">
                                    <li>• Título de la encuesta</li>
                                    <li>• Opción seleccionada</li>
                                    <li>• Fecha y hora exacta</li>
                                    <li>• UUID de participación</li>
                                    <li>• Información de sesión</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-semibold text-white mb-2">🔧 Funcionalidades:</h4>
                                <ul class="space-y-1 text-gray-300 text-sm">
                                    <li>• QR code ampliado</li>
                                    <li>• Enlace a página completa</li>
                                    <li>• Estadísticas adicionales</li>
                                    <li>• Estado de verificación</li>
                                    <li>• Tiempo de expiración</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 7: Seguridad -->
            <div id="seguridad-privacidad"
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
                            Medidas de Seguridad del Asistente
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="bg-gray-700/50 rounded-lg p-4">
                                    <h4 class="font-semibold text-white mb-2 flex items-center">
                                        <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                                        Identificación Segura del Dispositivo
                                    </h4>
                                    <p class="text-gray-300 text-sm">
                                        El asistente genera un fingerprint único y anónimo de tu dispositivo utilizando
                                        múltiples técnicas avanzadas sin comprometer tu privacidad.
                                    </p>
                                </div>
                                <div class="bg-gray-700/50 rounded-lg p-4">
                                    <h4 class="font-semibold text-white mb-2 flex items-center">
                                        <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                                        Detección de IP Privada
                                    </h4>
                                    <p class="text-gray-300 text-sm">
                                        Utiliza tecnología WebRTC para detectar tu IP privada de forma segura, añadiendo una
                                        capa adicional de verificación.
                                    </p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="bg-gray-700/50 rounded-lg p-4">
                                    <h4 class="font-semibold text-white mb-2 flex items-center">
                                        <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                                        Prevención de Votos Duplicados
                                    </h4>
                                    <p class="text-gray-300 text-sm">
                                        El sistema previene automáticamente votos múltiples en la misma encuesta manteniendo
                                        el anonimato completo.
                                    </p>
                                </div>
                                <div class="bg-gray-700/50 rounded-lg p-4">
                                    <h4 class="font-semibold text-white mb-2 flex items-center">
                                        <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                                        Encriptación de Datos
                                    </h4>
                                    <p class="text-gray-300 text-sm">
                                        Toda la comunicación se realiza a través de conexiones seguras HTTPS con
                                        encriptación de extremo a extremo.
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
                            Privacidad y Anonimato
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                                <div class="text-2xl mb-2">🔒</div>
                                <h4 class="font-semibold text-white mb-2">Datos Anónimos</h4>
                                <p class="text-gray-300 text-xs">
                                    No se almacena información personal identificable
                                </p>
                            </div>
                            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                                <div class="text-2xl mb-2">🛡️</div>
                                <h4 class="font-semibold text-white mb-2">Sin Tracking</h4>
                                <p class="text-gray-300 text-xs">
                                    No se utilizan cookies de seguimiento
                                </p>
                            </div>
                            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                                <div class="text-2xl mb-2">🔐</div>
                                <h4 class="font-semibold text-white mb-2">Datos Seguros</h4>
                                <p class="text-gray-300 text-xs">
                                    Almacenamiento encriptado y seguro
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-r from-green-700/20 to-green-800/20 rounded-xl p-6 border border-green-700/30">
                        <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                            Recomendaciones de Seguridad
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-semibold text-white mb-2">🔒 Para Mayor Privacidad:</h4>
                                <ul class="space-y-1 text-gray-300 text-sm">
                                    <li>• Usa modo incógnito/privado en tu navegador</li>
                                    <li>• Vota desde tu dispositivo personal</li>
                                    <li>• Evita redes WiFi públicas</li>
                                    <li>• Mantén tu navegador actualizado</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-semibold text-white mb-2">🛡️ Buenas Prácticas:</h4>
                                <ul class="space-y-1 text-gray-300 text-sm">
                                    <li>• No compartas tus códigos QR personales</li>
                                    <li>• Cierra la sesión al terminar</li>
                                    <li>• Verifica la URL antes de votar</li>
                                    <li>• Reporta cualquier comportamiento sospechoso</li>
                                </ul>
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
                                ¿Cómo accedo a las encuestas si no tengo tokens individuales?
                            </h3>
                            <p class="text-gray-300 ml-6">
                                <span class="text-blue-400 mr-2">A:</span>
                                El Asistente de Participación elimina la necesidad de tokens individuales. Simplemente
                                accede a <code class="bg-gray-600 px-2 py-1 rounded text-green-400">/voting/asistent</code>
                                y tendrás acceso automático a todas las encuestas activas.
                            </p>
                        </div>

                        <div class="bg-gray-700/50 rounded-xl p-6 border border-gray-600">
                            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <span class="text-green-400 mr-2">Q:</span>
                                ¿Qué pasa si el sistema no puede generar mi identificación de dispositivo?
                            </h3>
                            <p class="text-gray-300 ml-6">
                                <span class="text-blue-400 mr-2">A:</span>
                                El asistente tiene múltiples métodos de respaldo. Si la identificación avanzada falla,
                                puedes usar el botón "Continuar sin identificación avanzada" para proceder con un método
                                básico pero seguro.
                            </p>
                        </div>

                        <div class="bg-gray-700/50 rounded-xl p-6 border border-gray-600">
                            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <span class="text-green-400 mr-2">Q:</span>
                                ¿Puedo votar en las encuestas en cualquier orden?
                            </h3>
                            <p class="text-gray-300 ml-6">
                                <span class="text-blue-400 mr-2">A:</span>
                                Sí, el asistente te permite navegar hacia adelante y hacia atrás entre las encuestas. Puedes
                                omitir encuestas y regresar a ellas más tarde, o votar en el orden que prefieras.
                            </p>
                        </div>

                        <div class="bg-gray-700/50 rounded-xl p-6 border border-gray-600">
                            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <span class="text-green-400 mr-2">Q:</span>
                                ¿Qué información muestra el alert después de votar?
                            </h3>
                            <p class="text-gray-300 ml-6">
                                <span class="text-blue-400 mr-2">A:</span>
                                El alert inteligente muestra tu opción seleccionada, el progreso actualizado, cuántas
                                encuestas quedan, estadísticas de participación y opciones para continuar o quedarte en la
                                encuesta actual.
                            </p>
                        </div>

                        <div class="bg-gray-700/50 rounded-xl p-6 border border-gray-600">
                            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <span class="text-green-400 mr-2">Q:</span>
                                ¿Puedo recuperar mis códigos QR si cierro el navegador?
                            </h3>
                            <p class="text-gray-300 ml-6">
                                <span class="text-blue-400 mr-2">A:</span>
                                Los códigos QR están vinculados a tu identificación de dispositivo. Si regresas al asistente
                                desde el mismo dispositivo y navegador, podrás ver tus participaciones previas en el resumen
                                final.
                            </p>
                        </div>

                        <div class="bg-gray-700/50 rounded-xl p-6 border border-gray-600">
                            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <span class="text-green-400 mr-2">Q:</span>
                                ¿El asistente funciona en dispositivos móviles?
                            </h3>
                            <p class="text-gray-300 ml-6">
                                <span class="text-blue-400 mr-2">A:</span>
                                Sí, el asistente está completamente optimizado para dispositivos móviles. La interfaz se
                                adapta automáticamente y todas las funcionalidades están disponibles en smartphones y
                                tablets.
                            </p>
                        </div>

                        <div class="bg-gray-700/50 rounded-xl p-6 border border-gray-600">
                            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <span class="text-green-400 mr-2">Q:</span>
                                ¿Qué pasa si hay nuevas encuestas después de completar el asistente?
                            </h3>
                            <p class="text-gray-300 ml-6">
                                <span class="text-blue-400 mr-2">A:</span>
                                Puedes volver a acceder al asistente en cualquier momento. El sistema detectará
                                automáticamente las nuevas encuestas disponibles y te permitirá participar en ellas.
                            </p>
                        </div>

                        <div class="bg-gray-700/50 rounded-xl p-6 border border-gray-600">
                            <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                                <span class="text-green-400 mr-2">Q:</span>
                                ¿Es seguro usar el asistente en redes públicas?
                            </h3>
                            <p class="text-gray-300 ml-6">
                                <span class="text-blue-400 mr-2">A:</span>
                                Aunque el asistente utiliza conexiones seguras HTTPS, recomendamos usar redes privadas
                                cuando sea posible. Si debes usar WiFi público, considera usar una VPN para mayor seguridad.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de navegación finales -->
            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="{{ route('voting.asistent') }}"
                    class="inline-flex items-center px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Acceder al Asistente de Participación
                </a>
                {{-- <a href="{{ route('voting.index') }}"
                    class="inline-flex items-center px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                    Volver al Inicio
                </a> --}}
            </div>

            <!-- Footer informativo -->
            <div class="text-center mt-12 pt-8 border-t border-gray-700">
                <p class="text-gray-400 text-sm">
                    🤖 Esta guía describe el funcionamiento del Asistente de Participación - el método único y oficial para
                    votar.
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
