<div class="h-100 border-0 shadow p-1 mb-2 bg-white rounded"
    {{-- style=" max-width:10rem; align-items: start; display: flex; justify-content: center;" --}}
    >
    <div class="widget-chart text-left w-100">
        <div class="progress-circle-wrapper text-center">
            <div class="circle-progress d-inline-block circle-progress-primary">
                <i class="{{$icon ?? 'fas fa-user-edit'}}  fa-2x text-{{$class ?? ''}}" aria-hidden="true"></i>
            </div>
        </div>
        <div class="widget-chart-content">
            <div class="widget-subheading text-{{$class ?? ''}}">{{ $header ?? '' }}</div>
            <div class="font-weight-bolder widget-numbers">{{$total ?? '0'}}<span class="small text-muted">{{ $unidad ?? '' }}</span></div>
            <div class="font-weight-bolder text-center small text-{{$class ?? ''}}">{{$title ?? ''}}</div>
            <div class="widget-description text-success">

                @if ( !empty($subtitle) )
                    <span class="pl-1 small text-muted font-weight-bold">{{$subtitle ?? ''}}</span>
                @endif

                @if ( !empty($bars) && !empty($width))
                    <div class="px-2 small">
                        @component('directors.elements.progress.bars.simple')
                            {{-- @slot('title','% de avance') --}}
                            @slot('width',$width)
                            {{-- @slot('height',(!empty($height) ? $height:'1rem')) --}}
                            @slot('porcentage','true')
                        @endcomponent
                    </div>
                @endif

            </div>
        </div>
    </div>

</div>
