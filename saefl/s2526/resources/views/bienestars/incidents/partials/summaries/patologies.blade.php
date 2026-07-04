<h4 class="alert mb-1 pb-1">Enfermedades y condiciones especiales (Distribuciones porcentuales)</h4>
<div class="card-deck">
    <div class="card">
        <div class="card-body">
        <h5 class="card-title text-left pb-0 mb-0">Enfermedades</h5>
        <div class="text-muted font-weight-bold small"> ¿Cuales son las enfermedades graves más frecuentes? </div>
        <p class="card-text">
            @include('bienestars.student_records.partials.summaries.diseases')
        </p>
        {{-- <p class="card-text text-right"><small class="text-muted">Last updated 3 mins ago</small></p> Special diseases and conditions --}}
        </div>
    </div>
    <div class="card">
        <div class="card-body">
        <h5 class="card-title text-left pb-0 mb-0">Condiciones</h5>
        <div class="text-muted font-weight-bold small"> ¿Condiciones especiales presentan los escolares más frecuentes? </div>
        <p class="card-text">
            @include('bienestars.student_records.partials.summaries.conditions')
        </p>
        {{-- <p class="card-text text-right"><small class="text-muted">Last updated 3 mins ago</small></p> Special diseases and conditions --}}
        </div>
    </div>

    <div class="card">
        <div class="card-body">
        <h5 class="card-title text-left pb-0 mb-0">Tratado por algún especialista</h5>
        <div class="text-muted font-weight-bold small"> ¿Cuantos escolares estan recibiendo atención por algún especialista? </div>
        <p class="card-text">
            @include('bienestars.student_records.partials.summaries.specialist')
        </p>
        {{-- <p class="card-text text-right"><small class="text-muted">Last updated 3 mins ago</small></p> Special diseases and conditions --}}
        </div>
    </div>
</div>

{{-- Padre y madre --}}
