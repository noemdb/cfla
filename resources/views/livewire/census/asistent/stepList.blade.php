<div class="mx-auto w-full max-w-lg">
    <h2 class="mb-2 text-lg font-bold text-white">Resultados de Consulta</h2>
    <p class="mb-2 text-gray-300 font-medium">Estos son los registros asociados a tu cédula.</p>

    @include('livewire.census.asistent._flashAlert')

    <div class="space-y-4">
        @foreach($catchmentsList as $catchment)
            @php
                $interview = $catchment->catchmentInterviews->first();
                $statusColor = 'bg-gray-700 text-gray-300 border border-gray-600';
                $statusText = 'En Evaluación';
                
                if ($interview) {
                    if ($interview->accepted) {
                        $statusColor = 'bg-green-600 text-white border border-green-500 shadow-[0_0_15px_rgba(22,163,74,0.4)]';
                        $statusText = 'Aceptado';
                    } elseif ($interview->status_standby) {
                        $statusColor = 'bg-yellow-600 text-white border border-yellow-500 shadow-[0_0_15px_rgba(202,138,4,0.4)]';
                        $statusText = 'Lista de Espera';
                    }
                }
            @endphp
            
            <div class="p-5 bg-[#1a1a1b] border border-[#2d2f33] rounded-[24px] shadow-lg relative overflow-hidden transition-all duration-300 hover:border-gray-500">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-2">
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ $catchment->firstname }} {{ $catchment->lastname }}</h3>
                        <p class="text-sm font-medium text-gray-400">A cursar: <span class="text-gray-200">{{ $catchment->grado->name ?? 'No especificado' }}</span></p>
                    </div>
                    <span class="px-3.5 py-1.5 text-xs font-bold rounded-full {{ $statusColor }} whitespace-nowrap">
                        {{ $statusText }}
                    </span>
                </div>
                
                <div class="mt-4 pt-3 border-t border-[#2d2f33] text-sm text-gray-400 flex flex-col gap-1.5">
                    @if($catchment->ci_estudiant)
                        <p><strong>Cédula Estudiantil:</strong> {{ $catchment->ci_estudiant }}</p>
                    @endif
                    @if($catchment->day_appointment)
                        <p><strong>Día de Cita Pautado:</strong> {{ \Carbon\Carbon::parse($catchment->day_appointment)->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8 space-y-3 text-center">
        <x-button wire:click="startNewCatchment" xl positive label="Registrar un nuevo representado"
            class="w-full !bg-green-800 hover:!bg-green-900 border-2 !border-green-900 shadow-lg" />
        <x-button wire:click="restart" xl flat white label="Consultar otra cédula"
            class="w-full !border-gray-700 hover:!bg-gray-800" />
    </div>
</div>
