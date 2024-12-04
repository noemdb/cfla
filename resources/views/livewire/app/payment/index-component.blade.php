<div>

    @include('livewire.app.payment.start')
    @if ($modalSearch) @include('livewire.app.payment.search') @endif
    @if ($modalAssistent) @include('livewire.app.payment.assistant') @endif
    @if ($modalEmpty) @include('livewire.app.payment.empty') @endif
    @if ($modalOperOk) @include('livewire.app.payment.operOk') @endif

</div>