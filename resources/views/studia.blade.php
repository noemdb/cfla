<x-studia>

    {{-- <div class="fixed inset-0 bg-blue-500 flex items-center justify-center z-50">
        <img src="{{ asset('image/splash/ima.jpg') }}" alt="Preparación Física" class="h-48 w-auto">
    </div> --}}

    @php /* @endphp
    <section class="bg-gradient-to-br from-blue-400 to-purple-600 text-white py-20">
        <div class="container mx-auto text-center">
            <h1 class="text-4xl font-bold mb-4">Bienvenido a StudyAI</h1>
            <p class="text-lg mb-6">Transforma tu forma de estudiar con tecnología de vanguardia.</p>
            <button 
                onclick="loginWithGoogle()" 
                class="bg-white text-blue-600 px-6 py-3 rounded shadow hover:bg-gray-200">
                Comienza Ahora
            </button>
        </div>
    </section>

    <section id="features" class="py-16 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-bold mb-8">Características</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-6 bg-white dark:bg-gray-800 rounded shadow hover:shadow-lg">
                    <h3 class="font-bold text-xl mb-2">Aprendizaje Personalizado</h3>
                    <p>Genera ejercicios adaptados a tu nivel de conocimiento y objetivos de estudio.</p>
                </div>
                <div class="p-6 bg-white dark:bg-gray-800 rounded shadow hover:shadow-lg">
                    <h3 class="font-bold text-xl mb-2">IA Dinámica</h3>
                    <p>Utilizamos inteligencia artificial para crear preguntas dinámicas y desafiantes.</p>
                </div>
                <div class="p-6 bg-white dark:bg-gray-800 rounded shadow hover:shadow-lg">
                    <h3 class="font-bold text-xl mb-2">Seguimiento de Progreso</h3>
                    <p>Monitorea tu rendimiento y mejora continuamente con métricas detalladas.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="testimonials" class="py-16 bg-gray-100 dark:bg-gray-800">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-bold mb-8">Testimonios</h2>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach ($testimonials as $testimonial)
                <div class="p-6 bg-white dark:bg-gray-700 rounded shadow hover:shadow-lg">
                    <p class="text-sm italic">"{{ $testimonial->message }}"</p>
                    <p class="font-bold mt-4">{{ $testimonial->name }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="faq" class="py-16 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-8">Preguntas Frecuentes</h2>
            <div x-data="{ open: null }" class="grid gap-4">
                @foreach ($faqs as $index => $faq)
                <div>
                    <button 
                        class="w-full text-left font-bold text-lg p-4 bg-gray-200 dark:bg-gray-700 rounded" 
                        @click="open === {{ $index }} ? open = null : open = {{ $index }}">
                        {{ $faq->question }}
                    </button>
                    <div x-show="open === {{ $index }}" x-collapse class="p-4">
                        <p class="text-gray-700 dark:text-gray-300">{{ $faq->answer }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-gradient-to-br from-purple-600 to-blue-400 text-white py-20 text-center">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold mb-4">Únete a StudyAI</h2>
            <p class="mb-6">Transforma tu experiencia de aprendizaje con herramientas inteligentes y tecnología avanzada.</p>
            <button 
                onclick="loginWithGoogle()" 
                class="bg-white text-blue-600 px-6 py-3 rounded shadow hover:bg-gray-200">
                Empieza Ahora
            </button>
        </div>
    </section>

    @php */ @endphp

</x-studia>
