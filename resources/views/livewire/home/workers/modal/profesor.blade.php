<x-modal-card title="Docente" blur wire:model="modalShow" max-width="lg">

    <x-card>
        <p class="text-center text-gray-600">
            <div class="text-lg font-bold"> {{$profesor->name ?? null}} {{$profesor->lastname ?? null}}</div>
            <div>Fecha de Nacimiento: {{$profesor->date_birth ?? null}} </div>
            {{-- <div>Planes de Evaluación: {{$profesor->count_pevaluacions}} </div> --}}
        </p>

        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <img src="{{asset('image/avatar/user_default.png')}}" class="block w-full border rounded shadow" />
            </div>
        </x-slot>
    </x-card>


</x-modal-card>
