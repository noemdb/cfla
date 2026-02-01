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
                <x-maskable
                    wire:model="representant_phone"
                    label="Número de teléfono"
                    mask="## ### ###-####"
                    placeholder="58 414 145-9834"
                    corner-hint="Ej: 58 414 145-9834"
                    right-icon="phone"
                    class="mb-2"
                /> 
                <x-maskable
                    wire:model="representant_cellphone"
                    label="Número WhatsApp"
                    mask="## ### ###-####"
                    placeholder="58 414 145-9834"
                    corner-hint="Ej: 58 414 145-9834"
                    right-icon="chat-bubble-left-right"
                    class="mb-2"
                />               
            </div>

            <div class="space-y-2 mb-2">
                {{-- <x-maskable
                    wire:model="representant_cellphone"
                    label="Número WhatsApp: Ej: 58 414 145-9834"
                    mask="## ### ###-####"
                    placeholder="Número WhatsApp"
                    id="representant_cellphone"
                    right-icon="chat-bubble-left-right"
                /> --}}                
            </div>            

            <div class="space-y-2">

                @php 
                    $start = DateTime::createFromFormat('Y-m-d', $day_appointment_start); $start = ($start) ? $start->format('d-m-Y') : null;
                    $end = DateTime::createFromFormat('Y-m-d', $day_appointment_end); $end = ($end) ? $end->format('d-m-Y') : null;
                    $label = "Jordana 1: Desde ".$start." hasta ".$end ;
                @endphp
                <div class="block text-sm text-gray-400 dark:text-gray-700 font-medium border-b-2 border-gray-400 dark:border-gray-700">{{$label}}</div>
                
                <x-input 
                    wire:model="day_appointment"
                    label="Fecha en la que acudirá institucción" 
                    placeholder="año-mes-día" 
                    class="mb-2"
                    type="date"
                />
            </div>

            <div class="space-y-2 mb-2">
                <x-button
                    xl
                    wire:click="saveCatchment" 
                    positive 
                    label="Guarda tus datos" 
                    class="w-full my-2" 
                />
            </div>
        </div>

    </form>

</div>
