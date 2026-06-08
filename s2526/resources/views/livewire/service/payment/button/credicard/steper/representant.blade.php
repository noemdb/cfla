<div id="test-l-1" role="tabpanel" class="bs-stepper-pane active" aria-labelledby="stepper1trigger1">

    <div class="text-start ">

        <label for="ci_representant" class="fw-bold">Cédula del representante</label>
        <div class="d-flex">
            <div class="p-2">
                <select class="form-select input-group-text" wire:model="type_ci" id="type_ci" style="width: 5rem !important">
                    <option></option>
                    <option value="V">V</option>
                    <option value="J">J</option>
                    <option value="C">C</option>
                    <option value="E">E</option>
                    <option value="G">G</option>
                    <option value="P">P</option>
                </select>
            </div>
            <div class="p-2 flex-grow-1">
                <input type="number" wire:model.defer="ci_representant" id="ci_representant" class="form-control" placeholder="Ingrese CI">
            </div>
        </div>

        @error('ci_representant') <span class="text-danger small d-block text-right">{{ $message }}</span> @enderror
        @error('status_estudiants_formaly') <span class="text-danger small d-block text-right">{{ $message }}</span> @enderror
        @error('type_ci') <span class="text-danger small d-block text-right">{{ $message }}</span> @enderror

    </div>

    <div class="d-flex justify-content-center mt-3">
        <button wire:click="goStep(2)" class="btn btn-primary mx-1">Siguiente</button>
    </div>

</div>
