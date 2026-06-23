<div class="text-center shadow rounded rounded-lg h-100 alert-{{$class ?? ''}} " style="max-width:10rem;">
    <div class=" small font-weight-bolder">
        {!! $title ?? '' !!}
    </div>
    
    <i class="{{$icon ?? 'fas fa-user-edit'}} fa-2x text-{{$class ?? ''}} pt-2" aria-hidden="true"></i>
    <h1 class="font-weight-bolder align-middle pb-0 mb-0 text-dark">
        <span class="text-dark">{{ $total ?? '' }}<span class="text-muted" style="font-size:1.2rem">{{ $unidad ?? '' }}</span></span>
    </h1>
    
    @if ( !empty($subtitle) )
        <div class="py-1 small text-muted font-weight-bold" style="line-height:0.8rem;">
            {!! $subtitle ?? ''!!}
        </div>
    @endif
    @if ( !empty($bars) && !empty($total))
        <div class="px-2 small">
            @component('evaluacions.elements.progress.bars.simple')
                @slot('width',$total)
                @slot('porcentage','true')
            @endcomponent
        </div>
    @endif
</div>
