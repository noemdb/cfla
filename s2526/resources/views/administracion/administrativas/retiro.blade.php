@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-danger">

                <h4>
                    <i class="{{ $icon_menus['retiro'] }} fa-1x"></i>
                    Retiro administrativo de <span class="font-weight-bolder">Estudiantes</span>
                </h4>
            </div>

            <div class="card-body">

                <div class="">
                    @livewire('administracion.administrativa.asistente.retiro-component')
                </div>

            </div>
        </div>
    </main>
@endsection

@section('sweetalert')
    @parent
    <script>
        window.addEventListener('swal', function(e) {
            Swal.fire(e.detail);
        });

        window.addEventListener('swal:confirm', function(e) {
            Swal.fire({
                title: e.detail.message,
                text: e.detail.text,
                icon: e.detail.type,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('remove', e.detail.id);
                }
            });
        });

        window.addEventListener('swal:question', function(e) {
            Swal.fire({
                title: e.detail.message,
                text: e.detail.text,
                icon: e.detail.type,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit(e.detail.method, e.detail.id);
                }
            });
        });

        // Nuevo evento para retiro con observaciones
        window.addEventListener('swal:retiroWithObservations', function(e) {
            // Este evento ahora es manejado en el script del componente
            console.log('Evento swal:retiroWithObservations recibido');
        });
    </script>
@endsection


    {{-- Scripts para manejar el modal de observaciones --}}
    @section('scripts')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Escuchar el evento personalizado para retiro con observaciones
            window.addEventListener('swal:retiroWithObservations', function(e) {
                Swal.fire({
                    title: e.detail.message,
                    html: `
                        <p>${e.detail.text}</p>
                        <hr>
                        <div class="form-group text-left">
                            <label for="observations" class="font-weight-bold">
                                Observaciones Obligatorias <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                id="observations" 
                                class="form-control" 
                                rows="4" 
                                placeholder="Ingrese el razonamiento y justificación del retiro administrativo (mínimo 10 caracteres)..."
                                maxlength="500"
                            ></textarea>
                            <small class="form-text text-muted">
                                Mínimo 10 caracteres, máximo 500 caracteres. 
                                <span id="charCount" class="font-weight-bold">0/500</span>
                            </small>
                            <div id="error-message" class="text-danger small mt-1" style="display: none;"></div>
                        </div>
                    `,
                    icon: e.detail.type,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Confirmar Retiro',
                    cancelButtonText: 'Cancelar',
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    },
                    preConfirm: () => {
                        const observations = document.getElementById('observations').value.trim();
                        const errorElement = document.getElementById('error-message');
                        
                        // Validaciones
                        if (!observations) {
                            errorElement.textContent = 'Las observaciones son obligatorias.';
                            errorElement.style.display = 'block';
                            return false;
                        }
                        
                        if (observations.length < 10) {
                            errorElement.textContent = 'Las observaciones deben tener al menos 10 caracteres.';
                            errorElement.style.display = 'block';
                            return false;
                        }
                        
                        if (observations.length > 500) {
                            errorElement.textContent = 'Las observaciones no pueden exceder los 500 caracteres.';
                            errorElement.style.display = 'block';
                            return false;
                        }
                        
                        return observations;
                    },
                    didOpen: () => {
                        const textarea = document.getElementById('observations');
                        const charCount = document.getElementById('charCount');
                        const errorElement = document.getElementById('error-message');
                        
                        // Contador de caracteres
                        textarea.addEventListener('input', function() {
                            const length = this.value.length;
                            charCount.textContent = `${length}/500`;
                            
                            if (length >= 10 && length <= 500) {
                                charCount.className = 'font-weight-bold text-success';
                            } else {
                                charCount.className = 'font-weight-bold text-danger';
                            }
                            
                            // Ocultar error mientras escribe
                            if (errorElement.style.display === 'block') {
                                errorElement.style.display = 'none';
                            }
                        });
                        
                        // Enfocar el textarea
                        textarea.focus();
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        // Enviar observaciones al componente Livewire
                        window.livewire.emit('procesarRetiroWithObservations', e.detail.id, result.value);
                    }
                });
            });

            // Escuchar el evento para resetear observaciones
            window.addEventListener('resetObservations', function() {
                if (window.livewire) {
                    window.livewire.emit('resetObservations');
                }
            });
        });
    </script>
    @endsection