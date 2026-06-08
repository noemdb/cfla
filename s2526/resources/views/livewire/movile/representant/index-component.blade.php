<div>

    @php
        $estudiants = ($representant) ? $representant->estudiants_formaly : collect();
        $catchments = ($representant) ? $representant->catchments : collect();
        $late_payment = ($representant) ? $representant->late_payment : null;
        $late_index = round(100 * $late_payment, 1);
        $meet_payment = ($representant) ? $representant->meet_payment : null;
        $meet_index = round(100 * $meet_payment, 1);
        $expire_bill_pendientes = ($representant) ? $representant->expire_bill_pendientes : null;
        $exchange_ammount_expire_bill = ($representant) ? round($representant->exchange_ammount_expire_bill, 2) : null;
        $ammount_expire_bill_exchange = $exchange_rate_current ? $exchange_rate_current->ammount * $exchange_ammount_expire_bill : null;
        $ammount_expire_bill_exchange = round($ammount_expire_bill_exchange, 2);
        $registropagos = ($representant) ? $representant->getRegistroPagos() : collect();
        $announcements = ($representant) ? $representant->announcements : collect();
    @endphp

    @if ($estudiants->isNotEmpty() || $catchments->isNotEmpty())
        <nav>
            <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
                <button title="Detalles" wire:click="$set('tab', 'details')" class="nav-link p-1 @if($tab == 'details') active @endif" id="nav-details-tab" type="button" role="tab" aria-controls="nav-details" aria-selected="{{ $tab == 'details' ? 'true' : 'false' }}">
                    <i class="fas fa-info"></i>
                </button>
                <button title="Estudiante" wire:click="$set('tab', 'estudiants')" class="nav-link p-1 @if($tab == 'estudiants') active @endif" id="nav-estudiants-tab" type="button" role="tab" aria-controls="nav-estudiants" aria-selected="{{ $tab == 'estudiants' ? 'true' : 'false' }}">
                    <i class="{{ $icon_menus['estudiante'] ?? '' }}"></i>
                </button>
                <button title="Pagos" wire:click="$set('tab', 'payments')" class="nav-link p-1 @if($tab == 'payments') active @endif" id="nav-payments-tab" type="button" role="tab" aria-controls="nav-payments" aria-selected="{{ $tab == 'payments' ? 'true' : 'false' }}">
                    <i class="{{ $icon_menus['registro_pagos'] ?? '' }}"></i>
                </button>
                <button title="Competiciones" wire:click="$set('tab', 'competitions')" class="nav-link p-1 @if($tab == 'competitions') active @endif" id="nav-competitions-tab" type="button" role="tab" aria-controls="nav-competitions" aria-selected="{{ $tab == 'competitions' ? 'true' : 'false' }}">
                    <i class="{{ $icon_menus['competitions'] ?? '' }}"></i>
                </button>
            </div>
        </nav>
        <div class="tab-content border border-top-0 bg-white" id="nav-tabContent">
            <div class="tab-pane fade @if($tab == 'details') show active @endif p-2" id="nav-details" role="tabpanel" aria-labelledby="nav-details-tab" tabindex="0">
                @include('movile.android.module.representant.card')
            </div>
            <div class="tab-pane fade @if($tab == 'estudiants') show active @endif p-2" id="nav-estudiants" role="tabpanel" aria-labelledby="nav-estudiants-tab" tabindex="0">
                @include('movile.android.module.representant.estudiants.main')
            </div>
            <div class="tab-pane fade @if($tab == 'payments') show active @endif p-2" id="nav-payments" role="tabpanel" aria-labelledby="nav-payments-tab" tabindex="0">
                @include('movile.android.module.representant.payments.main')
            </div>
            <div class="tab-pane fade @if($tab == 'competitions') show active @endif p-2" id="nav-competitions" role="tabpanel" aria-labelledby="nav-competitions-tab" tabindex="0">

                @if ($exchange_ammount_expire_bill <= 0)
                    {{-- {{$user}} --}}
                    <nav>
                        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-index-tab" data-bs-toggle="tab" data-bs-target="#nav-index" type="button" role="tab" aria-controls="nav-index" aria-selected="true">
                                Debates
                            </button>
                            <button class="nav-link" id="nav-result-tab" data-bs-toggle="tab" data-bs-target="#nav-result" type="button" role="tab" aria-controls="nav-result" aria-selected="false">
                                Resultados
                            </button>
                        </div>
                    </nav>
                    <div class="tab-content border border-top-0 bg-white" id="nav-tabContent">
                        <div class="tab-pane fade show active p-1" id="nav-index" role="tabpanel" aria-labelledby="nav-index-tab" tabindex="0">
                            {{-- Contenido de la pestaña Index --}}
                            <livewire:movile.competition.index-component />
                        </div>
                        <div class="tab-pane fade p-1" id="nav-result" role="tabpanel" aria-labelledby="nav-result-tab" tabindex="0">
                            {{-- Contenido de la pestaña Result --}}
                            <livewire:movile.competition.result-component /> 
                        </div>
                    </div>
                @else
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div>
                            <strong>No tienes acceso a esta sección.</strong> Por favor, contacta con la administración de la institución sí crees que esto es un error.
                        </div>
                    </div>
                @endif 
                                

            </div>
        </div>
    @else
        <div class="alert alert-light">
            Actualmente no tiene estudiantes inscritos.
        </div>
    @endif

</div>
