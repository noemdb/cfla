<div>
    <x-card title="CONTACTO" class="bg-gray-100">

        @slot('header')
        <h3 class="p-2 mt-6 mb-4 text-3xl font-bold bg-green-100 text-neutral-800 dark:text-neutral-200">
            CONTÁCTANOS, ENVÍANOS UN MENSAJE
        </h3>
        @endslot

        <div class="grid grid-cols-12 gap-x-4">

            <!-- Column -->
            <div class="col-span-12 border rounded shadow md:col-span-12 xl:col-span-12">
                <section id="contact" class="py-12 bg-gray-100">
                    <div class="container px-4 mx-auto">

                        <h2 class="mb-8 text-2xl font-semibold text-center">Comunícate</h2>

                        {{-- <form> --}}
                            <div class="mb-4">
                                <label class="block mb-2 font-semibold" for="name">Nombre</label>
                                <input wire:model="name" class="w-full p-2 border" type="text" id="name">
                                @error('name')<div class="font-semibold text-right text-red-600 small">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block mb-2 font-semibold" for="email">Email</label>
                                <input wire:model="email" class="w-full p-2 border" type="email" id="email">
                                @error('email')<div class="font-semibold text-right text-red-600 small">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block mb-2 font-semibold" for="message">Mensaje</label>
                                <textarea wire:model="message" class="w-full p-2 border" id="message"></textarea>
                                @error('message')<div class="font-semibold text-right text-red-600 small">{{ $message }}</div> @enderror
                            </div>

                            <button wire:click="save()" class="px-6 py-3 text-white bg-blue-500 rounded-full hover:bg-blue-600">
                                Enviar
                            </button>

                        {{-- </form> --}}

                    </div>
                </section>

                <div class="p-4 m-4 border-t-2">
                    <div class="mb-4 text-lg font-bold h4">Otros canales de comunicación:</div>
                    <p class="flex items-center justify-center mb-4 md:justify-start">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 mr-3">
                            <path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z" />
                            <path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z" />
                        </svg>
                        colegiofrayluisa@gmail.com || direcciónacadémica.c.e.cfla@gmail.com || controldeestudios.c.e.cfla@gmail.com
                    </p>
                    <p class="flex items-center justify-center mb-4 md:justify-start">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 mr-3">
                            <path fill-rule="evenodd"
                                d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z"
                                clip-rule="evenodd" />
                        </svg>
                        + 058 0424-5891682 || + 058 0414-5442298 || + 058 0424-5027880
                    </p>
                </div>



            </div>

            <!-- Column -->
            {{-- <div class="col-span-12 border rounded shadow md:col-span-6 xl:col-span-6">

                <x-card title="Localízanos">
                    @slot('header')
                    <h3 class="mb-8 text-2xl font-semibold text-center">
                        Localízanos
                    </h3>
                    @endslot
                    <iframe class="w-full rounded shadow aspect-square border-spacing-2"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3924.8561340803612!2d-68.74191124972349!3d10.353386292573173!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e80cf4083bc5c85%3A0x668a89fe22d58027!2sColegio%20Fray%20Luis%20Amig%C3%B3!5e0!3m2!1ses!2sve!4v1600200177193!5m2!1ses!2sve"
                        frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                </x-card>

            </div> --}}

        </div>

    </x-card>
</div>