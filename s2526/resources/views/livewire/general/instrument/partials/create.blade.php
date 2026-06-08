<div class="card p-2">
    <div class="card-header">
        <h5>7. Crea.</h5>
    </div>

    <!-- Pregunta 7.1 -->
    <div class="mb-4">
        <label class="form-label">7.1) Tienes alguna propuesta de proyecto a desarrollar este año en Ciencias Naturales</label>
        <textarea class="form-control" name="p7_1" rows="3" placeholder="Escribe tu propuesta..."></textarea>
    </div>

    <!-- Pregunta 7.2 -->
    <div class="mb-4">
        <label class="form-label">7.2) Te gustaría participar en encuentros, talleres y retos de ciencias.</label>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="p7_2" value="Si" required>
            <label class="form-check-label" for="p7_2">
                Si
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="p7_2" value="No">
            <label class="form-check-label" for="p7_2">
                No
            </label>
        </div>
    </div>

    <!-- Pregunta 7.3 -->
    <div class="mb-4">
        <label class="form-label">7.3) Tienes alguna propuesta de montaje o prototipo</label>
        <textarea class="form-control" name="p7_3" rows="3" placeholder="Escribe tu propuesta de montaje o prototipo..."></textarea>
    </div>

    <!-- Pregunta 7.4 -->
    <div class="mb-4">
        <label class="form-label">7.4) Qué te gustaría hacer en nuestros espacios de laboratorio junto a la guía del docente</label>
        <textarea class="form-control" name="p7_4" rows="3" placeholder="Describe lo que te gustaría hacer..."></textarea>
    </div>


    @include('livewire.general.instrument.partials.buttons')
    
</div>