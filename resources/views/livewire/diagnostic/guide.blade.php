<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <div class="flex items-center justify-center mb-4">
            <svg class="w-12 h-12 text-green-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <h1 class="text-4xl font-bold text-white">
                Guía del Estudiante
            </h1>
        </div>
        <p class="text-gray-300 text-lg">
            Todo lo que necesitas saber para participar en el diagnóstico educativo.
        </p>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-gray-800 rounded-xl p-2 mb-8">
        <div class="flex flex-wrap gap-2">
            <button 
                wire:click="$set('activeTab', 'overview')"
                class="px-6 py-3 rounded-lg font-medium transition-all duration-200 {{ $activeTab === 'overview' ? 'bg-green-600 text-white shadow-lg' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Información General
                </div>
            </button>
            <button 
                wire:click="$set('activeTab', 'process')"
                class="px-6 py-3 rounded-lg font-medium transition-all duration-200 {{ $activeTab === 'process' ? 'bg-green-600 text-white shadow-lg' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Proceso Paso a Paso
                </div>
            </button>
            <button 
                wire:click="$set('activeTab', 'questions')"
                class="px-6 py-3 rounded-lg font-medium transition-all duration-200 {{ $activeTab === 'questions' ? 'bg-green-600 text-white shadow-lg' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Tipos de Preguntas
                </div>
            </button>
            <button 
                wire:click="$set('activeTab', 'tips')"
                class="px-6 py-3 rounded-lg font-medium transition-all duration-200 {{ $activeTab === 'tips' ? 'bg-green-600 text-white shadow-lg' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Consejos y Recomendaciones
                </div>
            </button>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="bg-gray-800 rounded-xl p-8">
        @if($activeTab === 'overview')
            <!-- Overview Tab -->
            <div class="space-y-8">
                <div class="text-center mb-8">
                    <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-white mb-4">¿Qué es el Diagnóstico Educativo?</h2>
                    <p class="text-gray-300 text-lg max-w-3xl mx-auto">
                        Es una herramienta diseñada para evaluar tus conocimientos actuales en diferentes áreas de formación, 
                        permitiendo identificar fortalezas y oportunidades de mejora en tu proceso de aprendizaje.
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-blue-900/50 to-blue-800/30 p-6 rounded-xl border border-blue-700/50">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 00-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Evaluación Personalizada</h3>
                        <p class="text-gray-300">Preguntas adaptadas a tu nivel y área de estudio específica.</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-900/50 to-purple-800/30 p-6 rounded-xl border border-purple-700/50">
                        <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Resultados Inmediatos</h3>
                        <p class="text-gray-300">Obtén retroalimentación instantánea sobre tu desempeño.</p>
                    </div>

                    <div class="bg-gradient-to-br from-green-900/50 to-green-800/30 p-6 rounded-xl border border-green-700/50">
                        <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Plan de Mejora</h3>
                        <p class="text-gray-300">Recibe recomendaciones específicas para fortalecer tus conocimientos.</p>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'process')
            <!-- Process Tab -->
            <div class="space-y-8">
                <h2 class="text-3xl font-bold text-white text-center mb-8">Proceso Paso a Paso</h2>
                
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-lg">1</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-white mb-2">Identificación del Estudiante</h3>
                            <p class="text-gray-300 mb-4">Ingresa tu cédula de identidad para acceder al sistema. El sistema verificará tu información y te dará la bienvenida.</p>
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <div class="flex items-center text-yellow-400 mb-2">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    Importante:
                                </div>
                                <p class="text-gray-300">Asegúrate de ingresar tu cédula correctamente, sin puntos ni espacios.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-lg">2</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-white mb-2">Selección del Área de Formación</h3>
                            <p class="text-gray-300 mb-4">En el dashboard verás todas las áreas disponibles con su progreso actual. Selecciona el área que deseas evaluar.</p>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="bg-gray-700 p-4 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-white font-medium">Progreso Visual</span>
                                        <div class="w-8 h-8 relative">
                                            <svg class="w-8 h-8 transform -rotate-90" viewBox="0 0 36 36">
                                                <path class="text-gray-600" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                                <path class="text-green-500" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="75, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                            </svg>
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <span class="text-xs font-semibold text-white">75%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-gray-400 text-sm">Cada área muestra tu progreso actual</p>
                                </div>
                                <div class="bg-gray-700 p-4 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-white font-medium">Estados</span>
                                        <div class="flex space-x-2">
                                            <span class="bg-yellow-600 text-white px-2 py-1 rounded-full text-xs">En progreso</span>
                                            <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs">✓ Completado</span>
                                        </div>
                                    </div>
                                    <p class="text-gray-400 text-sm">Identifica fácilmente el estado de cada área</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-lg">3</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-white mb-2">Responder las Preguntas</h3>
                            <p class="text-gray-300 mb-4">El sistema te presentará preguntas de forma aleatoria. Responde con honestidad y según tu conocimiento actual.</p>
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <div class="flex items-center text-blue-400 mb-2">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Recuerda:
                                </div>
                                <ul class="text-gray-300 space-y-1">
                                    <li>• No hay respuestas correctas o incorrectas</li>
                                    <li>• Puedes pausar y continuar cuando desees</li>
                                    <li>• Tu progreso se guarda automáticamente</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-lg">4</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-white mb-2">Revisar Resultados</h3>
                            <p class="text-gray-300 mb-4">Una vez completado el diagnóstico, podrás revisar todas tus respuestas y obtener un resumen de tu desempeño.</p>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'questions')
            <!-- Questions Tab -->
            <div class="space-y-8">
                <h2 class="text-3xl font-bold text-white text-center mb-8">Tipos de Preguntas</h2>
                
                <div class="grid gap-8">
                    <!-- Multiple Choice -->
                    <div class="bg-gradient-to-r from-blue-900/30 to-blue-800/20 p-6 rounded-xl border border-blue-700/50">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-semibold text-white">Selección Múltiple</h3>
                        </div>
                        <p class="text-gray-300 mb-4">Preguntas con varias opciones donde debes seleccionar la respuesta que mejor refleje tu conocimiento o experiencia.</p>
                        
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-white font-medium mb-3">Ejemplo:</h4>
                            <p class="text-gray-300 mb-4">¿Cuál es tu nivel de conocimiento en programación orientada a objetos?</p>
                            <div class="space-y-2">
                                <div class="flex items-center p-2 bg-gray-600 rounded">
                                    <span class="w-4 h-4 rounded-full border-2 border-gray-400 mr-3"></span>
                                    <span class="text-gray-300">Básico - Conozco los conceptos fundamentales</span>
                                </div>
                                <div class="flex items-center p-2 bg-green-600 rounded">
                                    <span class="w-4 h-4 rounded-full border-2 border-white bg-white mr-3 flex items-center justify-center">
                                        <svg class="w-2 h-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                    <span class="text-white">Intermedio - Puedo implementar clases y herencia</span>
                                </div>
                                <div class="flex items-center p-2 bg-gray-600 rounded">
                                    <span class="w-4 h-4 rounded-full border-2 border-gray-400 mr-3"></span>
                                    <span class="text-gray-300">Avanzado - Domino patrones de diseño</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scale Questions -->
                    <div class="bg-gradient-to-r from-purple-900/30 to-purple-800/20 p-6 rounded-xl border border-purple-700/50">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 00-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-semibold text-white">Escala Numérica</h3>
                        </div>
                        <p class="text-gray-300 mb-4">Evalúa tu nivel de conocimiento o confianza en una escala del 1 al 10.</p>
                        
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-white font-medium mb-3">Ejemplo:</h4>
                            <p class="text-gray-300 mb-4">En una escala del 1 al 10, ¿qué tan confiado te sientes resolviendo problemas de algoritmos?</p>
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-2xl font-bold text-purple-400">7</span>
                                <span class="text-gray-400">/ 10</span>
                                <div class="flex-1 bg-gray-600 rounded-full h-3 ml-4">
                                    <div class="bg-purple-500 h-3 rounded-full" style="width: 70%"></div>
                                </div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-400 mt-2">
                                <span>1 - Nada confiado</span>
                                <span>10 - Muy confiado</span>
                            </div>
                        </div>
                    </div>

                    <!-- Open Text -->
                    <div class="bg-gradient-to-r from-green-900/30 to-green-800/20 p-6 rounded-xl border border-green-700/50">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-semibold text-white">Respuesta Abierta</h3>
                        </div>
                        <p class="text-gray-300 mb-4">Preguntas que requieren una respuesta escrita donde puedes expresar tu conocimiento con tus propias palabras.</p>
                        
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-white font-medium mb-3">Ejemplo:</h4>
                            <p class="text-gray-300 mb-4">Describe brevemente qué entiendes por "desarrollo sostenible":</p>
                            <div class="bg-gray-600 p-3 rounded border-l-4 border-green-500">
                                <p class="text-white italic">
                                    "El desarrollo sostenible es un enfoque que busca satisfacer las necesidades del presente sin comprometer la capacidad de las futuras generaciones para satisfacer sus propias necesidades..."
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'tips')
            <!-- Tips Tab -->
            <div class="space-y-8">
                <h2 class="text-3xl font-bold text-white text-center mb-8">Consejos y Recomendaciones</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-gradient-to-br from-yellow-900/50 to-yellow-800/30 p-6 rounded-xl border border-yellow-700/50">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-yellow-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white">Gestión del Tiempo</h3>
                        </div>
                        <ul class="text-gray-300 space-y-2">
                            <li class="flex items-start">
                                <span class="text-yellow-400 mr-2">•</span>
                                No hay límite de tiempo, tómate el que necesites
                            </li>
                            <li class="flex items-start">
                                <span class="text-yellow-400 mr-2">•</span>
                                Puedes pausar y continuar cuando desees
                            </li>
                            <li class="flex items-start">
                                <span class="text-yellow-400 mr-2">•</span>
                                Tu progreso se guarda automáticamente
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gradient-to-br from-blue-900/50 to-blue-800/30 p-6 rounded-xl border border-blue-700/50">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white">Honestidad en las Respuestas</h3>
                        </div>
                        <ul class="text-gray-300 space-y-2">
                            <li class="flex items-start">
                                <span class="text-blue-400 mr-2">•</span>
                                Responde según tu conocimiento real
                            </li>
                            <li class="flex items-start">
                                <span class="text-blue-400 mr-2">•</span>
                                No busques respuestas en internet
                            </li>
                            <li class="flex items-start">
                                <span class="text-blue-400 mr-2">•</span>
                                El objetivo es identificar áreas de mejora
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gradient-to-br from-green-900/50 to-green-800/30 p-6 rounded-xl border border-green-700/50">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white">Ambiente de Estudio</h3>
                        </div>
                        <ul class="text-gray-300 space-y-2">
                            <li class="flex items-start">
                                <span class="text-green-400 mr-2">•</span>
                                Busca un lugar tranquilo y sin distracciones
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-400 mr-2">•</span>
                                Asegúrate de tener buena conexión a internet
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-400 mr-2">•</span>
                                Mantén una actitud positiva y relajada
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gradient-to-br from-purple-900/50 to-purple-800/30 p-6 rounded-xl border border-purple-700/50">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white">Después del Diagnóstico</h3>
                        </div>
                        <ul class="text-gray-300 space-y-2">
                            <li class="flex items-start">
                                <span class="text-purple-400 mr-2">•</span>
                                Revisa tus respuestas y reflexiona sobre ellas
                            </li>
                            <li class="flex items-start">
                                <span class="text-purple-400 mr-2">•</span>
                                Identifica las áreas que necesitas reforzar
                            </li>
                            <li class="flex items-start">
                                <span class="text-purple-400 mr-2">•</span>
                                Crea un plan de estudio basado en los resultados
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Important Notice -->
                <div class="bg-gradient-to-r from-red-900/50 to-red-800/30 p-6 rounded-xl border border-red-700/50">
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-red-600 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-2">Importante Recordar</h3>
                            <p class="text-gray-300">
                                Este diagnóstico es una herramienta de autoevaluación diseñada para ayudarte a identificar tus fortalezas y áreas de oportunidad. 
                                Los resultados no son una calificación, sino una guía para tu proceso de aprendizaje continuo.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-center space-x-4 mt-8">
        <button 
            wire:click="$set('currentView', 'dashboard')"
            class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v2H8V5z"></path>
            </svg>
            Ir al Dashboard
        </button>
    </div>
</div>
