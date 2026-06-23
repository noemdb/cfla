<div>

    {{-- Header Section --}}
    <div class="card card-primary mt-2">
        <div class="card-header alert-secondary">
            <div class="btn-group float-right pt-2">
                <button type="button" class="btn btn-secondary btn-sm" wire:click="resetFilters">
                    <i class="fas fa-sync"></i> Limpiar Filtros
                </button>
            </div>
            <h4>
                <span title="Listado especial con botones de acción"><u>Gestión de Anulación</u></span>
                de <span class="font-weight-bolder">Pagos Registrados</span>
            </h4>
        </div>
        <div class="card-body p-2">
            {{-- Search and Filter Section --}}
            <div class="card-header p-0 m-0 mb-2">
                <div class="row">
                    {{-- Search Input --}}
                    <div class="col-md-4">
                        <input type="text"
                            class="form-control form-control-sm"
                            placeholder="Buscar estudiante, representante, cédula o concepto..."
                            wire:model.debounce.500ms="search">
                    </div>
                    {{-- Date Range --}}
                    <div class="col-md-3">
                        <input type="date"
                            class="form-control form-control-sm"
                            placeholder="Fecha Inicial"
                            wire:model="finicial">
                    </div>
                    <div class="col-md-3">
                        <input type="date"
                            class="form-control form-control-sm"
                            placeholder="Fecha Final"
                            wire:model="ffinal">
                    </div>
                    {{-- Status Filter --}}
                    <div class="col-md-2">
                        <select class="form-control form-control-sm" wire:model="cancellation_status">
                            <option value="">Todos los estados</option>
                            <option value="active">Activos</option>
                            <option value="cancellable">Anulables</option>
                            <option value="cancelled">Anulados</option>
                            <option value="pending_approval">Pendiente Aprobación</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Loading Indicator --}}
            <div wire:loading class="text-center py-3">
                <i class="fas fa-spinner fa-spin"></i> Cargando...
            </div>

            {{-- Table Section --}}
            <div class="table-responsive" wire:loading.remove>
                <table class="table table-striped table-hover table-sm small">
                    <thead>
                        <tr>
                            <th class="d-none d-sm-table-cell">N</th>
                            <th class="d-none d-sm-table-cell text-center">Idents.</th>
                            <th>Estudiante</th>
                            <th class="d-none d-lg-table-cell">Concepto Cobro</th>
                            <th>Estado</th>
                            <th class="text-nowrap">F.Registro</th>
                            <th class="text-nowrap">Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registroPagos as $index => $registropago)
                            @if($registropago->estudiant)
                                @php
                                    $estudiant = $registropago->estudiant;
                                    $representant = $registropago->representant;
                                    $statusInfo = $this->getPaymentStatus($registropago);
                                @endphp
                                <tr class="{{ $registropago->status_unexpired ? 'table-info' : '' }}">
                                    <td class="d-none d-sm-table-cell">
                                        {{ ($registroPagos->currentPage() - 1) * $registroPagos->perPage() + $index + 1 }}
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <div class="font-weight-bold text-nowrap">
                                            <span class="badge badge-primary">P{{ $registropago->id }}</span>
                                            <span class="badge badge-warning">C{{ $registropago->registro_pago_combinado_id }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-primary">
                                            {{ $estudiant->name ?? '' }} {{ $estudiant->lastname ?? '' }}
                                        </div>
                                        <small class="text-muted">
                                            Est: {{ $estudiant->ci_estudiant }} |
                                            Rep: {{ $estudiant->representant->ci_representant ?? '' }}
                                        </small>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        {{ $registropago->cuentaxpagar->name ?? '' }}
                                    </td>
                                    <td>
                                        <div class="text-muted">
                                            @if ($registropago->cancellable)
                                                <span class="badge badge-warning">Anulable</span>
                                                <span class="font-weight-bold">Autorizado por: {{$registropago->approval?->username}}</span><br>
                                                <small class="text-muted">Fecha: {{$registropago->approval_date}}</small>
                                                @if($registropago->justification_annulment)
                                                    <br><small class="text-info" title="{{ $registropago->justification_annulment }}">
                                                        <i class="fas fa-comment"></i> Con justificación
                                                    </small>
                                                @endif
                                            @else
                                                <span class="text-muted">No anulable</span>
                                            @endif
                                        </div>
                                        <div class="">
                                            @if (empty($registropago->deleted_at) )
                                                <span class="badge badge-success">Activo</span>
                                            @else
                                            <span class="badge badge-danger">Anulado</span>
                                            <div class="text-muted">
                                                <small>Fec. Anulación: {{$registropago->cancelled_at}}</small>
                                                <small>Anulado por: {{$registropago->cancellation?->username}}</small>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-nowrap">
                                        {{ $registropago->created_at->format('d-m-Y') }}
                                    </td>
                                    <td class="text-nowrap">
                                        {{ $registropago->user->username ?? '' }}
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            {{-- Details Button --}}
                                            <button title="Mostrar detalles"
                                                    class="btn btn-info btn-sm"
                                                    wire:click="showDetails({{ $registropago->id }})">
                                                <i class="fas fa-info"></i>
                                            </button>

                                            {{-- Mark as Cancellable Button --}}
                                            @if(!$registropago->cancellable)
                                                <button title="Marcar como anulable"
                                                        class="btn btn-warning btn-sm"
                                                        wire:click="setShowJustificationModal({{ $registropago->id }}, '{{ $estudiant->name }} {{ $estudiant->lastname }}', '{{ $registropago->cuentaxpagar->name }}')">
                                                    <i class="fas fa-unlock"></i>
                                                </button>
                                            @endif

                                            {{-- Remove Cancellable Button --}}
                                            @php $disabled = ($registropago->deleted_at) ? 'disabled' : null @endphp
                                            @if($registropago->cancellable)
                                                <button title="Quitar estado anulable" {{$disabled}}
                                                        class="btn btn-secondary btn-sm"
                                                        onclick="confirmRemoveCancellable({{ $registropago->id }}, '{{ $estudiant->name }} {{ $estudiant->lastname }}', '{{ $registropago->cuentaxpagar->name }}')">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            @endif

                                            {{-- Show Cancellable Status --}}
                                            @if($registropago->cancellable)
                                                <span class="btn btn-outline-danger btn-sm" title="Pago marcado como anulable">
                                                    <i class="fas fa-check"></i> Anulable
                                                </span>
                                            @endif

                                            <a title="Listado de Reg. de Pagos" class="btn btn-outline-success btn-sm" href="{{ route('administracion.registropagos.crud',['ci'=>$representant->ci_representant]) }}" role="button">
                                                <i class="{{ $icon_menus['registropagos'] }} fa-1x text-dark"></i>
                                            </a>

                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">No se encontraron registros de pago que coincidan con los filtros aplicados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($registroPagos->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $registroPagos->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Justification Modal --}}
    @if($showJustificationModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="fas fa-unlock mr-2"></i>
                            Marcar Pago como Anulable
                        </h5>
                        <button type="button" class="close" wire:click="closeJustificationModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6 class="font-weight-bold mb-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Información del Pago
                            </h6>
                            <p class="mb-1"><strong>Estudiante:</strong> {{ $justificationStudentName }}</p>
                            <p class="mb-0"><strong>Concepto:</strong> {{ $justificationConceptName }}</p>
                        </div>

                        <form wire:submit.prevent="markAsCancellable">
                            <div class="form-group">
                                <label for="justificationText" class="font-weight-bold">
                                    <i class="fas fa-comment mr-1"></i>
                                    Justificación de la Anulación <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    id="justificationText"
                                    class="form-control @error('justificationText') is-invalid @enderror"
                                    wire:model.defer="justificationText"
                                    rows="4"
                                    placeholder="Ingrese la justificación para marcar este pago como anulable (mínimo 10 caracteres, máximo 500)"
                                    maxlength="500"></textarea>

                                @error('justificationText')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <small class="form-text text-muted">
                                    <span wire:ignore>
                                        <span id="charCount">0</span>/500 caracteres
                                    </span>
                                </small>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Importante:</strong> Una vez marcado como anulable, este pago podrá ser anulado por los usuarios autorizados.
                                Asegúrese de que la justificación sea clara y detallada.
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeJustificationModal">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-warning" wire:click="markAsCancellable">
                            <i class="fas fa-unlock mr-1"></i> Marcar como Anulable
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    {{-- Details Modal --}}
    @if($showDetailsModal && $selectedPayment)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-info-circle mr-2"></i>
                            Detalles del Registro de Pago #{{ $selectedPayment->id }}
                        </h5>
                        <button type="button" class="close text-white" wire:click="closeDetailsModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Estudiante:</label>
                                    <p>{{ $selectedPayment->estudiant->name ?? '' }} {{ $selectedPayment->estudiant->lastname ?? '' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Cédula Estudiante:</label>
                                    <p>{{ $selectedPayment->estudiant->ci_estudiant }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Cédula Representante:</label>
                                    <p>{{ $selectedPayment->estudiant->representant->ci_representant ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Concepto:</label>
                                    <p>{{ $selectedPayment->cuentaxpagar->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Monto Pagado:</label>
                                    <p class="font-weight-bold text-success">
                                        ${{ number_format($selectedPayment->pagos->sum('exchange_ammount'), 2) }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Crédito Generado:</label>
                                    <p class="font-weight-bold text-info">
                                        ${{ number_format($selectedPayment->creditoafavor->exchange_ammount ?? 0, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($selectedPayment->cancellable)
                            <hr>
                            <div class="alert alert-warning">
                                <h6 class="font-weight-bold text-danger mb-2">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Información de Anulación
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Fecha de Anulación:</label>
                                            <p>{{ $selectedPayment->cancelled_at }}</p>
                                        </div>
                                    </div>
                                    @if($selectedPayment->approval_date)
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Fecha de Aprobación:</label>
                                                <p>{{ $selectedPayment->approval_date }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if($selectedPayment->justification_annulment)
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="font-weight-bold">
                                                    <i class="fas fa-comment mr-1"></i>
                                                    Justificación de Anulación:
                                                </label>
                                                <p class="alert-warning p-3 rounded border">{{ $selectedPayment->justification_annulment }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetailsModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    @section('sweetalert')
        @parent
        {{-- SweetAlert2 Scripts --}}
        <script>
            // Character counter for justification textarea
            document.addEventListener('livewire:load', function () {
                const textarea = document.getElementById('justificationText');
                const charCount = document.getElementById('charCount');

                if (textarea && charCount) {
                    textarea.addEventListener('input', function() {
                        charCount.textContent = this.value.length;
                    });
                }
            });

            // Show alert events
            window.addEventListener('show-alert', event => {
                const { type, message } = event.detail;
                Swal.fire({
                    title: type === 'success' ? '¡Éxito!' : (type === 'warning' ? '¡Atención!' : '¡Error!'),
                    text: message,
                    icon: type === 'success' ? 'success' : (type === 'warning' ? 'warning' : 'error'),
                    confirmButtonText: 'Entendido',
                    timer: type === 'success' ? 3000 : null,
                    timerProgressBar: type === 'success' ? true : false
                });
            });

            // Open URL events
            window.addEventListener('open-url', event => {
                window.open(event.detail.url, '_blank');
            });

            // Confirm remove cancellable
            function confirmRemoveCancellable(paymentId, studentName, conceptName) {
                Swal.fire({
                    title: '¿Quitar estado anulable?',
                    html: `¿Está seguro que desea quitar el estado anulable del pago de:<br>
                        <strong>${studentName}</strong><br>
                        Concepto: <strong>${conceptName}</strong>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#6c757d',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: '<i class="fas fa-lock mr-1"></i> Sí, quitar estado anulable',
                    cancelButtonText: '<i class="fas fa-times mr-1"></i> Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('removeCancellable', paymentId);
                    }
                });
            }

            // Loading overlay for actions
            window.addEventListener('show-loading', event => {
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Por favor espere mientras se procesa la solicitud',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            });

            window.addEventListener('hide-loading', event => {
                Swal.close();
            });
        </script>
    @endsection
</div>
