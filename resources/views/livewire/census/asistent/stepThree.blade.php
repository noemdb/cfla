<div class="mx-auto max-w-sm">
    <h2 class="mb-2 text-3xl font-bold text-white">Paso 3</h2>
    <p class="mb-4 text-gray-400">Ingresa los datos del representante</p>

    <form method="POST" action="#" class="space-y-2">
        @csrf

        <div class="grid md:grid-cols-1">

            <div class="space-y-2 mb-2">
                <x-input 
                    wire:model="name_representant"
                    label="Nombrse" 
                    placeholder="Nombres" 
                    right-icon="user"
                    class="mb-2"
                />
            </div>
            <div class="space-y-2 mb-2">
                <x-input 
                    wire:model="lastname_representant"
                    label="Apellidos" 
                    placeholder="Apellidos" 
                    right-icon="user"
                    class="mb-2"
                />
            </div>

            <div class="space-y-2 mb-2">
                <x-inputs.number 
                    wire:model="ci_representant"
                    label="Cédula de identidad" 
                    placeholder="Cédula de identidad" 
                    right-icon="credit-card"numeric
                    class="mb-2"
                />
            </div>

            
            <div class="space-y-2 mb-2">
                <x-inputs.maskable
                wire:model="phone_representant"
                    label="Número de teléfono: Ej: 58 414 145-9834"
                    mask="## ### ###-####"
                    placeholder="Número de teléfono"
                    id="phone_representant"
                    right-icon="chat-alt-2"
                />
            </div>

            <div class="space-y-2 mb-2">
                <x-button
                    wire:click="saveEnrollment" 
                    xl
                    positive 
                    label="Guarda tus datos" 
                    class="w-full my-2" 
                />
            </div>
        </div>

    </form>

</div>
