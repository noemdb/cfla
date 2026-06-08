{{-- /home/nuser/code/s2526/resources/views/livewire/academico/diagnostic/partials/sessions.blade.php --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-2">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold text-primary">
                <i class="fas fa-users me-2"></i> Sesiones de Diagnóstico
            </h5>
            <div class="d-flex align-items-center">
                @if($filterDiagMainId)
                    <span class="badge bg-primary mr-2">
                        {{ $diagMainCurrent->name ?? 'Diagnóstico' }}
                    </span>
                @endif
                <input type="text" wire:model.debounce.500ms="search" 
                       class="form-control form-control-sm" placeholder="Buscar sesiones..."
                       style="width: 200px;">
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-3 py-2">Estudiante</th>
                        <th class="px-3 py-2">Diagnóstico</th>
                        <th class="px-3 py-2 text-center">Inicio</th>
                        <th class="px-3 py-2 text-center">Fin</th>
                        <th class="px-3 py-2 text-center">Duración</th>
                        <th class="px-3 py-2 text-center">Estado</th>
                        <th class="px-3 py-2 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Obtener sesiones recientes desde el controlador
                        $recentSessions = \App\Models\app\Instrument\DiagSession::with(['estudiant', 'diagMain'])
                            ->when($filterDiagMainId, function($query) {
                                return $query->where('diag_main_id', $filterDiagMainId);
                            })
                            ->when($search, function($query) {
                                return $query->whereHas('estudiant', function($q) {
                                    $q->where('name', 'like', "%{$search}%")
                                      ->orWhere('lastname', 'like', "%{$search}%");
                                });
                            })
                            ->orderBy('iniciado_at', 'desc')
                            ->limit(20)
                            ->get();
                    @endphp
                    
                    @forelse($recentSessions as $session)
                        <tr>
                            <td class="px-3 py-2">
                                <div class="font-weight-bold">{{ $session->estudiant->full_name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $session->estudiant->ci_estudiant ?? '' }}</small>
                            </td>
                            <td class="px-3 py-2">
                                {{ $session->diagMain->name ?? 'N/A' }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                {{ $session->iniciado_at ? \Carbon\Carbon::parse($session->iniciado_at)->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($session->completado_at)
                                    {{ \Carbon\Carbon::parse($session->completado_at)->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">En progreso</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($session->completado_at && $session->iniciado_at)
                                    @php
                                        $duration = \Carbon\Carbon::parse($session->iniciado_at)
                                                    ->diffInMinutes(\Carbon\Carbon::parse($session->completado_at));
                                    @endphp
                                    <span class="badge bg-info">{{ $duration }} min</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($session->completado_at)
                                    <span class="badge bg-success">Completada</span>
                                @else
                                    <span class="badge bg-warning">En progreso</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button class="btn btn-sm btn-outline-info"
                                        wire:click="showSessionDetails({{ $session->id }})"
                                        title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-users-slash fa-2x mb-2 opacity-50"></i>
                                <p class="mb-0">No se encontraron sesiones.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>