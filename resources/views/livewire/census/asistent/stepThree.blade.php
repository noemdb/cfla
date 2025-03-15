<div class="mx-auto max-w-sm">
    <h2 class="mb-2 text-3xl font-bold text-white">Paso 3</h2>
    <p class="mb-4 text-gray-400">Ingresa los datos del representante</p>

    <form method="POST" action="#" class="space-y-2">
        @csrf

        <div class="grid md:grid-cols-1">

            <div class="space-y-2 mb-2">
                <x-input 
                    wire:model="representant_name"
                    label="Nombre completo" 
                    placeholder="Nombre completo" 
                    right-icon="user"
                    class="mb-2"
                />
            </div>

            <div class="space-y-2 mb-2">

                <x-input 
                    wire:model="representant_ci"
                    label="Cédula de Identidad" 
                    placeholder="Cédula de Identidad" 
                    {{-- right-icon="user" --}}
                    class="mb-2"
                    suffix="#" 
                />
            </div>
            
            <div class="space-y-2 mb-2">
                <x-inputs.maskable
                    wire:model="representant_phone"
                    label="Número de teléfono"
                    mask="## ### ###-####"
                    placeholder="58 414 145-9834"
                    corner-hint="Ej: 58 414 145-9834"
                    right-icon="phone"
                    class="mb-2"
                /> 
                <x-inputs.maskable
                    wire:model="representant_cellphone"
                    label="Número WhatsApp"
                    mask="## ### ###-####"
                    placeholder="58 414 145-9834"
                    corner-hint="Ej: 58 414 145-9834"
                    right-icon="chat-alt-2"
                    class="mb-2"
                />               
            </div>

            <div class="space-y-2 mb-2">
                {{-- <x-inputs.maskable
                    wire:model="representant_cellphone"
                    label="Número WhatsApp: Ej: 58 414 145-9834"
                    mask="## ### ###-####"
                    placeholder="Número WhatsApp"
                    id="representant_cellphone"
                    right-icon="chat-alt-2"
                /> --}}                
            </div>

            <div class="space-y-2">
                
                <x-datetime-picker
                    wire:model.live="day_appointment"
                    label="Fecha en la que acudirá institucción"
                    placeholder="Seleccione"
                    without-time
                    display-format="YYYY-MM-DD"
                    class="mb-2"
                />               

            </div>

            <div class="space-y-2 mb-2">
                <x-button
                    xl
                    wire:click="saveEnrollment" 
                    positive 
                    label="Guarda tus datos" 
                    class="w-full my-2" 
                />
            </div>
        </div>

    </form>

</div>
