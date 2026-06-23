<section>
    <h2>Información del Representante</h2>
    <div class="mb-3">
        <label for="representant_name" class="form-label">Nombre del representante:</label>
        <div class="alert alert-secondary p-2 form-control fw-bold">{{$catchment->representant_name}}</div>
    </div>

    <div class="mb-3">
        <label for="representant_lastname" class="form-label">Apellido del representante:</label>
        <div class="alert alert-secondary p-2 form-control fw-bold">{{$catchment->representant_lastname}}</div>
    </div>

    <div class="mb-3">
        <label for="representant_lastname" class="form-label">Apellido del representante:</label>
        <div class="alert alert-secondary p-2 form-control fw-bold">{{$catchment->representant_ci}}</div>
    </div>

    <div class="mb-3">
        <label for="relationship" class="form-label">Parentesco con el estudiante:</label>
        <div class="alert alert-secondary p-2 form-control fw-bold">{{$catchment->relationship}}</div>
    </div>

    <div class="mb-3">
        <label for="occupation" class="form-label">Fecha de Nacimiento:</label>
        <div class="alert alert-secondary p-2 form-control fw-bold">{{f_date($catchment->representant_date_birth)}}</div>
    </div>

    {{-- <div class="mb-3">
        <label for="occupation" class="form-label">Ocupación:</label>
        <div class="alert alert-secondary p-2 form-control fw-bold">{{$catchment->occupation}}</div>
    </div>

    <div class="mb-3">
        <label for="educational_level" class="form-label">Nivel educativo:</label>
        <div class="alert alert-secondary p-2 form-control fw-bold">{{$catchment->educational_level}}</div>
    </div> --}}

    <div class="mb-3">
        <label for="representant_phone" class="form-label">Teléfono:</label>
        <div class="alert alert-secondary p-2 form-control fw-bold">{{$catchment->representant_phone}}</div>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico:</label>
        <div class="alert alert-secondary p-2 form-control fw-bold">{{$catchment->email}}</div>
    </div>
</section>