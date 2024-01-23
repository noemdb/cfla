<div class="mb-6">
    <div class="{{($step==1) ? 'block' : 'hidden'}}"
        id="tabs-home02" role="tabpanel" aria-labelledby="tabs-home-tab02">
        @include('livewire.app.enrollment.steper.estudiant')
    </div>
    <div class="{{($step==2) ? 'block' : 'hidden'}}"
        id="tabs-profile02" role="tabpanel" aria-labelledby="tabs-profile-tab02">
        @include('livewire.app.enrollment.steper.physical')
    </div>
    <div class="{{($step==3) ? 'block' : 'hidden'}}"
        id="tabs-messages02" role="tabpanel" aria-labelledby="tabs-profile-tab02">
        @include('livewire.app.enrollment.steper.representant')
    </div>
    <div class="{{($step==4) ? 'block' : 'hidden'}}"
        id="tabs-contact02" role="tabpanel" aria-labelledby="tabs-contact-tab02">
        @include('livewire.app.enrollment.steper.coexistence')
    </div>
    <div class="{{($step==5) ? 'block' : 'hidden'}}"
    id="tabs-contact02" role="tabpanel" aria-labelledby="tabs-contact-tab02">
    @include('livewire.app.enrollment.steper.potential')
</div>
</div>