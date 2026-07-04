<div class="alert alert-light border rounded" role="alert">

     <!-- Barra de Progreso -->
    <div class="progress mb-4" style="height: 20px;">
        <div class="progress-bar bg-primary" role="progressbar" 
            style="width: {{ round( 100 * $currentStep / 7  )  }}%;" 
            aria-valuenow="{{ $currentStep }}" 
            aria-valuemin="0" 
            aria-valuemax="7">
            Paso {{ $currentStep }} de 7
        </div>
    </div>

</div>




<div class="alert alert-light border rounded" role="alert">

    @if ($status_load_interview) 
        <div class="alert alert-success small">
            <strong>Datos cargados de la última entrevista registrada  para:</strong> {{$representant_name ?? null}} {{$representant_ci ?? null}}
            <small class="d-block text-muted">Modifíca lo que necesite antes de guardar.</small>
        </div>
    @endif
    
    @if ($currentStep == 1)
        @include('livewire.general.catchment.interview.form.representant')
    @elseif ($currentStep == 2)
        @include('livewire.general.catchment.interview.form.estudiant')
    @elseif ($currentStep == 3)
        @include('livewire.general.catchment.interview.form.living')
    @elseif ($currentStep == 4)
        @include('livewire.general.catchment.interview.form.economic')
    @elseif ($currentStep == 5)
        @include('livewire.general.catchment.interview.form.institution')
    @elseif ($currentStep == 6)
        @include('livewire.general.catchment.interview.form.agreement')
    @elseif ($currentStep == 7)
        @include('livewire.general.catchment.interview.form.catholic')
    @endif

</div>


@if ($errors->any())
    <hr>
    <div class="fw-bold pt-3 pb-0">Revise los siguientes mensajes</div>
    <div class="alert alert-danger alert-dismissible fade show small" role="alert">
        <ul class="list-group list-group-numbered">
            @foreach ($errors->all() as $error)
                <li class="list-group-item list-group-item-danger fw-bold">{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<hr>


<!-- Botones de Navegación -->
<div class="d-flex justify-content-between mt-4">
    <button type="button" class="btn btn-secondary" 
            wire:click="previousStep" 
            @if ($currentStep == 1) disabled @endif>
        Anterior
    </button>

    <button type="button" class="btn btn-primary" 
            wire:click="nextStep" 
            @if ($currentStep == 7) disabled @endif>
        Siguiente
    </button>
</div>

<!-- Botón Guardar -->
@if ($currentStep == 7)
    <hr>
    <button type="button" class="btn btn-success w-100" wire:click="save">
        Guardar
    </button>
@endif
