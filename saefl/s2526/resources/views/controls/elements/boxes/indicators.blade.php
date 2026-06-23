<div class="text-center shadow rounded rounded-lg h-100 alert-{{$class ?? ''}} " style="max-width:10rem;">
    <i class="{{$icon ?? 'fas fa-user-edit'}} fa-2x text-{{$class ?? ''}} pt-2" aria-hidden="true"></i>
    <h1 class="font-weight-bolder align-middle pb-0 mb-0 text-dark">
        <span class="text-dark">{{ $total ?? '' }}<span class="text-muted" style="font-size:1.2rem">{{ $unidad ?? '' }}</span></span>
    </h1>
    <div class=" small font-weight-bolder">
        {{ $title ?? '' }}
    </div>
    @if ( !empty($subtitle) )
        <div class="py-1 small text-muted font-weight-bold" style="line-height:0.8rem;">
            {{$subtitle ?? ''}}
        </div>
        {{-- <span class="pl-1 small text-muted font-weight-bold" style="line-height:1em;">{{$subtitle ?? ''}}</span> --}}
    @endif
    @if ( !empty($bars) && !empty($total))
        <div class="px-2 small">
            @component('controls.elements.progress.bars.simple')
                @slot('width',$total)
                @slot('porcentage','true')
            @endcomponent
        </div>
    @endif
</div>
