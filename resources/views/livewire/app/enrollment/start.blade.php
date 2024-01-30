<x-card class="p-4 cursor-pointer h-full border-0" wire:click="setStart">

    <x-slot name="header">
        <h3 class=" text-green-950 bg-primary-100 rounded-t-xl p-2 text-xl font-bold dark:text-neutral-200">
            <div class="h-full flex items-center">
                <x-icon name="document" class="flex-none w-10 h-10" />
                <div class="flex-initial">Actualización de matrícula.</div>
            </div>
        </h3>
    </x-slot>

    <div class="block rounded-lg bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700">
        <div class="relative overflow-hidden bg-cover bg-no-repeat" data-te-ripple-init data-te-ripple-color="light">
            <div class="flex justify-center">
                <div class="flex flex-col justify-center items-center">

                    <div class="w-32 h-32 border rounded-full bg-green-200 mb-4 flex justify-center items-center">
                        <img class="rounded-t-lg w-24 h-24" src="{{asset('image/highlighted/matricula.png')}}" alt="" />
                    </div>

                    <h5 class="mb-2 text-xl font-medium leading-tight border-b-2 border-b-blue-600">Actualización de datos</h5>
                </div>
                <a href="#!" wire:click="setStart">
                    <div class="absolute bottom-0 left-0 right-0 top-0 h-full w-full overflow-hidden bg-[hsla(0,0%,98%,0.15)] bg-fixed opacity-0 transition duration-300 ease-in-out hover:opacity-100"></div>
                </a>
            </div>
        </div>

        <div class="text-xs text-gray-200flex justify-between items-center bg-primary-100 p-4 my-4 rounded-lg origin-bottom rotate-2">
            <span class="font-bold">Actualizar información del estudiante:</span>
            La solicitud de actualización de matrícula es un documento que se utiliza para informar de los cambios que
            se han producido en los datos de un estudiante. Estos cambios pueden referirse a la dirección, el teléfono,
            la situación familiar o cualquier otra información relevante.
        </div>

        <div class="flex justify-end">
            <x-button label="Comenzar" wire:click="setStart" primary class="w-full bg-blue-900 text-white"/>
        </div>

    </div>

</x-card>
