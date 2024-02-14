<div>

    @include('livewire.app.enrollment.start')
    @if ($modalSearch) @include('livewire.app.enrollment.search') @endif
    @if ($modalAssistent) @include('livewire.app.enrollment.assistant') @endif

</div>