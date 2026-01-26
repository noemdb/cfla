<div x-data>

    @include('livewire.app.payment.start')

    <template x-teleport="body">
        <div>
            @if ($modalSearch)
                @include('livewire.app.payment.search')
            @endif
            @if ($modalAssistent)
                @include('livewire.app.payment.assistant')
            @endif
            @if ($modalEmpty)
                @include('livewire.app.payment.empty')
            @endif
            @if ($modalOperOk)
                @include('livewire.app.payment.operOk')
            @endif
        </div>
    </template>

</div>
