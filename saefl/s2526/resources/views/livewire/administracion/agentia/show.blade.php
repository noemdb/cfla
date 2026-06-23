<div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle Versión Prompt</h5>
                <button type="button" class="close" wire:click="closeViewModal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm small">
                        <tbody>
                            <tr>
                                <th class="w-25 bg-light">ID Global</th>
                                <td>{{ $prompt_id }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Tipo / Nombre</th>
                                <td>
                                    <span class="badge badge-{{ $prompt_type == 'system' ? 'dark' : 'info' }} mr-2">
                                        {{ ucfirst($prompt_type) }}
                                    </span>
                                    <span class="font-weight-bold">{{ $name }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Versión</th>
                                <td><span class="badge badge-light border">v{{ $version }}</span></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Estado</th>
                                <td>
                                    @if ($active)
                                        <span class="badge badge-success">Activo (En uso)</span>
                                    @else
                                        <span class="badge badge-secondary">Inactivo (Histórico)</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Descripción</th>
                                <td>{{ $description }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light" colspan="2">Contenido del Prompt</th>
                            </tr>
                            <tr>
                                <td colspan="2" class="p-0">
                                    <pre class="m-0 p-3 bg-light text-dark"
                                        style="white-space: pre-wrap; font-family: monospace; font-size: 0.85rem; border: none;">{{ $content }}</pre>
                                </td>
                            </tr>

                            <tr>
                                <th class="bg-light">Creado por</th>
                                <td>{{ $created_by?->username }}</td>
                            </tr>

                            <tr>
                                <th class="bg-light">Creado el</th>
                                <td>{{ \Carbon\Carbon::parse($created_at)->format('d/m/Y H:i') }}</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeViewModal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
