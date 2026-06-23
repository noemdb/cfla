<!-- Modal -->
<div class="modal fade" id="{{$id ?? 'exampleModal'}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    {{-- size: modal-sm modal-lg modal-xl --}}
    <div class="modal-dialog {{$size ?? ''}} {{$scrollable ?? ''}} modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header alert-{{$classH ?? ''}}  p-2">
                <h5 class="modal-title font-weight-bolder p-1" id="exampleModalLabel">
                    @if (!empty($title_icon))
                        <i class="{{$title_icon ?? ''}}" aria-hidden="true"></i>
                    @endif
                    {{$title ?? 'Modal title'}}
                    <small class="font-weight-bold text-muted d-block p-0">
                       <span class=" small p-0">{{ $subtitle ?? ''}}</span>
                    </small>
                    {{-- @if (!empty($subtitle))
                        <br>
                    @endif --}}
                </h5>
                @if ($close)
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                @endif
            </div>
            <div class="modal-body p-2">
                {{$body ?? ''}}
            </div>
            @if (!empty($footer))
                <div class="modal-footer">
                    {{$footer ?? ''}}
                </div>
            @endif
        </div>
    </div>
</div>
