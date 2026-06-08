<div class="modal fade show" style="display: block;" tabindex="-1">
    <div class="modal-dialog" style="max-width: calc(100% - 2rem); margin: 1rem auto;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la Actividad</h5>
                <button type="button" class="btn-close" wire:click="closeDetails"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información General</h6>
                        <table class="table table-sm">
                            <tr>
                                <th class="w-25">ID:</th>
                                <td>{{ $selectedActivity->id }}</td>
                            </tr>
                            <tr>
                                <th>Fecha:</th>
                                <td>{{ $selectedActivity->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Usuario:</th>
                                <td>
                                    @if($selectedActivity->causer)
                                        {{ $selectedActivity->causer->name }}
                                    @else
                                        Sistema
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Acción:</th>
                                <td>{{ $selectedActivity->description }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Información Técnica</h6>
                        <table class="table table-sm">
                            <tr>
                                <th class="w-25">Ruta:</th>
                                <td>{{ $selectedActivity->properties->get('route') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Método:</th>
                                <td>{{ $selectedActivity->properties->get('method') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>IP:</th>
                                <td>{{ $this->getIpAddress($selectedActivity) }}</td>
                            </tr>
                            <tr>
                                <th>User Agent:</th>
                                <td>{{ $selectedActivity->properties->get('user_agent') ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mt-3">
                    <h6>Propiedades</h6>
                    <pre class="bg-light p-3 rounded">{{ json_encode($selectedActivity->properties, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeDetails">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
