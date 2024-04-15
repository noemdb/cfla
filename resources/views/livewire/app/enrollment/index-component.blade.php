<div>

    @include('livewire.app.enrollment.start')
    @if ($modalSearch) @include('livewire.app.enrollment.search') @endif
    @if ($modalAssistent) @include('livewire.app.enrollment.assistant') @endif
    @if ($modalEmpty) @include('livewire.app.enrollment.modal.empty') @endif

</div>