<x-card>

    @slot('header')
    <h3 class=" bg-green-100 mb-4 mt-6 p-2 text-3xl font-bold text-neutral-800 dark:text-neutral-200">
        PVV
    </h3>
    @endslot

    <div
        class="block rounded-lg bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700">
        <div class="relative overflow-hidden bg-cover bg-no-repeat" data-te-ripple-init data-te-ripple-color="light">
            <div class="flex justify-center">
                <img class="rounded-t-lg" src="https://tecdn.b-cdn.net/img/new/standard/nature/186.jpg" alt="" />
                <a href="#!">
                    <div
                        class="absolute bottom-0 left-0 right-0 top-0 h-full w-full overflow-hidden bg-[hsla(0,0%,98%,0.15)] bg-fixed opacity-0 transition duration-300 ease-in-out hover:opacity-100">
                    </div>
                </a>
            </div>
        </div>
        <div class="p-6">

            <p class="mb-4 text-base text-neutral-600 dark:text-neutral-200">
                Some quick example text to build on the card title and make up the
                bulk of the card's content.
            </p>
            <div class="flex justify-end">
                <x-button primary label="Comenzar" />
            </div>
        </div>
    </div>

</x-card>