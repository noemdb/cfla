<div>
    <x-card title="CONTACTO" class="bg-gray-100">

        @slot('header')
        <h3 class=" bg-green-100 mb-4 mt-6 p-2 text-3xl font-bold text-neutral-800 dark:text-neutral-200">
            CONTÁCTANOS, ENVÍANOS UN MENSAJE
        </h3>
        @endslot

        <div class="grid grid-cols-12 gap-x-4">

            <!-- Column -->
            <div class="col-span-12 md:col-span-6 xl:col-span-6 border rounded shadow">
                <section id="contact" class="bg-gray-100 py-12">
                    <div class="container mx-auto px-4">

                        <h2 class="text-2xl font-semibold text-center mb-8">Comunícate</h2>

                        {{-- <form> --}}
                            <div class="mb-4">
                                <label class="block font-semibold mb-2" for="name">Nombre</label>
                                <input wire:model="name" class="border p-2 w-full" type="text" id="name">
                                @error('name')<div class=" text-right text-red-600 font-semibold small">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block font-semibold mb-2" for="email">Email</label>
                                <input wire:model="email" class="border p-2 w-full" type="email" id="email">
                                @error('email')<div class=" text-right text-red-600 font-semibold small">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block font-semibold mb-2" for="message">Mensaje</label>
                                <textarea wire:model="message" class="border p-2 w-full" id="message"></textarea>
                                @error('message')<div class=" text-right text-red-600 font-semibold small">{{ $message }}</div> @enderror
                            </div>

                            <button wire:click="save()" class="bg-blue-500 text-white rounded-full py-3 px-6 hover:bg-blue-600">
                                Enviar
                            </button>

                        {{-- </form> --}}

                    </div>
                </section>

                <div class="p-4 m-4 border-t-2">
                    <div class="h4 mb-4 text-lg font-bold">Otros canales de comunicación:</div>
                    <p class="mb-4 flex items-center justify-center md:justify-start">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mr-3 h-5 w-5">
                            <path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z" />
                            <path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z" />
                        </svg>
                        frayluisamigoyara@hotmail.com
                    </p>
                    <p class="mb-4 flex items-center justify-center md:justify-start">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mr-3 h-5 w-5">
                            <path fill-rule="evenodd"
                                d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z"
                                clip-rule="evenodd" />
                        </svg>
                        + 058 0424-5891682 - 0414-5442298
                    </p>
                </div>

                

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
</div>