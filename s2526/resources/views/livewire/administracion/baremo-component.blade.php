<div class="row px-2">
    <!-- Filters and Add Button -->
    <div class="col-md-12 mb-3">
        <div class="d-flex justify-content-between">
            <div class="form-group mb-0 w-25">
                <input type="text" wire:model="search" class="form-control form-control-sm"
                    placeholder="Buscar por valoración...">
            </div>
            <div>
                <button wire:click="create" class="btn btn-sm btn-primary shadow-sm rounded-pill px-3">
                    <i class="fas fa-plus mr-1"></i> Nuevo Baremo
                </button>
            </div>
        </div>
    </div>

    @if ($activePestudios->isEmpty())
        <div class="col-12">
            <div class="alert alert-info text-center border-0 shadow-sm">
                <i class="fas fa-info-circle fa-2x mb-2"></i><br>
                No se encontraron baremos registrados.
            </div>
        </div>
    @else
        <!-- Nav pills for Pestudios -->
        <div class="col-12 mb-4">
            <ul class="nav nav-tabs card-header-tabs nav-fill" id="pestudioTabs" role="tablist">
                @foreach ($activePestudios as $pestudio)
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold {{ $activePestudio == $pestudio->id ? 'active' : '' }}"
                            wire:click="setActivePestudio({{ $pestudio->id }})" id="pestudio-tab-{{ $pestudio->id }}"
                            data-toggle="pill" href="#pestudio-{{ $pestudio->id }}" role="tab"
                            aria-controls="pestudio-{{ $pestudio->id }}"
                            aria-selected="{{ $activePestudio == $pestudio->id ? 'true' : 'false' }}">
                            <i class="fas fa-graduation-cap min-w-20 mr-1"></i> {{ $pestudio->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="col-12">
            <div class="tab-content" id="pestudioTabsContent">
                @foreach ($activePestudios as $pestudio)
                    @php
                        $pestudioBaremos = $grouped_baremos->get($pestudio->id, collect());
                        $lapsoKeys = $pestudioBaremos->keys();
                    @endphp
                    <div class="tab-pane fade {{ $activePestudio == $pestudio->id ? 'show active' : '' }}"
                        id="pestudio-{{ $pestudio->id }}" role="tabpanel"
                        aria-labelledby="pestudio-tab-{{ $pestudio->id }}" wire:ignore.self>

                        <!-- Tabs content for lapsos inside Pestudio -->
                        <div class="card border-0 mb-4 border-secondary">
                            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                                <ul class="nav nav-tabs card-header-tabs nav-fill" id="lapsoTabs-{{ $pestudio->id }}"
                                    role="tablist">
                                    @foreach ($lapsoKeys as $lapsoKey)
                                        @php
                                            $lapsoName =
                                                $lapsoKey === 'general'
                                                    ? 'General (Aplica a todos los lapsos)'
                                                    : $lapsos->firstWhere('id', $lapsoKey)->name ??
                                                        'Lapso ' . $lapsoKey;
                                            $isLapsoActive =
                                                isset($activeLapso[$pestudio->id]) &&
                                                $activeLapso[$pestudio->id] == $lapsoKey;
                                        @endphp
                                        <li class="nav-item">
                                            <a class="nav-link text-dark font-weight-bold {{ $isLapsoActive ? 'active' : '' }}"
                                                wire:click="setActiveLapso({{ $pestudio->id }}, '{{ $lapsoKey }}')"
                                                id="lapso-tab-{{ $pestudio->id }}-{{ $lapsoKey }}"
                                                data-toggle="tab"
                                                href="#lapso-content-{{ $pestudio->id }}-{{ $lapsoKey }}"
                                                role="tab"
                                                aria-controls="lapso-content-{{ $pestudio->id }}-{{ $lapsoKey }}"
                                                aria-selected="{{ $isLapsoActive ? 'true' : 'false' }}">
                                                <i class="fas fa-calendar-alt text-primary mr-1"></i>
                                                {{ $lapsoName }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="card-body bg-light">
                                <div class="tab-content" id="lapsoTabsContent-{{ $pestudio->id }}">
                                    @foreach ($lapsoKeys as $lapsoKey)
                                        @php
                                            $isLapsoActive =
                                                isset($activeLapso[$pestudio->id]) &&
                                                $activeLapso[$pestudio->id] == $lapsoKey;
                                        @endphp
                                        <div class="tab-pane fade {{ $isLapsoActive ? 'show active' : '' }}"
                                            id="lapso-content-{{ $pestudio->id }}-{{ $lapsoKey }}" role="tabpanel"
                                            aria-labelledby="lapso-tab-{{ $pestudio->id }}-{{ $lapsoKey }}"
                                            wire:ignore.self>

                                            <div class="row">
                                                @foreach ($pestudioBaremos->get($lapsoKey) as $baremo)
                                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                                        <div class="card h-100 border-0 shadow-sm rounded-lg"
                                                            style="transition: transform 0.2s; border-left: 4px solid #4e73df !important;">
                                                            <div class="card-body p-3 d-flex flex-column">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-start mb-2">
                                                                    <h5
                                                                        class="card-title font-weight-bold text-primary mb-0">
                                                                        {{ $baremo->valoracion }}
                                                                    </h5>
                                                                    <div class="dropdown no-arrow">
                                                                        <a class="dropdown-toggle text-muted"
                                                                            href="#" role="button"
                                                                            id="dropdownMenuLink{{ $baremo->id }}"
                                                                            data-toggle="dropdown" aria-haspopup="true"
                                                                            aria-expanded="false">
                                                                            <i
                                                                                class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                                                        </a>
                                                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                                                            aria-labelledby="dropdownMenuLink{{ $baremo->id }}">
                                                                            <a class="dropdown-item" href="#"
                                                                                wire:click.prevent="edit({{ $baremo->id }})">
                                                                                <i
                                                                                    class="fas fa-edit fa-sm fa-fw mr-2 text-info"></i>
                                                                                Editar
                                                                            </a>
                                                                            <div class="dropdown-divider"></div>
                                                                            <a class="dropdown-item text-danger"
                                                                                href="#"
                                                                                wire:click.prevent="delete({{ $baremo->id }})">
                                                                                <i
                                                                                    class="fas fa-trash fa-sm fa-fw mr-2 text-danger"></i>
                                                                                Eliminar
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-2">
                                                                    <span
                                                                        class="badge badge-light border d-block text-left mb-1 py-1 px-2">
                                                                        <i
                                                                            class="fas fa-book-open text-secondary w-15px mr-1"></i>
                                                                        Pensum:
                                                                        {{ $baremo->pensum ? $baremo->pensum->fullname ?? $baremo->pensum->name : 'General' }}
                                                                    </span>
                                                                    <div
                                                                        class="d-flex justify-content-between bg-white rounded border py-1 px-2 mb-1">
                                                                        <span
                                                                            class="small text-muted font-weight-bold">Mínimo:
                                                                            <span
                                                                                class="text-dark">{{ $baremo->minimo }}</span></span>
                                                                        <span
                                                                            class="small text-muted font-weight-bold">Máximo:
                                                                            <span
                                                                                class="text-dark">{{ $baremo->maxima }}</span></span>
                                                                    </div>
                                                                </div>

                                                                <p class="card-text text-muted small mt-auto mb-0"
                                                                    style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                                    {{ $baremo->description ?: 'Sin descripción adicional.' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Modal Form (same as before) -->
    @if ($isModalOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold">
                            <i class="fas {{ $baremo_id ? 'fa-edit' : 'fa-plus-circle' }} mr-2"></i>
                            {{ $baremo_id ? 'Editar Baremo' : 'Nuevo Baremo' }}
                        </h5>
                        <button type="button" class="close text-white" wire:click="closeModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-dark">Plan Educativo <span
                                                class="text-danger">*</span></label>
                                        <select wire:model.defer="pestudio_id" class="form-control">
                                            <option value="">Seleccione...</option>
                                            @foreach ($pestudios as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('pestudio_id')
                                            <span class="text-danger small font-weight-bold">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-dark">Lapso (Opcional)</label>
                                        <select wire:model.defer="lapso_id" class="form-control">
                                            <option value="">General (Aplica a todos los lapsos)</option>
                                            @foreach ($lapsos as $l)
                                                <option value="{{ $l->id }}">{{ $l->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('lapso_id')
                                            <span class="text-danger small font-weight-bold">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-dark">Pensum (Opcional)</label>
                                        <select wire:model.defer="pensum_id" class="form-control">
                                            <option value="">General (Aplica a todos los pensums)</option>
                                            @foreach ($pensums as $p)
                                                <option value="{{ $p->id }}">
                                                    {{ $p->fullname ?? ($p->name ?? 'Pensum ' . $p->id) }}</option>
                                            @endforeach
                                        </select>
                                        @error('pensum_id')
                                            <span class="text-danger small font-weight-bold">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-dark">Valoración <span
                                                class="text-danger">*</span></label>
                                        <input type="text" wire:model.defer="valoracion" class="form-control"
                                            placeholder="Ej: A, B, Excelente">
                                        @error('valoracion')
                                            <span class="text-danger small font-weight-bold">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-dark">Mínimo <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" wire:model.defer="minimo"
                                            class="form-control">
                                        @error('minimo')
                                            <span class="text-danger small font-weight-bold">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-dark">Máximo <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" wire:model.defer="maxima"
                                            class="form-control">
                                        @error('maxima')
                                            <span class="text-danger small font-weight-bold">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 form-group mb-0">
                                        <label class="font-weight-bold text-dark">Descripción</label>
                                        <textarea wire:model.defer="description" class="form-control" rows="3"
                                            placeholder="Detalles o retroalimentación sugerida para esta valoración..."></textarea>
                                        @error('description')
                                            <span class="text-danger small font-weight-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white">
                        <button type="button" class="btn btn-outline-secondary font-weight-bold px-4 rounded-pill"
                            wire:click="closeModal">Cancelar</button>
                        <button type="button" class="btn btn-primary font-weight-bold px-4 rounded-pill shadow-sm"
                            wire:click="save">
                            <i class="fas fa-save mr-1"></i> Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <style>
        .w-15px {
            width: 15px;
            text-align: center;
        }

        .min-w-20 {
            min-width: 20px;
            text-align: center;
        }
    </style>
</div>
