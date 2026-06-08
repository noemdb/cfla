{{-- resources/views/livewire/administracion/calendar-event/list-component.blade.php --}}
<div>

    <div class="card card-primary mt-2">

        <div class="card-header pb-2 mb-2 alert-secondary">
            {{-- INI Menu rapido --}}
            <div class="btn-group float-right pt-0 pb-2">
                <button wire:click="create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Evento
                </button>
            </div>
            {{-- FIN Menu rapido --}}
            <h4><u title="Listado especial con botones de acción">Listado</u> de <span class="font-weight-bolder">Eventos y días feriados</span> registrados</h4>
        </div>

        <!-- Lista de Eventos -->
        <div class="table-responsive p-2">
            <table class="table table-bordered table-hover table-sm">
                <thead class="thead-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Día feriado/Bancario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($calendar_events as $event)
                        <tr>
                            <td>{{ $event->formatted_date }}</td>
                            <td><i class="{{ $event->icon }} text-primary mr-3"></i> {{ $event->name }}</td>
                            <td>{{ Str::limit($event->description, 50) }}</td>
                            <td>
                                @if($event->status_holidays)
                                    <span class="badge badge-success">Sí</span>
                                @else
                                    <span class="badge badge-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                <button wire:click="show({{ $event->id }})" class="btn btn-info btn-sm" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                @if(!$event->status_delete)

                                    <button wire:click="edit({{ $event->id }})" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="alertConfirm({{ $event->id }})" class="btn btn-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @else

                                    <button class="btn btn-warning btn-sm" disabled title="No se puede editar">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="btn btn-danger btn-sm" disabled title="No se puede eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay eventos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-center">
            {{ $calendar_events->links() }}
        </div>

        <!-- Modal para Crear -->
        @if($modalCreate)
            <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5)">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Crear Nuevo Evento</h5>
                            <button type="button" class="close text-white" wire:click="closeModals">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form wire:submit.prevent="save">
                                @include('livewire.administracion.calendar-event.form')
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" wire:click="closeModals">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal para Editar -->
        @if($modalEdit)
            <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5)">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title">Editar Evento</h5>
                            <button type="button" class="close text-white" wire:click="closeModals">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form wire:submit.prevent="save">
                                @include('livewire.administracion.calendar-event.form')
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" wire:click="closeModals">Cancelar</button>
                                    <button type="submit" class="btn btn-warning">Actualizar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal para Mostrar -->
        @if($modalShow)
            <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5)">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">Detalles del Evento</h5>
                            <button type="button" class="close text-white" wire:click="closeModals">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    {{-- <strong>Icono:</strong> --}}
                                    <div class="d-flex align-items-center mt-1">
                                        <i class="{{ $calendar_event->icon }} fa-2x text-primary mr-3"></i>
                                        <div>
                                            <div><strong>{{ $calendar_event->icon_text }}</strong></div>
                                            <small class="text-muted">{{ $calendar_event->icon }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Fecha:</strong> {{ $calendar_event->formatted_date }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Día feriado/Bancario:</strong> 
                                    @if($calendar_event->status_holidays)
                                        <span class="badge badge-success">Sí</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <strong>Nombre:</strong> {{ $calendar_event->name }}
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <strong>Descripción:</strong> 
                                    <p>{{ $calendar_event->description }}</p>
                                </div>
                            </div>
                            @if($calendar_event->observations)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <strong>Observaciones:</strong> 
                                    <p>{{ $calendar_event->observations }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>