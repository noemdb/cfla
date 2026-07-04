<div class="card p-2">
    <div class="card-header">
        <h5>2. Identifica estructuras.</h5>
    </div>
    <div class="mb-4">
        <h6>2.1 Indica qué parte de la célula se señala en la imagen 01, 1 punto</h6>
        <img src="{{ asset('images/diagnostic/imagen01.png') }}" alt="Imagen 01" class="img-fluid mb-3">
        <div class="form-check">
            <input type="radio" id="p2_1_op1" name="p2_1" value="Vacuola" class="form-check-input" required>
            <label for="p2_1_op1" class="form-check-label">Vacuola</label>
        </div>
        <div class="form-check">
            <input type="radio" id="p2_1_op2" name="p2_1" value="Lisosoma" class="form-check-input">
            <label for="p2_1_op2" class="form-check-label">Lisosoma</label>
        </div>
        <div class="form-check">
            <input type="radio" id="p2_1_op3" name="p2_1" value="Mitocondria" class="form-check-input">
            <label for="p2_1_op3" class="form-check-label">Mitocondria</label>
        </div>
    </div>

    <!-- Pregunta 2.2 -->
    <div class="mb-4">
        <h6>2.2 Indica qué parte de la célula se señala en la imagen 02, 1 punto</h6>
        <img src="{{ asset('images/diagnostic/imagen02.png') }}" alt="Imagen 02" class="img-fluid mb-3">
        <div class="form-check">
            <input type="radio" id="p2_2_op1" name="p2_2" value="Núcleo" class="form-check-input" required>
            <label for="p2_2_op1" class="form-check-label">Núcleo</label>
        </div>
        <div class="form-check">
            <input type="radio" id="p2_2_op2" name="p2_2" value="Nucléolo" class="form-check-input">
            <label for="p2_2_op2" class="form-check-label">Nucléolo</label>
        </div>
        <div class="form-check">
            <input type="radio" id="p2_2_op3" name="p2_2" value="Membrana nuclear" class="form-check-input">
            <label for="p2_2_op3" class="form-check-label">Membrana nuclear</label>
        </div>
    </div>

    <!-- Pregunta 2.3 -->
    <div class="mb-4">
        <h6>2.3 Indica qué parte de la célula se señala en la imagen 03, 1 punto</h6>
        <img src="{{ asset('images/diagnostic/imagen03.png') }}" alt="Imagen 03" class="img-fluid mb-3">
        <div class="form-check">
            <input type="radio" id="p2_3_op1" name="p2_3" value="Retículo Endoplasmático" class="form-check-input" required>
            <label for="p2_3_op1" class="form-check-label">Retículo Endoplasmático</label>
        </div>
        <div class="form-check">
            <input type="radio" id="p2_3_op2" name="p2_3" value="Citoesqueleto" class="form-check-input">
            <label for="p2_3_op2" class="form-check-label">Citoesqueleto</label>
        </div>
        <div class="form-check">
            <input type="radio" id="p2_3_op3" name="p2_3" value="Aparato de Golgi" class="form-check-input">
            <label for="p2_3_op3" class="form-check-label">Aparato de Golgi</label>
        </div>
    </div>

    <!-- Pregunta 2.4 -->
    <div class="mb-4">
        <h6>2.4 Indica qué parte de la célula se señala en la imagen 04, 1 punto</h6>
        <img src="{{ asset('images/diagnostic/imagen04.png') }}" alt="Imagen 04" class="img-fluid mb-3">
        <div class="form-check">
            <input type="radio" id="p2_4_op1" name="p2_4" value="Citosol" class="form-check-input" required>
            <label for="p2_4_op1" class="form-check-label">Citosol</label>
        </div>
        <div class="form-check">
            <input type="radio" id="p2_4_op2" name="p2_4" value="Citoplasma" class="form-check-input">
            <label for="p2_4_op2" class="form-check-label">Citoplasma</label>
        </div>
        <div class="form-check">
            <input type="radio" id="p2_4_op3" name="p2_4" value="Núcleo" class="form-check-input">
            <label for="p2_4_op3" class="form-check-label">Núcleo</label>
        </div>
    </div>

    @include('livewire.general.instrument.partials.buttons')

</div>