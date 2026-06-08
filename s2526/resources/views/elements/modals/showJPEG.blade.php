<!-- Button trigger modal -->
<a class="btn btn-link" href="#" role="button" data-toggle="modal" data-target="#{{$modal_id ?? 'exampleModal'}}">
    @if ($preview)
        <img src="{{ asset($fileUrl) }}" class="mx-auto d-block img-fluid img-thumbnail shadow-sm p-2">
    @else
        <i class="fa fa-file-image p-2 border rounded shadow-sm text-primary" aria-hidden="true"></i>
    @endif
</a>

<!-- Modal -->
<div class="modal fade" id="{{$modal_id ?? 'exampleModal'}}" tabindex="-1" role="dialog" aria-labelledby="{{$modal_id ?? 'exampleModal'}}Label" aria-hidden="true" style="z-index: 2050">
    <div class="modal-dialog modal-lg" style="width: 98%;height: 92%; padding: 0;">
        <div class="modal-content" style="height: 99%;">
            <div class="modal-header py-1">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    {{-- <div class="d-flex justify-content-center"> --}}
                        <img src="{{ asset($fileUrl) }}" class="img-fluid">
                    {{-- </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
