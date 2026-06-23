<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
        <h5 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-layer-group mr-2"></i> Diagnóstico por Secciones
        </h5>
        <div class="d-flex align-items-center">
            <div class="input-group input-group-sm mr-2" style="width: 250px;">
                <input type="text" wire:model.debounce.500ms="search" class="form-control"
                    placeholder="Buscar sección...">
                <div class="input-group-append">
                    <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                </div>
            </div>
            <button wire:click="resetFilters" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-sync-alt mr-1"></i> Reiniciar
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="py-3">Grado / Sección</th>
                        <th class="py-3 text-center">Inscritos</th>
                        <th class="py-3 text-center">Estado Informe</th>
                        <th class="py-3 text-right px-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sectionsPaginated as $section)
                        @php
                            $report = \App\Models\app\Instrument\SectionDiagnosticReport::where(
                                'section_id',
                                $section->id,
                            )
                                ->where('diagnostic_id', $filterDiagMainId)
                                ->first();
                        @endphp
                        <tr>
                            <td class="px-4 text-muted small">{{ $section->id }}</td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $section->grado->name ?? 'N/A' }}</div>
                                <div class="text-muted small">Sección {{ $section->name }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-light border px-2 py-1">
                                    <i class="fas fa-user-graduate mr-1 text-primary"></i>
                                    {{ $section->inscripcions->count() }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($report)
                                    <span class="badge badge-success px-3 py-2">
                                        <i class="fas fa-check-circle mr-1"></i> GENERADO
                                    </span>
                                    <div class="text-xs text-muted mt-1">
                                        {{ $report->generated_at ? $report->generated_at->diffForHumans() : '' }}
                                    </div>
                                @else
                                    <span class="badge badge-secondary px-3 py-2">
                                        <i class="fas fa-clock mr-1"></i> PENDIENTE
                                    </span>
                                @endif
                            </td>
                            <td class="text-right px-4">
                                <div class="btn-group btn-group-sm">
                                    <button wire:click="generateSectionReport({{ $section->id }})"
                                        wire:loading.attr="disabled" class="btn btn-primary"
                                        title="Generar / Actualizar Informe">
                                        <i class="fas fa-magic {{ $report ? 'fa-sync-alt' : '' }}"></i>
                                        {{ $report ? 'Actualizar' : 'Generar' }}
                                    </button>
                                    @if ($report)
                                        <button wire:click="viewSectionReport({{ $section->id }})" class="btn btn-info"
                                            title="Visualizar Informe">
                                            <i class="fas fa-eye"></i> Ver
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-muted">
                                <i class="fas fa-info-circle fa-2x mb-3 d-block"></i>
                                No se encontraron secciones que coincidan con los filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white pt-3">
        {{ $sectionsPaginated->links() }}
    </div>
</div>
