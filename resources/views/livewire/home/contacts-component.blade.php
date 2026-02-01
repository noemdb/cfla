<div class="space-y-6">

    <div
        class="diagnostic-card bg-white/60 dark:bg-gray-900/40 backdrop-blur-sm border border-emerald-200 dark:border-emerald-500/30 rounded-xl overflow-hidden shadow-2xl">

        <div
            class="bg-gradient-to-r from-emerald-600 to-green-600 dark:from-emerald-900 dark:to-gray-900 p-6 border-b border-emerald-200 dark:border-emerald-500/30">
            <h3 class="text-3xl font-bold text-white dark:text-emerald-100 flex items-center">
                <x-icon name="chat-bubble-left-right" class="w-8 h-8 mr-3 text-white dark:text-emerald-400" />
                CONTÁCTANOS
            </h3>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-12 gap-8">

            <!-- Form Column -->
            <div class="col-span-12 md:col-span-12">
                <section id="contact">
                    <div class="container mx-auto">

                        <h2 class="mb-8 text-2xl font-semibold text-center text-gray-800 dark:text-gray-200">Envíanos un
                            Mensaje</h2>

                        <div class="max-w-2xl mx-auto space-y-4">
                            <div class="mb-4">
                                <label class="block mb-2 font-semibold text-emerald-700 dark:text-emerald-300"
                                    for="name">Nombre</label>
                                <input wire:model="name"
                                    class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-emerald-500/30 text-gray-900 dark:text-white focus:border-emerald-500 focus:ring focus:ring-emerald-500/20 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                    type="text" id="name" placeholder="Tu nombre completo">
                                @error('name')
                                    <div class="font-semibold text-right text-red-500 dark:text-red-400 text-sm mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block mb-2 font-semibold text-emerald-700 dark:text-emerald-300"
                                    for="email">Email</label>
                                <input wire:model="email"
                                    class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-emerald-500/30 text-gray-900 dark:text-white focus:border-emerald-500 focus:ring focus:ring-emerald-500/20 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                    type="email" id="email" placeholder="tu@email.com">
                                @error('email')
                                    <div class="font-semibold text-right text-red-500 dark:text-red-400 text-sm mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block mb-2 font-semibold text-emerald-700 dark:text-emerald-300"
                                    for="message">Mensaje</label>
                                <textarea wire:model="message" rows="4"
                                    class="w-full p-3 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-emerald-500/30 text-gray-900 dark:text-white focus:border-emerald-500 focus:ring focus:ring-emerald-500/20 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                    id="message" placeholder="¿En qué podemos ayudarte?"></textarea>
                                @error('message')
                                    <div class="font-semibold text-right text-red-500 dark:text-red-400 text-sm mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <button wire:click="save()"
                                class="w-full md:w-auto px-8 py-3 text-white bg-gradient-to-r from-emerald-600 to-green-600 rounded-full hover:from-emerald-500 hover:to-green-500 transition-all shadow-lg hover:shadow-emerald-500/30 font-bold transform hover:-translate-y-1">
                                Enviar Mensaje
                            </button>
                        </div>

                    </div>
                </section>

                <div
                    class="mt-12 p-6 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-emerald-200 dark:border-emerald-500/20">
                    <div
                        class="mb-4 text-lg font-bold text-emerald-800 dark:text-emerald-100 border-b border-emerald-200 dark:border-emerald-500/20 pb-2">
                        Otros
                        canales de comunicación:</div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="flex items-start text-gray-700 dark:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-5 h-5 mr-3 mt-1 text-emerald-600 dark:text-emerald-400 flex-shrink-0">
                                <path
                                    d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z" />
                                <path
                                    d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z" />
                            </svg>
                            <div class="flex flex-col text-sm break-all">
                                <span>colegiofrayluisa@gmail.com</span>
                                <span>direccionacademica.c.e.cfla@gmail.com</span>
                                <span>controldeestudios.c.e.cfla@gmail.com</span>
                            </div>
                        </div>

                        <div class="flex items-start text-gray-700 dark:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-5 h-5 mr-3 mt-1 text-emerald-600 dark:text-emerald-400 flex-shrink-0">
                                <path fill-rule="evenodd"
                                    d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div class="flex flex-col text-sm">
                                <span>+58 424-5891682</span>
                                <span>+58 414-5442298</span>
                                <span>+58 424-5027880</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
