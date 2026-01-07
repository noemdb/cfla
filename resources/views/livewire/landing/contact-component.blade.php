<div class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl">
    @if ($success)
        <div
            class="mb-6 p-4 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 text-center">
            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="font-bold text-lg">¡Mensaje Enviado!</h3>
            <p>Gracias por contactarnos. Te responderemos a la brevedad posible.</p>
        </div>
    @else
        <form wire:submit.prevent="send" class="space-y-6">
            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Correo
                    Electrónico</label>
                <input wire:model="email" type="email" id="email"
                    class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                    placeholder="nombre@colegio.edu">
                @error('email')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="subject"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Asunto</label>
                <input wire:model="subject" type="text" id="subject"
                    class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Solicitud de información">
                @error('subject')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="message"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Mensaje</label>
                <textarea wire:model="message" id="message" rows="4"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg shadow-sm border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Déjanos saber cómo podemos ayudarte..."></textarea>
                @error('message')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            @if ($errors->has('general'))
                <div class="p-3 bg-red-100 text-red-700 rounded-lg text-sm">
                    {{ $errors->first('general') }}
                </div>
            @endif

            <button type="submit"
                class="w-full text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-300 font-medium rounded-lg text-sm px-5 py-3 text-center dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled">
                <span wire:loading.remove>Enviar Mensaje</span>
                <span wire:loading>Enviando...</span>
            </button>
        </form>
    @endif
</div>
