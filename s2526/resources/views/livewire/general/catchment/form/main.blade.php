<div class="container">

    <div class="alert alert-dark" role="alert">
        <div class="fw-bold text-center">Paso {{$step ?? null}}</div>
        <div class="progress my-0 py-0" style="height: 4px">
            @php $valuenow = ($limit) ? round(100 * $step / $limit) : null; @endphp
            <div class="progress-bar bg-secondary" role="progressbar" aria-label="Basic example" style="width: {{$valuenow ?? null}}%" aria-valuenow="{{$valuenow ?? null}}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

    @switch($step)
        @case(1) @include('livewire.general.catchment.form.partials.representant') @break
        @case(2) @include('livewire.general.catchment.form.partials.estudiant') @break
        {{-- @case(3) @include('livewire.general.catchment.form.partials.education') @break --}}
        @case(3) @include('livewire.general.catchment.form.partials.institution') @break
        @case(4) @include('livewire.general.catchment.form.partials.profile') @break                
    @endswitch

    <div class="btn-group w-100" role="group">
        <button class="btn btn-light btn-sm {{($step==1) ? 'disabled' : null}}" wire:click="back({{$step}})" role="button">Atrás</button>
        <button class="btn btn-primary btn-sm {{($step==$limit) ? 'disabled' : null}}" wire:click="next({{$step}})" role="button">Siguiente</button>
    </div>

    <div class="p-2 text-center fw-bold">
        <div wire:loading.delay.shortest class="alert alert-secondary text-muted p-2">
            Procesando...
        </div>
    </div>

</div>
