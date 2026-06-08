@if ($statusLoad)
    <div class="d-flex">
        <div class="p-2 flex-shrink-1">
            <i class="bi bi-card-text" style="font-size:4rem;"></i>
        </div>
        <div class="p-2 w-100">
            <label for="ci_representant" class="form-label">Cédula</label>
            <div class="input-group mb-0">
                <input type="text" name="ci" wire:model.defer="ci" id="ci" class="form-control" placeholder="Cédula" aria-label="Cédula" aria-describedby="button-addon2">

                <a class="btn btn-primary" type="button" href="#" wire:click="loadUser" id="redirect">Comenzar</a>
            </div>
            <div id="representanteHelp" class="form-text">Ingrese la su cédula, sólo números.</div>
            @error('ci')<span class="text-danger small">{{ $message }}</span> @enderror
        </div>
    </div>
@else
    <div class="jumbotron">
        <h1 class="display-4 text-center">Cédula no se encontrada...</h1>
    </div>
    <a class="btn btn-success" type="button" href="#" wire:click="goToStart" id="redirect">ir al Inicio</a>
@endif
