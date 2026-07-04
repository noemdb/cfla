@extends('administracion.layouts.dashboard.app')

@section('title')
    SAEFL - Configuración de Baremos
@endsection

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">
        <div class="card card-primary mt-2">
            <div class="card-header p-0 m-0">
                <!-- ALERT INFORMATIVO -->
                <div class="card border-0 border-left-info p-1 mb-2 bg-white rounded" id="baremoInfoAlert"
                    style="border-left: 5px solid #17a2b8 !important;">
                    <div class="card-body p-1 alert-secondary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-start">
                                <div class="mr-3 text-info">
                                    <i class="fas fa-balance-scale fa-3x"></i>
                                </div>
                                <div>
                                    <h4 class="text-dark font-weight-bold mb-2">Gestión de Baremos</h4>
                                    <p class="text-muted mb-3 lead" style="font-size: 1rem;">
                                        Administre las <strong>escalas de calificación (baremos)</strong> para los planes
                                        educativos.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-2">
                <livewire:administracion.baremo-component />
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
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then((result) => {
                if (result.value) {
                    window.livewire.emit(e.detail.method, e.detail.id);
                }
            });
        });
    </script>
@endsection
