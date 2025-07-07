@extends('layouts.proposal')

@section('title', 'Propuesta Comercial - SAEFL Módulo de Votaciones Avanzado')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Executive Summary -->
        <section class="mb-16">
            <div class="bg-white rounded-2xl shadow-corporate p-8 lg:p-12">
                <div class="text-center mb-8">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Resumen</h2>
                    <div class="w-24 h-1 gradient-primary mx-auto rounded-full"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-900 mb-6"> <strong>SAEFL</strong> Módulo de Votaciones de Nueva Generación</h3>
                        <p class="text-lg text-gray-700 leading-relaxed mb-6">
                            Se presenta una solución integral de votación digital que combina <strong>seguridad
                                avanzada</strong>,
                            <strong>facilidad de uso</strong> y <strong>transparencia total</strong>. Nuestro módulo está
                            diseñado
                            para organizaciones que requieren procesos de votación confiables, escalables y completamente
                            auditables.
                        </p>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-3xl font-bold text-blue-600">99.9%</div>
                                <div class="text-sm text-blue-800">Disponibilidad</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-3xl font-bold text-green-600">100%</div>
                                <div class="text-sm text-green-800">Seguridad</div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <div class="text-3xl font-bold text-purple-600">24/7</div>
                                <div class="text-sm text-purple-800">Soporte</div>
                            </div>
                            <div class="text-center p-4 bg-orange-50 rounded-lg">
                                <div class="text-3xl font-bold text-orange-600">∞</div>
                                <div class="text-sm text-orange-800">Escalabilidad</div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="bg-gradient-to-r from-green-500 to-green-700 rounded-2xl p-8 text-white">
                            <h4 class="text-xl font-semibold mb-4">Beneficios Clave</h4>
                            <ul class="space-y-3">
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 text-blue-200" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Implementación sin tokens individuales
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 text-blue-200" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Asistente inteligente de participación
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 text-blue-200" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Comprobantes QR automáticos
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 text-blue-200" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Identificación avanzada de dispositivos
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 text-blue-200" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Resultados en tiempo real
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Funcionalidades Principales -->
        <section class="mb-16 print-break">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Funcionalidades Principales</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Cada componente ha sido diseñado pensando en la experiencia del usuario y la seguridad integral
                </p>
                <div class="w-24 h-1 gradient-secondary mx-auto rounded-full mt-6"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Asistente Inteligente -->
                <div class="feature-card bg-white rounded-2xl shadow-corporate p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 gradient-primary rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Asistente Inteligente</h3>
                            <p class="text-blue-600 font-medium">Guía Automática de Participación</p>
                        </div>
                    </div>

                    <p class="text-gray-700 mb-6 leading-relaxed">
                        El corazón de nuestro módulo es un asistente inteligente que elimina la complejidad de la votación
                        tradicional.
                        Los usuarios acceden a una única URL y son guiados automáticamente a través de todas las encuestas
                        activas.
                    </p>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Navegación Automática</h4>
                                <p class="text-gray-600 text-sm">Progresión fluida entre encuestas sin intervención manual
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Progreso Visual</h4>
                                <p class="text-gray-600 text-sm">Indicadores en tiempo real del avance y encuestas restantes
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Alertas Inteligentes</h4>
                                <p class="text-gray-600 text-sm">Confirmaciones contextuales con estadísticas actualizadas
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center text-blue-800">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">Ventaja Competitiva:</span>
                        </div>
                        <p class="text-blue-700 text-sm mt-1">
                            Reduce el tiempo de participación en un 70% comparado con sistemas tradicionales
                        </p>
                    </div>
                </div>

                <!-- Seguridad Avanzada -->
                <div class="feature-card bg-white rounded-2xl shadow-corporate p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 gradient-secondary rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Seguridad Avanzada</h3>
                            <p class="text-green-600 font-medium">Identificación Sin Compromiso</p>
                        </div>
                    </div>

                    <p class="text-gray-700 mb-6 leading-relaxed">
                        Implementamos un sistema de identificación de dispositivos de múltiples capas que garantiza la
                        integridad
                        del proceso sin comprometer la privacidad del usuario.
                    </p>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Fingerprinting Avanzado</h4>
                                <p class="text-gray-600 text-sm">Identificación única basada en características del
                                    dispositivo</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Detección IP Privada</h4>
                                <p class="text-gray-600 text-sm">Tecnología robustas para validación adicional de identidad
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Prevención de Duplicados</h4>
                                <p class="text-gray-600 text-sm">Algoritmos que impiden votos múltiples manteniendo el
                                    anonimato</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center text-green-800">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                            <span class="font-medium">Certificación:</span>
                        </div>
                        <p class="text-green-700 text-sm mt-1">
                            Cumple con estándares internacionales de seguridad y privacidad de datos
                        </p>
                    </div>
                </div>

                <!-- Comprobantes QR -->
                <div class="feature-card bg-white rounded-2xl shadow-corporate p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 gradient-accent rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Comprobantes QR</h3>
                            <p class="text-purple-600 font-medium">Transparencia Total</p>
                        </div>
                    </div>

                    <p class="text-gray-700 mb-6 leading-relaxed">
                        Cada voto genera automáticamente un código QR único que sirve como comprobante permanente y
                        verificable
                        de la participación, garantizando transparencia y auditabilidad completa.
                    </p>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Generación Automática</h4>
                                <p class="text-gray-600 text-sm">Códigos únicos e irrepetibles para cada participación</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Verificación Instantánea</h4>
                                <p class="text-gray-600 text-sm">Acceso inmediato a detalles de participación via escaneo
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Auditoría Completa</h4>
                                <p class="text-gray-600 text-sm">Trazabilidad total sin comprometer el anonimato</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-purple-50 rounded-lg">
                        <div class="flex items-center text-purple-800">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="font-medium">Innovación:</span>
                        </div>
                        <p class="text-purple-700 text-sm mt-1">
                            Primera solución en el mercado con comprobantes QR automáticos integrados
                        </p>
                    </div>
                </div>

                <!-- Interfaz Intuitiva -->
                <div class="feature-card bg-white rounded-2xl shadow-corporate p-8">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Interfaz Intuitiva</h3>
                            <p class="text-orange-600 font-medium">Experiencia de Usuario Superior</p>
                        </div>
                    </div>

                    <p class="text-gray-700 mb-6 leading-relaxed">
                        Diseño responsive y accesible que se adapta a cualquier dispositivo, garantizando una experiencia
                        consistente y profesional en computadoras, tablets y smartphones.
                    </p>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-orange-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Diseño Responsive</h4>
                                <p class="text-gray-600 text-sm">Adaptación automática a cualquier tamaño de pantalla</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-orange-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Accesibilidad Web</h4>
                                <p class="text-gray-600 text-sm">Cumple con estándares WCAG para usuarios con
                                    discapacidades</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-orange-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Carga Optimizada</h4>
                                <p class="text-gray-600 text-sm">Tiempos de respuesta inferiores a 2 segundos</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-orange-50 rounded-lg">
                        <div class="flex items-center text-orange-800">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                </path>
                            </svg>
                            <span class="font-medium">Satisfacción:</span>
                        </div>
                        <p class="text-orange-700 text-sm mt-1">
                            95% de satisfacción del usuario en pruebas de usabilidad
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Arquitectura Técnica -->
        <section class="mb-16 print-break">
            <div class="bg-gray-900 text-white rounded-2xl p-8 lg:p-12">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold mb-4">Arquitectura Técnica</h2>
                    <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                        Construido sobre tecnologías modernas y escalables para garantizar rendimiento y confiabilidad
                    </p>
                    <div class="w-24 h-1 bg-white mx-auto rounded-full mt-6"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="bg-gray-800 rounded-xl p-6">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h6a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h6a2 2 0 002-2v-4a2 2 0 00-2-2m8-8V6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Backend Robusto</h3>
                        <ul class="text-gray-300 space-y-2 text-sm">
                            <li>• Laravel 10+ Framework</li>
                            <li>• Base de datos MySQL/PostgreSQL</li>
                            <li>• APIs RESTful optimizadas</li>
                            <li>• Cache Redis integrado</li>
                            <li>• Queue system para procesos</li>
                        </ul>
                    </div>

                    <div class="bg-gray-800 rounded-xl p-6">
                        <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Frontend Moderno</h3>
                        <ul class="text-gray-300 space-y-2 text-sm">
                            <li>• Livewire para interactividad</li>
                            <li>• Tailwind CSS responsive</li>
                            <li>• Alpine.js para dinamismo</li>
                            <li>• PWA capabilities</li>
                            <li>• Optimización SEO</li>
                        </ul>
                    </div>

                    <div class="bg-gray-800 rounded-xl p-6">
                        <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Seguridad Integral</h3>
                        <ul class="text-gray-300 space-y-2 text-sm">
                            <li>• Encriptación HTTPS/TLS</li>
                            <li>• Validación CSRF</li>
                            <li>• Sanitización de datos</li>
                            <li>• Rate limiting avanzado</li>
                            <li>• Logs de auditoría</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-6">
                        <h3 class="text-xl font-bold mb-4">Escalabilidad</h3>
                        <p class="text-blue-100 mb-4">
                            Arquitectura diseñada para crecer, desde 100 hasta 1000+ usuarios
                            simultáneos.
                        </p>
                        <div class="flex items-center text-blue-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            Auto-scaling en la nube
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl p-6">
                        <h3 class="text-xl font-bold mb-4">Monitoreo</h3>
                        <p class="text-green-100 mb-4">
                            Supervisión 24/7 con alertas proactivas y métricas detalladas de rendimiento y uso.
                        </p>
                        <div class="flex items-center text-green-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9z">
                                </path>
                            </svg>
                            Dashboard en tiempo real
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Casos de Uso -->
        <section class="mb-16">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Casos de Uso Ideales</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Nuestro sistema se adapta perfectamente a diversos escenarios organizacionales
                </p>
                <div class="w-24 h-1 gradient-primary mx-auto rounded-full mt-6"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl shadow-corporate p-6 text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Empresas Corporativas</h3>
                    <p class="text-gray-600 text-sm">
                        Elecciones de junta directiva, decisiones estratégicas, encuestas de satisfacción laboral
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-corporate p-6 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Instituciones Educativas</h3>
                    <p class="text-gray-600 text-sm">
                        Elecciones estudiantiles, evaluaciones docentes, decisiones académicas participativas
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-corporate p-6 text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Organizaciones Civiles</h3>
                    <p class="text-gray-600 text-sm">
                        Consultas ciudadanas, elecciones de representantes, decisiones comunitarias
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-corporate p-6 text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Asociaciones Profesionales</h3>
                    <p class="text-gray-600 text-sm">
                        Elecciones de colegios profesionales, decisiones gremiales, encuestas sectoriales
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-corporate p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">ONGs y Fundaciones</h3>
                    <p class="text-gray-600 text-sm">
                        Elecciones de patronatos, decisiones de financiamiento, consultas a beneficiarios
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-corporate p-6 text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Eventos y Conferencias</h3>
                    <p class="text-gray-600 text-sm">
                        Votaciones en tiempo real, encuestas de audiencia, selección de ponencias
                    </p>
                </div>
            </div>
        </section>

        <!-- Paquetes y Precios -->
        <section class="mb-16 print-break">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Implementación</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Soluciones flexibles adaptadas a las necesidades y presupuesto de su organización
                </p>
                <div class="w-24 h-1 gradient-secondary mx-auto rounded-full mt-6"></div>
            </div>

            <div class="grid grid-cols-1 gap-8">  {{-- lg:grid-cols-3  --}}
                <!-- Paquete Básico -->
                <div class="bg-white rounded-2xl shadow-corporate p-8 relative">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Básico</h3>
                        <p class="text-gray-600">Ideal para organizaciones pequeñas</p>
                        <div class="mt-4">
                            <span class="text-4xl font-bold text-blue-600">$200</span>
                            <span class="text-gray-500">/implementación</span>
                        </div>
                    </div>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Hasta 2,000 participantes
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            5 encuestas simultáneas
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Asistente inteligente
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Comprobantes QR
                        </li>
                        {{-- <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Soporte por email
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Hosting incluido (1 año)
                        </li>
                        --}}
                    </ul>

                    <button
                        class="w-full bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors">
                        Solicitar Cotización
                    </button>
                </div>

                {{--
                <!-- Paquete Profesional -->
                <div class="bg-white rounded-2xl shadow-corporate p-8 relative border-2 border-green-500">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span
                            class="bg-green-500 text-white px-4 py-1 rounded-full text-sm font-semibold">Recomendado</span>
                    </div>

                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Paquete Profesional</h3>
                        <p class="text-gray-600">Para organizaciones medianas</p>
                        <div class="mt-4">
                            <span class="text-4xl font-bold text-green-600">$5,500</span>
                            <span class="text-gray-500">/implementación</span>
                        </div>
                    </div>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Hasta 10,000 participantes
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Encuestas ilimitadas
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Todas las funcionalidades básicas
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Dashboard de administración
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Reportes avanzados
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Soporte telefónico
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Personalización de marca
                        </li>
                    </ul>

                    <button
                        class="w-full bg-green-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-green-700 transition-colors">
                        Solicitar Cotización
                    </button>
                </div>

                <!-- Paquete Enterprise -->
                <div class="bg-white rounded-2xl shadow-corporate p-8 relative">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Paquete Enterprise</h3>
                        <p class="text-gray-600">Para grandes organizaciones</p>
                        <div class="mt-4">
                            <span class="text-4xl font-bold text-purple-600">$12,000</span>
                            <span class="text-gray-500">/implementación</span>
                        </div>
                    </div>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Participantes ilimitados
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Todas las funcionalidades profesionales
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Integración con Active Directory
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            API personalizada
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Soporte dedicado 24/7
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Infraestructura dedicada
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            SLA garantizado 99.9%
                        </li>
                    </ul>

                    <button
                        class="w-full bg-purple-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-purple-700 transition-colors">
                        Contactar Ventas
                    </button>
                </div>
                --}}
            </div>

            <div class="mt-12 text-center">
                <div class="bg-gray-100 rounded-xl p-6 inline-block">
                    <p class="text-gray-700 mb-2">
                        <strong>Se incluye:</strong>
                    </p>
                    <div class="flex flex-wrap justify-center gap-4 text-sm text-gray-600">
                        <span>✓ Capacitación inicial</span>
                        <span>✓ Migración de datos</span>
                        <span>✓ Configuración personalizada</span>
                        <span>✓ Documentación completa</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cronograma de Implementación -->
        {{-- <section class="mb-16">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-8 lg:p-12">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Cronograma de Implementación</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Proceso estructurado y eficiente para poner en marcha su sistema de votaciones
                    </p>
                    <div class="w-24 h-1 gradient-accent mx-auto rounded-full mt-6"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-xl p-6 shadow-lg">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                            <span class="text-xl font-bold text-blue-600">1</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Análisis y Diseño</h3>
                        <p class="text-gray-600 text-sm mb-3">Semana 1-2</p>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li>• Reunión de requerimientos</li>
                            <li>• Diseño de arquitectura</li>
                            <li>• Plan de implementación</li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-lg">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
                            <span class="text-xl font-bold text-green-600">2</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Desarrollo</h3>
                        <p class="text-gray-600 text-sm mb-3">Semana 3-6</p>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li>• Configuración del sistema</li>
                            <li>• Personalización de marca</li>
                            <li>• Integración de APIs</li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-lg">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                            <span class="text-xl font-bold text-purple-600">3</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Pruebas</h3>
                        <p class="text-gray-600 text-sm mb-3">Semana 7-8</p>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li>• Pruebas de funcionalidad</li>
                            <li>• Pruebas de seguridad</li>
                            <li>• Pruebas de carga</li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-lg">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                            <span class="text-xl font-bold text-orange-600">4</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Lanzamiento</h3>
                        <p class="text-gray-600 text-sm mb-3">Semana 9</p>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li>• Capacitación de usuarios</li>
                            <li>• Puesta en producción</li>
                            <li>• Soporte post-lanzamiento</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <div class="inline-flex items-center bg-white rounded-lg px-6 py-3 shadow-lg">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-semibold text-gray-900">Tiempo total de implementación: 8-9 semanas</span>
                    </div>
                </div>
            </div>
        </section> --}}

        <!-- Garantías y Soporte -->
        <section class="mb-16">
            <div class="bg-white rounded-2xl shadow-corporate p-8 lg:p-12">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Garantías y Soporte</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Su tranquilidad es nuestra prioridad. Ofrecemos garantías sólidas y soporte continuo
                    </p>
                    <div class="w-24 h-1 gradient-primary mx-auto rounded-full mt-6"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Garantías Incluidas</h3>
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div
                                    class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Garantía de Funcionamiento</h4>
                                    <p class="text-gray-600">
                                        Se garantiza el correcto funcionamiento del sistema durante los primeros 12 meses.
                                        Cualquier falla será corregida sin costo adicional.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div
                                    class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-2">SLA de Disponibilidad</h4>
                                    <p class="text-gray-600">
                                        Se garantiza 99.9% de disponibilidad del sistema. En caso de no cumplir,
                                        ofrecemos créditos de servicio proporcionales al tiempo de inactividad.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div
                                    class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Garantía de Seguridad</h4>
                                    <p class="text-gray-600">
                                        Garantizamos la integridad y confidencialidad de todos los datos procesados.
                                        Incluye seguro de responsabilidad civil por brechas de seguridad.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Niveles de Soporte</h3>
                        <div class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    {{-- <h4 class="font-semibold text-gray-900">Soporte Básico</h4> --}}
                                    <h4 class="font-semibold text-gray-900">Soporte Profesional</h4>
                                    <span class="text-sm text-gray-500">Incluido</span>
                                </div>
                                <ul class="text-gray-600 text-sm space-y-1">
                                    <li>• Email support (respuesta en menos de 24h)</li>
                                    <li>• Base de conocimientos</li>
                                    <li>• Actualizaciones de seguridad</li>
                                </ul>
                            </div>

                            {{--
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-900">Soporte Profesional</h4>
                                    <span class="text-sm text-blue-600">+$500/mes</span>
                                </div>
                                <ul class="text-gray-600 text-sm space-y-1">
                                    <li>• Todo lo del soporte básico</li>
                                    <li>• Soporte telefónico (respuesta en 4h)</li>
                                    <li>• Actualizaciones de funcionalidades</li>
                                    <li>• Monitoreo proactivo</li>
                                </ul>
                            </div>

                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-900">Soporte Premium</h4>
                                    <span class="text-sm text-green-600">+$1,200/mes</span>
                                </div>
                                <ul class="text-gray-600 text-sm space-y-1">
                                    <li>• Todo lo del soporte profesional</li>
                                    <li>• Soporte 24/7 (respuesta en 1h)</li>
                                    <li>• Gestor de cuenta dedicado</li>
                                    <li>• Desarrollo de funcionalidades personalizadas</li>
                                </ul>
                            </div>
                            --}}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="mb-16">
            <div class="gradient-primary rounded-2xl p-8 lg:p-12 text-white text-center">
                <h2 class="text-4xl font-bold mb-4">¿Listo para Revolucionar sus Votaciones?</h2>
                <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
                    Únase a las organizaciones que ya confían en nuestro sistema para sus procesos de votación más
                    importantes.
                    {{-- Solicite una demostración personalizada hoy mismo. --}}
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                    {{-- <button
                        class="bg-white text-blue-600 font-bold py-4 px-8 rounded-lg hover:bg-blue-50 transition-colors shadow-lg">
                        Solicitar Demostración
                    </button> --}}
                    <button
                        class="border-2 border-white text-white font-bold py-4 px-8 rounded-lg hover:bg-white hover:text-blue-600 transition-colors">
                        Descargar Propuesta PDF
                    </button>
                </div>

                <div class="mt-8 flex items-center justify-center space-x-8 text-blue-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                            </path>
                        </svg>
                        +58 (412) 156-0804
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                        soporte.saefl@gmail.com
                    </div>
                </div>
            </div>
        </section>

        <!-- Botones de Acción -->
        <div class="text-center no-print">
            <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <button data-print
                    class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Imprimir Propuesta
                </button>
                <a href="{{ route('voting.asistent') }}"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                    Ver Demo en Vivo
                </a>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    @parent
    <script>
        // Animaciones adicionales para la propuesta
        document.addEventListener('DOMContentLoaded', function() {
            // Contador animado para estadísticas
            const counters = document.querySelectorAll('[data-counter]');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-counter'));
                const duration = 2000;
                const step = target / (duration / 16);
                let current = 0;

                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }, 16);
            });

            // Efecto parallax suave para secciones
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const parallaxElements = document.querySelectorAll('.parallax');

                parallaxElements.forEach(element => {
                    const speed = element.dataset.speed || 0.5;
                    const yPos = -(scrolled * speed);
                    element.style.transform = `translateY(${yPos}px)`;
                });
            });

            // Highlight de características al hacer hover
            const featureCards = document.querySelectorAll('.feature-card');
            featureCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px) scale(1.02)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
@endsection
