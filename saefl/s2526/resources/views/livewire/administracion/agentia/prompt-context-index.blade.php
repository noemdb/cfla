<div class="col-md-10">    

    <div class="card card-outline card-primary mt-2">
        <div class="card-header p-2 alert-secondary">

            <h4 class="card-title">Gestión de Prompts para el Diagnóstico Educativo</h4>

            {{-- 
            <div class="card-tools">
                <span class="badge badge-light border">Sección para la gestión de los prompts</span>
            </div> 
            --}}
            
        </div>

        @if (session()->has('message'))
            <div class="p-2">
                <div class="alert alert-success alert-dismissible fade show mb-1" role="alert">
                    {{ session('message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <hr class="m-0 p-0">
        @endif

        <div class="card-body p-2">

            {{-- INFO Politicas de activacion --}}
            <div class="alert alert-secondary my-1" id="infoPrompt">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle mr-2"></i>
                        Política Institucional de Activación de Prompts
                    </h6>
                    <button class="btn btn-sm btn-secondary" 
                            type="button" 
                            data-toggle="collapse" 
                            data-target="#collapseInfo" 
                            aria-expanded="false" 
                            aria-controls="collapseInfo">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse mt-3" id="collapseInfo">
                    <!-- Contenido detallado aquí -->
                    <div class="card card-outline card-info mt-2">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="text-primary">
                                        <i class="fas fa-shield-alt mr-2"></i>Principio Fundamental:
                                    </h6>
                                    <p class="mb-3">
                                        <strong>"Siempre debe existir una versión activa por contexto institucional"</strong>. 
                                        Esto garantiza que todo informe diagnóstico generado tenga un marco normativo y pedagógico definido.
                                    </p>

                                    <h6 class="text-primary">
                                        <i class="fas fa-exchange-alt mr-2"></i>Lógica de Activación Automática:
                                    </h6>
                                    <div class="pl-3">
                                        <p><strong>✅ Al ACTIVAR una versión inactiva:</strong></p>
                                        <ul>
                                            <li>La versión anteriormente activa se <span class="text-danger">desactiva automáticamente</span>.</li>
                                            <li>Los informes futuros utilizarán <strong>exclusivamente</strong> esta nueva versión.</li>
                                            <li>Los informes ya firmados <strong>no se ven afectados</strong> (mantienen su versión original).</li>
                                        </ul>

                                        <p><strong>⚠️ Al DESACTIVAR una versión activa:</strong></p>
                                        <ul>
                                            <li>El sistema busca <strong>automáticamente</strong> la última versión inactiva disponible.</li>
                                            <li>Si existe otra versión, se <span class="text-success">activa automáticamente</span>.</li>
                                            <li>Si NO existe otra versión, <strong>la operación se bloquea</strong> (no se puede quedar sin prompt activo).</li>
                                        </ul>
                                    </div>

                                    <h6 class="text-primary">
                                        <i class="fas fa-history mr-2"></i>Implicaciones para los Reportes:
                                    </h6>
                                    <div class="alert alert-warning p-2 mb-0">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <strong>Nota crítica:</strong> Cada informe diagnóstico guarda una referencia exacta al 
                                        <strong>ID y versión del prompt utilizado</strong>. Cambiar el prompt activo 
                                        <strong>NO afecta</strong> informes ya generados o firmados.
                                    </div>

                                    <hr class="my-3">

                                    <div class="small text-muted">
                                        <i class="fas fa-book mr-1"></i>
                                        <strong>Base normativa:</strong> Esta política implementa los principios de 
                                        <em>"Versionado Institucional"</em> y <em>"Trazabilidad Total"</em> establecidos 
                                        en el Roadmap Maestro (Fase 4.10).
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>           

        </div>
    </div>
    
    {{-- Modals --}}

    @if ($isOpen)
        @include('livewire.administracion.agentia.create')
    @endif

    @if ($isViewOpen)
        @include('livewire.administracion.agentia.show')
    @endif

    <div class="card card-outline card-secondary p-2 mt-2">
        <div class="card-header">
            {{-- Filstros --}}
            <div class="continer-fluid">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="search" class="small mb-1">Buscar</label>
                            <div class="input-group input-group-sm">
                                <input wire:model="search" type="text" id="search"
                                    class="form-control form-control-sm" placeholder="Nombre, descripción, versión...">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label for="filterType" class="small mb-1">Tipo de Prompt</label>
                            <select wire:model="filterType" id="filterType" class="form-control form-control-sm">
                                <option value="all">Todos</option>
                                <option value="system">System (Fijo)</option>
                                <option value="user">User (Dinámico)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5 text-right">
                        <button wire:click="create()" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nueva Versión
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0 table-responsive">

            {{-- tabla de datos --}}
            <ul class="nav nav-tabs my-2 nav-fill" id="promptsTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" role="tab">
                        <i class="fas fa-check-circle text-success mr-1"></i>
                        Activos
                        <span class="badge badge-success ml-1">{{ $prompts->where('active', true)->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="inactive-tab" data-toggle="tab" href="#inactive" role="tab">
                        <i class="fas fa-archive text-secondary mr-1"></i>
                        Históricos
                        <span class="badge badge-secondary ml-1">{{ $prompts->where('active', false)->count() }}</span>
                    </a>
                </li>
            </ul>

            {{-- CONTENIDO DE PESTAÑAS --}}
            <div class="tab-content" id="promptsTabContent">
                {{-- PESTAÑA ACTIVOS --}}
                <div class="tab-pane fade show active" id="active" role="tabpanel">
                    @php $activePrompts = $prompts->where('active', true); @endphp
                    
                    @if($activePrompts->count() > 0)
                        <div class="alert alert-success small mb-3">
                            <i class="fas fa-info-circle"></i>
                            Estos prompts están actualmente activos y se utilizan para generar nuevos informes diagnósticos.
                        </div>
                        
                        <table class="table table-bordered table-hover table-sm small">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">ID</th>
                                    <th width="100">Tipo</th>
                                    <th>Nombre</th>
                                    <th width="80">Ver.</th>
                                    <th>Descripción</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activePrompts as $prompt)
                                <tr class="table-success">
                                    <td class="text-center">{{ $prompt->id }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $prompt->prompt_type == 'system' ? 'dark' : 'info' }}">
                                            {{ ucfirst($prompt->prompt_type) }}
                                        </span>
                                    </td>
                                    <td class="font-weight-bold">
                                        <i class="fas fa-crown text-warning mr-1"></i>{{ $prompt->name }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light border">v{{ $prompt->version }}</span>
                                    </td>
                                    <td class="text-truncate" style="max-width: 200px;">{{ $prompt->description }}</td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button wire:click="show({{ $prompt->id }})" class="btn btn-info btn-xs">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button wire:click="clone({{ $prompt->id }})" class="btn btn-warning btn-xs">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button wire:click="toggleActive({{ $prompt->id }})" class="btn btn-secondary btn-xs">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-warning text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <h5>No hay prompts activos</h5>
                            <p class="mb-0">Es necesario tener al menos un prompt activo para generar informes.</p>
                            <button wire:click="create()" class="btn btn-primary btn-sm mt-2">
                                <i class="fas fa-plus"></i> Crear primer prompt
                            </button>
                        </div>
                    @endif
                </div>

                {{-- PESTAÑA HISTÓRICOS --}}
                <div class="tab-pane fade" id="inactive" role="tabpanel">
                    @php $inactivePrompts = $prompts->where('active', false); @endphp
                    
                    @if($inactivePrompts->count() > 0)
                        <div class="alert alert-secondary small mb-3">
                            <i class="fas fa-info-circle"></i>
                            Versiones históricas conservadas para trazabilidad. No se utilizan en informes nuevos.
                        </div>
                        
                        <table class="table table-bordered table-hover table-sm small">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">ID</th>
                                    <th width="100">Tipo</th>
                                    <th>Nombre</th>
                                    <th width="80">Ver.</th>
                                    <th>Creado el</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inactivePrompts as $prompt)
                                <tr class="text-muted">
                                    <td class="text-center">{{ $prompt->id }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $prompt->prompt_type == 'system' ? 'dark' : 'info' }}">
                                            {{ ucfirst($prompt->prompt_type) }}
                                        </span>
                                    </td>
                                    <td>{{ $prompt->name }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-light border">v{{ $prompt->version }}</span>
                                    </td>
                                    <td class="small">{{ $prompt->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button wire:click="show({{ $prompt->id }})" class="btn btn-info btn-xs">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button wire:click="clone({{ $prompt->id }})" class="btn btn-warning btn-xs">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button wire:click="toggleActive({{ $prompt->id }})" class="btn btn-success btn-xs">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            {{-- 
                                            <button wire:click="delete({{ $prompt->id }})" class="btn btn-danger btn-xs"
                                                onclick="confirm('¿Eliminar esta versión histórica?')">
                                                <i class="fas fa-trash"></i>
                                            </button> 
                                            --}}
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-info text-center py-4">
                            <i class="fas fa-history fa-2x mb-3"></i>
                            <h5>No hay versiones históricas</h5>
                            <p class="mb-0">Todas las versiones creadas están activas.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
        @if ($prompts->hasPages())
            <div class="card-footer clearfix p-2">
                <div class="float-right">
                    {{ $prompts->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Indicador de carga global para cualquier acción -->
    <div wire:loading.flex 
         style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
        <div class="alert alert-light shadow-lg">
            <div class="flex items-center font-weight-bold text-dark">
                <div class="spinner-border spinner-border-sm mr-2"></div>
                <span>Procesando...</span>
            </div>
        </div>
    </div>

</div>
