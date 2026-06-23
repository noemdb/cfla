<div>
    <nav style="font-size: 1.1rem !important">
        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
            <a wire:click="setIncident()" class="nav-item nav-link  {{ ($tabActive=='incidents') ? 'active':null }} " id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true"><span class=" font-weight-bold">Incidencias</span></a>
            <a wire:click="setAgreement()" class="nav-item nav-link {{ ($tabActive=='agreements') ? 'active':null }} " id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false"><span class=" font-weight-bold">Acuerdos</span></a>
            <a wire:click="setAction()" class="nav-item nav-link {{ ($tabActive=='actions') ? 'active':null }} " id="nav-actions-tab" data-toggle="tab" href="#nav-actions" role="tab" aria-controls="nav-actions" aria-selected="false"><span class=" font-weight-bold">Correctivos</span></a>
            <a wire:click="setInterview()" class="nav-item nav-link {{ ($tabActive=='interview') ? 'active':null }} " id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false"><span class=" font-weight-bold">Agenda</span></a>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade border border-top-0 rounded-bottom p-2 {{ ($tabActive=='incidents') ? 'show active':null }}" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
            <livewire:profesor.incident.main.index-component />
        </div>        
        <div class="tab-pane fade border border-top-0 rounded-bottom p-2 {{ ($tabActive=='agreements') ? 'show active':null }}" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
            <livewire:profesor.incident.agreement.index-component />
        </div>
        <div class="tab-pane fade border border-top-0 rounded-bottom p-2 {{ ($tabActive=='actions') ? 'show active':null }}" id="nav-actions" role="tabpanel" aria-labelledby="nav-actions-tab">
            <livewire:profesor.incident.action.index-component />
        </div>
        <div class="tab-pane fade border border-top-0 rounded-bottom p-2 {{ ($tabActive=='interview') ? 'show active':null }}" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
            <livewire:profesor.incident.interview.index-component />
        </div>
    </div>
</div>
