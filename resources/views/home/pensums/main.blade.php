<x-card>

    @slot('header')
        <h3 class=" bg-green-100 mb-4 mt-6 p-2 text-3xl font-bold text-neutral-800 dark:text-neutral-200">
            Descubre Nuestro Pensum Académico: Formación Integral para el Futuro
        </h3>
        <small>

            En la U.E. Colegio Fray Luis Amigó, ofrecemos una educación de excelencia en Educación Inicial, Educación Primaria y Media General en Ciencia y Tecnología, diseñada para desarrollar habilidades académicas, personales y sociales en cada estudiante.
        </small>
    @endslot

    <div class=" lg:p-8">
        @include('home.pensums.items')
    </div>



<div x-data="{ open: false }" class="text-center mt-6">
    <button 
        @click="open = true"
        class="px-6 py-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-900 font-semibold rounded-xl shadow-sm transition-all duration-200 ease-in-out transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-emerald-300"
    >
        Inscribirse a Danzas Joropo Recio
    </button>

    <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>

            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full"
                 role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-3">
                        Formulario de Inscripción<br>
                        <span class="text-amber-600">Danzas Joropo Recio</span>
                    </h2>
                    <p class="text-sm text-gray-600 mb-4">
                        Complete todos los campos. La información se usará solo para inscripción y seguridad.
                    </p>

                    <div class="relative w-full overflow-hidden rounded border border-gray-200">
                        <iframe
                            src="https://makeform.ai/e/cjO2oCgV"
                            width="100%"
                            height="400"
                            style="border: none; margin: 0; padding: 0;"
                            title="Formulario de inscripción - Danzas Joropo Recio"
                            loading="lazy"
                        ></iframe>
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button @click="open = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded font-medium">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    

</x-card>
