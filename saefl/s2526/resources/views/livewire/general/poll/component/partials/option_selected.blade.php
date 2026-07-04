<div>
    <div class="alert alert-info my-2">
        <div class="d-flex justify-content-end">
            <button type="button" wire:click="close()" class="btn-close p-2 fw-bold" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <div>
            <div class="d-flex justify-content-center text-center fw-bold text-muted mb-2">
                <div class="h4 text-capitalize">
                    {{ Str::title(Str::lower($poll_option->text)) ?? null}}
                </div>
                {{-- <div> Gustos: </div> --}}
            </div>

            @if ($poll_option->image_url)

                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card w-100">
                                <img src="{!! asset($poll_option->image_url) !!}" class="card-img-top rounded-circle" alt="...">
                                <div class="card-body">
                                    <div class="card-text">
                                        {{-- <div class="fw-bold"> <span class=" text-muted">{{ $poll_option->description ?? null}}</span></div> --}}
                                        @if ($poll_option->observations) <i class="fw-normal"> {{$poll_option->observations}}. </i> @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            @if ($poll_option->body)
                                <div class="text-start ms-2">
                                    <div class="">{!! $poll_option->body!!}</div>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

            @endif

            <div class="d-flex justify-content-center mt-2 pt-2">
                <button type="button" class="btn btn-success btn-lg w-75" wire:click="save({{$poll_option->id}})" wire:loading.attr="disabled">Registrar su selección.</button>
            </div>

        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger py-2 my-2 text-start">
            @foreach ($errors->all() as $error)
                <div class="border border-bottom-0">{{$loop->iteration}}.- {{ $error }}</div>
            @endforeach
        </div>
    @endif

</div>
