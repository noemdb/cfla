<x-card title="CONTACTO">

    @slot('header')
        <h3 class=" bg-green-100 mb-4 mt-6 p-2 text-3xl font-bold text-neutral-800 dark:text-neutral-200">
            CONTÁCTANOS
        </h3>
    @endslot

    <div class="grid grid-cols-12 gap-x-4">

        <!-- Column -->
        <div class="col-span-12 md:col-span-6 xl:col-span-6 border rounded shadow">
            <section id="contact" class="bg-gray-100 py-12">
                <div class="container mx-auto px-4">

                    <h2 class="text-2xl font-semibold text-center mb-8">Comunícate</h2>

                    <form>
                        <div class="mb-4">
                            <label class="block font-semibold mb-2" for="name">Nombre</label>
                            <input class="border p-2 w-full" type="text" id="name">
                        </div>

                        <div class="mb-4">
                            <label class="block font-semibold mb-2" for="email">Email</label>
                            <input class="border p-2 w-full" type="email" id="email">
                        </div>

                        <div class="mb-4">
                            <label class="block font-semibold mb-2" for="message">Mensaje</label>
                            <textarea class="border p-2 w-full" id="message"></textarea>
                        </div>

                        <button class="bg-blue-500 text-white rounded-full py-3 px-6 hover:bg-blue-600">
                            Enviar
                        </button>

                    </form>

                </div>
            </section>
        </div>

        <!-- Column -->
        <div class="col-span-12 md:col-span-6 xl:col-span-6 border rounded shadow">

            <x-card title="Localízanos">
                @slot('header')
                    <h3 class="text-2xl font-semibold text-center mb-8">
                        Localízanos
                    </h3>
                @endslot
                <iframe class="w-full aspect-square border-spacing-2 rounded shadow"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3924.8561340803612!2d-68.74191124972349!3d10.353386292573173!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e80cf4083bc5c85%3A0x668a89fe22d58027!2sColegio%20Fray%20Luis%20Amig%C3%B3!5e0!3m2!1ses!2sve!4v1600200177193!5m2!1ses!2sve"
                frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
            </x-card>
            
        </div>

    </div>

</x-card>