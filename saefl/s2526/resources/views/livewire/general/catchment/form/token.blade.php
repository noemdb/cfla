<section>
    <h3>Mi información como representante:</h3>
    <div class="mb-3">
        <label for="representant_name" class="form-label">Nombre:</label>
        <input type="text" class="form-control" id="representant_name" name="representant_name" wire:model.defer="catchment.representant_name">
        @error('catchment.representant_name')<span class="text-danger small">{{ $message }}</span> @enderror
    </div>

    <div class="mb-3">
        <label for="representant_lastname" class="form-label">Apellido:</label>
        <input type="text" class="form-control" id="representant_lastname" name="representant_lastname" wire:model.defer="catchment.representant_lastname">
        @error('catchment.representant_lastname')<span class="text-danger small">{{ $message }}</span> @enderror
    </div>

    <div class="mb-3">
        <label for="representant_ci" class="form-label">Cédula de Identidad:</label>
        <input type="number" class="form-control" id="representant_ci" name="representant_ci" wire:model.defer="catchment.representant_ci">
        @error('catchment.representant_ci')<span class="text-danger small">{{ $message }}</span> @enderror
    </div>

    <div class="mb-3">
        <label for="relationship" class="form-label">Parentesco con el estudiante:</label>
        <input type="text" class="form-control" id="relationship" name="relationship" wire:model.defer="catchment.relationship">
        @error('catchment.relationship')<span class="text-danger small">{{ $message }}</span> @enderror
    </div>

    <div class="form-group pb-2">
        @php $name = 'representant_date_birth'; $model = 'catchment.' . $name;  @endphp
        <label for="{{$model}}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        <input type="date" id="{{$model}}" name="{{$model}}" class="form-control" wire:model.defer="{{$model}}">
        @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
    </div>

    {{-- <div class="mb-3">
        <label for="occupation" class="form-label">Ocupación:</label>
        <select class="form-select" id="occupation" name="occupation"  wire:model.defer="catchment.occupation">
            <option value="">Seleccionar</option>
            <option value="profesional">Profesional</option>
            <option value="estudiante">Estudiante</option>
            <option value="empleado">Empleado</option>
            <option value="empresario">Empresario</option>
            <option value="freelancer">Freelancer</option>
            <option value="otro">Otro</option>
        </select>
        @error('catchment.occupation')<span class="text-danger small">{{ $message }}</span> @enderror
    </div> --}}

    {{-- <div class="mb-3">
        <label for="educational_level" class="form-label">Nivel educativo:</label>
        <select class="form-select" id="educational_level" name="educational_level" wire:model.defer="catchment.educational_level" >
            <option value="">Seleccionar</option>
            <option value="primaria">Primaria</option>
            <option value="secundaria">Bachillerato</option>
            <option value="universidad">Universidad</option>
            <option value="posgrado">Posgrado</option>
            <option value="ninguno">Ninguno</option>
        </select>
        @error('catchment.representant_name')<span class="text-danger small">{{ $message }}</span> @enderror
    </div> --}}

    <div class="mb-3">
        <label for="representant_phone" class="form-label">Teléfono:</label>
        <input type="tel" class="form-control" id="representant_phone" name="representant_phone" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" placeholder="Formato: 123-456-7890"  wire:model.defer="catchment.representant_phone">
        @error('catchment.representant_phone')<span class="text-danger small">{{ $message }}</span> @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico:</label>
        <input type="email" class="form-control" id="email" name="email"  wire:model.defer="catchment.email">
        @error('catchment.email')<span class="text-danger small">{{ $message }}</span> @enderror
    </div>

    <button type="submit" class="btn btn-primary w-100" wire:click="save">Enviar</button>

    <div class="p-2">
        <small wire:loading.delay.shortest class="text-muted small px-2">
            Procesando...
        </small>
    </div>
    
</section> 