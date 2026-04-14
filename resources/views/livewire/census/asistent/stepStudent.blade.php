<div class="mx-auto max-w-sm">
    <h2 class="mb-2 text-3xl font-bold text-white">Paso 3</h2>
    <p class="mb-4 text-gray-400">Ingresa los datos del estudiante</p>

    <form method="POST" action="#" class="space-y-2">
        @csrf

        <div class="grid md:grid-cols-1">

            <div class="space-y-2">
                <x-input wire:model="firstname" label="Nombres" placeholder="Nombres" right-icon="user" class="mb-2" />
            </div>

            <div class="space-y-2">
                <x-input wire:model="lastname" label="Apellidos" placeholder="Apellidos" right-icon="users"
                    class="mb-2" />
            </div>

            <div class="space-y-2">
                <x-datetime-picker wire:model="date_birth" label="Fecha de nacimiento" placeholder="Seleccione"
                    without-time display-format="YYYY-MM-DD" class="mb-2" />
            </div>

            <div class="space-y-2">
                <x-select wire:model="grade" label="Selecciona el grado/año" placeholder="Selecciona" class="mb-2">
                    <x-select.option label="1er Grupo Inicial" value="22" />
                    <x-select.option label="2do Grupo Inicial" value="23" />
                    <x-select.option label="3er Grupo Inicial" value="24" />
                    <x-select.option label="1er Grado" value="1" />
                    <x-select.option label="2do Grado" value="2" />
                    <x-select.option label="3er Grado" value="3" />
                    <x-select.option label="4to Grado" value="4" />
                    <x-select.option label="5to Grado" value="5" />
                    <x-select.option label="6to Grado" value="6" />
                    <x-select.option label="1er Año" value="12" />
                    <x-select.option label="2do Año" value="13" />
                    <x-select.option label="3er Año" value="14" />
                    <x-select.option label="4to Año" value="10" />
                    {{-- <x-select.option label="5to Año" value="11" /> --}}
                </x-select>
            </div>

            <hr class="my-2">

            <div class="space-y-2 mb-2">
                <span>
                    Datos del representante: {{ $representant_ci }}
                </span>
            </div>

            <div class="space-y-2 mb-2">
                <x-input wire:model="representant_name" label="Nombre completo del representante"
                    placeholder="Nombre completo" right-icon="user" class="mb-2" />
            </div>

            <div class="space-y-2 mb-2">
                <x-input right-icon="phone" wire:model="representant_phone" label="Número de teléfono"
                    placeholder="04141459834" class="mb-2" />
            </div>

            <div class="space-y-2">

                @php
                    $start = DateTime::createFromFormat('Y-m-d', $day_appointment_start);
                    $start = $start ? $start->format('d-m-Y') : null;
                    $end = DateTime::createFromFormat('Y-m-d', $day_appointment_end);
                    $end = $end ? $end->format('d-m-Y') : null;
                    $label = 'Jordana 1: Desde ' . $start . ' hasta ' . $end;
                @endphp
                <div
                    class="block text-sm text-gray-400 dark:text-gray-700 font-medium border-b-2 border-gray-400 dark:border-gray-700">
                    {{ $label }}</div>

                <x-input wire:model="day_appointment" label="Fecha en la que acudirá institucción"
                    placeholder="año-mes-día" class="mb-2" type="date" />
            </div>

            <div class="space-y-2">
                <x-button wire:click="saveCatchment" xl positive label="Guarda tus datos"
                    class="w-full my-2 !bg-green-800 hover:!bg-green-900 border-2 !border-green-900 shadow-lg" />
            </div>

        </div>

    </form>

</div>
