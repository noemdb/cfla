<div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Log</h5>
                <button type="button" class="btn-close" wire:click="closeDetails"></button>
            </div>
            <div class="modal-body">
                @php
                    $log = collect($logs->items())->firstWhere('id', $selectedLog);
                @endphp
                @if($log)
                    <div class="mb-3">
                        <strong>Fecha:</strong>
                        <p>{{ \Carbon\Carbon::parse($log['date'])->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Nivel:</strong>
                        <p><span class="badge bg-{{ $this->getLogLevelClass($log['level']) }}">{{ strtoupper($log['level']) }}</span></p>
                    </div>
                    <div class="mb-3">
                        <strong>Canal:</strong>
                        <p>{{ $log['channel'] }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Mensaje:</strong>
                        <pre class="bg-light p-3 rounded">{{ $log['message'] }}</pre>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeDetails">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
