<div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="text-start col-sm-6 col-md-3">
                    <label for="start_date" class="form-label">Fecha inicial</label>
                    <input type="date" wire:model="start_date" class="form-control" id="start_date">
                </div>
                <div class="text-start col-sm-6 col-md-3">
                    <label for="end_date" class="form-label">Fecha final</label>
                    <input type="date" wire:model="end_date" class="form-control" id="end_date">
                </div>
                <div class="text-start col-sm-6 col-md-3">
                    <label for="status" class="form-label">Estado</label>
                    <select wire:model="status" class="form-select" id="status">
                        <option value="">Todos los estados</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-start col-sm-6 col-md-3">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" wire:model="search" class="form-control" id="search" placeholder="Buscar en asunto, destinatario...">
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-2 cursor-pointer" wire:click="sortBy('created_at')" style="cursor: pointer;">
                                Fecha
                                @if($sortField === 'created_at')
                                    @if($sortDirection === 'asc')
                                        <i class="fas fa-sort-up ms-1"></i>
                                    @else
                                        <i class="fas fa-sort-down ms-1"></i>
                                    @endif
                                @endif
                            </th>
                            <th class="px-3 py-2">De</th>
                            <th class="px-3 py-2">Para</th>
                            <th class="px-3 py-2 cursor-pointer" wire:click="sortBy('subject')" style="cursor: pointer;">
                                Asunto
                                @if($sortField === 'subject')
                                    @if($sortDirection === 'asc')
                                        <i class="fas fa-sort-up ms-1"></i>
                                    @else
                                        <i class="fas fa-sort-down ms-1"></i>
                                    @endif
                                @endif
                            </th>
                            <th class="px-3 py-2 cursor-pointer" wire:click="sortBy('status')" style="cursor: pointer;">
                                Estado
                                @if($sortField === 'status')
                                    @if($sortDirection === 'asc')
                                        <i class="fas fa-sort-up ms-1"></i>
                                    @else
                                        <i class="fas fa-sort-down ms-1"></i>
                                    @endif
                                @endif
                            </th>
                            <th class="px-3 py-2 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($emails as $email)
                            <tr>
                                <td class="px-3 py-2">
                                    {{ $email->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $email->from }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $email->to }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $email->subject }}
                                </td>
                                <td class="px-3 py-2">
                                    <span class="badge bg-{{ $this->getStatusClass($email->status) }}">
                                        {{ $statuses[$email->status] ?? $email->status }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <button wire:click="showDetails({{ $email->id }})" class="btn btn-sm btn-link text-primary">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    No se encontraron emails
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                {{ $emails->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    @if($showDetails && $selectedEmail)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title">
                            <i class="fas fa-envelope me-2"></i>
                            Detalles del Email
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeDetails"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Asunto:</label>
                                    <p class="mb-0">{{ $selectedEmail->subject }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">De:</label>
                                    <p class="mb-0">{{ $selectedEmail->from }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Para:</label>
                                    <p class="mb-0">{{ $selectedEmail->to }}</p>
                                </div>
                                @if($selectedEmail->cc)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">CC:</label>
                                        <p class="mb-0">
                                            @if(is_array($selectedEmail->cc))
                                                {{ implode(', ', $selectedEmail->cc) }}
                                            @else
                                                {{ $selectedEmail->cc }}
                                            @endif
                                        </p>
                                    </div>
                                @endif
                                @if($selectedEmail->bcc)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">BCC:</label>
                                        <p class="mb-0">
                                            @if(is_array($selectedEmail->bcc))
                                                {{ implode(', ', $selectedEmail->bcc) }}
                                            @else
                                                {{ $selectedEmail->bcc }}
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Estado:</label>
                                    <p class="mb-0">
                                        <span class="badge bg-{{ $this->getStatusClass($selectedEmail->status) }}">
                                            {{ $statuses[$selectedEmail->status] ?? $selectedEmail->status }}
                                        </span>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">ID Resend:</label>
                                    <p class="mb-0">{{ $selectedEmail->resend_id }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Fecha de Creación:</label>
                                    <p class="mb-0">{{ $selectedEmail->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                                @if($selectedEmail->sent_at)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Enviado:</label>
                                        <p class="mb-0">{{ $selectedEmail->sent_at->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                @endif
                                @if($selectedEmail->delivered_at)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Entregado:</label>
                                        <p class="mb-0">{{ $selectedEmail->delivered_at->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Contenido:</label>
                                    <div class="border p-3 bg-light rounded">
                                        @if($selectedEmail->html)
                                            <div class="email-content">
                                                {!! $selectedEmail->html !!}
                                            </div>
                                        @else
                                            <pre class="mb-0">{{ $selectedEmail->text }}</pre>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($selectedEmail->events)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Eventos:</label>
                                        <div class="border p-3 bg-light rounded">
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Fecha</th>
                                                            <th>Tipo</th>
                                                            <th>Detalles</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($selectedEmail->events as $event)
                                                            <tr>
                                                                <td>{{ isset($event['timestamp']) ? \Carbon\Carbon::parse($event['timestamp'])->format('d/m/Y H:i:s') : '-' }}</td>
                                                                <td>{{ $event['type'] ?? '-' }}</td>
                                                                <td>
                                                                    @if(isset($event['details']))
                                                                        <pre class="mb-0">{{ json_encode($event['details'], JSON_PRETTY_PRINT) }}</pre>
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetails">
                            <i class="fas fa-times me-1"></i>
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
