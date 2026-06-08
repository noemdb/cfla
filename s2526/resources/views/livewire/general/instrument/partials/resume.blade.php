<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h5>Información del Estudiante</h5>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center mb-4">
                @php $avatar_url = 'images/avatar/estudiant/female.png' @endphp
                <img src="{{ asset($avatar_url) }}" alt="Avatar del estudiante" class="rounded-circle me-3" width="80" height="80">
                {{-- <img class="card-img-top"
        src="{{ (isset($estudiant->logo)) ? asset($estudiant->logo) : asset('images/avatar/user_default.png') }}"
        alt="Card image cap"> --}}
                <div>
                    <p class="mb-1"><strong>Nombre:</strong> Carla Andreina</p>
                    <p class="mb-1"><strong>Apellido:</strong> Altuve Sierra</p>
                    <p class="mb-1"><strong>Cédula:</strong> 32019845</p>
                    <p class="mb-1"><strong>Inscripción:</strong> 3er Año</p>
                    <p class="mb-1"><strong>Representante:</strong> David Sierra</p>
                </div>
            </div>

            <h6>Rendimiento Académico</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Área de Formación</th>
                            <th>Nota Definitiva</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Biolgía</td>
                            <td>19</td>
                        </tr>
                        <tr>
                            <td>Matemática</td>
                            <td>18</td>
                        </tr>
                        <tr>
                            <td>Química</td>
                            <td>19</td>
                        </tr>
                        <tr>
                            <td>Física</td>
                            <td>18</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @include('livewire.general.instrument.partials.buttons')
        
    </div>
</div>
