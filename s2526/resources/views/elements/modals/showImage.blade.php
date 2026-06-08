<!-- Button trigger modal -->
<a name="" id="" class="btn btn-link" href="#" role="button" data-toggle="modal" data-target="#{{$modal_id ?? 'exampleModal'}}">
    <img src="{{ asset($imageUrl) }}" class="mx-auto d-block img-fluid img-thumbnail shadow-sm p-2">
</a>

<!-- Modal -->
<div class="modal fade" id="{{$modal_id ?? 'exampleModal'}}" tabindex="-1" role="dialog" aria-labelledby="{{$modal_id ?? 'exampleModal'}}Label" aria-hidden="true" style="z-index: 2050">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header py-1">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                <div class="container" style="max-height: 400px;">
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset($fileUrl) }}" class="img-fluid" style="max-height: 400px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

