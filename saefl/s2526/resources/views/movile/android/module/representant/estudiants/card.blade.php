
<div class="text-start p-1 m-1 small">
    @if ($estudiants->isNotEmpty())
        <div class=" pl-1 ml-1 text-dark">
            <div class="ml-1 pl-1">
                @foreach ($estudiants as $estudiant)

                    @include('movile.android.module.representant.estudiants.simple')

                @endforeach
            </div>
        </div>
    @endif
</div>
