<div class="min-h-screen bg-gray-900 text-white">
    @if($currentView === 'student-identification')
        @include('livewire.diagnostic.student-identification')
    @elseif($currentView === 'dashboard')
        @include('livewire.diagnostic.dashboard')
    @elseif($currentView === 'wizard')
        @include('livewire.diagnostic.wizard')
    @elseif($currentView === 'summary')
        @include('livewire.diagnostic.summary')
    @elseif($currentView === 'guide')
        @include('livewire.diagnostic.guide')
    @endif
</div>
