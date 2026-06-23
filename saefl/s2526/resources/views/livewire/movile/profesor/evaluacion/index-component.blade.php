<div>
    <div id="main-evaluacions-load">

        @if ($modeMain)
            <div>
                @include('movile.android.module.profesor.pevaluacions.partials.evaluacions')
            </div>
        @endif

        @if ($modeLoad)
            <div>
                @include('movile.android.module.profesor.pevaluacions.partials.load')
            </div>
        @endif
    </div>

</div>
