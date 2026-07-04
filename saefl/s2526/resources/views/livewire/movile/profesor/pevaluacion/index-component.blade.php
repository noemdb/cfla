<div>

    @if ($modeMain)
        <div id="nav-tab-profesor">
            @include('movile.android.module.profesor.pevaluacions.partials.notas')
        </div>
    @endif

    @if ($modeLoad)
        <div id="nav-tab-profesor-load">
        @include('livewire.movile.profesor.load')
        </div>
    @endif


</div>
