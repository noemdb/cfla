@if($showModal)
<div class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog {{ in_array($modalType, ['view', 'review', 'summary', 'strategy']) ? 'modal-xl' : 'modal-lg' }} modal-dialog-scrollable" role="document">
        <div class="modal-content" style="max-height: 90vh;">
            <div class="modal-header bg-primary text-white sticky-top">
                <h5 class="modal-title">
                    @switch($modalType)
                        @case('create')
                            <i class="fas fa-plus mr-2"></i>Nuevo Proyecto de Aula
                            @break
                        @case('edit')
                            <i class="fas fa-edit mr-2"></i>Editar Proyecto de Aula
                            @break
                        @case('view')
                            <i class="fas fa-eye mr-2"></i>Detalles del Proyecto
                            @break
                        @case('review')
                            <i class="fas fa-search mr-2"></i>Gestionar Revisión
                            @break
                        @case('summary')
                            <i class="fas fa-list mr-2"></i>Gestionar Resumen
                            @break
                        @case('strategy')
                            <i class="fas fa-users mr-2"></i>Gestionar Estrategias
                            @break
                        @case('edit-review')
                            <i class="fas fa-edit mr-2"></i>Editar Revisión
                            @break
                        @case('edit-summary')
                            <i class="fas fa-edit mr-2"></i>Editar Resumen
                            @break
                        @case('edit-strategy')
                            <i class="fas fa-edit mr-2"></i>Editar Estrategia
                            @break
                    @endswitch
                </h5>
                <button type="button" class="close text-white" wire:click="closeModal">
                    <span>&times;</span>
                </button>
            </div>
            
            <div class="modal-body" style="max-height: calc(90vh - 120px); overflow-y: auto;">
                @switch($modalType)
                    @case('create')
                    @case('edit')
                        @include('livewire.inicial.modal.eiprojectk.project-form')
                        @break
                    @case('view')
                        @include('livewire.inicial.modal.eiprojectk.project-details')
                        @break
                    @case('review')
                        @include('livewire.inicial.modal.eiprojectk.review-manager')
                        @break
                    @case('summary')
                        @include('livewire.inicial.modal.eiprojectk.summary-manager')
                        @break
                    @case('strategy')
                        @include('livewire.inicial.modal.eiprojectk.strategy-form')
                        @break
                    @case('edit-review')
                        @include('livewire.inicial.modal.eiprojectk.review-form')
                        @break
                    @case('edit-summary')
                        @include('livewire.inicial.modal.eiprojectk.summary-form')
                        @break
                    @case('edit-strategy')
                        @include('livewire.inicial.modal.eiprojectk.strategy-form')
                        @break
                @endswitch
            </div>
        </div>
    </div>
</div>

@section('stylesheet')
@parent
<style>
    .modal-dialog-scrollable {
        height: calc(100% - 1rem);
    }
    
    .modal-dialog-scrollable .modal-content {
        max-height: calc(100vh - 1rem);
        overflow: hidden;
    }
    
    .modal-dialog-scrollable .modal-body {
        overflow-y: auto;
    }
    
    .modal-header.sticky-top {
        z-index: 1020;
        background-color: #007bff !important;
        border-bottom: 1px solid #dee2e6;
    }
    
    .modal-body::-webkit-scrollbar {
        width: 8px;
    }
    
    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .modal-body::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    
    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    @media (max-width: 576px) {
        .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }
        
        .modal-content {
            max-height: calc(100vh - 1rem);
        }
        
        .modal-body {
            max-height: calc(100vh - 120px);
            padding: 1rem 0.75rem;
        }
    }
    
    @media (max-width: 768px) {
        .modal-xl {
            max-width: 95%;
        }
    }
</style>
@endsection
@endif