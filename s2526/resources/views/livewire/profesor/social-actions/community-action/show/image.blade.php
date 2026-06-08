<div class=" d-flex justify-content-center">
    <div class="row">
        <div class="col-auto">

            <div class="table-secondary py-2 alert-dismissible fade show rounded" role="alert" wire:click="close()">
                &nbsp;
                <button type="button" class="close" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="border rounded border-top-0 shadow-sm pl-2">

                <div class="mx-4 px-4">
                    <span class=" font-weight-bold">{{$list_comment['title'] ?? ''}}</span>: <span
                        class=" font-weight-normal"> {{$community_action->title ?? ''}}</span> <br>
                    <span class=" font-weight-bold">{{$list_comment['description'] ?? ''}}</span>: <span
                        class=" font-weight-normal"> {{$community_action->description ?? ''}}</span> <br>
                    <span class=" font-weight-bold">{{$list_comment['status'] ?? ''}}</span>: <span
                        class=" font-weight-normal"> {{ ($community_action->status) ? 'Activa' : 'Desactiva' }}</span>
                </div>

                <div class="d-flex justify-content-center">
                    <img src="{{ asset($community_action->image_url) ?? null}}"
                        class="img-fluid border rounded shadow-lg p-4 m-4" alt="Imagen borrada">
                </div>

            </div>

        </div>
    </div>
</div>