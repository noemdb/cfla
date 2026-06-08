<!-- Modal -->
<div class="modal fade modal-obj" id="{{$modal_id ?? 'exampleModal'}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog {{$size ?? ''}} modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header alert-{{$classH ?? ''}}">
            <h4 class="modal-title" id="{{$modal_id ?? ''}}_ModalLabel">{{$title ?? 'SAEFL'}}</h4>
                {{-- @if (!empty($close)) --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                {{-- @endif --}}
            </div>
            <div class="modal-body">
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
