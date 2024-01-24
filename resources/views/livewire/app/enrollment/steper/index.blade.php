<div class="mb-6">
    <div class="{{($step==1) ? 'block' : 'hidden'}}" id="tabs-estudiant">
        @include('livewire.app.enrollment.steper.estudiant')
    </div>
    <div class="{{($step==2) ? 'block' : 'hidden'}}" id="physical">
        @include('livewire.app.enrollment.steper.physical')
    </div>
    <div class="{{($step==3) ? 'block' : 'hidden'}}" id="representant">
        @include('livewire.app.enrollment.steper.representant')
    </div>
    <div class="{{($step==4) ? 'block' : 'hidden'}}" id="coexistence">
        @include('livewire.app.enrollment.steper.coexistence')
    </div>
    <div class="{{($step==5) ? 'block' : 'hidden'}}" id="potential">
        @include('livewire.app.enrollment.steper.potential')
    </div>
    <div class="{{($step==6) ? 'block' : 'hidden'}}" id="illness">
        @include('livewire.app.enrollment.steper.illness')
    </div>
    <div class="{{($step==7) ? 'block' : 'hidden'}}" id="conditions">
        @include('livewire.app.enrollment.steper.conditions')
    </div>
    <div class="{{($step==8) ? 'block' : 'hidden'}}" id="specialist">
        @include('livewire.app.enrollment.steper.specialist')
    </div>
    <div class="{{($step==9) ? 'block' : 'hidden'}}" id="parents">
        @include('livewire.app.enrollment.steper.parents')
    </div>
</div>