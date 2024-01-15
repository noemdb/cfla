<x-card title="CONTACTO">
    
    <div class="grid grid-cols-12 gap-x-4">

        <!-- Column -->
        <div class="col-span-12 md:col-span-12 xl:col-span-12">
            <div class="mb-4 rounded-lg bg-secondary-100 px-6 py-5 text-base text-secondary-800" role="alert">
                CONTÁCTANOS
            </div>            
        </div>

        <!-- Column -->
        <div class="col-span-12 md:col-span-6 xl:col-span-6">
            <section id="contact" class="bg-gray-100 py-12">
                <div class="container mx-auto px-4">

                    <h2 class="text-3xl font-semibold text-center mb-8">Contacto</h2>

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
        <div class="col-span-12 md:col-span-6 xl:col-span-6">
            Lorem, ipsum dolor sit amet consectetur adipisicing elit. Doloremque odio officia voluptatibus ipsa
            inventore molestias eos ipsam amet, consequatur saepe nihil minima ea minus. Accusantium natus non aperiam
            placeat numquam.
        </div>

    </div>


</x-card>