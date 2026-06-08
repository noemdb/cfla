<!-- Button trigger modal -->
<a name="" id="" class="btn btn-link" href="#" role="button" data-toggle="modal" data-target="#{{$modal_id ?? 'exampleModal'}}">
    <i class="fa fa-file-pdf p-2 border rounded shadow-sm text-danger" aria-hidden="true"></i>
</a>

<!-- Modal -->
<div class="modal fade" id="{{$modal_id ?? 'exampleModal'}}" tabindex="-1" role="dialog" aria-labelledby="{{$modal_id ?? 'exampleModal'}} Label" aria-hidden="true" style="z-index: 2050">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header py-1">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                <div class="text-center embed-responsive embed-responsive-16by9">
                    <embed class="" src="{{ asset($fileUrl) }}" type="application/pdf">
                </div>
            </div>
        </div>
    </div>
</div>

