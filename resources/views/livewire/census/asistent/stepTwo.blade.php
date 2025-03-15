<div class="mx-auto max-w-sm">
    <h2 class="mb-2 text-3xl font-bold text-white">Paso 2</h2>
    <p class="mb-4 text-gray-400">Ingresa los datos del estudiante</p>

    <form method="POST" action="#" class="space-y-2">
        @csrf

        <div class="grid md:grid-cols-1">

            <div class="space-y-2">
                <x-input 
                    wire:model="firstname"
                    label="Nombres" 
                    placeholder="Nombres" 
                    right-icon="user"
                    class="mb-2"
                />
            </div>

            <div class="space-y-2">
                <x-input 
                    wire:model="lastname"
                    label="Apellidos" 
                    placeholder="Apellidos" 
                    right-icon="users"
                    class="mb-2"
                />
            </div>

            <div class="space-y-2">
                <x-datetime-picker
                    wire:model="date_birth"
                    label="Fecha de nacimiento"
                    placeholder="Seleccione"
                    without-time
                    display-format="YYYY-MM-DD"
                    class="mb-2"
                />
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
                    <x-select.option label="5to Año" value="11" />
                </x-select>
            </div>

            <div class="space-y-2">
                <x-button wire:click="validateStudent" xl black label="Verifica los datos" class="w-full my-2" />
            </div>

        </div>

    </form>

</div>
