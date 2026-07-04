<div>

    <div class="row mb-3">
        <div class="col-md-12">
            <form wire:submit.prevent="$refresh" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" wire:model.debounce.300ms="search" placeholder="Buscar por asunto o destinatario...">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" wire:model="dateFrom">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" wire:model="dateTo">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Asunto</th>
                    <th>Destinatario</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($emails as $email)
                    <tr>
                        <td>{{ $email->id }}</td>
                        <td>{{ $email->subject }}</td>
                        <td>{{ $email->to }}</td>
                        <td>{{ $email->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>
                            <span class="badge badge-{{ $email->status === 'delivered' ? 'success' : ($email->status === 'sent' ? 'primary' : ($email->status === 'scheduled' ? 'warning' : 'secondary')) }}">
                                {{ $email->status }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" wire:click="showEmailDetails({{ $email->id }})">
                                <i class="fa fa-info" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No se encontraron mensajes</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $emails->links() }}
    </div>

    <!-- Custom Modal -->
    <div class="custom-modal" id="emailDetailsModal" wire:ignore.self>
        <div class="custom-modal-dialog">
            <div class="custom-modal-content">
                <div class="custom-modal-header">
                    <h5 class="custom-modal-title">Detalles del Mensaje</h5>
                    <button type="button" class="custom-modal-close" wire:click="closeModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="custom-modal-body">
                    @if($selectedEmail)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>ID del Mensaje:</strong></label>
                                    <p>{{ $selectedEmail->id }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Estado:</strong></label>
                                    <p>
                                        <span class="badge badge-{{ $selectedEmail->status === 'delivered' ? 'success' : ($selectedEmail->status === 'sent' ? 'primary' : ($selectedEmail->status === 'scheduled' ? 'warning' : 'secondary')) }}">
                                            {{ $selectedEmail->status }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Asunto:</strong></label>
                                    <p>{{ $selectedEmail->subject }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Fecha de Envío:</strong></label>
                                    <p>{{ $selectedEmail->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Destinatario:</strong></label>
                                    <p>{{ $selectedEmail->to }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Remitente:</strong></label>
                                    <p>{{ $selectedEmail->from }}</p>
                                </div>
                            </div>
                        </div>

                        @if($selectedEmail->cc)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><strong>CC:</strong></label>
                                        <p>{{ is_array($selectedEmail->cc) ? implode(', ', $selectedEmail->cc) : $selectedEmail->cc }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($selectedEmail->bcc)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><strong>BCC:</strong></label>
                                        <p>{{ is_array($selectedEmail->bcc) ? implode(', ', $selectedEmail->bcc) : $selectedEmail->bcc }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><strong>Contenido:</strong></label>
                                    <div class="border p-3 bg-light">
                                        {!! $selectedEmail->html ?? 'No hay contenido disponible' !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="custom-modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

</div>

@section('stylesheet')
@parent
<link rel="stylesheet" href="{{ asset('css/modals.css') }}">
@endsection

@section('scripts')
@parent
<script>
    document.addEventListener('livewire:load', function () {
        let modal = document.getElementById('emailDetailsModal');

        Livewire.on('modal:show', () => {
            modal.classList.add('show');
        });

        Livewire.on('modal:hide', () => {
            modal.classList.remove('show');
        });

        // Cerrar el modal cuando se hace clic fuera de él
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                @this.closeModal();
            }
        });
    });
</script>
@endsection
