<div>
	<h3 style="margin-bottom:0">Registro de incidencias y acuerdos alcanzados</h3>

	@include('livewire.bienestar.estudiant.pdf.partials.incidencias.table')

    <div style="page-break-after:always;"></div>

    @php
        $incidets_notificated = $estudiant->incidets_notificated;
    @endphp

    @if ($incidets_notificated->count())
        <h4 style="margin-bottom:0">Notificaciones enviadas</h4>
        <div style="margin-left: 0.5rem; margin-right: 0.5rem;">
            @include('livewire.bienestar.estudiant.pdf.partials.incidencias.notify')
        </div>
    @else
        <div style="font-weight: bold">
            Sin notificaciones enviadas
        </div>        
    @endif

    

	{{-- views/livewire/bienestar/estudiant/pdf/partials/incidencias/table.blade.php --}}

    {{-- @include('livewire.bienestar.estudiant.pdf.partials.table.estudiant') --}}

    {{-- @include('livewire.bienestar.estudiant.pdf.partials.table.illness') --}}

    {{-- @include('livewire.bienestar.estudiant.pdf.partials.table.parents') --}}

</div>
