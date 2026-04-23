<x-pulse>
    <livewire:pulse.servers cols="full" />

    <!-- Reverb WebSockets (Mitad y Mitad) -->
    <livewire:reverb.connections cols="6" />
    <livewire:reverb.messages cols="6" />

    <!-- Colas y Trabajos (Mitad y Mitad) -->
    <livewire:pulse.queues cols="6" />
    <livewire:pulse.slow-jobs cols="6" />

    <!-- Bloque Combinado (12 columnas) -->
    <!-- Usage ocupa 2 filas de altura. Lo acompañamos con Cache y Excepciones en la fila superior -->
    <livewire:pulse.usage cols="4" rows="2" />
    <livewire:pulse.cache cols="4" />
    <livewire:pulse.exceptions cols="4" />

    <!-- Segunda fila del bloque combinado. Usage ya ocupa 4 columnas, quedan 8 para Slow Queries -->
    <livewire:pulse.slow-queries cols="8" />

    <!-- Peticiones Lentas (Mitad y Mitad) -->
    <livewire:pulse.slow-requests cols="6" />
    <livewire:pulse.slow-outgoing-requests cols="6" />
</x-pulse>
